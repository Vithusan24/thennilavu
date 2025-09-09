<?php
/*************************************************
 *  CALL MANAGEMENT - SINGLE FILE (PHP/HTML/CSS/JS)
 *  DB: members(id, name, phone, profession, ...)
 *      calls(call_id, caller_id, receiver_id, call_date, duration)
 *************************************************/

///////////////////////
// 0) DB CONNECTION  //
///////////////////////
$host = "127.0.0.1";
$user = "root";   // change if needed
$pass = "";       // change if needed
$db   = "matrimony";

$conn = @new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    die("DB Connection failed: " . $conn->connect_error);
}

// Utility: JSON response
function json_out($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// Utility: sanitize date-time from <input type="datetime-local">
function parse_dt_local($s) {
    if (!$s) return date("Y-m-d H:i:s");
    $s = str_replace('T',' ',$s);
    // if only date provided, add time
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) $s .= ' 00:00:00';
    return $s;
}

////////////////////////////////////////
// 1) ROUTED AJAX/CSV ACTIONS (API)   //
////////////////////////////////////////
$action = $_GET['action'] ?? null;

if ($action === 'get_member') {
    // GET ?action=get_member&id=123
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) json_out(['error' => 'Invalid member id']);

    $stmt = $conn->prepare("SELECT id, name, phone, profession FROM members WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $m = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$m) json_out(['error' => 'Member not found']);
    // We’ll show profession as “package” since members table has no package column.
    $m['package'] = $m['profession'] ?: '—';
    json_out($m);
}

if ($action === 'add_call' && $_SERVER['REQUEST_METHOD']==='POST') {
    // POST fields expected:
    // receiver_id (member id you called), call_date (datetime-local), duration (minutes), notes (optional)
    $receiver_id = intval($_POST['receiver_id'] ?? 0);
    $call_date   = parse_dt_local($_POST['call_date'] ?? '');
    $duration    = intval($_POST['duration'] ?? 0);
    // caller_id: if you track staff/admin as member, pass it. Else NULL.
    $caller_id   = isset($_POST['caller_id']) && $_POST['caller_id'] !== '' ? intval($_POST['caller_id']) : null;

    if ($receiver_id <= 0) json_out(['status'=>'error','message'=>'Receiver (member) is required']);

    // Insert call
    if ($caller_id === null) {
        $stmt = $conn->prepare("INSERT INTO calls (caller_id, receiver_id, call_date, duration) VALUES (NULL, ?, ?, ?)");
        $stmt->bind_param("isi", $receiver_id, $call_date, $duration);
    } else {
        $stmt = $conn->prepare("INSERT INTO calls (caller_id, receiver_id, call_date, duration) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $caller_id, $receiver_id, $call_date, $duration);
    }
    if ($stmt->execute()) {
        $id = $stmt->insert_id;
        $stmt->close();
        json_out(['status'=>'success','message'=>'Call saved','call_id'=>$id]);
    } else {
        $err = $conn->error;
        $stmt->close();
        json_out(['status'=>'error','message'=>$err]);
    }
}

if ($action === 'get_calls') {
    // Optional search: ?q=searchterm
    $q = trim($_GET['q'] ?? '');
    $like = '%' . $conn->real_escape_string($q) . '%';

    $sql = "
        SELECT 
            c.call_id, c.call_date, c.duration,
            m2.id AS member_id, m2.name AS member_name, m2.phone AS member_phone, m2.profession AS member_package,
            m1.id AS caller_id, m1.name AS caller_name
        FROM calls c
        LEFT JOIN members m1 ON c.caller_id = m1.id
        LEFT JOIN members m2 ON c.receiver_id = m2.id
    ";
    if ($q !== '') {
        $sql .= " WHERE (m2.name LIKE '$like' OR m2.phone LIKE '$like') ";
    }
    $sql .= " ORDER BY c.call_date DESC, c.call_id DESC";

    $res = $conn->query($sql);
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $r['member_package'] = $r['member_package'] ?: '—';
        $rows[] = $r;
    }
    json_out($rows);
}

if ($action === 'get_history') {
    // history per member: ?member_id=123
    $member_id = intval($_GET['member_id'] ?? 0);
    if ($member_id <= 0) json_out(['error'=>'Invalid member id']);

    $stmt = $conn->prepare("
        SELECT call_id, call_date, duration 
        FROM calls 
        WHERE receiver_id=? 
        ORDER BY call_date DESC, call_id DESC
        LIMIT 100
    ");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $out = [];
    while ($row = $res->fetch_assoc()) {
        // status inference: treat duration 0 as Missed, else Completed
        $row['status'] = ($row['duration'] > 0) ? 'Completed' : 'Missed';
        $out[] = $row;
    }
    $stmt->close();
    json_out($out);
}

if ($action === 'get_summary') {
    // ?range=today|yesterday|month
    $range = $_GET['range'] ?? 'today';
    if ($range === 'today') {
        $cond = "DATE(call_date)=CURDATE()";
    } elseif ($range === 'yesterday') {
        $cond = "DATE(call_date)=CURDATE()-INTERVAL 1 DAY";
    } else { // month
        $cond = "YEAR(call_date)=YEAR(CURDATE()) AND MONTH(call_date)=MONTH(CURDATE())";
    }
    $sql = "
        SELECT 
          COUNT(*) AS total_calls,
          SUM(CASE WHEN duration>0 THEN 1 ELSE 0 END) AS completed_calls,
          SUM(CASE WHEN duration=0 THEN 1 ELSE 0 END) AS missed_calls,
          ROUND(AVG(duration),2) AS avg_duration
        FROM calls
        WHERE $cond
    ";
    $r = $conn->query($sql)->fetch_assoc();
    $r['avg_duration'] = $r['avg_duration'] ?? 0;
    json_out($r);
}

if ($action === 'export_csv') {
    // Export a member’s call history
    $member_id = intval($_GET['member_id'] ?? 0);
    if ($member_id <= 0) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Invalid member id";
        exit;
    }
    // fetch member
    $m = $conn->query("SELECT name FROM members WHERE id=$member_id")->fetch_assoc();
    $member_name = $m['name'] ?? ("member_" . $member_id);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="'.preg_replace('/[^a-z0-9_]+/i','_', $member_name).'_calls.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Call ID','Call Date','Duration (min)','Status']);

    $res = $conn->query("
        SELECT call_id, call_date, duration
        FROM calls
        WHERE receiver_id=$member_id
        ORDER BY call_date DESC, call_id DESC
    ");
    while ($row = $res->fetch_assoc()) {
        $status = ($row['duration']>0) ? 'Completed' : 'Missed';
        fputcsv($output, [$row['call_id'], $row['call_date'], $row['duration'], $status]);
    }
    fclose($output);
    exit;
}

// If we got this far without exiting, render the HTML UI below.
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Call Management</title>
<style>
  :root{
    --bg:#0b1220; --card:#121a2b; --muted:#9aa4b2; --text:#e7edf5; --accent:#3b82f6; --accent-2:#22c55e; --danger:#ef4444;
    --border:#1e2a44; --warn:#f59e0b; --shadow:0 8px 30px rgba(0,0,0,.35);
  }
  *{box-sizing:border-box}
  body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:linear-gradient(120deg,#0b1220,#0a1327);color:var(--text)}
  .top-nav{display:flex;align-items:center;justify-content:space-between;padding:12px 18px;border-bottom:1px solid var(--border);position:sticky;top:0;background:rgba(11,18,32,.8);backdrop-filter:blur(6px);z-index:10}
  .logo-circle{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#1f2937,#0ea5e9);font-weight:700}
  .main-menu a{color:var(--muted);text-decoration:none;margin-left:18px}
  .dashboard-layout{display:grid;grid-template-columns:260px 1fr;min-height:calc(100vh - 64px)}
  aside.sidebar{border-right:1px solid var(--border);padding:18px;background:rgba(6,12,24,.6)}
  .matrimony-name{font-weight:700;margin-bottom:14px}
  .sidebar-menu{list-style:none;padding:0;margin:0;display:grid;gap:6px}
  .sidebar-link{display:block;padding:10px 12px;border-radius:10px;color:var(--muted);text-decoration:none;border:1px solid transparent}
  .sidebar-link:hover{background:rgba(255,255,255,.04)}
  .sidebar-link.active{background:linear-gradient(135deg,#0b1220,#0f172a);border-color:var(--border);color:#fff}
  main.main-content{padding:22px;display:grid;gap:22px}
  .content-section{background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);padding:18px}
  h2{margin:0 0 12px 0;font-size:18px}
  .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .form-group{display:grid;gap:6px;margin-bottom:10px}
  label{color:var(--muted);font-size:12px}
  input,select,button{font:inherit}
  input,select{background:#0b1424;border:1px solid var(--border);border-radius:12px;padding:10px 12px;color:#e8f0ff;outline:none}
  input:focus{border-color:#0ea5e9;box-shadow:0 0 0 3px rgba(14,165,233,.15)}
  .action-btn, .btn{background:var(--accent);border:none;color:#fff;padding:10px 14px;border-radius:12px;cursor:pointer}
  .action-btn.secondary{background:#334155}
  .btn.secondary{background:#334155}
  .btn.danger{background:var(--danger)}
  .btn.ghost{background:transparent;border:1px solid var(--border);color:var(--muted)}
  .top-actions{display:flex;gap:10px;flex-wrap:wrap}
  .search-bar{display:flex;align-items:center;gap:10px;background:#0b1424;border:1px solid var(--border);border-radius:12px;padding:8px 12px}
  .search-bar input{flex:1;border:none;background:transparent;padding:6px 0}
  table{width:100%;border-collapse:collapse}
  .data-table th,.data-table td{padding:10px;border-bottom:1px solid var(--border);text-align:left}
  .data-table thead th{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.04em}
  .badge{padding:2px 8px;border-radius:999px;border:1px solid var(--border);font-size:12px;color:#dbeafe;background:#0b1424;display:inline-block}
  .badge.premium{border-color:#06b6d4;color:#a5f3fc}
  .badge.gold{border-color:#f59e0b;color:#fde68a}
  .badge.silver{border-color:#9ca3af;color:#e5e7eb}
  .modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:50}
  .modal-content{max-width:920px;margin:5vh auto;background:var(--card);border:1px solid var(--border);border-radius:16px;box-shadow:var(--shadow);overflow:hidden}
  .modal-header{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;border-bottom:1px solid var(--border)}
  .modal-body{padding:16px;display:grid;gap:16px}
  .modal-stats{display:flex;gap:12px;flex-wrap:wrap}
  .stat-item{background:#0b1424;border:1px solid var(--border);border-radius:12px;padding:10px 14px;min-width:140px}
  .stat-number{font-weight:700;font-size:18px}
  .status-badge{padding:2px 8px;border-radius:10px;font-size:12px;border:1px solid var(--border)}
  .status-badge.completed{background:rgba(34,197,94,.12);color:#86efac}
  .status-badge.missed{background:rgba(239,68,68,.12);color:#fca5a5}
  .table-scroll{max-height:360px;overflow:auto;border:1px solid var(--border);border-radius:12px}
  .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
  @media (max-width:900px){.dashboard-layout{grid-template-columns:1fr}.form-row{grid-template-columns:1fr}}
</style>
</head>
<body>
<header class="top-nav">
  <div class="logo-circle">Logo</div>
  <nav class="main-menu">
    <a href="#" class="logout">Logout</a>
  </nav>
  <div class="user-info" id="userInfo"><span id="userDisplay"></span></div>
</header>

<div class="dashboard-layout">
  <aside class="sidebar">
    <div class="matrimony-name">Matrimony Name</div>
    <ul class="sidebar-menu">
      <li><a href="#" class="sidebar-link">Manage members</a></li>
      <li><a href="#" class="sidebar-link active">Call management</a></li>
      <li><a href="#" class="sidebar-link">User message management</a></li>
      <li><a href="#" class="sidebar-link">Review management</a></li>
      <li><a href="#" class="sidebar-link">Transaction management</a></li>
      <li><a href="#" class="sidebar-link">Packages management</a></li>
      <li><a href="#" class="sidebar-link">Blog management</a></li>
      <li><a href="#" class="sidebar-link">Total earnings</a></li>
      <li><a id="staffLink" href="#" class="sidebar-link">Staff management</a></li>
    </ul>
  </aside>

  <main class="main-content">
    <!-- Quick stats -->
    <div class="top-actions">
      <button class="action-btn" id="todayCallsBtn">Today calls</button>
      <button class="action-btn secondary" id="yesterdayCallsBtn">Yesterday calls</button>
      <button class="action-btn" id="monthCallsBtn">Month calls</button>
      <div class="search-bar" style="margin-left:auto;min-width:260px">
        <span>🔎</span>
        <input id="searchInput" type="text" placeholder="Search by name or phone"/>
      </div>
    </div>

    <!-- Call Form -->
    <div class="content-section">
      <h2>Call Details</h2>
      <form id="callForm">
        <div class="form-row">
          <div>
            <div class="form-group">
              <label>Member ID (receiver)</label>
              <input type="number" id="memberId" placeholder="Enter member ID" required/>
            </div>
            <div class="form-group">
              <label>Package (auto from member.profession)</label>
              <input type="text" id="packageField" placeholder="—" readonly/>
            </div>
            <div class="form-group">
              <label>Call Date & Time</label>
              <input type="datetime-local" id="callDate"/>
            </div>
          </div>
          <div>
            <div class="form-group">
              <label>Member Name</label>
              <input type="text" id="memberName" placeholder="Auto-fill" readonly/>
            </div>
            <div class="form-group">
              <label>Contact Number</label>
              <input type="text" id="memberPhone" placeholder="Auto-fill" readonly/>
            </div>
            <div class="form-group">
              <label>Duration (minutes)</label>
              <input type="number" id="duration" min="0" value="0"/>
            </div>
          </div>
        </div>
        <div style="display:flex;gap:10px">
          <button type="submit" class="btn">Submit</button>
          <button type="button" class="btn ghost" id="resetForm">Reset</button>
        </div>
      </form>
    </div>

    <!-- Calls Table -->
    <div class="content-section">
      <h2>Members Call Details</h2>
      <div class="table-scroll">
        <table class="data-table" id="callsTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Member</th>
              <th>Package</th>
              <th>Phone</th>
              <th>Date & Time</th>
              <th>Duration</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody><!-- rows injected --></tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<!-- History Modal -->
<div id="callHistoryModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>📞 Call History — <span id="histMemberName"></span></h3>
      <button id="closeModal" class="btn ghost">✖</button>
    </div>
    <div class="modal-body">
      <div class="modal-stats">
        <div class="stat-item"><div class="stat-number" id="histTotal">0</div><div class="stat-label">Total Calls</div></div>
        <div class="stat-item"><div class="stat-number" id="histCompleted">0</div><div class="stat-label">Completed</div></div>
        <div class="stat-item"><div class="stat-number" id="histMissed">0</div><div class="stat-label">Missed</div></div>
      </div>
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr><th>Date</th><th>Duration</th><th>Status</th></tr>
          </thead>
          <tbody id="histBody"><!-- rows --></tbody>
        </table>
      </div>
      <div class="grid-2">
        <button id="histCallBtn" class="btn">📲 Call Now</button>
        <button id="histExportBtn" class="btn secondary">⬇ Export CSV</button>
      </div>
    </div>
  </div>
</div>

<script>
// ----------------------
// AUTH UI (optional)
// ----------------------
document.addEventListener('DOMContentLoaded', () => {
  const userType = localStorage.getItem('userType') || 'admin';
  const username = localStorage.getItem('username') || 'staff';
  const isLoggedIn = localStorage.getItem('isLoggedIn') || '1';

  if (!isLoggedIn) {
    // window.location.href = 'login.html';
  }
  const userDisplay = document.getElementById('userDisplay');
  if (userDisplay) userDisplay.textContent = (userType || 'USER').toUpperCase();

  const staffLink = document.getElementById('staffLink');
  if (staffLink && userType !== 'admin') staffLink.style.display = 'none';
});

// ----------------------
// HELPERS
// ----------------------
function fmtDuration(mins){
  mins = Number(mins||0);
  return mins+" min";
}
function badgeForPackage(pkg){
  const p = (pkg || '—').toLowerCase();
  let cls = 'badge';
  if (p.includes('premium')) cls += ' premium';
  else if (p.includes('gold')) cls += ' gold';
  else if (p.includes('silver')) cls += ' silver';
  return `<span class="${cls}">${pkg||'—'}</span>`;
}

// ----------------------
// LOAD CALLS
// ----------------------
const callsTableBody = document.querySelector('#callsTable tbody');
const searchInput = document.getElementById('searchInput');

async function loadCalls(q=''){
  const res = await fetch(`?action=get_calls${q ? '&q='+encodeURIComponent(q): ''}`);
  const data = await res.json();
  callsTableBody.innerHTML = '';
  data.forEach(row=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${row.call_id}</td>
      <td>${row.member_name || 'Unknown'}</td>
      <td>${badgeForPackage(row.member_package)}</td>
      <td>${row.member_phone || ''}</td>
      <td>${row.call_date}</td>
      <td>${fmtDuration(row.duration)}</td>
      <td>
        <button class="btn secondary btn-history" data-id="${row.member_id}" data-name="${row.member_name}">History</button>
        <button class="btn ghost btn-export" data-id="${row.member_id}" data-name="${row.member_name}">Export</button>
      </td>
    `;
    callsTableBody.appendChild(tr);
  });
}

searchInput.addEventListener('input', e=>{
  loadCalls(e.target.value.trim());
});

// ----------------------
// SUMMARY BUTTONS
// ----------------------
async function showSummary(range, label){
  const r = await (await fetch(`?action=get_summary&range=${range}`)).json();
  const msg = `${label}\n\nTotal: ${r.total_calls||0}\nCompleted: ${r.completed_calls||0}\nMissed: ${r.missed_calls||0}\nAvg duration: ${r.avg_duration||0} min`;
  alert(msg);
}
document.getElementById('todayCallsBtn').onclick = ()=>showSummary('today','Today\'s Calls');
document.getElementById('yesterdayCallsBtn').onclick = ()=>showSummary('yesterday','Yesterday\'s Calls');
document.getElementById('monthCallsBtn').onclick = ()=>showSummary('month','This Month\'s Calls');

// ----------------------
// FORM: AUTO-FILL MEMBER BY ID
// ----------------------
const memberIdInput = document.getElementById('memberId');
memberIdInput.addEventListener('blur', async ()=>{
  const id = memberIdInput.value.trim();
  if(!id) return;
  const r = await (await fetch(`?action=get_member&id=${encodeURIComponent(id)}`)).json();
  if (r.error){
    alert('Member not found');
    document.getElementById('memberName').value = '';
    document.getElementById('memberPhone').value = '';
    document.getElementById('packageField').value = '';
    return;
  }
  document.getElementById('memberName').value = r.name || '';
  document.getElementById('memberPhone').value = r.phone || '';
  document.getElementById('packageField').value = r.package || '—';
});

// ----------------------
// FORM: SUBMIT ADD CALL
// ----------------------
document.getElementById('callForm').addEventListener('submit', async (e)=>{
  e.preventDefault();
  const receiver_id = memberIdInput.value.trim();
  if(!receiver_id){ alert('Please enter Member ID'); return; }
  const call_date = document.getElementById('callDate').value;
  const duration  = document.getElementById('duration').value || 0;

  const fd = new FormData();
  fd.append('receiver_id', receiver_id);
  fd.append('call_date', call_date);
  fd.append('duration', duration);
  // If you track caller/staff as a member, uncomment and set:
  // fd.append('caller_id', '1');

  const r = await (await fetch('?action=add_call', {method:'POST', body:fd})).json();
  alert(r.message || 'Saved.');
  if (r.status === 'success'){
    e.target.reset();
    document.getElementById('packageField').value = '';
    document.getElementById('memberName').value = '';
    document.getElementById('memberPhone').value = '';
    loadCalls(searchInput.value.trim());
  }
});
document.getElementById('resetForm').onclick = ()=>{
  document.getElementById('callForm').reset();
  document.getElementById('packageField').value = '';
  document.getElementById('memberName').value = '';
  document.getElementById('memberPhone').value = '';
};

// ----------------------
// HISTORY MODAL
// ----------------------
const modal = document.getElementById('callHistoryModal');
const closeModalBtn = document.getElementById('closeModal');
const histMemberName = document.getElementById('histMemberName');
const histBody = document.getElementById('histBody');
const histTotal = document.getElementById('histTotal');
const histCompleted = document.getElementById('histCompleted');
const histMissed = document.getElementById('histMissed');
let activeMemberId = null;

document.addEventListener('click', (e)=>{
  const hbtn = e.target.closest('.btn-history');
  const ebtn = e.target.closest('.btn-export');
  if (hbtn){
    activeMemberId = hbtn.dataset.id;
    const name = hbtn.dataset.name || 'Member';
    openHistory(activeMemberId, name);
  }
  if (ebtn){
    const mid = ebtn.dataset.id;
    window.location = `?action=export_csv&member_id=${encodeURIComponent(mid)}`;
  }
});

async function openHistory(memberId, name){
  histMemberName.textContent = name;
  const data = await (await fetch(`?action=get_history&member_id=${encodeURIComponent(memberId)}`)).json();
  histBody.innerHTML = '';
  let total=0, comp=0, miss=0;
  data.forEach(r=>{
    total++;
    const status = (r.status||'').toLowerCase();
    if (status==='completed') comp++; else miss++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${r.call_date}</td>
      <td>${fmtDuration(r.duration)}</td>
      <td><span class="status-badge ${status}">${r.status}</span></td>
    `;
    histBody.appendChild(tr);
  });
  histTotal.textContent = total;
  histCompleted.textContent = comp;
  histMissed.textContent = miss;
  modal.style.display = 'block';
}
closeModalBtn.onclick = ()=> modal.style.display = 'none';
window.addEventListener('click', (ev)=>{ if (ev.target === modal) modal.style.display='none' });

// ----------------------
// INIT
// ----------------------
document.addEventListener('DOMContentLoaded', ()=>{
  loadCalls();
  // set default datetime to now
  const now = new Date();
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
  document.getElementById('callDate').value = now.toISOString().slice(0,16);
});
</script>
</body>
</html>

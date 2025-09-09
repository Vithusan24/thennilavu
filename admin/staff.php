<?php
session_start();

// DB connection - change as needed
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'matrimony';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

// Helper to sanitize POST safely
function get_post($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : null;
}

// Handle actions
$action = strtolower(get_post('action') ?? ($_GET['action'] ?? ''));

if ($action === 'add') {
    // required fields
    $name = get_post('name');
    $email = get_post('email');
    $password = get_post('password');
    $role = get_post('role');

    if (!$name || !$email || !$password || !$role) {
        $_SESSION['error'] = "Please fill required fields.";
        header("Location: staff.php");
        exit;
    }

    // additional optional fields
    $age = get_post('age') ?: null;
    $gender = get_post('gender') ?: null;
    $address = get_post('address') ?: null;
    $position = get_post('position') ?: null;
    $phone = get_post('phone') ?: null;
    $access_level = get_post('access_level') ?: 'restricted';

    // Check duplicate email
    $stmt = $conn->prepare("SELECT staff_id FROM staff WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email already exists.";
        $stmt->close();
        header("Location: staff.php");
        exit;
    }
    $stmt->close();

    // Hash password
    $pw_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO staff (name, email, password, role, phone, age, gender, address, position, access_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssissss", $name, $email, $pw_hash, $role, $phone, $age, $gender, $address, $position, $access_level);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff added.";
    } else {
        $_SESSION['error'] = "DB error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: staff.php");
    exit;
}

if ($action === 'edit') {
    $staff_id = (int)get_post('staff_id');
    if (!$staff_id) {
        $_SESSION['error'] = "Invalid staff id.";
        header("Location: staff.php");
        exit;
    }

    $name = get_post('name');
    $email = get_post('email');
    $password = get_post('password'); // optional: only update if provided
    $role = get_post('role');
    $age = get_post('age') ?: null;
    $gender = get_post('gender') ?: null;
    $address = get_post('address') ?: null;
    $position = get_post('position') ?: null;
    $phone = get_post('phone') ?: null;
    $access_level = get_post('access_level') ?: 'restricted';

    // simple validation
    if (!$name || !$email || !$role) {
        $_SESSION['error'] = "Please fill required fields.";
        header("Location: staff.php");
        exit;
    }

    // If email changed - ensure no duplicate (optional)
    $stmt = $conn->prepare("SELECT staff_id FROM staff WHERE email = ? AND staff_id <> ?");
    $stmt->bind_param("si", $email, $staff_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Another user already uses that email.";
        $stmt->close();
        header("Location: staff.php");
        exit;
    }
    $stmt->close();

    // Build update query; include password only if non-empty
    if (!empty($password)) {
        $pw_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE staff SET name=?, email=?, password=?, role=?, phone=?, age=?, gender=?, address=?, position=?, access_level=?, updated_at=NOW() WHERE staff_id=?");
        $stmt->bind_param("sssssissssi", $name, $email, $pw_hash, $role, $phone, $age, $gender, $address, $position, $access_level, $staff_id);
    } else {
        $stmt = $conn->prepare("UPDATE staff SET name=?, email=?, role=?, phone=?, age=?, gender=?, address=?, position=?, access_level=?, updated_at=NOW() WHERE staff_id=?");
        $stmt->bind_param("ssssissssi", $name, $email, $role, $phone, $age, $gender, $address, $position, $access_level, $staff_id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff updated.";
    } else {
        $_SESSION['error'] = "DB error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: staff.php");
    exit;
}

if ($action === 'delete') {
    $staff_id = (int)get_post('staff_id');
    if (!$staff_id) {
        $_SESSION['error'] = "Invalid staff id.";
        header("Location: staff.php");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM staff WHERE staff_id = ?");
    $stmt->bind_param("i", $staff_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff deleted.";
    } else {
        $_SESSION['error'] = "DB error: " . $stmt->error;
    }
    $stmt->close();
    header("Location: staff.php");
    exit;
}

if ($action === 'get') {
    $staff_id = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : 0;
    
    if ($staff_id > 0) {
        $stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id = ?");
        $stmt->bind_param("i", $staff_id);
    } else {
        $stmt = $conn->prepare("SELECT * FROM staff");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $staff = $result->fetch_all(MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'staff' => $staff
    ]);
    exit;
}

// If unknown action
if (!empty($action)) {
    $_SESSION['error'] = "Unknown action.";
    header("Location: staff.php");
    exit;
}

// Fetch all staff for display
$result = $conn->query("SELECT * FROM staff");
$all_staff = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Staff - Admin Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f7f9;
      color: #333;
      line-height: 1.6;
    }

    /* Top Navigation */
    .top-nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .logo-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #4a6cf7;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
    }

    .main-menu {
      display: flex;
      gap: 1.5rem;
    }

    .logout {
      color: #4a6cf7;
      text-decoration: none;
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      transition: background-color 0.3s;
    }

    .logout:hover {
      background-color: #f0f3ff;
    }

    /* Dashboard Layout */
    .dashboard-layout {
      display: flex;
      min-height: calc(100vh - 72px);
    }

    /* Sidebar */
    .sidebar {
      width: 260px;
      background-color: #fff;
      box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
      padding: 1.5rem 1rem;
      display: flex;
      flex-direction: column;
    }

    .matrimony-name {
      font-weight: 700;
      font-size: 1.2rem;
      margin-bottom: 2rem;
      padding: 0 0.5rem;
      color: #4a6cf7;
    }

    .sidebar-menu {
      list-style: none;
      flex-grow: 1;
    }

    .sidebar-link {
      display: block;
      padding: 0.8rem 1rem;
      text-decoration: none;
      color: #555;
      border-radius: 6px;
      margin-bottom: 0.5rem;
      transition: all 0.3s;
    }

    .sidebar-link:hover {
      background-color: #f0f3ff;
      color: #4a6cf7;
    }

    .sidebar-link.active {
      background-color: #4a6cf7;
      color: white;
    }

    /* Main Content */
    .main-content {
      flex-grow: 1;
      padding: 2rem;
    }

    .content-section {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
      padding: 2rem;
    }

    /* Top Actions */
    .top-actions {
      display: flex;
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .action-btn {
      padding: 0.7rem 1.5rem;
      background-color: #4a6cf7;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      transition: background-color 0.3s;
    }

    .action-btn:hover {
      background-color: #3b5be9;
    }

    /* Staff Form */
    .staff-form {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
      padding: 1.5rem;
      background-color: #f9fafc;
      border-radius: 8px;
    }

    .staff-form input, .staff-form select {
      padding: 0.8rem;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-family: 'Inter', sans-serif;
    }

    .staff-form button {
      grid-column: span 2;
    }

    /* Data Table */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1.5rem;
    }

    .data-table th,
    .data-table td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    .data-table th {
      background-color: #f9fafc;
      font-weight: 600;
    }

    .data-table tr:hover {
      background-color: #f9fafc;
    }

    .action-cell {
      display: flex;
      gap: 0.5rem;
    }

    .edit-btn, .delete-btn {
      padding: 0.4rem 0.8rem;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 0.9rem;
    }

    .edit-btn {
      background-color: #e9f5ff;
      color: #2a8bf2;
    }

    .delete-btn {
      background-color: #ffe9e9;
      color: #ff2a2a;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: #fff;
      padding: 2rem;
      border-radius: 8px;
      width: 500px;
      max-width: 90%;
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .close-btn {
      background: none;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      color: #777;
    }

    /* Message alerts */
    .alert {
      padding: 1rem;
      border-radius: 6px;
      margin-bottom: 1.5rem;
    }

    .alert-success {
      background-color: #e9ffe9;
      color: #2a8b2a;
      border: 1px solid #2a8b2a;
    }

    .alert-error {
      background-color: #ffe9e9;
      color: #ff2a2a;
      border: 1px solid #ff2a2a;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .dashboard-layout {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
        padding: 1rem;
      }
      
      .sidebar-menu {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
      }
      
      .sidebar-link {
        margin-bottom: 0;
      }
    }

    @media (max-width: 768px) {
      .staff-form {
        grid-template-columns: 1fr;
      }
      
      .staff-form button {
        grid-column: 1;
      }
      
      .top-actions {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <!-- Top Navigation -->
  <header class="top-nav">
    <div class="logo-circle">Logo</div>
    <nav class="main-menu">
      <a href="#" class="logout">Logout</a>
    </nav>
  </header>

  <div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="matrimony-name">Matrimony Name</div>
      <ul class="sidebar-menu">
        <li><a href="members.php" class="sidebar-link">Manage members</a></li>
        <li><a href="call-management.php" class="sidebar-link">Call management</a></li>
        <li><a href="user-message-management.php" class="sidebar-link">User message management</a></li>
        <li><a href="review-management.php" class="sidebar-link">Review management</a></li>
        <li><a href="transaction-management.php" class="sidebar-link">Transaction management</a></li>
        <li><a href="packages-management.php" class="sidebar-link">Packages management</a></li>
        <li><a href="blog-management.php" class="sidebar-link">Blog management</a></li>
        <li><a href="total-earnings.php" class="sidebar-link">Total earnings</a></li>
        <li><a id="staffLink" href="staff.php" class="sidebar-link active">Staff management</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <div class="content-section">
        <div id="messageArea">
          <?php
          if (isset($_SESSION['success'])) {
              echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
              unset($_SESSION['success']);
          }
          if (isset($_SESSION['error'])) {
              echo '<div class="alert alert-error">' . $_SESSION['error'] . '</div>';
              unset($_SESSION['error']);
          }
          ?>
        </div>
        
        <div class="top-actions">
          <button class="action-btn" id="managerBtn">Manager details</button>
          <button class="action-btn" id="staffBtn">Staff details</button>
          <button class="action-btn" id="addStaffBtn">Add New Staff</button>
        </div>
        
        <table class="data-table">
          <thead>
            <tr>
              <th>Employee ID</th>
              <th>Name</th>
              <th>Position</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Access Level</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="staffTableBody">
            <?php foreach ($all_staff as $staff): ?>
            <tr>
              <td><?= $staff['staff_id'] ?></td>
              <td><?= htmlspecialchars($staff['name']) ?></td>
              <td><?= htmlspecialchars($staff['position'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($staff['email']) ?></td>
              <td><?= htmlspecialchars($staff['phone'] ?? 'N/A') ?></td>
              <td><?= htmlspecialchars($staff['access_level']) ?></td>
              <td class="action-cell">
                <button class="edit-btn" data-id="<?= $staff['staff_id'] ?>">Edit</button>
                <button class="delete-btn" data-id="<?= $staff['staff_id'] ?>">Delete</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Add/Edit Staff Modal -->
  <div class="modal" id="staffModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 id="modalTitle">Add Staff Member</h2>
        <button class="close-btn" id="closeModal">&times;</button>
      </div>
      <form class="staff-form" id="staffForm" method="POST" action="staff.php">
        <input type="hidden" id="staffId" name="staff_id">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="text" id="name" name="name" placeholder="Employee name" required>
        <input type="email" id="email" name="email" placeholder="Email address" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <input type="number" id="age" name="age" placeholder="Age" min="18" max="65">
        <select id="gender" name="gender" required>
          <option value="">Select Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
        <input type="text" id="address" name="address" placeholder="Address">
        <input type="text" id="position" name="position" placeholder="Position" required>
        <input type="tel" id="phone" name="phone" placeholder="Phone number">
        <select id="role" name="role" required>
          <option value="">Select Role</option>
          <option value="admin">Admin</option>
          <option value="manager">Manager</option>
          <option value="staff">Staff</option>
        </select>
        <select id="accessLevel" name="access_level" required>
          <option value="">Access Level</option>
          <option value="full">Full Access</option>
          <option value="limited">Limited Access</option>
          <option value="restricted">Restricted Access</option>
        </select>
        <button class="action-btn" type="submit">Save Staff</button>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal" id="deleteModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Confirm Deletion</h2>
        <button class="close-btn" id="closeDeleteModal">&times;</button>
      </div>
      <p>Are you sure you want to delete this staff member? This action cannot be undone.</p>
      <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
        <form id="deleteForm" method="POST" action="staff.php">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="staff_id" id="deleteStaffId">
          <button class="action-btn" type="submit" style="background-color: #ff2a2a;">Delete</button>
        </form>
        <button class="action-btn" id="cancelDelete" style="background-color: #777;">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    // DOM Elements
    const staffTableBody = document.getElementById('staffTableBody');
    const staffModal = document.getElementById('staffModal');
    const deleteModal = document.getElementById('deleteModal');
    const staffForm = document.getElementById('staffForm');
    const modalTitle = document.getElementById('modalTitle');
    const addStaffBtn = document.getElementById('addStaffBtn');
    const closeModal = document.getElementById('closeModal');
    const closeDeleteModal = document.getElementById('closeDeleteModal');
    const cancelDelete = document.getElementById('cancelDelete');
    const messageArea = document.getElementById('messageArea');
    const deleteForm = document.getElementById('deleteForm');
    const formAction = document.getElementById('formAction');

    // Show modal for adding new staff
    addStaffBtn.addEventListener('click', () => {
      modalTitle.textContent = 'Add Staff Member';
      staffForm.reset();
      document.getElementById('staffId').value = '';
      formAction.value = 'add';
      document.getElementById('password').required = true;
      staffModal.style.display = 'flex';
    });

    // Close modal
    closeModal.addEventListener('click', () => {
      staffModal.style.display = 'none';
    });

    // Close delete modal
    closeDeleteModal.addEventListener('click', () => {
      deleteModal.style.display = 'none';
    });

    cancelDelete.addEventListener('click', () => {
      deleteModal.style.display = 'none';
    });

    // Edit staff member
    function editStaff(id) {
      fetch(`staff.php?action=get&staff_id=${id}`)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.staff.length > 0) {
            const staff = data.staff[0];
            modalTitle.textContent = 'Edit Staff Member';
            document.getElementById('staffId').value = staff.staff_id;
            document.getElementById('name').value = staff.name;
            document.getElementById('email').value = staff.email;
            document.getElementById('password').value = ''; // Don't fill password for security
            document.getElementById('password').required = false;
            document.getElementById('age').value = staff.age || '';
            document.getElementById('gender').value = staff.gender || '';
            document.getElementById('address').value = staff.address || '';
            document.getElementById('position').value = staff.position;
            document.getElementById('phone').value = staff.phone || '';
            document.getElementById('role').value = staff.role;
            document.getElementById('accessLevel').value = staff.access_level;
            formAction.value = 'edit';
            staffModal.style.display = 'flex';
          } else {
            showMessage('Error loading staff data', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showMessage('Error loading staff data', 'error');
        });
    }

    // Show delete confirmation modal
    function showDeleteModal(id) {
      document.getElementById('deleteStaffId').value = id;
      deleteModal.style.display = 'flex';
    }

    // Show message
    function showMessage(message, type) {
      messageArea.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
      
      // Auto hide after 5 seconds
      setTimeout(() => {
        messageArea.innerHTML = '';
      }, 5000);
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
      // Add event listeners to edit and delete buttons
      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = parseInt(e.target.getAttribute('data-id'));
          editStaff(id);
        });
      });
      
      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = parseInt(e.target.getAttribute('data-id'));
          showDeleteModal(id);
        });
      });
      
      // Close modals if clicked outside
      window.addEventListener('click', (e) => {
        if (e.target === staffModal) {
          staffModal.style.display = 'none';
        }
        if (e.target === deleteModal) {
          deleteModal.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html>
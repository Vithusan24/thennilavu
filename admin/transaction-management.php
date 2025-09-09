<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "matrimony";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create transactions table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS transactions (
    id VARCHAR(20) PRIMARY KEY,
    member_id INT,
    member_name VARCHAR(100),
    transaction_date DATETIME,
    amount DECIMAL(10, 2),
    status VARCHAR(20),
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (member_id) REFERENCES members(id)
)";

if (!$conn->query($createTableSQL)) {
    echo "Error creating table: " . $conn->error;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_transaction'])) {
    $id = $_POST['id'];
    $member_id = $_POST['member_id'];
    $member_name = $_POST['member_name'];
    $transaction_date = $_POST['transaction_date'];
    $amount = $_POST['amount'];
    $status = $_POST['status'];
    $payment_method = $_POST['payment_method'];
    
    // Insert transaction
    $stmt = $conn->prepare("INSERT INTO transactions (id, member_id, member_name, transaction_date, amount, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisdsss", $id, $member_id, $member_name, $transaction_date, $amount, $status, $payment_method);
    
    if ($stmt->execute()) {
        echo "<script>alert('Transaction added successfully!');</script>";
    } else {
        echo "<script>alert('Error adding transaction: " . $conn->error . "');</script>";
    }
    
    $stmt->close();
}

// Fetch members for autocomplete
$members = [];
$result = $conn->query("SELECT id, name FROM members");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $members[] = $row;
    }
}

// Fetch transactions
$transactions = [];
$result = $conn->query("SELECT * FROM transactions ORDER BY transaction_date DESC");
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}

// Calculate total transactions
$totalTransactions = count($transactions);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Transaction Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }
    
    body {
      background-color: #f5f7f9;
      color: #333;
    }
    
    /* Top Navigation */
    .top-nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
      gap: 20px;
    }
    
    .logout {
      color: #e74c3c;
      text-decoration: none;
      font-weight: 600;
    }
    
    .user-info {
      font-weight: 600;
      color: #4a6cf7;
    }
    
    /* Dashboard Layout */
    .dashboard-layout {
      display: flex;
      min-height: calc(100vh - 70px);
    }
    
    /* Sidebar */
    .sidebar {
      width: 250px;
      background-color: #fff;
      padding: 20px 0;
      box-shadow: 2px 0 4px rgba(0,0,0,0.1);
    }
    
    .matrimony-name {
      padding: 0 20px 20px;
      font-weight: bold;
      font-size: 18px;
      color: #4a6cf7;
      border-bottom: 1px solid #eee;
      margin-bottom: 20px;
    }
    
    .sidebar-menu {
      list-style: none;
      padding: 0 15px;
    }
    
    .sidebar-menu li {
      margin-bottom: 5px;
    }
    
    .sidebar-link {
      display: block;
      padding: 10px 15px;
      text-decoration: none;
      color: #555;
      border-radius: 5px;
      transition: all 0.3s;
    }
    
    .sidebar-link:hover, .sidebar-link.active {
      background-color: #f0f5ff;
      color: #4a6cf7;
    }
    
    /* Main Content */
    .main-content {
      flex: 1;
      padding: 30px;
      overflow-y: auto;
    }
    
    /* Action Buttons */
    .top-actions {
      display: flex;
      gap: 15px;
      margin-bottom: 20px;
    }
    
    .action-btn {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
    }
    
    .action-btn:not(.secondary) {
      background-color: #4a6cf7;
      color: white;
    }
    
    .action-btn.secondary {
      background-color: #f8f9fa;
      color: #555;
      border: 1px solid #ddd;
    }
    
    .action-btn:hover {
      opacity: 0.9;
      transform: translateY(-2px);
    }
    
    /* Search Bar */
    .search-bar {
      position: relative;
      margin-bottom: 25px;
      max-width: 400px;
    }
    
    .search-bar i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #888;
    }
    
    .search-bar input {
      width: 100%;
      padding: 12px 15px 12px 45px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
    }
    
    /* Content Sections */
    .content-section {
      background-color: #fff;
      border-radius: 10px;
      padding: 25px;
      margin-bottom: 25px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .content-section h2 {
      margin-bottom: 20px;
      color: #333;
      font-size: 20px;
    }
    
    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }
    
    .stat-card {
      display: flex;
      align-items: center;
      padding: 20px;
      background-color: #f8f9fa;
      border-radius: 10px;
      gap: 15px;
    }
    
    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 10px;
      background-color: #e8f0fe;
      color: #4a6cf7;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }
    
    .stat-info h3 {
      font-size: 14px;
      color: #777;
      margin-bottom: 5px;
    }
    
    .stat-number {
      font-size: 24px;
      font-weight: bold;
      color: #333;
      margin-bottom: 5px;
    }
    
    .stat-change {
      font-size: 12px;
      font-weight: 600;
    }
    
    .stat-change.positive {
      color: #2ecc71;
    }
    
    /* Data Table */
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .data-table th, .data-table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    
    .data-table th {
      font-weight: 600;
      color: #555;
      background-color: #f8f9fa;
    }
    
    .data-table tr:hover {
      background-color: #f9fafb;
    }
    
    .amount {
      font-weight: 600;
      color: #2ecc71;
    }
    
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    
    .status-badge.success {
      background-color: #e6f7ee;
      color: #2ecc71;
    }
    
    .status-badge.pending {
      background-color: #fef5e6;
      color: #f39c12;
    }
    
    .status-badge.failed {
      background-color: #fde9e7;
      color: #e74c3c;
    }
    
    .action-buttons {
      display: flex;
      gap: 10px;
    }
    
    .view-btn, .delete-btn {
      width: 35px;
      height: 35px;
      border-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border: none;
      transition: all 0.3s;
    }
    
    .view-btn {
      background-color: #e8f0fe;
      color: #4a6cf7;
    }
    
    .delete-btn {
      background-color: #fde9e7;
      color: #e74c3c;
    }
    
    .view-btn:hover, .delete-btn:hover {
      opacity: 0.8;
      transform: scale(1.05);
    }
    
    /* Filter Form */
    .filter-form {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
    }
    
    .form-row {
      display: flex;
      gap: 20px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }
    
    .form-column {
      flex: 1;
      min-width: 200px;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      color: #555;
    }
    
    .form-group input, .form-group select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
    }
    
    .form-actions {
      display: flex;
      gap: 15px;
    }
    
    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      align-items: center;
      justify-content: center;
    }
    
    .modal-content {
      background-color: #fff;
      border-radius: 10px;
      width: 100%;
      max-width: 700px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px;
      border-bottom: 1px solid #eee;
    }
    
    .modal-header h2 {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #333;
    }
    
    .close {
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
      color: #888;
    }
    
    .close:hover {
      color: #333;
    }
    
    .modal-body {
      padding: 20px;
    }
    
    .btn-primary, .btn-secondary {
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background-color: #4a6cf7;
      color: white;
    }
    
    .btn-secondary {
      background-color: #f8f9fa;
      color: #555;
      border: 1px solid #ddd;
    }
    
    .btn-primary:hover, .btn-secondary:hover {
      opacity: 0.9;
    }
    
    /* Autocomplete */
    .autocomplete {
      position: relative;
    }
    
    .autocomplete-items {
      position: absolute;
      border: 1px solid #ddd;
      border-top: none;
      z-index: 99;
      top: 100%;
      left: 0;
      right: 0;
      background-color: #fff;
      max-height: 200px;
      overflow-y: auto;
      border-radius: 0 0 5px 5px;
    }
    
    .autocomplete-item {
      padding: 10px;
      cursor: pointer;
      border-bottom: 1px solid #eee;
    }
    
    .autocomplete-item:hover {
      background-color: #f0f5ff;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .dashboard-layout {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
      }
      
      .form-row {
        flex-direction: column;
        gap: 0;
      }
      
      .form-column {
        width: 100%;
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
    <div class="user-info" id="userInfo">
      <span id="userDisplay"></span>
    </div>
  </header>

  <div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="matrimony-name">Matrimony Name</div>
      <ul class="sidebar-menu">
        <li><a href="members.php" class="sidebar-link active">Manage members</a></li>
        <li><a href="call-management.php" class="sidebar-link">Call management</a></li>
        <li><a href="user-message-management.php" class="sidebar-link">User message management</a></li>
        <li><a href="review-management.php" class="sidebar-link">Review management</a></li>
        <li><a href="transaction-management.php" class="sidebar-link">Transaction management</a></li>
        <li><a href="packages-management.php" class="sidebar-link">Packages management</a></li>
        <li><a href="blog-management.php" class="sidebar-link">Blog management</a></li>
        <li><a href="total-earnings.php" class="sidebar-link">Total earnings</a></li>
        <li><a id="staffLink" href="staff.php" class="sidebar-link">Staff management</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Action Buttons -->
      <div class="top-actions">
        <button class="action-btn" id="newTransactionBtn">
          <i class="fas fa-plus"></i>
          New Transaction
        </button>
        <button class="action-btn secondary">
          <i class="fas fa-download"></i>
          Export Data
        </button>
      </div>

      <!-- Search Bar -->
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search transactions" id="searchInput"/>
      </div>

      <!-- Transaction Statistics -->
      <div class="content-section">
        <h2>Transaction Overview</h2>
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-credit-card"></i>
            </div>
            <div class="stat-info">
              <h3>Total Transactions</h3>
              <p class="stat-number"><?php echo $totalTransactions; ?></p>
              <span class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                8.7%
              </span>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #e6f7ee; color: #2ecc71;">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
              <h3>Completed</h3>
              <p class="stat-number">
                <?php 
                  $completed = array_filter($transactions, function($t) { 
                    return $t['status'] == 'Completed'; 
                  });
                  echo count($completed);
                ?>
              </p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #fef5e6; color: #f39c12;">
              <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
              <h3>Pending</h3>
              <p class="stat-number">
                <?php 
                  $pending = array_filter($transactions, function($t) { 
                    return $t['status'] == 'Pending'; 
                  });
                  echo count($pending);
                ?>
              </p>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #fde9e7; color: #e74c3c;">
              <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
              <h3>Failed</h3>
              <p class="stat-number">
                <?php 
                  $failed = array_filter($transactions, function($t) { 
                    return $t['status'] == 'Failed'; 
                  });
                  echo count($failed);
                ?>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Transaction Table -->
      <div class="content-section">
        <h2>Transaction History</h2>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Member Name</th>
              <th>Transaction Date</th>
              <th>Amount</th>
              <th>Payment Method</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($transactions)): ?>
              <?php foreach ($transactions as $transaction): ?>
                <tr>
                  <td><?php echo $transaction['id']; ?></td>
                  <td><?php echo $transaction['member_name']; ?></td>
                  <td><?php echo date('Y-m-d H:i', strtotime($transaction['transaction_date'])); ?></td>
                  <td class="amount">$<?php echo number_format($transaction['amount'], 2); ?></td>
                  <td>
                    <span class="payment-method">
                      <?php 
                        if (strpos($transaction['payment_method'], 'Visa') !== false) {
                          echo '<i class="fab fa-cc-visa"></i> ';
                        } elseif (strpos($transaction['payment_method'], 'Mastercard') !== false) {
                          echo '<i class="fab fa-cc-mastercard"></i> ';
                        } elseif (strpos($transaction['payment_method'], 'PayPal') !== false) {
                          echo '<i class="fab fa-cc-paypal"></i> ';
                        }
                        echo $transaction['payment_method']; 
                      ?>
                    </span>
                  </td>
                  <td>
                    <span class="status-badge 
                      <?php 
                        if ($transaction['status'] == 'Completed') echo 'success';
                        elseif ($transaction['status'] == 'Pending') echo 'pending';
                        else echo 'failed';
                      ?>
                    "><?php echo $transaction['status']; ?></span>
                  </td>
                  <td>
                    <div class="action-buttons">
                      <button class="view-btn" title="View Details">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="delete-btn" title="Delete Transaction">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align: center;">No transactions found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

  
      
      <!-- Add Transaction Modal -->
      <div id="addTransactionModal" class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h2><i class="fas fa-plus"></i> Add New Transaction</h2>
            <span class="close" id="closeAddTransactionModal">&times;</span>
          </div>
          <div class="modal-body">
            <form id="addTransactionForm" method="POST" action="">
              <input type="hidden" name="add_transaction" value="1">
              
              <!-- Row 1: ID, Member ID -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="transactionId">Transaction ID</label>
                    <input id="transactionId" name="id" type="text" placeholder="e.g., TXN006" required autofocus />
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="memberId">Member ID</label>
                    <input id="memberId" name="member_id" type="text" placeholder="Enter member ID" required />
                  </div>
                </div>
              </div>

              <!-- Row 2: Member Name -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group autocomplete">
                    <label for="memberName">Member Name</label>
                    <input id="memberName" name="member_name" type="text" placeholder="Start typing member name" required />
                  </div>
                </div>
              </div>

              <!-- Row 3: Transaction Date, Amount -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="transactionDate">Transaction Date</label>
                    <input id="transactionDate" name="transactionDate" type="datetime-local" required />
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="amount">Amount (USD)</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0" placeholder="e.g., 299.00" required />
                  </div>
                </div>
              </div>

              <!-- Row 4: Status, Payment Method -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                      <option value="">Select status</option>
                      <option value="Completed">Completed</option>
                      <option value="Pending">Pending</option>
                      <option value="Failed">Failed</option>
                    </select>
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="paymentMethod">Payment Method</label>
                    <select id="paymentMethod" name="payment_method" required>
                      <option value="">Select payment method</option>
                      <option value="Visa ****1234">Visa ****1234</option>
                      <option value="Mastercard ****5678">Mastercard ****5678</option>
                      <option value="PayPal">PayPal</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Transaction</button>
                <button type="button" id="cancelAddTransaction" class="btn-secondary">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
  
  <script>
    // Check authentication on page load
    document.addEventListener('DOMContentLoaded', function() {
      const isLoggedIn = localStorage.getItem('isLoggedIn');
      const userType = localStorage.getItem('userType');
      const username = localStorage.getItem('username');
      
      if (!isLoggedIn) {
        window.location.href = 'login.html';
        return;
      }
      
      // Display user info
      if (username && userType) {
        console.log(`Welcome ${username} (${userType})`);
        const userDisplay = document.getElementById('userDisplay');
        if (userDisplay) {
          userDisplay.textContent = `${userType.toUpperCase()}`;
        }
      }
      
      // Force update user info
      forceUpdateUserInfo();

      const staffLink = document.getElementById('staffLink');
      if (staffLink && userType !== 'admin') {
        staffLink.style.display = 'none';
      }

      // Add Transaction modal logic
      const newTransactionBtn = document.getElementById('newTransactionBtn');
      const addTransactionModal = document.getElementById('addTransactionModal');
      const closeAddTransactionModal = document.getElementById('closeAddTransactionModal');
      const cancelAddTransaction = document.getElementById('cancelAddTransaction');
      const addTransactionForm = document.getElementById('addTransactionForm');

      if (newTransactionBtn && addTransactionModal) {
        newTransactionBtn.addEventListener('click', () => {
          addTransactionModal.style.display = 'flex';
        });
      }

      if (closeAddTransactionModal) {
        closeAddTransactionModal.addEventListener('click', () => {
          addTransactionModal.style.display = 'none';
        });
      }

      if (cancelAddTransaction) {
        cancelAddTransaction.addEventListener('click', () => {
          addTransactionModal.style.display = 'none';
        });
      }

      window.addEventListener('click', (e) => {
        if (e.target === addTransactionModal) {
          addTransactionModal.style.display = 'none';
        }
      });

      // Autocomplete functionality
      const memberNameInput = document.getElementById('memberName');
      const memberIdInput = document.getElementById('memberId');
      
      // Sample member data (in a real application, this would come from the server)
      const members = <?php echo json_encode($members); ?>;
      
      memberNameInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        
        // Clear previous autocomplete items
        const existingAutocomplete = document.getElementById('autocomplete-list');
        if (existingAutocomplete) {
          existingAutocomplete.remove();
        }
        
        if (value.length < 2) return;
        
        const filteredMembers = members.filter(member => 
          member.name.toLowerCase().includes(value)
        );
        
        if (filteredMembers.length === 0) return;
        
        const autocompleteList = document.createElement('div');
        autocompleteList.setAttribute('id', 'autocomplete-list');
        autocompleteList.setAttribute('class', 'autocomplete-items');
        
        filteredMembers.forEach(member => {
          const item = document.createElement('div');
          item.setAttribute('class', 'autocomplete-item');
          item.textContent = `${member.name} (ID: ${member.id})`;
          
          item.addEventListener('click', function() {
            memberNameInput.value = member.name;
            memberIdInput.value = member.id;
            autocompleteList.remove();
          });
          
          autocompleteList.appendChild(item);
        });
        
        this.parentNode.appendChild(autocompleteList);
      });
      
      // Close autocomplete when clicking outside
      document.addEventListener('click', function(e) {
        if (e.target !== memberNameInput) {
          const autocompleteList = document.getElementById('autocomplete-list');
          if (autocompleteList) {
            autocompleteList.remove();
          }
        }
      });
      
      // Search functionality
      const searchInput = document.getElementById('searchInput');
      const tableRows = document.querySelectorAll('.data-table tbody tr');
      
      searchInput.addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        
        tableRows.forEach(row => {
          const rowText = row.textContent.toLowerCase();
          if (rowText.includes(searchText)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    });

    function forceUpdateUserInfo() {
      // This function would update user info if needed
      console.log("User info updated");
    }
  </script>
</body>
</html>
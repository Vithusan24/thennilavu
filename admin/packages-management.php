<?php
// Database connection
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "matrimony";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$message = '';
$message_type = '';

// Process form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new package
    if (isset($_POST['add_package'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $duration_days = $_POST['duration_days'];
        $status = $_POST['status'];
        $description = $_POST['description'];
        $features = $_POST['features'];

        $sql = "INSERT INTO packages (name, price, duration_days, status, description, features)
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssss", $name, $price, $duration_days, $status, $description, $features);
        
        if ($stmt->execute()) {
            $message = "New package added successfully!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }
        
        $stmt->close();
    }
    
    // Update package
    if (isset($_POST['update_package'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $duration_days = $_POST['duration_days'];
        $status = $_POST['status'];
        $description = $_POST['description'];
        $features = $_POST['features'];

        $sql = "UPDATE packages SET name=?, price=?, duration_days=?, status=?, description=?, features=? WHERE package_id=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssssi", $name, $price, $duration_days, $status, $description, $features, $id);
        
        if ($stmt->execute()) {
            $message = "Package updated successfully!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }
        
        $stmt->close();
    }
    
    // Delete package
    if (isset($_POST['delete_package'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM packages WHERE package_id=?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $message = "Package deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }
        
        $stmt->close();
    }
}

// Get all packages
$packages = [];
$sql = "SELECT * FROM packages ORDER BY package_id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}

// Get package by ID for editing
$edit_package = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $sql = "SELECT * FROM packages WHERE package_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $edit_package = $result->fetch_assoc();
    }
    
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Packages Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
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
      padding: 1rem 2rem;
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
      background-color: #7e3af2;
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
      color: #7e3af2;
      text-decoration: none;
      font-weight: 600;
    }
    
    .user-info {
      font-weight: 600;
      color: #333;
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
      padding: 1.5rem 1rem;
      box-shadow: 2px 0 4px rgba(0,0,0,0.1);
    }
    
    .matrimony-name {
      font-size: 1.2rem;
      font-weight: 700;
      color: #7e3af2;
      margin-bottom: 2rem;
      text-align: center;
    }
    
    .sidebar-menu {
      list-style: none;
    }
    
    .sidebar-menu li {
      margin-bottom: 0.5rem;
    }
    
    .sidebar-link {
      display: block;
      padding: 0.75rem 1rem;
      text-decoration: none;
      color: #333;
      border-radius: 6px;
      transition: all 0.2s ease;
    }
    
    .sidebar-link:hover {
      background-color: #f5f7f9;
    }
    
    .sidebar-link.active {
      background-color: #7e3af2;
      color: white;
    }
    
    /* Main Content */
    .main-content {
      flex: 1;
      padding: 2rem;
      overflow-y: auto;
    }
    
    /* Top Actions */
    .top-actions {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    
    .action-btn {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .action-btn:first-child {
      background-color: #7e3af2;
      color: white;
    }
    
    .action-btn.secondary {
      background-color: #fff;
      color: #333;
      border: 1px solid #e5e7eb;
    }
    
    .action-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    /* Search Bar */
    .search-bar {
      position: relative;
      margin-bottom: 1.5rem;
      max-width: 400px;
    }
    
    .search-bar i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
    }
    
    .search-bar input {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      font-size: 1rem;
    }
    
    /* Content Section */
    .content-section {
      background-color: #fff;
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .content-section h2 {
      margin-bottom: 1.5rem;
      color: #333;
    }
    
    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }
    
    .stat-card {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1.5rem;
      background-color: #f9fafb;
      border-radius: 8px;
    }
    
    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      background-color: #7e3af2;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }
    
    .stat-info h3 {
      font-size: 0.875rem;
      color: #6b7280;
      margin-bottom: 0.5rem;
    }
    
    .stat-number {
      font-size: 1.5rem;
      font-weight: 700;
      color: #333;
      margin-bottom: 0.25rem;
    }
    
    .stat-change {
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }
    
    .stat-change.positive {
      color: #059669;
    }
    
    /* Data Table */
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .data-table th,
    .data-table td {
      padding: 0.75rem 1rem;
      text-align: left;
    }
    
    .data-table thead {
      background-color: #f9fafb;
    }
    
    .data-table thead th {
      font-weight: 600;
      color: #374151;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .data-table tbody tr {
      border-bottom: 1px solid #e5e7eb;
    }
    
    .data-table tbody tr:hover {
      background-color: #f9fafb;
    }
    
    /* Status badges */
    .status-badge {
      padding: 0.35rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-block;
    }
    
    .status-badge.active {
      background-color: #def7ec;
      color: #03543f;
    }
    
    .status-badge.inactive {
      background-color: #fde8e8;
      color: #9b1c1c;
    }
    
    .status-badge.draft {
      background-color: #fef3c7;
      color: #92400e;
    }
    
    /* Action buttons */
    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }
    
    .edit-btn, .delete-btn {
      width: 32px;
      height: 32px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .edit-btn {
      background-color: #10B981;
      color: white;
    }
    
    .delete-btn {
      background-color: #EF4444;
      color: white;
    }
    
    .edit-btn:hover, .delete-btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
      background-color: rgba(0, 0, 0, 0.5);
      align-items: center;
      justify-content: center;
    }
    
    .modal-content {
      background-color: #fff;
      border-radius: 8px;
      width: 90%;
      max-width: 700px;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .modal-header h2 {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #333;
    }
    
    .close {
      font-size: 1.5rem;
      font-weight: bold;
      cursor: pointer;
      color: #9ca3af;
    }
    
    .close:hover {
      color: #333;
    }
    
    .modal-body {
      padding: 1.5rem;
    }
    
    /* Form Styles */
    .form-row {
      display: flex;
      gap: 1rem;
      margin-bottom: 1rem;
    }
    
    .form-column {
      flex: 1;
    }
    
    .form-group {
      margin-bottom: 1rem;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #374151;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      font-size: 1rem;
    }
    
    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 1rem;
      margin-top: 1.5rem;
    }
    
    .btn-primary, .btn-secondary {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .btn-primary {
      background-color: #7e3af2;
      color: white;
    }
    
    .btn-secondary {
      background-color: #fff;
      color: #333;
      border: 1px solid #e5e7eb;
    }
    
    .btn-primary:hover, .btn-secondary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    /* Alert styles */
    .alert {
      padding: 12px 16px;
      margin-bottom: 16px;
      border-radius: 6px;
      font-weight: 500;
    }
    
    .alert-success {
      background-color: #D1FAE5;
      color: #065F46;
      border: 1px solid #A7F3D0;
    }
    
    .alert-error {
      background-color: #FEE2E2;
      color: #B91C1C;
      border: 1px solid #FECACA;
    }
    
    .loading {
      text-align: center;
      padding: 2rem;
      color: #6b7280;
    }
  </style>
</head>
<body>
  <!-- Top Navigation -->
  <header class="top-nav">
    <div class="logo-circle">M</div>
    <nav class="main-menu">
      <a href="#" class="logout">Logout</a>
    </nav>
    <div class="user-info" id="userInfo">
      <span id="userDisplay">ADMIN</span>
    </div>
  </header>

  <div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="matrimony-name">Matrimony Services</div>
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
      <!-- Display success/error messages -->
      <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <!-- Top Action Buttons -->
      <div class="top-actions">
        <button id="addPackageBtn" class="action-btn">
          <i class="fas fa-plus"></i>
          Add New Package
        </button>
        <button id="exportPdfBtn" class="action-btn secondary">
          <i class="fas fa-download"></i>
          Export Packages PDF
        </button>
      </div>

      <!-- Search Bar -->
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Search packages"/>
      </div>

      <!-- Package Statistics -->
      <div class="content-section">
        <h2>Package Overview</h2>
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
              <h3>Total Packages</h3>
              <p class="stat-number" id="totalPackages"><?php echo count($packages); ?></p>
              <span class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <?php echo count($packages); ?> total
              </span>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
              <h3>Active Packages</h3>
              <?php
                $active_count = 0;
                foreach ($packages as $pkg) {
                  if ($pkg['status'] === 'active') $active_count++;
                }
                $active_percentage = count($packages) > 0 ? round(($active_count / count($packages)) * 100) : 0;
              ?>
              <p class="stat-number" id="activePackages"><?php echo $active_count; ?></p>
              <span class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                <?php echo $active_percentage; ?>%
              </span>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
              <h3>Total Subscribers</h3>
              <p class="stat-number">1,247</p>
              <span class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                12.5%
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Packages Table -->
      <div class="content-section">
        <h2>Package List</h2>
        <?php if (count($packages) === 0): ?>
          <div class="loading">No packages found</div>
        <?php else: ?>
          <table class="data-table" id="packagesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Package Name</th>
                <th>Price</th>
                <th>Duration</th>
                <th>Features</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="packagesTableBody">
              <?php foreach ($packages as $pkg): ?>
                <tr>
                  <td><?php echo $pkg['package_id']; ?></td>
                  <td><?php echo htmlspecialchars($pkg['name']); ?></td>
                  <td>$<?php echo number_format($pkg['price'], 2); ?></td>
                  <td><?php echo $pkg['duration_days']; ?> days</td>
                  <td><?php echo htmlspecialchars($pkg['features']); ?></td>
                  <td><span class="status-badge <?php echo $pkg['status']; ?>"><?php echo $pkg['status']; ?></span></td>
                  <td>
                    <div class="action-buttons">
                      <button class="edit-btn" onclick="openUpdateModal(<?php echo $pkg['package_id']; ?>)" title="Edit">
                        <i class="fas fa-edit"></i>
                      </button>
                      <form method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $pkg['package_id']; ?>">
                        <button type="submit" name="delete_package" class="delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this package?')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>

      <!-- Add Package Modal -->
      <div id="addPackageModal" class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h2><i class="fas fa-box"></i> Add New Package</h2>
            <span class="close" id="closeAddPackageModal">&times;</span>
          </div>
          <div class="modal-body">
            <form method="POST" id="addPackageForm">
              <!-- Row 1: Package Name, Price -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="pkgName">Package Name</label>
                    <input id="pkgName" name="name" type="text" placeholder="e.g., Premium Plan" required />
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="pkgPrice">Price (USD)</label>
                    <input id="pkgPrice" name="price" type="number" step="0.01" min="0" placeholder="e.g., 79.99" required />
                  </div>
                </div>
              </div>

              <!-- Row 2: Duration, Features, Status -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="pkgDuration">Duration (Days)</label>
                    <select id="pkgDuration" name="duration_days" required>
                      <option value="">Select duration</option>
                      <option value="7">7 Days</option>
                      <option value="30">1 Month</option>
                      <option value="90">3 Months</option>
                      <option value="180">6 Months</option>
                      <option value="365">12 Months</option>
                    </select>
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="pkgFeatures">Features (comma separated)</label>
                    <input id="pkgFeatures" name="features" type="text" placeholder="Unlimited Messages, Advanced Search, ..." />
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="pkgStatus">Status</label>
                    <select id="pkgStatus" name="status" required>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                      <option value="draft">Draft</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="pkgDesc">Description</label>
                <textarea id="pkgDesc" name="description" rows="3" placeholder="Short description..."></textarea>
              </div>

              <div class="form-actions">
                <button type="submit" name="add_package" class="btn-primary"><i class="fas fa-save"></i> Save Package</button>
                <button type="button" id="cancelAddPackage" class="btn-secondary">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Update Package Modal -->
      <div id="updatePackageModal" class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Update Package</h2>
            <span class="close" id="closeUpdatePackageModal">&times;</span>
          </div>
          <div class="modal-body">
            <?php if ($edit_package): ?>
            <form method="POST" id="updatePackageForm">
              <input type="hidden" name="id" value="<?php echo $edit_package['package_id']; ?>">
              
              <!-- Row 1: Package Name, Price -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="updatePkgName">Package Name</label>
                    <input id="updatePkgName" name="name" type="text" value="<?php echo htmlspecialchars($edit_package['name']); ?>" placeholder="e.g., Premium Plan" required />
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="updatePkgPrice">Price (USD)</label>
                    <input id="updatePkgPrice" name="price" type="number" step="0.01" min="0" value="<?php echo $edit_package['price']; ?>" placeholder="e.g., 79.99" required />
                  </div>
                </div>
              </div>

              <!-- Row 2: Duration, Features, Status -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="updatePkgDuration">Duration (Days)</label>
                    <select id="updatePkgDuration" name="duration_days" required>
                      <option value="">Select duration</option>
                      <option value="7" <?php if ($edit_package['duration_days'] == 7) echo 'selected'; ?>>7 Days</option>
                      <option value="30" <?php if ($edit_package['duration_days'] == 30) echo 'selected'; ?>>1 Month</option>
                      <option value="90" <?php if ($edit_package['duration_days'] == 90) echo 'selected'; ?>>3 Months</option>
                      <option value="180" <?php if ($edit_package['duration_days'] == 180) echo 'selected'; ?>>6 Months</option>
                      <option value="365" <?php if ($edit_package['duration_days'] == 365) echo 'selected'; ?>>12 Months</option>
                    </select>
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="updatePkgFeatures">Features (comma separated)</label>
                    <input id="updatePkgFeatures" name="features" type="text" value="<?php echo htmlspecialchars($edit_package['features']); ?>" placeholder="Unlimited Messages, Advanced Search, ..." />
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="updatePkgStatus">Status</label>
                    <select id="updatePkgStatus" name="status" required>
                      <option value="active" <?php if ($edit_package['status'] == 'active') echo 'selected'; ?>>Active</option>
                      <option value="inactive" <?php if ($edit_package['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                      <option value="draft" <?php if ($edit_package['status'] == 'draft') echo 'selected'; ?>>Draft</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="updatePkgDesc">Description</label>
                <textarea id="updatePkgDesc" name="description" rows="3" placeholder="Short description..."><?php echo htmlspecialchars($edit_package['description']); ?></textarea>
              </div>

              <div class="form-actions">
                <button type="submit" name="update_package" class="btn-primary"><i class="fas fa-save"></i> Update Package</button>
                <button type="button" id="cancelUpdatePackage" class="btn-secondary">Cancel</button>
              </div>
            </form>
            <?php else: ?>
              <div class="loading">Package not found</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
<script>
  // Export Packages PDF
  document.getElementById("exportPdfBtn").addEventListener("click", function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(16);
    doc.text("Packages Summary", 14, 20);

    // AutoTable
    doc.autoTable({
      startY: 30,
      head: [['ID', 'Name', 'Price', 'Duration', 'Features', 'Status']],
      body: Array.from(document.querySelectorAll("#packagesTableBody tr")).map(tr => {
        return Array.from(tr.querySelectorAll("td")).slice(0, 6).map(td => td.innerText);
      }),
    });

    // Open in new tab instead of download
    window.open(doc.output("bloburl"), "_blank");
  });
</script>
<script>
  // Search packages
  document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#packagesTableBody tr");
    rows.forEach(row => {
      let text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });
</script>

  <script>
    // DOM elements
    const addPackageBtn = document.getElementById('addPackageBtn');
    const addPackageModal = document.getElementById('addPackageModal');
    const closeAddPackageModal = document.getElementById('closeAddPackageModal');
    const cancelAddPackage = document.getElementById('cancelAddPackage');
    
    const updatePackageModal = document.getElementById('updatePackageModal');
    const closeUpdatePackageModal = document.getElementById('closeUpdatePackageModal');
    const cancelUpdatePackage = document.getElementById('cancelUpdatePackage');
    
    const searchInput = document.getElementById('searchInput');
    const exportPdfBtn = document.getElementById('exportPdfBtn');

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
      // Show modals based on URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('edit_id')) {
        updatePackageModal.style.display = 'flex';
      }
      
      // Event listeners for modals
      addPackageBtn.addEventListener('click', () => addPackageModal.style.display = 'flex');
      closeAddPackageModal.addEventListener('click', () => addPackageModal.style.display = 'none');
      cancelAddPackage.addEventListener('click', () => addPackageModal.style.display = 'none');
      
      closeUpdatePackageModal.addEventListener('click', () => {
        updatePackageModal.style.display = 'none';
        // Remove edit_id from URL
        const url = new URL(window.location);
        url.searchParams.delete('edit_id');
        window.history.replaceState({}, '', url);
      });
      
      cancelUpdatePackage.addEventListener('click', () => {
        updatePackageModal.style.display = 'none';
        // Remove edit_id from URL
        const url = new URL(window.location);
        url.searchParams.delete('edit_id');
        window.history.replaceState({}, '', url);
      });
      
      // Close modals when clicking outside
      window.addEventListener('click', (e) => {
        if (e.target === addPackageModal) addPackageModal.style.display = 'none';
        if (e.target === updatePackageModal) {
          updatePackageModal.style.display = 'none';
          // Remove edit_id from URL
          const url = new URL(window.location);
          url.searchParams.delete('edit_id');
          window.history.replaceState({}, '', url);
        }
      });
      
      // Search functionality
      searchInput.addEventListener('input', filterPackages);
      
      // PDF export
      if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', exportToPDF);
      }
    });

    // Filter packages based on search input
    function filterPackages() {
      const searchTerm = searchInput.value.toLowerCase();
      const rows = document.querySelectorAll('#packagesTableBody tr');
      
      for (let row of rows) {
        const name = row.cells[1].textContent.toLowerCase();
        const features = row.cells[4].textContent.toLowerCase();
        const status = row.cells[5].textContent.toLowerCase();
        
        if (name.includes(searchTerm) || features.includes(searchTerm) || status.includes(searchTerm)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      }
    }
    
    // Open update modal
    function openUpdateModal(id) {
      // Add edit_id to URL
      const url = new URL(window.location);
      url.searchParams.set('edit_id', id);
      window.history.replaceState({}, '', url);
      
      // Reload page to load the package data
      window.location.reload();
    }
    
    // Pass PHP package data to JS
    const packagesData = <?php echo json_encode($packages); ?>;
    const totalPackages = packagesData.length;
    const activePackages = packagesData.filter(pkg => pkg.status === 'active').length;

    // Export to PDF
    function exportToPDF() {
      // Use jsPDF UMD syntax
      const doc = new window.jspdf.jsPDF();

      // Add title
      doc.setFontSize(20);
      doc.text('Package Summary Report', 14, 15);

      // Add date
      doc.setFontSize(12);
      doc.setTextColor(100);
      doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 14, 22);

      // Add statistics
      doc.setFontSize(14);
      doc.setTextColor(0);
      doc.text('Overview', 14, 35);

      doc.setFontSize(12);
      doc.text(`Total Packages: ${totalPackages}`, 14, 42);
      doc.text(`Active Packages: ${activePackages}`, 14, 49);

      // Prepare table data
      const tableBody = packagesData.map(pkg => [
        pkg.package_id,
        pkg.name,
        `$${Number(pkg.price).toFixed(2)}`,
        `${pkg.duration_days} days`,
        pkg.status
      ]);

      // Add table
      doc.autoTable({
        startY: 55,
        head: [['ID', 'Name', 'Price', 'Duration', 'Status']],
        body: tableBody,
        theme: 'grid',
        headStyles: {
          fillColor: [126, 58, 242],
          textColor: 255
        }
      });

      // Open PDF in new tab and download
      const pdfBlob = doc.output('blob');
      const pdfUrl = URL.createObjectURL(pdfBlob);

  window.open(pdfUrl, '_blank');
    }
  </script>
</body>
</html>
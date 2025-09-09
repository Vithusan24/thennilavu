<?php
// Database connection
$host = "127.0.0.1";
$user = "root"; // change if needed
$pass = "";     // change if needed
$db   = "matrimony";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Calculate statistics
$stats_sql = "SELECT 
    COUNT(*) as total_reviews, 
    AVG(rating) as average_rating 
    FROM reviews";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
$total_reviews = $stats['total_reviews'];
$average_rating = round($stats['average_rating'], 1);

// Handle search/filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where_clause = '';
if (!empty($search)) {
    $search_term = $conn->real_escape_string($search);
    $where_clause = "WHERE name LIKE '%$search_term%' OR comment LIKE '%$search_term%'";
}

// Get reviews
$sql = "SELECT * FROM reviews $where_clause ORDER BY review_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Review Management</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="sidebar.css"/>
  <link rel="stylesheet" href="maincontent.css"/>
  <link rel="stylesheet" href="tables.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    /* Additional styles for the review management page */
    .star-rating {
      color: #ffc107;
    }
    
    .popup-btn {
      background-color: #4a6cf7;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
    }
    
    .popup-btn:hover {
      background-color: #3a5cd8;
    }
    
    .search-form {
      display: flex;
      margin-bottom: 20px;
    }
    
    .search-input {
      flex: 1;
      padding: 10px 15px 10px 40px;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      font-size: 14px;
    }
    
    .search-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #888;
    }
    
    .search-container {
      position: relative;
      width: 100%;
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
      <h1>Review Management</h1>

   

      <!-- Review Statistics -->
      <div class="content-section">
        <h2>Review Statistics</h2>
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-star"></i>
            </div>
            <div class="stat-info">
              <h3>Total Reviews</h3>
              <p class="stat-number"><?php echo $total_reviews; ?></p>
              <span class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                12.5%
              </span>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-star-half-alt"></i>
            </div>
            <div class="stat-info">
              <h3>Average Rating</h3>
              <p class="stat-number"><?php echo $average_rating; ?></p>
              <span class="stat-change positive">
                <i class="fas fa-arrow-up"></i>
                0.3
              </span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Review Table -->
      <div class="content-section">
        <h2>All Reviews</h2>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Date</th>
                <th>Comment</th>
                <th>Rating</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                  <tr>
                    <td><?= $row['review_id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= $row['review_date']; ?></td>
                    <td>
                      <?php 
                        // Display truncated comment with view button for full text
                        $truncated_comment = strlen($row['comment']) > 50 
                          ? substr($row['comment'], 0, 50) . '...' 
                          : $row['comment'];
                      ?>
                      <span title="<?= htmlspecialchars($row['comment']); ?>">
                        <?= htmlspecialchars($truncated_comment); ?>
                      </span>
                      <?php if (strlen($row['comment']) > 50): ?>
                        <button class="popup-btn" onclick="alert('<?= addslashes($row['comment']); ?>')">View Full</button>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div class="star-rating">
                        <?php
                          $rating = (int)$row['rating'];
                          for ($i=1; $i<=5; $i++) {
                            if ($i <= $rating) {
                              echo '<i class="fas fa-star"></i>';
                            } else {
                              echo '<i class="far fa-star"></i>';
                            }
                          }
                        ?>
                        <span>(<?= $rating; ?>/5)</span>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" style="text-align: center;">
                    No reviews found
                    <?php if (!empty($search)): ?>
                      for search term "<?php echo htmlspecialchars($search); ?>"
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
  <script src="script.js"></script>
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
    });
  </script>
</body>
</html>
<?php $conn->close(); ?>
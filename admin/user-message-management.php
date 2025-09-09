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

// Handle reply form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $sender_name = $_POST['sender_name'];
    $sender_email = $_POST['sender_email'];
    $recipient_email = $_POST['recipient_email'];
    $subject = $_POST['subject'];
    $message_text = $_POST['message_text'];
    $priority = $_POST['priority'];
    
    // Insert the reply into the database
    $stmt = $conn->prepare("INSERT INTO messages (sender_name, sender_email, subject, message_text, sent_time) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $sender_name, $sender_email, $subject, $message_text);
    
    if ($stmt->execute()) {
        $success_message = "Reply sent successfully!";
    } else {
        $error_message = "Error sending reply: " . $conn->error;
    }
    
    $stmt->close();
}

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $sender_name = $_POST['sender_name'];
    $sender_email = $_POST['sender_email'];
    $recipient_email = $_POST['recipient_email'];
    $subject = $_POST['subject'];
    $message_text = $_POST['message_text'];
    $priority = $_POST['priority'];
    
    // Insert the new message into the database
    $stmt = $conn->prepare("INSERT INTO messages (sender_name, sender_email, subject, message_text, sent_time) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $sender_name, $sender_email, $subject, $message_text);
    
    if ($stmt->execute()) {
        $success_message = "Message sent successfully!";
    } else {
        $error_message = "Error sending message: " . $conn->error;
    }
    
    $stmt->close();
}

// Fetch messages
$query = "SELECT * FROM messages ORDER BY sent_time DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Message Management</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="sidebar.css"/>
  <link rel="stylesheet" href="maincontent.css"/>
  <link rel="stylesheet" href="tables.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
  <style>
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
    }
    
    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 600px;
      border-radius: 8px;
    }
    
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
    
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    
    .alert {
      padding: 10px;
      margin: 10px 0;
      border-radius: 4px;
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    
    /* Basic styling for elements that might not be defined in other CSS files */
    .top-nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .logo-circle {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #4a90e2;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .dashboard-layout {
      display: flex;
      min-height: calc(100vh - 70px);
    }
    
    .sidebar {
      width: 250px;
      background-color: #f8f9fa;
      padding: 20px;
    }
    
    .main-content {
      flex: 1;
      padding: 20px;
      background-color: #f5f7f9;
    }
    
    .search-bar {
      display: flex;
      align-items: center;
      background: white;
      border-radius: 8px;
      padding: 8px 15px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .search-bar input {
      border: none;
      outline: none;
      margin-left: 10px;
      width: 100%;
    }
    
    .content-section {
      background: white;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .form-row {
      display: flex;
      gap: 20px;
      margin-bottom: 15px;
    }
    
    .form-column {
      flex: 1;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
    
    .form-actions {
      display: flex;
      gap: 10px;
    }
    
    .action-btn {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 600;
    }
    
    .action-btn.primary {
      background-color: #4a90e2;
      color: white;
    }
    
    .action-btn.secondary {
      background-color: #f1f1f1;
      color: #333;
    }
    
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .data-table th,
    .data-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    
    .data-table th {
      background-color: #f8f9fa;
      font-weight: 600;
    }
    
    .popup-btn, .reply-btn, .delete-btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    
    .reply-btn {
      background-color: #4a90e2;
      color: white;
    }
    
    .delete-btn {
      background-color: #e24a4a;
      color: white;
    }
    
    .tab-buttons {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
    }
    
    .tab-btn {
      padding: 8px 16px;
      border: 1px solid #ddd;
      background: white;
      border-radius: 4px;
      cursor: pointer;
    }
    
    .tab-btn.active {
      background-color: #4a90e2;
      color: white;
      border-color: #4a90e2;
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
      <!-- Display success/error messages -->
      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
        <div class="alert alert-error"><?php echo $error_message; ?></div>
      <?php endif; ?>

      <!-- Search Bar -->
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search messages"/>
      </div>

      <!-- Message Form -->
      <div class="content-section">
        <h2>Send Message</h2>
        <form class="message-form" method="POST" action="">
          <div class="form-row">
            <div class="form-column">
              <div class="form-group">
                <label>From (Sender Name)</label>
                <input type="text" name="sender_name" placeholder="Enter sender name" required/>
              </div>
              <div class="form-group">
                <label>From (Sender Email)</label>
                <input type="email" name="sender_email" placeholder="Enter sender email" required/>
              </div>
            </div>
            <div class="form-column">
              <div class="form-group">
                <label>To (Recipient Email)</label>
                <input type="email" name="recipient_email" placeholder="Enter recipient email" required/>
              </div>
              <div class="form-group">
                <label>Priority</label>
                <select name="priority">
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" placeholder="Enter message subject" required/>
          </div>
          <div class="form-group">
            <label>Message Content</label>
            <textarea name="message_text" placeholder="Enter your message here..." rows="6" required></textarea>
          </div>
          <div class="form-actions">
            <button type="submit" name="send_message" class="action-btn primary">Send Message</button>
            <button type="button" class="action-btn secondary">Save Draft</button>
          </div>
        </form>
      </div>

      <!-- Message History Table -->
      <div class="content-section">
        <h2>Message History</h2>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Sender</th>
              <th>Sender Email</th>
              <th>Subject</th>
              <th>Message (Popup)</th>
              <th>Date/Time</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            if ($result && mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) { 
            ?>
              <tr>
                <td><?php echo $row['message_id']; ?></td>
                <td><?php echo htmlspecialchars($row['sender_name']); ?></td>
                <td><?php echo htmlspecialchars($row['sender_email']); ?></td>
                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                <td>
                  <button class="popup-btn" onclick="viewMessage('<?php echo addslashes($row['message_text']); ?>')">View</button>
                </td>
                <td><?php echo $row['sent_time']; ?></td>
                <td>
                  <button class="reply-btn" onclick="openReplyModal('<?php echo $row['sender_email']; ?>', '<?php echo addslashes($row['subject']); ?>', '<?php echo addslashes($row['message_text']); ?>', '<?php echo addslashes($row['sender_name']); ?>')">Reply</button>
                  <a href="delete_message.php?id=<?php echo $row['message_id']; ?>" onclick="return confirm('Are you sure?')">
                    <button class="delete-btn">Delete</button>
                  </a>
                </td>
              </tr>
            <?php 
              }
            } else {
            ?>
              <tr>
                <td colspan="7" style="text-align: center;">No messages found</td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- Reply Modal -->
      <div id="replyModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeModal()">&times;</span>
          <h2>Reply to Message</h2>
          <form class="message-form" method="POST" action="">
            <input type="hidden" name="recipient_email" id="reply_recipient">
            <div class="form-group">
              <label>From (Sender Name)</label>
              <input type="text" name="sender_name" id="reply_sender_name" placeholder="Your name" required>
            </div>
            <div class="form-group">
              <label>From (Sender Email)</label>
              <input type="email" name="sender_email" id="reply_sender_email" placeholder="Your email" required>
            </div>
            <div class="form-group">
              <label>Subject</label>
              <input type="text" name="subject" id="reply_subject" required>
            </div>
            <div class="form-group">
              <label>Priority</label>
              <select name="priority">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
              </select>
            </div>
            <div class="form-group">
              <label>Original Message</label>
              <textarea id="original_message" readonly rows="3"></textarea>
            </div>
            <div class="form-group">
              <label>Your Reply</label>
              <textarea name="message_text" placeholder="Type your reply here..." rows="6" required></textarea>
            </div>
            <div class="form-actions">
              <button type="submit" name="reply_message" class="action-btn primary">Send Reply</button>
              <button type="button" class="action-btn secondary" onclick="closeModal()">Cancel</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Inbox/Outbox Tabs -->
      <div class="content-section">
        <div class="tab-buttons">
          <button class="tab-btn active">Inbox</button>
          <button class="tab-btn">Outbox</button>
          <button class="tab-btn">Drafts</button>
        </div>
        <div class="message-list">
          <!-- Message items will be displayed here -->
        </div>
      </div>
    </main>
  </div>
  
  <script>
    // Get the modal
    var modal = document.getElementById("replyModal");
    
    // Function to open the reply modal
    function openReplyModal(email, subject, originalMessage, senderName) {
      document.getElementById("reply_recipient").value = email;
      document.getElementById("reply_subject").value = "Re: " + subject;
      document.getElementById("original_message").value = originalMessage;
      document.getElementById("reply_sender_name").value = "Admin"; // Default value
      document.getElementById("reply_sender_email").value = "admin@matrimony.com"; // Default value
      modal.style.display = "block";
    }
    
    // Function to close the modal
    function closeModal() {
      modal.style.display = "none";
    }
    
    // Close the modal when clicking outside of it
    window.onclick = function(event) {
      if (event.target == modal) {
        closeModal();
      }
    }
    
    // View message function
    function viewMessage(msg) {
      alert("Message: " + msg);
    }

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

      const staffLink = document.getElementById('staffLink');
      if (staffLink && userType !== 'admin') {
        staffLink.style.display = 'none';
      }
    });
  </script>
</body>
</html>
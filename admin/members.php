<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Members - Admin Panel</title>
  <link rel="stylesheet" href="styles.css"/>
  <link rel="stylesheet" href="sidebar.css"/>
  <link rel="stylesheet" href="maincontent.css"/>
  <link rel="stylesheet" href="tables.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
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
      <div class="top-actions">
        <button class="action-btn" id="newMemberBtn">New Member</button>
        <button class="action-btn" id="freeMemberBtn">Free Member</button>
        <button class="action-btn" id="paidMemberBtn">Paid Member</button>
        <button class="action-btn" id="activeMemberBtn">Active Member</button>
        <button class="action-btn" id="blockedMemberBtn">Blocked Member</button>
      </div>
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search member"/>
      </div>
      <div class="content-section">
        <h2>Members Table</h2>
        <table class="data-table">
          <thead>
            <tr>
              <th>id</th>
              <th>name</th>
              <th>looking for</th>
              <th>married status</th>
              <th>register date/time</th>
              <th>package</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>M001</td>
              <td>Sarah Johnson</td>
              <td>Male, 25-35</td>
              <td>Never Married</td>
              <td>2024-05-15 10:30</td>
              <td><span class="package-badge premium">Premium</span></td>
            </tr>
            <tr>
              <td>M002</td>
              <td>Michael Chen</td>
              <td>Female, 23-32</td>
              <td>Divorced</td>
              <td>2024-05-18 14:45</td>
              <td><span class="package-badge gold">Gold</span></td>
            </tr>
            <tr>
              <td>M003</td>
              <td>Emma Davis</td>
              <td>Male, 28-38</td>
              <td>Never Married</td>
              <td>2024-05-20 09:15</td>
              <td><span class="package-badge free">Free</span></td>
            </tr>
            <tr>
              <td>M004</td>
              <td>David Wilson</td>
              <td>Female, 24-34</td>
              <td>Widowed</td>
              <td>2024-05-22 16:20</td>
              <td><span class="package-badge premium">Premium</span></td>
            </tr>
            <tr>
              <td>M005</td>
              <td>Lisa Anderson</td>
              <td>Male, 26-36</td>
              <td>Never Married</td>
              <td>2024-05-25 11:30</td>
              <td><span class="package-badge silver">Silver</span></td>
            </tr>
            <tr>
              <td>M006</td>
              <td>James Brown</td>
              <td>Female, 22-30</td>
              <td>Divorced</td>
              <td>2024-05-28 13:45</td>
              <td><span class="package-badge free">Free</span></td>
            </tr>
            <tr>
              <td>M007</td>
              <td>Jennifer Garcia</td>
              <td>Male, 30-40</td>
              <td>Never Married</td>
              <td>2024-05-30 08:20</td>
              <td><span class="package-badge premium">Premium</span></td>
            </tr>
            <tr>
              <td>M008</td>
              <td>Robert Martinez</td>
              <td>Female, 25-35</td>
              <td>Never Married</td>
              <td>2024-06-01 15:10</td>
              <td><span class="package-badge gold">Gold</span></td>
            </tr>
            <tr>
              <td>M009</td>
              <td>Amanda Taylor</td>
              <td>Male, 27-37</td>
              <td>Divorced</td>
              <td>2024-06-03 12:00</td>
              <td><span class="package-badge free">Free</span></td>
            </tr>
            <tr>
              <td>M010</td>
              <td>Christopher Lee</td>
              <td>Female, 23-33</td>
              <td>Never Married</td>
              <td>2024-06-05 17:30</td>
              <td><span class="package-badge premium">Premium</span></td>
            </tr>
          </tbody>
        </table>
        <h3>High price package member table</h3>
        <table class="data-table">
          <thead>
            <tr>
              <th>name</th>
              <th>package</th>
              <th>login history</th>
              <th>full details</th>
              <th>remaining time</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Sarah Johnson</td>
              <td><span class="package-badge premium">Premium</span></td>
              <td>Last: 2 hours ago</td>
              <td><button class="view-details-btn">View Details</button></td>
              <td>5 months 15 days</td>
            </tr>
            <tr>
              <td>David Wilson</td>
              <td><span class="package-badge premium">Premium</span></td>
              <td>Last: 1 day ago</td>
              <td><button class="view-details-btn">View Details</button></td>
              <td>6 months 8 days</td>
            </tr>
            <tr>
              <td>Jennifer Garcia</td>
              <td><span class="package-badge premium">Premium</span></td>
              <td>Last: 3 hours ago</td>
              <td><button class="view-details-btn">View Details</button></td>
              <td>4 months 20 days</td>
            </tr>
            <tr>
              <td>Christopher Lee</td>
              <td><span class="package-badge premium">Premium</span></td>
              <td>Last: 5 hours ago</td>
              <td><button class="view-details-btn">View Details</button></td>
              <td>7 months 2 days</td>
            </tr>
            <tr>
              <td>Michael Chen</td>
              <td><span class="package-badge gold">Gold</span></td>
              <td>Last: 1 hour ago</td>
              <td><button class="view-details-btn">View Details</button></td>
              <td>2 months 15 days</td>
            </tr>
            <tr>
              <td>Robert Martinez</td>
              <td><span class="package-badge gold">Gold</span></td>
              <td>Last: 4 hours ago</td>
              <td><button class="view-details-btn">View Details</button></td>
              <td>3 months 8 days</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Add Member Modal -->
      <div id="addMemberModal" class="modal">
        <div class="modal-content">
          <div class="modal-header">
            <h2><i class="fas fa-user-plus"></i> Add New Member</h2>
            <span class="close" id="closeAddMemberModal">&times;</span>
          </div>
          <div class="modal-body">
            <form id="addMemberForm">
              <!-- Row 1: ID, NAME, GENDER -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="memberIdInput">ID</label>
                    <input id="memberIdInput" name="id" type="text" placeholder="e.g., M011" required autofocus />
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="memberNameInput">NAME</label>
                    <input id="memberNameInput" name="name" type="text" placeholder="Full name" required />
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="memberGender">GENDER</label>
                    <select id="memberGender" name="gender" required>
                      <option value="">Select gender</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Row 2: MARRIED STATUS, REGISTERDATE, PACKAGE -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="memberMaritalStatus">MARRIED STATUS</label>
                    <select id="memberMaritalStatus" name="maritalStatus" required>
                      <option value="">Select status</option>
                      <option value="Never Married">Never Married</option>
                      <option value="Divorced">Divorced</option>
                      <option value="Widowed">Widowed</option>
                    </select>
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="memberRegisterDate">REGISTERDATE</label>
                    <input id="memberRegisterDate" name="registerDate" type="datetime-local" required />
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
                    <label for="memberPackage">PACKAGE</label>
                    <select id="memberPackage" name="package" required>
                      <option value="">Select package</option>
                      <option value="Free">Free</option>
                      <option value="Silver">Silver</option>
                      <option value="Gold">Gold</option>
                      <option value="Premium">Premium</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Row 3: COUNTRY -->
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
                    <label for="memberCountry">COUNTRY</label>
                    <input id="memberCountry" name="country" type="text" placeholder="e.g., United States" required />
                  </div>
                </div>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Member</button>
                <button type="button" id="cancelAddMember" class="btn-secondary">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </main>
  </div>

  <!-- Member Details Modal -->
  <div id="memberDetailsModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2><i class="fas fa-user"></i> Member Details</h2>
        <span class="close">&times;</span>
      </div>
      <div class="modal-body">
        <div class="member-profile">
          <div class="profile-header">
            <div class="profile-avatar">
              <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
              <h3 id="memberName">Sarah Johnson</h3>
              <p id="memberId">Member ID: M001</p>
              <span id="memberPackage" class="package-badge premium">Premium</span>
            </div>
          </div>
          <div class="member-details-grid">
            <div class="detail-section">
              <h4>Personal Information</h4>
              <div class="detail-item">
                <label>Age:</label>
                <span id="memberAge">28 years</span>
              </div>
              <div class="detail-item">
                <label>Location:</label>
                <span id="memberLocation">New York, NY</span>
              </div>
              <div class="detail-item">
                <label>Marital Status:</label>
                <span id="memberMaritalStatus">Never Married</span>
              </div>
              <div class="detail-item">
                <label>Looking For:</label>
                <span id="memberLookingFor">Male, 25-35</span>
              </div>
            </div>
            <div class="detail-section">
              <h4>Account Information</h4>
              <div class="detail-item">
                <label>Registration Date:</label>
                <span id="memberRegDate">May 15, 2024</span>
              </div>
              <div class="detail-item">
                <label>Last Login:</label>
                <span id="memberLastLogin">2 hours ago</span>
              </div>
              <div class="detail-item">
                <label>Profile Status:</label>
                <span id="memberProfileStatus" class="status-badge active">Complete</span>
              </div>
              <div class="detail-item">
                <label>Package Expiry:</label>
                <span id="memberExpiry">November 15, 2024</span>
              </div>
            </div>
          </div>
          <div class="member-actions">
            <button class="btn-primary">Edit Profile</button>
            <button class="btn-secondary">Send Message</button>
            <button class="btn-danger">Block Member</button>
          </div>
        </div>
      </div>
    </div>
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

      // Setup action buttons
      setupActionButtons();
      setupMemberDetailsButtons();

      // Add Member modal logic
      const addMemberModal = document.getElementById('addMemberModal');
      const closeAddMemberModal = document.getElementById('closeAddMemberModal');
      const cancelAddMember = document.getElementById('cancelAddMember');
      const addMemberForm = document.getElementById('addMemberForm');

      if (closeAddMemberModal) {
        closeAddMemberModal.addEventListener('click', function() {
          addMemberModal.style.display = 'none';
        });
      }

      if (cancelAddMember) {
        cancelAddMember.addEventListener('click', function() {
          addMemberModal.style.display = 'none';
        });
      }

      window.addEventListener('click', function(event) {
        if (event.target === addMemberModal) {
          addMemberModal.style.display = 'none';
        }
      });

      if (addMemberForm) {
        addMemberForm.addEventListener('submit', function() {
          // Close after global form handler
          addMemberModal.style.display = 'none';
        });
      }
    });

    // Setup action buttons
    function setupActionButtons() {
      const buttons = {
        'newMemberBtn': showNewMembers,
        'freeMemberBtn': showFreeMembers,
        'paidMemberBtn': showPaidMembers,
        'activeMemberBtn': showActiveMembers,
        'blockedMemberBtn': showBlockedMembers
      };

      Object.keys(buttons).forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
          button.addEventListener('click', buttons[buttonId]);
        }
      });
    }

    // Setup member details buttons
    function setupMemberDetailsButtons() {
      const detailButtons = document.querySelectorAll('.view-details-btn');
      detailButtons.forEach(button => {
        button.addEventListener('click', function() {
          const row = this.closest('tr');
          const memberName = row.cells[0].textContent;
          showMemberDetails(memberName);
        });
      });
    }

    // Show new members
    function showNewMembers() {
      const modal = document.getElementById('addMemberModal');
      if (modal) {
        modal.style.display = 'block';
      }
    }

    // Show free members (filter table to only Free package)
    function showFreeMembers() {
      const table = document.querySelector('.content-section .data-table');
      if (!table) return;
      const rows = table.querySelectorAll('tbody tr');
      rows.forEach(row => {
        const packageCell = row.cells[5];
        const isFree = packageCell && packageCell.textContent.toLowerCase().includes('free');
        row.style.display = isFree ? '' : 'none';
      });
    }

    // Show paid members (Premium/Gold/Silver)
    function showPaidMembers() {
      const table = document.querySelector('.content-section .data-table');
      if (!table) return;
      const rows = table.querySelectorAll('tbody tr');
      rows.forEach(row => {
        const packageCell = row.cells[5];
        const text = packageCell ? packageCell.textContent.toLowerCase() : '';
        const isPaid = text.includes('premium') || text.includes('gold') || text.includes('silver');
        row.style.display = isPaid ? '' : 'none';
      });
    }

    // Show active members (example: show all rows, as we don't have an active flag in table)
    function showActiveMembers() {
      const table = document.querySelector('.content-section .data-table');
      if (!table) return;
      const rows = table.querySelectorAll('tbody tr');
      rows.forEach(row => {
        row.style.display = '';
      });
    }

    // Show blocked members (no blocked flag available; hide all for now)
    function showBlockedMembers() {
      const table = document.querySelector('.content-section .data-table');
      if (!table) return;
      const rows = table.querySelectorAll('tbody tr');
      rows.forEach(row => {
        row.style.display = 'none';
      });
    }

    // Show member details modal
    function showMemberDetails(memberName) {
      const modal = document.getElementById('memberDetailsModal');
      const closeBtn = modal.querySelector('.close');

      // Member data
      const memberData = {
        'Sarah Johnson': {
          id: 'M001',
          age: '28 years',
          location: 'New York, NY',
          maritalStatus: 'Never Married',
          lookingFor: 'Male, 25-35',
          regDate: 'May 15, 2024',
          lastLogin: '2 hours ago',
          profileStatus: 'Complete',
          expiry: 'November 15, 2024',
          package: 'Premium'
        },
        'David Wilson': {
          id: 'M004',
          age: '30 years',
          location: 'Houston, TX',
          maritalStatus: 'Widowed',
          lookingFor: 'Female, 24-34',
          regDate: 'May 22, 2024',
          lastLogin: '1 day ago',
          profileStatus: 'Complete',
          expiry: 'December 22, 2024',
          package: 'Premium'
        },
        'Jennifer Garcia': {
          id: 'M007',
          age: '27 years',
          location: 'San Antonio, TX',
          maritalStatus: 'Never Married',
          lookingFor: 'Male, 30-40',
          regDate: 'May 30, 2024',
          lastLogin: '3 hours ago',
          profileStatus: 'Complete',
          expiry: 'November 30, 2024',
          package: 'Premium'
        },
        'Christopher Lee': {
          id: 'M010',
          age: '34 years',
          location: 'San Jose, CA',
          maritalStatus: 'Never Married',
          lookingFor: 'Female, 23-33',
          regDate: 'June 5, 2024',
          lastLogin: '5 hours ago',
          profileStatus: 'Complete',
          expiry: 'December 5, 2024',
          package: 'Premium'
        },
        'Michael Chen': {
          id: 'M002',
          age: '32 years',
          location: 'Los Angeles, CA',
          maritalStatus: 'Divorced',
          lookingFor: 'Female, 23-32',
          regDate: 'May 18, 2024',
          lastLogin: '1 hour ago',
          profileStatus: 'Complete',
          expiry: 'August 18, 2024',
          package: 'Gold'
        },
        'Robert Martinez': {
          id: 'M008',
          age: '33 years',
          location: 'San Diego, CA',
          maritalStatus: 'Never Married',
          lookingFor: 'Female, 25-35',
          regDate: 'June 1, 2024',
          lastLogin: '4 hours ago',
          profileStatus: 'Complete',
          expiry: 'September 1, 2024',
          package: 'Gold'
        }
      };

      const member = memberData[memberName] || memberData['Sarah Johnson'];

      // Update modal content
      document.getElementById('memberName').textContent = memberName;
      document.getElementById('memberId').textContent = `Member ID: ${member.id}`;
      document.getElementById('memberAge').textContent = member.age;
      document.getElementById('memberLocation').textContent = member.location;
      document.getElementById('memberMaritalStatus').textContent = member.maritalStatus;
      document.getElementById('memberLookingFor').textContent = member.lookingFor;
      document.getElementById('memberRegDate').textContent = member.regDate;
      document.getElementById('memberLastLogin').textContent = member.lastLogin;
      document.getElementById('memberProfileStatus').textContent = member.profileStatus;
      document.getElementById('memberExpiry').textContent = member.expiry;
      
      // Update package badge
      const packageBadge = document.getElementById('memberPackage');
      packageBadge.textContent = member.package;
      packageBadge.className = `package-badge ${member.package.toLowerCase()}`;

      // Show modal
      modal.style.display = 'block';

      // Close modal functionality
      closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
      });

      window.addEventListener('click', function(event) {
        if (event.target === modal) {
          modal.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html> 
<?php
// Database connection
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db = "matrimony";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: ".$conn->connect_error);

// Handle AJAX requests
if(isset($_POST['action'])){
    $action = $_POST['action'];
    
    if($action === 'add'){
        // Handle file upload for author photo
        $author_photo = '';
        if(isset($_FILES['author_photo']) && $_FILES['author_photo']['error'] == 0) {
            $target_dir = "uploads/";
            if(!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $file_extension = pathinfo($_FILES["author_photo"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $filename;
            
            // Check if image file is actual image
            $check = getimagesize($_FILES["author_photo"]["tmp_name"]);
            if($check !== false) {
                if(move_uploaded_file($_FILES["author_photo"]["tmp_name"], $target_file)) {
                    $author_photo = $target_file;
                }
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO blog (title, content, author_id, author_name, author_photo, category, status, publish_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssisssss",
            $_POST['title'],
            $_POST['content'],
            $_POST['author_id'],
            $_POST['author_name'],
            $author_photo,
            $_POST['category'],
            $_POST['status'],
            $_POST['publish_date']
        );
        echo $stmt->execute() ? json_encode(['success'=>true]) : json_encode(['success'=>false,'error'=>$stmt->error]);
        $stmt->close();
        exit;
    }
    elseif($action === 'get'){
        $id = intval($_POST['blog_id']);
        $res = $conn->query("SELECT * FROM blog WHERE blog_id=$id");
        if($res->num_rows>0){
            $blog = $res->fetch_assoc();
            echo json_encode(['success'=>true,'blog'=>$blog]);
        } else echo json_encode(['success'=>false]);
        exit;
    }
    elseif($action === 'edit'){
        // Handle file upload for author photo if a new one is provided
        $author_photo = $_POST['existing_author_photo'];
        if(isset($_FILES['author_photo']) && $_FILES['author_photo']['error'] == 0) {
            $target_dir = "uploads/";
            if(!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $file_extension = pathinfo($_FILES["author_photo"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $filename;
            
            // Check if image file is actual image
            $check = getimagesize($_FILES["author_photo"]["tmp_name"]);
            if($check !== false) {
                if(move_uploaded_file($_FILES["author_photo"]["tmp_name"], $target_file)) {
                    // Delete old photo if it exists
                    if(!empty($author_photo) && file_exists($author_photo)) {
                        unlink($author_photo);
                    }
                    $author_photo = $target_file;
                }
            }
        }
        
        $stmt = $conn->prepare("UPDATE blog SET title=?, content=?, author_name=?, author_photo=?, category=?, status=?, publish_date=? WHERE blog_id=?");
        $stmt->bind_param(
            "sssssssi",
            $_POST['title'],
            $_POST['content'],
            $_POST['author_name'],
            $author_photo,
            $_POST['category'],
            $_POST['status'],
            $_POST['publish_date'],
            $_POST['blog_id']
        );
        
        echo $stmt->execute() ? json_encode(['success'=>true]) : json_encode(['success'=>false,'error'=>$stmt->error]);
        $stmt->close();
        exit;
    }
    elseif($action === 'delete'){
        // Get the blog to delete the associated author photo
        $id = intval($_POST['blog_id']);
        $res = $conn->query("SELECT author_photo FROM blog WHERE blog_id=$id");
        if($res->num_rows>0){
            $blog = $res->fetch_assoc();
            // Delete the author photo file if it exists
            if(!empty($blog['author_photo']) && file_exists($blog['author_photo'])) {
                unlink($blog['author_photo']);
            }
        }
        
        $stmt = $conn->prepare("DELETE FROM blog WHERE blog_id=?");
        $stmt->bind_param("i", $_POST['blog_id']);
        echo $stmt->execute() ? json_encode(['success'=>true]) : json_encode(['success'=>false,'error'=>$stmt->error]);
        $stmt->close();
        exit;
    }
}

// Fetch stats and blogs
$total_blogs = $conn->query("SELECT COUNT(*) as count FROM blog")->fetch_assoc()['count'];
$published_blogs = $conn->query("SELECT COUNT(*) as count FROM blog WHERE status='published'")->fetch_assoc()['count'];
$draft_blogs = $conn->query("SELECT COUNT(*) as count FROM blog WHERE status='draft'")->fetch_assoc()['count'];
$archived_blogs = $conn->query("SELECT COUNT(*) as count FROM blog WHERE status='archived'")->fetch_assoc()['count'];

$blogs_result = $conn->query("SELECT * FROM blog ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blog Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet"/>
<style>
/* === Basic Styles === */
body{margin:0;font-family:'Inter',sans-serif;background:#f5f7f9;color:#333;}
.top-nav{display:flex;justify-content:space-between;align-items:center;padding:15px 20px;background:#fff;box-shadow:0 2px 4px rgba(0,0,0,0.1);}
.logo-circle{width:40px;height:40px;border-radius:50%;background:#4a90e2;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:bold;}
.main-menu a{color:#4a90e2;text-decoration:none;font-weight:600;}
.user-info{font-weight:600;color:#555;}
.dashboard-layout{display:flex;min-height:calc(100vh - 70px);}
.sidebar{width:250px;background:#f8f9fa;padding:20px;border-right:1px solid #e0e0e0;}
.matrimony-name{font-size:18px;font-weight:700;margin-bottom:20px;color:#4a90e2;}
.sidebar-menu{list-style:none;padding:0;}
.sidebar-menu li{margin-bottom:10px;}
.sidebar-menu a{display:block;padding:10px 15px;color:#555;text-decoration:none;border-radius:6px;transition:all 0.3s;}
.sidebar-menu a:hover,.sidebar-menu a.active{background:#4a90e2;color:#fff;}
.main-content{flex:1;padding:20px;background:#f5f7f9;}
.top-actions{display:flex;gap:15px;margin-bottom:20px;}
.action-btn{display:flex;align-items:center;gap:8px;padding:10px 20px;border:none;border-radius:6px;cursor:pointer;font-weight:600;transition:all 0.3s;}
.action-btn.primary{background:#4a90e2;color:#fff;}
.action-btn.secondary{background:#f1f1f1;color:#333;}
.action-btn:hover{opacity:.9;transform:translateY(-2px);}
.search-bar{display:flex;align-items:center;background:#fff;border-radius:8px;padding:8px 15px;margin-bottom:20px;box-shadow:0 2px 4px rgba(0,0,0,0.05);}
.search-bar input{border:none;outline:none;margin-left:10px;width:100%;font-size:16px;}
.content-section{background:#fff;border-radius:8px;padding:20px;margin-bottom:20px;box-shadow:0 2px 4px rgba(0,0,0,0.05);}
.content-section h2{margin-bottom:20px;color:#333;font-size:22px;}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}
.stat-card{display:flex;align-items:center;padding:20px;background:#f8f9fa;border-radius:8px;border-left:4px solid #4a90e2;}
.stat-icon{width:50px;height:50px;border-radius:50%;background:#4a90e2;color:#fff;display:flex;align-items:center;justify-content:center;margin-right:15px;font-size:20px;}
.stat-info h3{font-size:14px;color:#777;margin-bottom:5px;}
.stat-number{font-size:24px;font-weight:700;color:#333;margin-bottom:5px;}
.stat-change{font-size:12px;display:flex;align-items:center;gap:5px;}
.stat-change.positive{color:#28a745;}
.stat-change.pending{color:#ffc107;}
.stat-change.negative{color:#dc3545;}
.data-table{width:100%;border-collapse:collapse;}
.data-table th,.data-table td{padding:12px 15px;text-align:left;border-bottom:1px solid #ddd;}
.data-table th{background:#f8f9fa;font-weight:600;color:#555;}
.data-table tr:hover{background:#f8f9fa;}
.blog-info{display:flex;flex-direction:column;}
.blog-title{font-weight:600;margin-bottom:5px;}
.blog-excerpt{font-size:12px;color:#777;}
.author-info{display:flex;flex-direction:column;}
.author-name{font-weight:600;margin-bottom:5px;}
.author-role{font-size:12px;color:#777;}
.category-badge{padding:5px 10px;border-radius:20px;font-size:12px;font-weight:600;}
.category-badge.dating-tips{background:#e8f5e9;color:#2e7d32;}
.category-badge.relationships{background:#e3f2fd;color:#1565c0;}
.category-badge.wedding{background:#f3e5f5;color:#7b1fa2;}
.category-badge.communication{background:#fff3e0;color:#ef6c00;}
.category-badge.safety{background:#ffebee;color:#c62828;}
.status-badge{padding:5px 10px;border-radius:20px;font-size:12px;font-weight:600;}
.status-badge.success{background:#e8f5e9;color:#2e7d32;}
.status-badge.pending{background:#fff3e0;color:#ef6c00;}
.status-badge.failed{background:#ffebee;color:#c62828;}
.action-buttons{display:flex;gap:8px;}
.view-btn,.edit-btn,.delete-btn{width:32px;height:32px;border:none;border-radius:4px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
.view-btn{background:#e3f2fd;color:#1565c0;}
.edit-btn{background:#fff3e0;color:#ef6c00;}
.delete-btn{background:#ffebee;color:#c62828;}
.text-center{text-align:center;}
.modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;overflow:auto;background:rgba(0,0,0,0.4);}
.modal-content{background:#fefefe;margin:5% auto;padding:0;border:1px solid #888;width:80%;max-width:800px;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.2);}
.modal-header{display:flex;justify-content:space-between;align-items:center;padding:20px;border-bottom:1px solid #e0e0e0;}
.modal-header h2{margin:0;display:flex;align-items:center;gap:10px;}
.modal-body{padding:20px;}
.close{color:#aaa;font-size:28px;font-weight:bold;cursor:pointer;}
.close:hover{color:#000;}
.form-row{display:flex;gap:20px;margin-bottom:15px;}
.form-column{flex:1;}
.form-column.full-width{flex:100%;}
.form-group{margin-bottom:15px;}
.form-group label{display:block;margin-bottom:5px;font-weight:600;color:#555;}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;font-size:16px;}
.form-group textarea{resize:vertical;min-height:120px;}
.form-actions{display:flex;gap:10px;margin-top:20px;}
.btn-primary,.btn-secondary{padding:10px 20px;border:none;border-radius:6px;cursor:pointer;font-weight:600;display:flex;align-items:center;gap:8px;}
.btn-primary{background:#4a90e2;color:#fff;}
.btn-secondary{background:#f1f1f1;color:#333;}
.author-photo-preview {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-top: 10px;
    display: none;
}
</style>
</head>
<body>
  <header class="top-nav">
<div class="logo-circle">M</div>
<nav class="main-menu"><a href="#" class="logout">Logout</a></nav>
<div class="user-info" id="userInfo"><span id="userDisplay">ADMIN</span></div>
  </header>

  <div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="matrimony-name">Matrimony Name</div>
      <ul class="sidebar-menu">
        <li><a href="members.html" class="sidebar-link">Manage members</a></li>
        <li><a href="call-management.html" class="sidebar-link">Call management</a></li>
        <li><a href="user-message-management.php" class="sidebar-link">User message management</a></li>
        <li><a href="review-management.html" class="sidebar-link">Review management</a></li>
        <li><a href="transaction-management.html" class="sidebar-link">Transaction management</a></li>
        <li><a href="packages-management.html" class="sidebar-link">Packages management</a></li>
        <li><a href="blog-management.php" class="sidebar-link active">Blog management</a></li>
        <li><a href="total-earnings.html" class="sidebar-link">Total earnings</a></li>
        <li><a id="staffLink" href="staff.html" class="sidebar-link">Staff management</a></li>
      </ul>
    </aside>

    <main class="main-content">

      <div class="top-actions">
<button id="addBlogBtn" class="action-btn primary"><i class="fas fa-plus"></i>Add New Blog</button>
      </div>

      <div class="content-section">
        <h2>Blog Overview</h2>
        <div class="stats-grid">
<div class="stat-card"><div class="stat-icon"><i class="fas fa-list"></i></div><div class="stat-info"><h3>Total Blogs</h3><div class="stat-number"><?=$total_blogs?></div></div></div>
<div class="stat-card"><div class="stat-icon"><i class="fas fa-check"></i></div><div class="stat-info"><h3>Published Blogs</h3><div class="stat-number"><?=$published_blogs?></div></div></div>
<div class="stat-card"><div class="stat-icon"><i class="fas fa-file-alt"></i></div><div class="stat-info"><h3>Draft Blogs</h3><div class="stat-number"><?=$draft_blogs?></div></div></div>
<div class="stat-card"><div class="stat-icon"><i class="fas fa-archive"></i></div><div class="stat-info"><h3>Archived Blogs</h3><div class="stat-number"><?=$archived_blogs?></div></div></div>
            </div>
          </div>
          
      <div class="content-section">
<h2>All Blogs</h2>
        <table class="data-table">
          <thead>
            <tr>
<th>Blog</th>
              <th>Author</th>
              <th>Category</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
<?php 
if($blogs_result->num_rows > 0) {
    while($blog = $blogs_result->fetch_assoc()): 
?>
<tr>
<td class="blog-info">
<div class="blog-title"><?=htmlspecialchars($blog['title'])?></div>
<div class="blog-excerpt"><?=substr(htmlspecialchars($blog['content']),0,50).'...'?></div>
              </td>
<td class="author-info">
  <div class="author-name"><?=htmlspecialchars($blog['author_name'])?></div>
  <?php if(!empty($blog['author_photo'])): ?>
    <img src="<?=htmlspecialchars($blog['author_photo'])?>" width="40" height="40" style="border-radius:50%;margin-top:5px;">
  <?php endif; ?>
  <div class="author-role">Author</div>
              </td>

<td>
<span class="category-badge"><?=htmlspecialchars($blog['category'])?></span>
              </td>
              <td>
<?php if($blog['status']=='published'): ?>
                <span class="status-badge success">Published</span>
<?php elseif($blog['status']=='draft'): ?>
                <span class="status-badge pending">Draft</span>
<?php else: ?>
                <span class="status-badge failed">Archived</span>
<?php endif; ?>
              </td>
<td class="action-buttons">
<button class="view-btn" onclick="viewBlog(<?=$blog['blog_id']?>)"><i class="fas fa-eye"></i></button>
<button class="edit-btn" onclick="openEditModal(<?=$blog['blog_id']?>)"><i class="fas fa-edit"></i></button>
<button class="delete-btn" onclick="deleteBlog(<?=$blog['blog_id']?>)"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
<?php 
    endwhile; 
} else {
    echo '<tr><td colspan="5" class="text-center">No blogs found</td></tr>';
}
?>
          </tbody>
        </table>
      </div>

</main>
</div>

<!-- Modal -->
<div id="blogModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2><i class="fas fa-blog"></i> <span id="modalTitle">Add Blog</span></h2>
<span class="close" id="closeModal">&times;</span>
</div>
<div class="modal-body">
<form id="blogForm" enctype="multipart/form-data">
<input type="hidden" name="blog_id" id="blogId">
<input type="hidden" name="existing_author_photo" id="existingAuthorPhoto">
          <div class="form-row">
            <div class="form-column">
              <div class="form-group">
<label>Title</label>
<input type="text" name="title" id="title" required>
              </div>
            </div>
            <div class="form-column">
              <div class="form-group">
<label>Author ID</label>
<input type="number" name="author_id" id="author_id" required>
          </div>
        </div>
      </div>
              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
    <label>Author Name</label>
    <input type="text" name="author_name" id="author_name" required>
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
    <label>Author Photo </label>
    <input type="file" name="author_photo" id="author_photo" accept="image/*" onchange="previewImage(this)">
    <img id="authorPhotoPreview" class="author-photo-preview" src="" alt="Author Photo Preview">
                  </div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-column">
                  <div class="form-group">
<label>Category</label>
<select name="category" id="category" required>
                      <option value="Dating Tips">Dating Tips</option>
                      <option value="Relationships">Relationships</option>
                      <option value="Wedding">Wedding</option>
                      <option value="Communication">Communication</option>
                      <option value="Safety">Safety</option>
                    </select>
                  </div>
                </div>
                <div class="form-column">
                  <div class="form-group">
<label>Status</label>
<select name="status" id="status" required>
                      <option value="published">Published</option>
                      <option value="draft">Draft</option>
                      <option value="archived">Archived</option>
                    </select>
                  </div>
                </div>
              </div>

<div class="form-row">
<div class="form-column full-width">
<div class="form-group">
<label>Publish Date</label>
<input type="date" name="publish_date" id="publish_date" required>
</div>
</div>
</div>

              <div class="form-row">
                <div class="form-column full-width">
                  <div class="form-group">
<label>Content</label>
<textarea name="content" id="content" required></textarea>
                  </div>
                </div>
              </div>

              <div class="form-actions">
<button type="submit" class="btn-primary">Save</button>
<button type="button" class="btn-secondary" id="cancelModal">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>

  <script>
// Modal
const blogModal = document.getElementById('blogModal');
const addBlogBtn = document.getElementById('addBlogBtn');
const closeModal = document.getElementById('closeModal');
const cancelModal = document.getElementById('cancelModal');
const modalTitle = document.getElementById('modalTitle');
const blogForm = document.getElementById('blogForm');

addBlogBtn.onclick = ()=>{
  blogForm.reset();
  document.getElementById('blogId').value = '';
  document.getElementById('existingAuthorPhoto').value = '';
  document.getElementById('authorPhotoPreview').style.display = 'none';
  modalTitle.innerText = 'Add Blog';
  blogModal.style.display = 'block';
};
closeModal.onclick = ()=> blogModal.style.display = 'none';
cancelModal.onclick = ()=> blogModal.style.display = 'none';
window.onclick = e=> { if(e.target==blogModal) blogModal.style.display='none'; };

// Image preview function
function previewImage(input) {
    const preview = document.getElementById('authorPhotoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        preview.src = '';
    }
}

// AJAX
blogForm.onsubmit = async e=>{
  e.preventDefault();
  const formData = new FormData(blogForm);
  let action = formData.get('blog_id') ? 'edit' : 'add';
  formData.append('action', action);

  try {
    const res = await fetch('', {method:'POST', body:formData});
    const data = await res.json();
    if(data.success){
      alert('Saved successfully!');
      location.reload();
    } else {
      alert('Error: '+(data.error||'Failed to save'));
    }
  } catch (error) {
    alert('Error: ' + error.message);
  }
};

function openEditModal(id){
  modalTitle.innerText = 'Edit Blog';
  const formData = new FormData();
  formData.append('action','get');
  formData.append('blog_id',id);

  fetch('',{method:'POST',body:formData})
  .then(r=>r.json())
  .then(data=>{
    if(data.success){
      const blog = data.blog;
      document.getElementById('blogId').value = blog.blog_id;
      document.getElementById('title').value = blog.title;
      document.getElementById('author_id').value = blog.author_id;
      document.getElementById('author_name').value = blog.author_name;
      document.getElementById('existingAuthorPhoto').value = blog.author_photo;
      document.getElementById('category').value = blog.category;
      document.getElementById('status').value = blog.status;
      document.getElementById('publish_date').value = blog.publish_date;
      document.getElementById('content').value = blog.content;
      
      // Show existing author photo if available
      const preview = document.getElementById('authorPhotoPreview');
      if (blog.author_photo) {
        preview.src = blog.author_photo;
        preview.style.display = 'block';
      } else {
        preview.style.display = 'none';
      }
      
      blogModal.style.display = 'block';
    } else {
      alert('Blog not found');
    }
  })
  .catch(error => {
    alert('Error: ' + error.message);
  });
}

function deleteBlog(id){
  if(confirm('Are you sure you want to delete this blog?')){
    const formData = new FormData();
    formData.append('action','delete');
    formData.append('blog_id',id);
    fetch('',{method:'POST',body:formData})
    .then(r=>r.json())
    .then(data=>{
      if(data.success) {
              location.reload();
            } else {
        alert('Error: '+data.error);
      }
    })
    .catch(error => {
      alert('Error: ' + error.message);
    });
  }
}

function viewBlog(id) {
  // You can implement a view functionality here
  alert('View functionality for blog ID: ' + id);
}
  </script>
</body>
</html> 
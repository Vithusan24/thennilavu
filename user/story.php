<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "matrimony";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle review form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name       = $_POST['name'] ?? '';
    $profession = $_POST['profession'] ?? '';
    $country    = $_POST['country'] ?? '';
    $message    = $_POST['message'] ?? '';
    $rating     = $_POST['rating'] ?? 0;
    $photo_name = null;

    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $allowed_types = ['image/jpeg','image/png','image/gif'];
        $mime_type = mime_content_type($_FILES["photo"]["tmp_name"]);
        if (in_array($mime_type, $allowed_types)) {
            $photo_name = time().'_'.basename($_FILES["photo"]["name"]);
            move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir.$photo_name);
        }
    }

    $stmt = $conn->prepare("INSERT INTO reviews (name, profession, country, comment, rating, photo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $name, $profession, $country, $message, $rating, $photo_name);
    $stmt->execute();
    $stmt->close();
}

// Fetch success stories (only published)
$stories = $conn->query("SELECT * FROM blog WHERE status='published' ORDER BY publish_date DESC LIMIT 6");

// Fetch popular posts
$popular = $conn->query("SELECT * FROM blog WHERE status='published' ORDER BY RAND() LIMIT 4");

// Fetch latest posts
$latest = $conn->query("SELECT * FROM blog WHERE status='published' ORDER BY created_at DESC LIMIT 4");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Success Stories</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Segoe UI', sans-serif; }
.story-card { transition: transform 0.3s; cursor: pointer; height: 100%; }
.story-card:hover { transform: scale(1.03); }
.review-section { background-color: #f8f9fa; padding: 60px 0; }
.counter { font-size: 2.5rem; font-weight: bold; color: #ff4d6d; }
.modal-img { width: 100%; border-radius: 10px; }
.btn a { text-decoration: none; color: black; }
.heart { position: absolute; color: red; font-size: 20px; pointer-events: none; animation: floatUp 3s ease-out forwards; opacity: 1; z-index: 9999; }
@keyframes floatUp { 0% { transform: translate(0, 0) scale(1); opacity: 1; } 100% { transform: translate(-20px, -200px) scale(1.5); opacity: 0; } }
.nav-link:hover { color: #ffe0f0 !important; text-decoration: underline; }
.navbar-brand:hover { color: #fff0f5 !important; }
.card-img-top { height: 250px; object-fit: cover; }
</style>
</head>
<body>
<div class="container-fluid bg-black bg-opacity-75">
    <nav class="navbar fixed-top" style="background-color: rgb(127, 8, 8);">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="" width="30" height="30" class="d-inline-block align-text-top">
            </a>
            <span style="color: aliceblue;">sample@gmail.com</span>
            <div class="ms-auto d-flex gap-2">
                <button class="btn bg-primary">
                    <a class="nav-link text-white fw-semibold me-3" href="dashboard.html">DashBoard</a>
                </button>
                <button class="btn bg-secondary">
                    <a class="nav-link text-white fw-semibold me-3" href="#">Log Out</a>
                </button>
            </div>
        </div>
    </nav>
    <br><br><br>
    <nav class="navbar navbar-expand-lg shadow-sm" style="background: linear-gradient(to right, #ec3107, #fda101); border-radius: 10px;">
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold" href="#">
                <img src="logo.png" alt="" width="30" height="30" class="d-inline-block align-text-top me-2">
                TheanNilavu
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link text-white fw-semibold me-3" href="home.php">Home</a>
                    <a class="nav-link text-white fw-semibold me-3" href="members.php">Member Ship</a>
                    <a class="nav-link text-white fw-semibold me-3" href="mem.php">Members</a>
                    <a class="nav-link text-white fw-semibold me-3" href="package.php">Packages</a>
                    <a class="nav-link text-white fw-semibold me-3" href="contact.php">Contact Us</a>
                    <a class="nav-link text-white fw-semibold" style="text-decoration: underline;" href="story.php">Stories</a>
                </div>
            </div>
        </div>
    </nav>
</div>

<section class="py-5" style="background: linear-gradient(to right, #a37303, #3a011c);">
    <div class="container">
        <h2 class="text-center mb-5 text-white">Success Stories</h2>
        <div class="row g-4">
            <?php if ($stories && $stories->num_rows > 0): ?>
                <?php $i=1; while($s = $stories->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card story-card" data-bs-toggle="modal" data-bs-target="#storyModal<?=$i?>">
                        <img src="<?= $s['author_photo'] ? 'uploads/' . htmlspecialchars($s['author_photo']) : 'img/default.jpg' ?>" class="card-img-top" alt="Couple Story">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($s['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($s['content'], 0, 60)) ?>...</p>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="storyModal<?=$i?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($s['title']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <img src="<?= $s['author_photo'] ? 'uploads/' . htmlspecialchars($s['author_photo']) : 'img/default.jpg' ?>" class="modal-img mb-3 w-100" alt="Couple">
                                <p><?= nl2br(htmlspecialchars($s['content'])) ?></p>
                                <hr>
                                <h6>📌 Popular Posts</h6>
                                <div class="d-flex gap-3 overflow-auto">
                                    <?php if ($popular && $popular->num_rows > 0): ?>
                                        <?php while($p = $popular->fetch_assoc()): ?>
                                        <div class="card" style="min-width: 10rem;">
                                            <img src="<?= $p['author_photo'] ? 'uploads/' . htmlspecialchars($p['author_photo']) : 'img/default.jpg' ?>" class="card-img-top" alt="" style="height: 120px; object-fit: cover;">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= htmlspecialchars($p['title']) ?></h6>
                                                <p class="card-text small"><?= htmlspecialchars(substr($p['content'], 0, 40)) ?>...</p>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p>No popular posts available.</p>
                                    <?php endif; ?>
                                </div>
                                <hr>
                                <h6>🕊 Latest Posts</h6>
                                <div class="row g-3">
                                    <?php if ($latest && $latest->num_rows > 0): ?>
                                        <?php while($l = $latest->fetch_assoc()): ?>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <img src="<?= $l['author_photo'] ? 'uploads/' . htmlspecialchars($l['author_photo']) : 'img/default.jpg' ?>" class="card-img-top" alt="" style="height: 150px; object-fit: cover;">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?= htmlspecialchars($l['title']) ?></h6>
                                                    <p class="card-text"><?= htmlspecialchars(substr($l['content'], 0, 60)) ?>...</p>
                                                    <a href="#" class="btn btn-sm btn-outline-primary mt-2">Read Full Story</a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p>No latest posts available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $i++; endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="text-white">No success stories available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="review-section">
    <div class="container">
        <h2 class="text-center mb-5">Share Your Experience</h2>
        <div class="row">
            <div class="col-md-6">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="member_id" value="1">
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" placeholder="Your name" required></div>
                    <div class="mb-3"><label class="form-label">Profession</label><input type="text" name="profession" class="form-control" placeholder="E.g. Engineer"></div>
                    <div class="mb-3"><label class="form-label">Country</label><input type="text" name="country" class="form-control" placeholder="E.g. Sri Lanka"></div>
                    <div class="mb-3"><label class="form-label">Message</label><textarea name="message" class="form-control" rows="4" placeholder="Your inspiring experience..." required></textarea></div>
                    <div class="mb-3"><label class="form-label">Rating</label><select name="rating" class="form-control" required>
                        <option value="5">★★★★★</option>
                        <option value="4">★★★★</option>
                        <option value="3">★★★</option>
                        <option value="2">★★</option>
                        <option value="1">★</option>
                    </select></div>
                    <div class="mb-3"><label class="form-label">Add Your Photo (JPEG, PNG, GIF)</label><input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/gif"></div>
                    <button type="submit" class="btn btn-success">Submit Review</button>
                </form>
            </div>
            <div class="col-md-6 text-center g-2">
                <img src="img/d81b6668b4cd875545b963b9d8541773.jpg" class="img-fluid rounded" alt="Review" style="height: 700px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Our Community</h2>
        <div class="row text-center">
            <div class="col-md-4"><div class="counter" id="free">0</div><p>Free Members</p></div>
            <div class="col-md-4"><div class="counter" id="paid">0</div><p>Paid Members</p></div>
            <div class="col-md-4"><div class="counter" id="active">0</div><p>Active Members</p></div>
        </div>
    </div>
</section>

<footer class="bg-dark text-white pt-4 pb-2 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-3"><img src="logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top"></div>
            <div class="col-md-3 mb-3"><h5>About Us</h5><p>We provide top-notch services to help you achieve your goals with ease and satisfaction.</p></div>
            <div class="col-md-3 mb-3"><h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="home.php" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="members.php" class="text-white text-decoration-none">Services</a></li>
                    <li><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>
                    <li><a href="#" class="text-white text-decoration-none">Login</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3"><h5>Contact</h5>
                <p>Email: info@example.com</p>
                <p>Phone: +94 77 123 4567</p>
                <p>Address: Colombo, Sri Lanka</p>
            </div>
        </div>
        <div class="text-center pt-3 border-top mt-3"><small>&copy; 2025 YourCompany. All rights reserved.</small></div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function animateCounter(id, target) {
    let count = 0;
    const speed = 20;
    const interval = setInterval(() => {
        if (count < target) {
            count++;
            document.getElementById(id).textContent = count;
        } else { clearInterval(interval); }
    }, speed);
}

window.onload = function () {
    animateCounter("free", 1520);
    animateCounter("paid", 630);
    animateCounter("active", 978);
};

document.addEventListener("mousemove", function(e) {
    const heart = document.createElement("div");
    heart.className = "heart";
    heart.innerHTML = "❤️";
    const scale = 1 + Math.random();
    const duration = 2 + Math.random() * 2;
    heart.style.left = e.pageX + "px";
    heart.style.top = e.pageY + "px";
    heart.style.transform = `translate(0, 0) scale(${scale})`;
    heart.style.animationDuration = `${duration}s`;
    document.body.appendChild(heart);
    setTimeout(() => { heart.remove(); }, duration * 1000);
});
</script>
</body>
</html>

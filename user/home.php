<?php
session_start();

// Database connection and initialization
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "matrimony";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure search log table exists
$conn->query("CREATE TABLE IF NOT EXISTS search_queries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  looking_for VARCHAR(20) NULL,
  age_range VARCHAR(20) NULL,
  country VARCHAR(100) NULL,
  city VARCHAR(100) NULL,
  religion VARCHAR(50) NULL,
  user_ip VARCHAR(45) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Fetch latest reviews
$reviews_sql = "SELECT name, profession, country, comment, rating, photo 
                FROM reviews 
                ORDER BY review_date DESC 
                LIMIT 10";
$reviews_result = $conn->query($reviews_sql);

// Fetch packages
$packages_sql = "SELECT * FROM packages WHERE status = 'active' ORDER BY created_at DESC";
$packages_result = $conn->query($packages_sql);

// Fetch success stories
$stories_sql = "SELECT blog_id, title, author_name, author_photo, content 
                FROM blog 
                WHERE status='published' AND category='Wedding' 
                ORDER BY publish_date DESC LIMIT 4";
$stories_result = $conn->query($stories_sql);

// Process search if form submitted
$search_results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    // Sanitize inputs
    $lookingFor = sanitizeInput($_POST['lookingFor'] ?? '');
    $age_range = sanitizeInput($_POST['age'] ?? '');
    $country = sanitizeInput($_POST['country'] ?? '');
    $city = sanitizeInput($_POST['city'] ?? '');
    $religion = sanitizeInput($_POST['religion'] ?? '');

    // Log search submission
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($stmtLog = $conn->prepare("INSERT INTO search_queries (looking_for, age_range, country, city, religion, user_ip) VALUES (?, ?, ?, ?, ?, ?)")) {
        $stmtLog->bind_param('ssssss', $lookingFor, $age_range, $country, $city, $religion, $ip);
        $stmtLog->execute();
    }

    // Build search query
    $clauses = [];
    $params = [];
    $types = '';

    if (!empty($lookingFor)) {
        $clauses[] = 'gender = ?';
        $params[] = $lookingFor;
        $types .= 's';
    }

    if (!empty($age_range)) {
        if (strpos($age_range, '+') !== false) {
            $min_age = (int)str_replace('+', '', $age_range);
            $clauses[] = '(m.dob IS NOT NULL AND m.dob <= CURDATE() AND TIMESTAMPDIFF(YEAR, m.dob, CURDATE()) >= ?)';
            $params[] = $min_age;
            $types .= 'i';
        } else {
            $parts = explode('-', $age_range);
            if (count($parts) === 2) {
                $min_age = (int)$parts[0];
                $max_age = (int)$parts[1];
                $clauses[] = '(m.dob IS NOT NULL AND m.dob <= CURDATE() AND TIMESTAMPDIFF(YEAR, m.dob, CURDATE()) BETWEEN ? AND ?)';
                $params[] = $min_age;
                $params[] = $max_age;
                $types .= 'ii';
            }
        }
    }

    if (!empty($country)) {
        $clauses[] = 'country LIKE ?';
        $params[] = "%$country%";
        $types .= 's';
    }

    if (!empty($city)) {
        $clauses[] = 'city LIKE ?';
        $params[] = "%$city%";
        $types .= 's';
    }

    if (!empty($religion)) {
        $clauses[] = 'LOWER(m.religion) = LOWER(?)';
        $params[] = $religion;
        $types .= 's';
    }

    $where = count($clauses) ? ('WHERE ' . implode(' AND ', $clauses)) : '';
    $sql = "SELECT 
              m.id,
              m.name,
              m.photo,
              m.city,
              m.country,
              m.profession,
              TIMESTAMPDIFF(YEAR, m.dob, CURDATE()) AS age,
              p.complexion,
              p.height_cm,
              p.weight_kg,
              p.blood_group,
              pe.preferred_country,
              pe.min_age AS pref_min_age,
              pe.max_age AS pref_max_age
            FROM members m
            LEFT JOIN physical_info p 
              ON p.member_id = m.id AND p.id = (SELECT MAX(id) FROM physical_info WHERE member_id = m.id)
            LEFT JOIN partner_expectations pe
              ON pe.member_id = m.id AND pe.id = (SELECT MAX(id) FROM partner_expectations WHERE member_id = m.id)
            $where
            ORDER BY m.created_at DESC
            LIMIT 50";
            
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheanNilavu Matrimony - Find Your Perfect Match</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ec3107;
            --secondary-color: #fda101;
            --dark-color: #4a3d3d;
            --light-color: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        
        .btn a {
            text-decoration: none;
            color: black;
        }
        
        .card-text{
            color: blue;
        }
        
        .card-text1{
            color: white;
        }
        
        .heart {
            position: absolute;
            color: red;
            font-size: 20px;
            pointer-events: none;
            animation: floatUp 3s ease-out forwards;
            opacity: 1;
            z-index: 9999;
        }
        
        @keyframes floatUp {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(-20px, -200px) scale(1.5);
                opacity: 0;
            }
        }
        
        .package-card {
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }
        
        .package-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            z-index: 2;
        }
        
        .counter-box h2 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 10px;
        }
        
        .counter-box p {
            font-size: 1.1rem;
            color: #555;
            margin: 0;
        }
        
        .nav-link:hover {
            color: #ffe0f0 !important;
            text-decoration: underline;
        }
        
        .navbar-brand:hover {
            color: #fff0f5 !important;
        }
        
        .story-img {
            transition: transform 0.5s ease;
            height: 400px;
            object-fit: cover;
            width: 100%;
        }
        
        .story-img:hover {
            transform: scale(1.03);
        }
        
        .zoom-bounce {
            animation: zoomBounce 1s ease-out;
        }
        
        @keyframes zoomBounce {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
                opacity: 1;
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .testimonial-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            min-width: 300px;
            max-width: 350px;
            display: inline-block;
        }
        
        .scrolling-wrapper {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 20px;
            -webkit-overflow-scrolling: touch;
        }
        
        .scrolling-wrapper::-webkit-scrollbar {
            height: 8px;
        }
        
        .scrolling-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .scrolling-wrapper::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        .profile-card {
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        @media (max-width: 768px) {
            .display-1 {
                font-size: 2rem !important;
                text-align: center;
            }
            
            .zoom-bounce {
                animation: zoomBounce 1s ease-in-out;
            }
            
            .col-6.justify-content-start {
                justify-content: center !important;
                text-align: center;
                margin-bottom: 1.5rem;
            }
            
            .col-md-4 {
                margin-left: 0% !important;
                width: 100%;
            }
            
            form {
                padding: 2rem 1rem !important;
            }
            
            .story-img {
                height: auto;
            }
        }
        
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/holding-hand.jpg');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
        }
        
        .search-form {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .section-title {
            position: relative;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: var(--primary-color);
            margin: 15px auto;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        }
    </style>
</head>
<body>
    <div class="container-fluid bg-light bg-opacity-75">
        <!-- Fixed top navbar -->
        <nav class="navbar fixed-top" style="background-color: rgb(127, 8, 8);">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
                </a>
                <span style="color: aliceblue;">sample@gmail.com</span>
                
                <div class="ms-auto d-flex gap-2">
                    <button class="btn bg-primary">
                        <a class="nav-link text-white fw-semibold" style="text-decoration: none;" href="dashboard.html">DashBoard</a>
                    </button>
                    <button class="btn bg-secondary">
                        <a class="nav-link text-white fw-semibold" style="text-decoration: none;" href="#">Log Out</a>
                    </button>
                </div>
            </div>
        </nav>
        
        <br><br><br>
        
        <!-- Main Navigation -->
        <nav class="navbar navbar-expand-lg shadow-sm bg-gradient-primary" style="border-radius: 10px;">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-bold" href="#">
                    <img src="logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
                    TheanNilavu
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                    aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a class="nav-link text-white fw-semibold me-3 active" href="home.php">Home</a>
                        <a class="nav-link text-white fw-semibold me-3" href="members.php">Member Ship</a>
                        <a class="nav-link text-white fw-semibold me-3" href="mem.php">Members</a>
                        <a class="nav-link text-white fw-semibold me-3" href="package.php">Packages</a>
                        <a class="nav-link text-white fw-semibold me-3" href="contact.php">Contact Us</a>
                        <a class="nav-link text-white fw-semibold me-3" href="story.php">Stories</a>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Hero Section with Search Form -->
        <section class="hero-section text-white text-center">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-5 text-start">
                        <h1 class="display-1 fw-bold zoom-bounce">Welcome to TheanNilavu Matrimony</h1>
                        <p class="lead mt-3">Find your perfect life partner with us. Trusted by thousands of families worldwide.</p>
                    </div>
                    
                    <div class="col-lg-5 offset-lg-1">
                        <form id="searchForm" class="search-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <h3 class="text-center mb-4 fw-bold">Find Your Partner</h3>
                            
                            <div class="mb-3">
                                <label for="lookingFor" class="form-label">Looking for</label>
                                <select class="form-select" id="lookingFor" name="lookingFor">
                                    <option value="" selected disabled>Select Gender</option>
                                    <option value="Male" <?php echo (isset($_POST['lookingFor']) && $_POST['lookingFor'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (isset($_POST['lookingFor']) && $_POST['lookingFor'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="age" class="form-label">Age</label>
                                <select name="age" id="age" class="form-select">
                                    <option value="" selected disabled>Select Age Range</option>
                                    <option value="18-25" <?php echo (isset($_POST['age']) && $_POST['age'] == '18-25') ? 'selected' : ''; ?>>18-25</option>
                                    <option value="26-30" <?php echo (isset($_POST['age']) && $_POST['age'] == '26-30') ? 'selected' : ''; ?>>26-30</option>
                                    <option value="31-35" <?php echo (isset($_POST['age']) && $_POST['age'] == '31-35') ? 'selected' : ''; ?>>31-35</option>
                                    <option value="36-40" <?php echo (isset($_POST['age']) && $_POST['age'] == '36-40') ? 'selected' : ''; ?>>36-40</option>
                                    <option value="41-45" <?php echo (isset($_POST['age']) && $_POST['age'] == '41-45') ? 'selected' : ''; ?>>41-45</option>
                                    <option value="46+" <?php echo (isset($_POST['age']) && $_POST['age'] == '46+') ? 'selected' : ''; ?>>46+</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" placeholder="Enter country" value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="Enter city" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="religion" class="form-label">Religion</label>
                                <select class="form-select" id="religion" name="religion">
                                    <option value="" selected>Any</option>
                                    <option value="Buddhist" <?php echo (isset($_POST['religion']) && $_POST['religion'] == 'Buddhist') ? 'selected' : ''; ?>>Buddhist</option>
                                    <option value="Hindu" <?php echo (isset($_POST['religion']) && $_POST['religion'] == 'Hindu') ? 'selected' : ''; ?>>Hindu</option>
                                    <option value="Muslim" <?php echo (isset($_POST['religion']) && $_POST['religion'] == 'Muslim') ? 'selected' : ''; ?>>Muslim</option>
                                    <option value="Christian" <?php echo (isset($_POST['religion']) && $_POST['religion'] == 'Christian') ? 'selected' : ''; ?>>Christian</option>
                                    <option value="Catholic" <?php echo (isset($_POST['religion']) && $_POST['religion'] == 'Catholic') ? 'selected' : ''; ?>>Catholic</option>
                                    <option value="Other" <?php echo (isset($_POST['religion']) && $_POST['religion'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="search" class="btn btn-primary px-4">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Search Results -->
                <?php if (!empty($search_results)): ?>
                <div class="row mt-5" id="results">
                    <h3 class="text-white mb-4">Search Results</h3>
                    <div class="col-lg-10 mx-auto">
                        <?php foreach ($search_results as $profile): ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="row g-0 align-items-center">
                                <div class="col-md-2">
                                    <img src="<?php echo htmlspecialchars($profile['photo'] ? $profile['photo'] : 'img/d.webp'); ?>" class="img-fluid rounded-start" alt="<?php echo htmlspecialchars($profile['name']); ?>" style="height: 120px; width: 100%; object-fit: cover;">
                                </div>
                                <div class="col-md-7">
                                    <div class="card-body py-3">
                                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($profile['name']); ?></h5>
                                        <div class="text-muted small">Age: <?php echo htmlspecialchars($profile['age']); ?> · <?php echo htmlspecialchars($profile['city'] . ', ' . $profile['country']); ?></div>
                                        <div class="small mt-1">Profession: <?php echo htmlspecialchars($profile['profession']); ?></div>
                                        <div class="small text-muted mt-1">
                                            <?php if (!empty($profile['height_cm'])): ?>Height: <?php echo htmlspecialchars($profile['height_cm']); ?> cm · <?php endif; ?>
                                            <?php if (!empty($profile['complexion'])): ?>Complexion: <?php echo htmlspecialchars($profile['complexion']); ?> · <?php endif; ?>
                                            <?php if (!empty($profile['blood_group'])): ?>Blood: <?php echo htmlspecialchars($profile['blood_group']); ?><?php endif; ?>
                                        </div>
                                        <?php if (isset($profile['pref_min_age']) || isset($profile['pref_max_age'])): ?>
                                        <div class="small text-muted">Pref Age: <?php echo htmlspecialchars(($profile['pref_min_age'] ?? '')); ?>–<?php echo htmlspecialchars(($profile['pref_max_age'] ?? '')); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3 text-end pe-3">
                                    <?php $allowed = !empty($_SESSION['has_active_package']) && $_SESSION['has_active_package'] === true; ?>
                                    <?php if ($allowed): ?>
                                        <a href="profile.php?id=<?php echo (int)$profile['id']; ?>" class="btn btn-primary btn-sm mt-2">View Profile</a>
                                    <?php else: ?>
                                        <a href="package.html" class="btn btn-outline-warning btn-sm mt-2" title="Upgrade to view profiles">Choose Package to View</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="row mt-5" id="results">
                    <div class="col-12">
                        <div class="alert alert-info text-dark">
                            No profiles found matching your criteria. Please try different search parameters.
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- About Us Section -->
        <section class="py-5 bg-light" id="about">
            <div class="container">
                <h2 class="section-title">About Us</h2>
                <div class="row align-items-center">
                    <div class="col-md-6" data-aos="zoom-in" data-aos-duration="1200">
                        <img src="img/112024-house-on-the-cloud.jpeg" class="img-fluid rounded shadow" alt="About Image">
                    </div>
                    
                    <div class="col-md-6" data-aos="fade-left" data-aos-duration="1000">
                        <p>TheanNilavu Matrimony is a trusted and inclusive matrimonial platform designed to help individuals find their life partners with honesty, respect, and safety. While our roots are inspired by Tamil culture and traditions, we proudly welcome people from all backgrounds, communities, and cultures. This is not just a Tamil-only site — it's a place for everyone seeking meaningful relationships.</p>
                        
                        <h4 class="mt-4">Our Vision</h4>
                        <p>At TheanNilavu, we don't just create matches — we help build lifelong relationships. We aim to offer a peaceful, secure, and user-friendly space where people can find partners who truly understand their values, preferences, and aspirations.</p>
                        
                        <h4 class="mt-4">Our Mission</h4>
                        <p>We are committed to providing a platform that is safe, respectful, and inclusive for all. Our mission is to connect individuals from diverse backgrounds, fostering relationships based on mutual respect and understanding.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Packages Section -->
        <section class="py-5 bg-gradient-primary text-white">
            <div class="container">
                <h2 class="section-title text-white">Our Packages</h2>
                <p class="text-center mb-5">Choose from our affordable and flexible membership packages tailored to meet your preferences and requirements.</p>
                
                <div class="row g-4">
                    <?php if ($packages_result && $packages_result->num_rows > 0): ?>
                        <?php while ($package = $packages_result->fetch_assoc()): ?>
                        <div class="col-md-4" data-aos="zoom-in" data-aos-delay="<?php echo rand(100, 300); ?>">
                            <div class="card package-card h-100 text-center shadow-sm" style="background-color: var(--dark-color); border: 1px solid #090800;">
                                <div class="card-body">
                                    <h5 class="card-title text-white"><?php echo htmlspecialchars($package['name']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-warning">
                                        $<?php echo number_format($package['price'], 2); ?> / <?php echo $package['duration_days']; ?> days
                                    </h6>
                                    <p class="card-text text-light">
                                        <?php echo htmlspecialchars($package['description']); ?><br>
                                        <small><?php echo htmlspecialchars($package['features']); ?></small>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="package.html" class="btn w-100 btn-outline-warning">Choose Plan</a>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center">No packages available at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
        <!-- How It Works Section -->
        <section class="py-5 text-white" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('img/holding-hand.jpg') center/cover fixed;">
            <div class="container">
                <h2 class="section-title">How It Works</h2>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100 border-0 shadow-sm bg-dark bg-opacity-50">
                            <div class="card-body text-center p-4">
                                <div class="display-1 text-primary mb-3">1</div>
                                <h3 class="card-title card-text1">Sign Up</h3>
                                <p class="card-text1">Create your free profile in just a few easy steps. Provide basic details like your name, age, religion, and preferences.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card h-100 border-0 shadow-sm bg-dark bg-opacity-50">
                            <div class="card-body text-center p-4">
                                <div class="display-1 text-primary mb-3">2</div>
                                <h3 class="card-title card-text1">Choose a Package</h3>
                                <p class="card-text1">Select a membership plan that suits your needs. Our flexible packages offer the right features to help you find your perfect match.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card h-100 border-0 shadow-sm bg-dark bg-opacity-50">
                            <div class="card-body text-center p-4">
                                <div class="display-1 text-primary mb-3">3</div>
                                <h3 class="card-title card-text1">Enjoy the Service</h3>
                                <p class="card-text1">Explore matches, send interests, and connect with genuine profiles. Our platform offers a smooth, secure matchmaking experience.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Happy Stories Section -->
        <section class="py-5 bg-white" id="happy-stories">
            <div class="container">
                <h2 class="section-title">Happy Stories</h2>
                <div class="row g-4">
                    <?php if ($stories_result && $stories_result->num_rows > 0): ?>
                        <?php 
                        $aos = ['fade-right', 'fade-left'];
                        $i = 0;
                        while ($row = $stories_result->fetch_assoc()): 
                        ?>
                        <div class="col-lg-6" data-aos="<?php echo $aos[$i % 2]; ?>">
                            <div class="card h-100 shadow-sm">
                                <div class="row g-0">
                                    <div class="col-md-5">
                                        <img src="<?php echo htmlspecialchars($row['author_photo'] ?: 'img/default.jpg'); ?>" 
                                             class="img-fluid rounded-start h-100 w-100 object-cover" 
                                             alt="Success Story">
                                    </div>
                                    <div class="col-md-7">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($row['author_name']); ?></h5>
                                            <p class="card-text">
                                                "<?php echo substr(strip_tags($row['content']), 0, 100); ?>..."
                                            </p>
                                            <a href="story.php?id=<?php echo $row['blog_id']; ?>" 
                                               class="btn btn-sm btn-primary">Read Full Story</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                        $i++;
                        endwhile; 
                        ?>
                    <?php else: ?>
                        <div class="col-12 text-center"><p>No success stories found.</p></div>
                    <?php endif; ?>
                    
                    <div class="col-12 text-center mt-4">
                        <a href="story.php" class="btn btn-primary px-4">View More Success Stories</a>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Testimonials Section -->
        <section class="py-5 bg-light" id="testimonials">
            <div class="container">
                <h2 class="section-title">What Our Clients Say</h2>
                <div class="scrolling-wrapper">
                    <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
                        <?php while ($review = $reviews_result->fetch_assoc()): ?>
                            <div class="testimonial-card">
                                <img src="uploads/<?php echo htmlspecialchars($review['photo']); ?>" class="rounded-circle mb-3" style="width:100px;height:100px;object-fit:cover;" alt="<?php echo htmlspecialchars($review['name']); ?>">
                                <p class="fst-italic">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                                <h5 class="mt-3 fw-semibold"><?php echo htmlspecialchars($review['name']); ?></h5>
                                <small class="text-muted"><?php echo htmlspecialchars($review['profession']); ?>, <?php echo htmlspecialchars($review['country']); ?></small>
                                <div class="text-warning mt-2">
                                    <?php for ($i=1;$i<=5;$i++){echo $i<=$review['rating']?"★":"☆";} ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="testimonial-card">
                            <p>No reviews available at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
    
    <!-- Footer Section -->
    <footer class="bg-dark text-white pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">TheanNilavu Matrimony</h5>
                    <p>Connecting hearts, building relationships since 2023.</p>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="home.html" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="members.html" class="text-white text-decoration-none">Membership</a></li>
                        <li><a href="package.html" class="text-white text-decoration-none">Packages</a></li>
                        <li><a href="contact.html" class="text-white text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Resources</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Success Stories</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Blog</a></li>
                        <li><a href="#" class="text-white text-decoration-none">FAQ</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Safety Tips</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Contact Us</h5>
                    <p><i class="fas fa-envelope me-2"></i> info@theannilavu.com</p>
                    <p><i class="fas fa-phone me-2"></i> +94 77 123 4567</p>
                    <p><i class="fas fa-map-marker-alt me-2"></i> Colombo, Sri Lanka</p>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <p class="mb-0">&copy; 2025 TheanNilavu Matrimony. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end mb-3">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });
        
        // Heart animation
        document.addEventListener("mousemove", function(e) {
            if (Math.random() > 0.98) { // Reduce frequency for better performance
                const heart = document.createElement("div");
                heart.className = "heart";
                heart.innerHTML = "❤️";
                
                const driftX = Math.random() * 40 - 20;
                const scale = 1 + Math.random();
                const duration = 2 + Math.random() * 2;
                
                heart.style.left = e.pageX + "px";
                heart.style.top = e.pageY + "px";
                heart.style.transform = `translate(0, 0) scale(${scale})`;
                heart.style.animationDuration = `${duration}s`;
                
                document.body.appendChild(heart);
                
                setTimeout(() => {
                    heart.remove();
                }, duration * 1000);
            }
        });
        
        // Auto-scroll testimonials
        function autoScrollTestimonials() {
            const container = document.querySelector('.scrolling-wrapper');
            let scrollAmount = 0;
            const scrollSpeed = 1;
            
            setInterval(() => {
                scrollAmount += scrollSpeed;
                if (scrollAmount >= container.scrollWidth / 2) {
                    scrollAmount = 0;
                }
                container.scrollLeft = scrollAmount;
            }, 30);
        }
        
        // Start auto-scroll when page loads
        window.addEventListener('load', autoScrollTestimonials);
    </script>
</body>
</html>
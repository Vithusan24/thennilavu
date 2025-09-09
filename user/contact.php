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

// Fetch latest reviews
$sql = "SELECT name, profession, country, comment, rating, photo 
        FROM reviews 
        ORDER BY review_date DESC 
        LIMIT 10";
$result = $conn->query($sql);





// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = $conn->real_escape_string($_POST['name']);
    $email   = $conn->real_escape_string($_POST['email']);
    $subject = $conn->real_escape_string($_POST['subject']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO messages (sender_name, sender_email, subject, message_text) 
            VALUES ('$name', '$email', '$subject', '$message')";

    if ($conn->query($sql) === TRUE) {
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members</title>
    <link rel="stylesheet" href="bootstrap.css">
    <style>
        body {
      font-family: 'Segoe UI', sans-serif;
    }
    .contact-info i {
      color: #ff4d6d;
      margin-right: 10px;
    }
    .form-section {
      background-color: #f8f9fa;
      padding: 60px 0;
    }
    .mapouter {
      position: relative;
      text-align: right;
      height: 250px;
      width: 100%;
    }
    .gmap_canvas {
      overflow: hidden;
      background: none !important;
      height: 250px;
      width: 100%;
    }


      .btn a{
          text-decoration: none;
          color: black;
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
    
.nav-link:hover {
  color: #ffe0f0 !important;
  text-decoration: underline;
}

.navbar-brand:hover {
  color: #fff0f5 !important;
}


/* .faq-list {
    max-width: 800px;
    margin: auto;
  }

  .faq-question {
    width: 100%;
    text-align: left;
    background: #f8f9fa;
    border: none;
    padding: 15px 20px;
    font-size: 18px;
    font-weight: bold;
    position: relative;
    cursor: pointer;
    transition: background 0.3s;
    border-radius: 6px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .faq-question:hover {
    background: #e0f2f1;
  }

  .faq-question .arrow {
    transition: transform 0.3s ease;
    font-size: 20px;
  }

  .faq-question.active .arrow {
    transform: rotate(180deg);
  }

  .faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease, opacity 0.4s ease;
    background: #f1f1f1;
    padding: 0 20px;
    border-radius: 0 0 6px 6px;
    opacity: 0;
    margin-top: -10px;
    margin-bottom: 15px;
  }

  .faq-answer.open {
    padding: 15px 20px;
    max-height: 300px;
    opacity: 1;
  } */



  .pic{
    width: 500px;
    height: 600px;
    background-size: 100% 100%;
    border-radius: 10px;
    box-shadow: 0px 20px 20px 10px rgba(0, 0, 0, 0.088);
    animation: animate 20s linear infinite;

  }
  @keyframes animate {
    0% {
      background-image: url('img/R.jpg');
    }
    25% {
      background-image: url('img/d81b6668b4cd875545b963b9d8541773.jpg');
    }
    50% {
      background-image: url('img/355a26bc03afc2fe5f425ddbc79b4180.jpg');
    }
    75% {
      background-image: url('img/1c282bd5c77d49d7bae64ed7a01ff0ab.jpg');
    }
    100% {
      background-image: url('img/d4ab9fa85e1530032ebcd2afa6a17e44.jpg');
    }
  }

  @media (max-width: 768px) {
    .pic {
      width: 100%;
      height: 400px;
    }
  }

  </style>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS (required for toggler to work) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>
    <div class="container-fluid bg-black bg-opacity-75 ">
      <!-- Add this to head for Bootstrap (if not already included) -->

<!-- Fixed top navbar -->
<nav class="navbar fixed-top" style="background-color: rgb(127, 8, 8);">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img src="logo.png" alt="" width="30" height="30" class="d-inline-block align-text-top">
    </a>
    <span style="color: aliceblue;">sample@gmail.com</span>

    <div class="ms-auto d-flex gap-2">
      <button class="btn bg-primary" style=" align-items: center;"> <a class="nav-link text-white fw-semibold me-3" style="margin-left: 15px; text-decoration: none;"  href="dashboard.html">DashBoard</a></button>
      <button class="btn bg-secondary" style=" text-align: center;"> <a class="nav-link text-white fw-semibold me-3" style="margin-left: 15px; text-decoration: none;" href="">Log Out</a></button>
      
    </div>
  </div>
</nav>
<br><br><br>

<!-- Optional content spacing for fixed nav -->
<div style="height: 80px;"> 

<!-- Navbar -->
<nav class="navbar navbar-expand-lg shadow-sm " style="background: linear-gradient(to right, #ec3107, #fda101); border-radius: 10px;">
  <div class="container-fluid">
    <!-- Logo -->
    <a class="navbar-brand text-white fw-bold" href="#">
      <img src="logo.png" alt="" width="30" height="30" class="d-inline-block align-text-top me-2">
      TheanNilavu
    </a>

    <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
      aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navigation Links -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-link  text-white fw-semibold me-3" href="home.php">Home</a>
        <a class="nav-link  text-white fw-semibold me-3"  href="members.php">member Ship</a>
         <a class="nav-link  text-white fw-semibold me-3"  href="mem.php">Members</a>
        <a class="nav-link text-white fw-semibold me-3" href="package.php">Packages</a>
        <a class="nav-link text-white fw-semibold me-3" style="text-decoration: underline;" href="contact.php">Contact Us</a>
        <a class="nav-link text-white fw-semibold" href="story.php">Stories</a>
      </div>
    </div>
  </div>
</nav>
</div>

<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-4">Contact Information</h2>
    <div class="row gy-4">
      <!-- Google Map -->
      <div class="col-md-6">
        <div class="mapouter">
          <div class="gmap_canvas">
            <iframe width="100%" height="250" id="gmap_canvas" src="https://maps.google.com/maps?q=colombo&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no"></iframe>
          </div>
        </div>
      </div>
      <!-- Contact Details -->
      <div class="col-md-6">
        <p><strong>About Us:</strong> TheanNilavu Matrimony is a trusted and inclusive matrimonial platform designed to help individuals find their life partners with honesty, respect, and safety. While our roots are inspired by Tamil culture and traditions, we proudly welcome people from all backgrounds, communities, and cultures. This is not just a Tamil-only site — it’s a place for everyone seeking meaningful relationships.</p>
        <div class="contact-info mt-3">
          <p><i class="bi bi-geo-alt-fill"></i> Address: No.123, Main Street, Colombo, Sri Lanka</p>
          <p><i class="bi bi-envelope-fill"></i> Email: info@theannilavu.com</p>
          <p><i class="bi bi-telephone-fill"></i> Phone: +94 77 123 4567</p>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Section 2: Contact Form -->
<section class="form-section" style="background: linear-gradient(to right, #ec3107, #fda101);">
  <div class="container">
    <h2 class="text-center mb-5">Get in Touch with Us</h2>
    <div class="row align-items-center">
      <!-- Contact Form -->
      <div class="col-md-6">
        <form action="" method="POST" style="border: black; border-radius: 10px; padding: 20px;">
          <div class="mb-3">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter your name">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
          </div>
          <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" required placeholder="Subject">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="4" required placeholder="Type your message..."></textarea>
          </div>
          <button type="submit" class="btn btn-dark">Submit</button>
        </form>
      </div>

      <!-- Right-side Image -->
      <div class="col-md-6 text-center">
        <div class="pic border border-2 border-dark rounded">
          <!-- You can add any image here -->
        </div>
      </div>
    </div>
  </div>
</section>



<!-- <section class="py-5 bg-white">
  <div class="container">
    <h2 class="text-center mb-4">Frequently Asked Questions</h2>
    <p class="text-center mb-5">Here are some answers to the most common questions about our services and membership process.</p>

    <div class="faq-list">

      <div class="faq-item">
        <button class="faq-question">1. How do I create a profile?<span class="arrow">▼</span></button>
        <div class="faq-answer">
          You can create a profile by filling in the registration form with your personal, educational, and family details. After submitting, your profile will be reviewed and published.
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">2. Is my data safe and private?<span class="arrow">▼</span></button>
        <div class="faq-answer">
          Yes, we take your privacy seriously. Your personal information is secured and only accessible by verified users.
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">3. How do I upgrade to a premium plan?<span class="arrow">▼</span></button>
        <div class="faq-answer">
          You can upgrade any time by visiting the “Our Packages” section and selecting the desired plan. Payment can be made via card, bank transfer, or mobile payment.
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-question">4. Can I contact other members directly?<span class="arrow">▼</span></button>
        <div class="faq-answer">
          Contacting other members depends on your membership level. Premium and Elite members have full access to messaging features.
        </div>
      </div>

    </div>
  </div>
</section> -->

<!-- <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> -->

<!-- <section class="py-5 bg-light text-center" id="how-it-works" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('img/holding-hand.jpg'); 
            background-size: cover; 
            background-position: center;">
  <div class="container">
    <h2 class="mb-4 fw-bold" data-aos="fade-down" style="color: #f2f2f2;">How It Works</h2>
    <div class="row g-4">
      
     
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body">
            <h3 class="card-title">1. Sign Up</h3>
            <p class="card-text">Create your free profile in just a few easy steps. Provide basic details like your name, age, religion, and preferences. It only takes a minute to get started on your journey to finding the right life partner.</p>
          </div>
        </div>
      </div>

    
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body">
            <h3 class="card-title">2. Choose a Package</h3>
            <p class="card-text">Select a membership plan that suits your needs. Whether you're just exploring or ready to connect seriously, our flexible packages offer the right features to help you find your perfect match.</p>
          </div>
        </div>
      </div>

     
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
        <div class="card h-100 border-0 shadow-sm">
          <div class="card-body">
            <h3 class="card-title">3. Enjoy the Service</h3>
            <p class="card-text">Once you're set up, explore matches, send interests, and connect with genuine profiles. Our platform is designed to offer a smooth, secure, and meaningful matchmaking experience every step of the way.</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</section> -->




<!-- Testimonial Section -->
<section class="py-5 bg-light text-center" id="testimonials">
  <div class="container">
    <h2 class="fw-bold mb-4">What Our Clients Say</h2>

    <div class="scrolling-wrapper px-2" id="autoScroll">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="testimonial-card text-center">
          <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" 
               class="rounded-circle mb-3" 
               style="width:100px;height:100px;object-fit:cover;" 
               alt="<?php echo htmlspecialchars($row['name']); ?>">

          <p class="fst-italic">
            "<?php echo htmlspecialchars($row['comment']); ?>"
          </p>
          <h5 class="mt-3 fw-semibold">
            <?php echo htmlspecialchars($row['name']); ?>
          </h5>
          <small class="text-muted">
            <?php echo htmlspecialchars($row['profession']); ?>, <?php echo htmlspecialchars($row['country']); ?>
          </small>
          <div class="text-warning mt-2">
            <?php
              // Display stars
              for ($i = 1; $i <= 5; $i++) {
                  echo $i <= $row['rating'] ? "★" : "☆";
              }
            ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>






<footer class="bg-dark text-white pt-4 pb-2 mt-5">
  <div class="container">
    <div class="row">

      <div class="col-md-3 mb-3">
        <img src="logo.png" alt="" width="30" height="30" class="d-inline-block align-text-top">
      </div>

      <!-- About -->
      <div class="col-md-3 mb-3">
        <h5>About Us</h5>
        <p>We provide top-notch services to help you achieve your goals with ease and satisfaction.</p>
      </div>

      <!-- Quick Links -->
      <div class="col-md-3 mb-3">
        <h5>Quick Links</h5>
        <ul class="list-unstyled">
          <li><a href="#" class="text-white text-decoration-none">Home</a></li>
          <li><a href="#" class="text-white text-decoration-none">Services</a></li>
          <li><a href="#" class="text-white text-decoration-none">Contact</a></li>
          <li><a href="#" class="text-white text-decoration-none">Login</a></li>
        </ul>
      </div>

      <!-- Contact Info -->
      <div class="col-md-3 mb-3">
        <h5>Contact</h5>
        <p>Email: info@example.com</p>
        <p>Phone: +94 77 123 4567</p>
        <p>Address: Colombo, Sri Lanka</p>
      </div>

    </div>

    <!-- Bottom copyright -->
    <div class="text-center pt-3 border-top mt-3">
      <small>&copy; 2025 YourCompany. All rights reserved.</small>
    </div>
  </div>
</footer>









<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
  
  document.addEventListener("mousemove", function(e) {
  const heart = document.createElement("div");
  heart.className = "heart";
  heart.innerHTML = "❤️";

  // Random left/right drift
  const driftX = Math.random() * 40 - 20; // -20 to +20px
  const scale = 1 + Math.random(); // Scale 1–2
  const duration = 2 + Math.random() * 2; // 2–4s duration

  heart.style.left = e.pageX + "px";
  heart.style.top = e.pageY + "px";
  heart.style.transform = `translate(0, 0) scale(${scale})`;
  heart.style.animationDuration = `${duration}s`;

  document.body.appendChild(heart);

  setTimeout(() => {
    heart.remove();
  }, duration * 1000);
});



const questions = document.querySelectorAll('.faq-question');

  questions.forEach((btn) => {
    btn.addEventListener('click', () => {
      const answer = btn.nextElementSibling;
      const isOpen = answer.classList.contains('open');

      document.querySelectorAll('.faq-answer').forEach((a) => {
        a.classList.remove('open');
      });

      document.querySelectorAll('.faq-question').forEach((q) => {
        q.classList.remove('active');
      });

      if (!isOpen) {
        answer.classList.add('open');
        btn.classList.add('active');
      }
    });
  });
</script>
 

<script>
  function animateCounter(counter) {
    const target = +counter.getAttribute('data-count');
    let count = 0;
    const speed = 20;
    const increment = Math.ceil(target / 100);

    const updateCount = () => {
      count += increment;
      if (count >= target) {
        counter.innerText = target.toLocaleString();
      } else {
        counter.innerText = count.toLocaleString();
        requestAnimationFrame(updateCount);
      }
    };
    updateCount();
  }

  function initCountersOnScroll() {
    const counters = document.querySelectorAll('.counter');
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.7 });

    counters.forEach(counter => observer.observe(counter));
  }

  document.addEventListener('DOMContentLoaded', initCountersOnScroll);
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
  AOS.init({
    duration: 800,
    once: true
  });
</script> 

</body>
</html>

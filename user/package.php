<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package</title>
    <link rel="stylesheet" href="bootstrap.css">
    <style>
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
    
    
  .faq-list {
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







</style>
 
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

<!-- Optional content spacing for fixed nav -->
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
        <a class="nav-link  text-white fw-semibold me-3"  href="members.php">Member Ship</a>
         <a class="nav-link  text-white fw-semibold me-3"  href="mem.php">Members</a>
        <a class="nav-link text-white fw-semibold me-3" style="text-decoration: underline;" href="package.php">Packages</a>
        <a class="nav-link text-white fw-semibold me-3" href="contact.php">Contact Us</a>
        <a class="nav-link text-white fw-semibold" href="story.php">Stories</a>
      </div>
    </div>
  </div>
</nav>
</div>


<!-- ✅ AOS CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />

<section class="py-5" style="background: linear-gradient(to right, #ec3107, #fda101);">
  <div class="container">
    <h2 class="text-center mb-3" data-aos="fade-down" style="color: #fdfdfd;">Our Packages</h2>
    <p class="text-center mb-5" data-aos="fade-up" style="color: #f2f2f2;">
      Choose from our affordable and flexible membership packages tailored to meet your preferences and requirements.
    </p>
    
    <div class="row g-4" id="packagesRoot">
      <!-- Packages will be injected here -->
    </div>
  </div>
</section>

<!-- ✅ AOS JS -->

<section class="py-5 bg-white">
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
</section>

<section class="py-5 bg-white" id="about-matrimony">
  <div class="container">
    <h2 class="text-center mb-4" data-aos="fade-down">About Our Matrimony</h2>
    <p class="text-center mb-5" data-aos="fade-up">
      Trusted by thousands, we are committed to connecting hearts and building meaningful relationships.
    </p>

    <div class="row text-center g-4">
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
        <div class="counter-box">
          <h2 class="counter" data-count="25000">0</h2>
          <p>Total Members</p>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
        <div class="counter-box">
          <h2 class="counter" data-count="8700">0</h2>
          <p>Success Stories</p>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
        <div class="counter-box">
          <h2 class="counter" data-count="1200">0</h2>
          <p>Premium Members</p>
        </div>
      </div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
        <div class="counter-box">
          <h2 class="counter" data-count="10">0</h2>
          <p>Years of Service</p>
        </div>
      </div>
    </div>
  </div>
</section>






<!-- Footer Section -->
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
          <li><a href="home.php" class="text-white text-decoration-none">Home</a></li>
          <li><a href="" class="text-white text-decoration-none">Services</a></li>
          <li><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>
          <li><a href="login.html" class="text-white text-decoration-none">Login</a></li>
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



<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
  const PKG_API = 'api_packages.php';
  const packagesRoot = document.getElementById('packagesRoot');

  function pkgCard(pkg, delay){
    const col = document.createElement('div');
    col.className = 'col-md-4';
    col.setAttribute('data-aos', 'zoom-in');
    col.setAttribute('data-aos-delay', String(delay));
    col.innerHTML = `
      <div class="card package-card h-100 text-center shadow-sm" style="background-color: #4a3d3d; border: 1px solid #090800;">
        <div class="card-body">
          <h5 class="card-title text-white">${pkg.name}</h5>
          <h6 class="card-subtitle mb-2" style="color:#fdfdfd;">$${Number(pkg.price).toFixed(2)} / ${pkg.duration_days} days</h6>
          <p class="card-text text-light">${pkg.description || ''}</p>
          <div class="text-light small">${pkg.features || ''}</div>
          <div class="mt-2"><span class="badge bg-${pkg.status==='active'?'success':'secondary'} text-uppercase">${pkg.status}</span></div>
        </div>
        <div class="card-footer bg-transparent">
          <a href="#" class="btn w-100 btn-outline-warning">Choose</a>
        </div>
      </div>`;
    return col;
  }

  async function loadPackages(status='active'){
    packagesRoot.innerHTML = '<div class="text-center text-white">Loading...</div>';
    const res = await fetch(`${PKG_API}?status=${encodeURIComponent(status)}`);
    const json = await res.json();
    const list = json.data || [];
    packagesRoot.innerHTML = '';
    if (!list.length){
      packagesRoot.innerHTML = '<div class="text-center text-white">No packages</div>';
      return;
    }
    list.forEach((p, i) => packagesRoot.appendChild(pkgCard(p, 100 * ((i%3)+1))));
    if (window.AOS) { AOS.refresh(); }
  }

  document.addEventListener('DOMContentLoaded', ()=> loadPackages(''));
  AOS.init({
    duration: 800,
    once: true
  });
</script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init();</script>

<!-- ✅ Counter JS -->
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
</body>
</html>
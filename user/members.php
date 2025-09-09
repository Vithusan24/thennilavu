<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'matrimony');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Member Details form
    if (isset($_POST['name'])) {
        $name = $_POST['name'] ?? '';
        $looking_for = $_POST['looking_for'] ?? '';
        $dob = $_POST['dob'] ?? '';
        $religion = $_POST['religion'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $marital_status = $_POST['marital_status'] ?? '';
        $language = $_POST['language'] ?? '';
        $profession = $_POST['profession'] ?? '';
        $country = $_POST['country'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $smoking = $_POST['smoking'] ?? '';
        $drinking = $_POST['drinking'] ?? '';
        $present_address = $_POST['present_address'] ?? '';
        $city = $_POST['city'] ?? '';
        $zip = $_POST['zip'] ?? '';
        $permanent_address = $_POST['permanent_address'] ?? '';
        $permanent_city = $_POST['permanent_city'] ?? '';

        $photo = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $photo = 'uploads/' . time() . '_' . $_FILES['photo']['name'];
            move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
        }

        $stmt = $conn->prepare("INSERT INTO members 
            (name, photo, looking_for, dob, religion, gender, marital_status, language, profession, country, phone, smoking, drinking, present_address, city, zip, permanent_address, permanent_city) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param(
            "ssssssssssssssssss",
            $name,
            $photo,
            $looking_for,
            $dob,
            $religion,
            $gender,
            $marital_status,
            $language,
            $profession,
            $country,
            $phone,
            $smoking,
            $drinking,
            $present_address,
            $city,
            $zip,
            $permanent_address,
            $permanent_city
        );
        $stmt->execute();
        $_SESSION['member_id'] = $stmt->insert_id;
        echo "<div class='alert alert-success' role='alert'>Member saved. Continue next steps.</div>";
    }

    // 2) Physical Info form
    if (isset($_POST['complexion']) && isset($_SESSION['member_id'])) {
        $member_id = (int)$_SESSION['member_id'];
        $complexion = $_POST['complexion'] ?? null;
        $height = $_POST['height'] ?? null;
        $weight = $_POST['weight'] ?? null;
        $blood_group = $_POST['blood_group'] ?? null;
        $eye_color = $_POST['eye_color'] ?? null;
        $hair_color = $_POST['hair_color'] ?? null;
        $disability = $_POST['disability'] ?? null;

        $stmt2 = $conn->prepare("INSERT INTO physical_info (member_id, complexion, height_cm, weight_kg, blood_group, eye_color, hair_color, disability) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt2) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt2->bind_param("isddssss", $member_id, $complexion, $height, $weight, $blood_group, $eye_color, $hair_color, $disability);
        $stmt2->execute();
        echo "<div class='alert alert-success' role='alert'>Physical info saved.</div>";
    }

    // 3) Education form
    if (!empty($_POST['institute']) && is_array($_POST['institute']) && isset($_SESSION['member_id'])) {
        $member_id = (int)$_SESSION['member_id'];
        for ($i = 0; $i < count($_POST['institute']); $i++) {
            $institute = $_POST['institute'][$i] ?? '';
            $degree = $_POST['degree'][$i] ?? '';
            $field = $_POST['field'][$i] ?? '';
            $regnum = $_POST['regnum'][$i] ?? '';
            $startyear = (int)($_POST['startyear'][$i] ?? 0);
            $endyear = (int)($_POST['endyear'][$i] ?? 0);

            $stmt3 = $conn->prepare("INSERT INTO education (member_id, level, school_or_institute, stream_or_degree, field, reg_number, start_year, end_year) VALUES (?, 'Higher', ?, ?, ?, ?, ?, ?)");
            if (!$stmt3) {
                die('Prepare failed: ' . $conn->error);
            }
            $stmt3->bind_param("issssii", $member_id, $institute, $degree, $field, $regnum, $startyear, $endyear);
            $stmt3->execute();
        }
        echo "<div class='alert alert-success' role='alert'>Education saved.</div>";
    }

    // 4) Family form
    if (isset($_POST['father_name']) && isset($_SESSION['member_id'])) {
        $member_id = (int)$_SESSION['member_id'];
        $father_name = $_POST['father_name'] ?? '';
        $father_profession = $_POST['father_profession'] ?? '';
        $father_contact = $_POST['father_contact'] ?? '';
        $mother_name = $_POST['mother_name'] ?? '';
        $mother_profession = $_POST['mother_profession'] ?? '';
        $mother_contact = $_POST['mother_contact'] ?? '';
        $brothers = (int)($_POST['brothers'] ?? 0);
        $sisters = (int)($_POST['sisters'] ?? 0);

        $stmt4 = $conn->prepare("INSERT INTO family (member_id, father_name, father_profession, father_contact, mother_name, mother_profession, mother_contact, brothers_count, sisters_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt4) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt4->bind_param("issssssii", $member_id, $father_name, $father_profession, $father_contact, $mother_name, $mother_profession, $mother_contact, $brothers, $sisters);
        $stmt4->execute();
        echo "<div class='alert alert-success' role='alert'>Family info saved.</div>";
    }

    // 5) Partner Expectations form
    if (isset($_POST['partner_country']) && isset($_SESSION['member_id'])) {
        $member_id = (int)$_SESSION['member_id'];
        $partner_country = $_POST['partner_country'] ?? '';
        $min_age = (int)($_POST['min_age'] ?? 0);
        $max_age = (int)($_POST['max_age'] ?? 0);
        $min_height = (int)($_POST['min_height'] ?? 0);
        $max_height = (int)($_POST['max_height'] ?? 0);
        $partner_marital_status = $_POST['partner_marital_status'] ?? '';
        $partner_religion = $_POST['partner_religion'] ?? '';
        $partner_smoking = $_POST['partner_smoking'] ?? '';
        $partner_drinking = $_POST['partner_drinking'] ?? '';

        $stmt5 = $conn->prepare("INSERT INTO partner_expectations (member_id, preferred_country, min_age, max_age, min_height, max_height, marital_status, religion, smoking, drinking) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt5) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt5->bind_param("isiiiissss", $member_id, $partner_country, $min_age, $max_age, $min_height, $max_height, $partner_marital_status, $partner_religion, $partner_smoking, $partner_drinking);
        $stmt5->execute();
        echo "<div class='alert alert-success' role='alert'>Partner expectations saved.</div>";
    }

    // 6) Horoscope form
    if (isset($_POST['birth_date']) && isset($_SESSION['member_id'])) {
        $member_id = (int)$_SESSION['member_id'];
        $birth_date = $_POST['birth_date'] ?? '';
        $birth_time = $_POST['birth_time'] ?? '';
        $zodiac = $_POST['zodiac'] ?? '';
        $nakshatra = $_POST['nakshatra'] ?? '';
        $karmic_debt = $_POST['karmic_debt'] ?? '';

        $planet_img = '';
        if (isset($_FILES['planet_image']) && $_FILES['planet_image']['error'] === 0) {
            $planet_img = 'uploads/' . time() . '_' . $_FILES['planet_image']['name'];
            move_uploaded_file($_FILES['planet_image']['tmp_name'], $planet_img);
        }

        $navamsha_img = '';
        if (isset($_FILES['navamsha_image']) && $_FILES['navamsha_image']['error'] === 0) {
            $navamsha_img = 'uploads/' . time() . '_' . $_FILES['navamsha_image']['name'];
            move_uploaded_file($_FILES['navamsha_image']['tmp_name'], $navamsha_img);
        }

        $stmt6 = $conn->prepare("INSERT INTO horoscope (member_id, birth_date, birth_time, zodiac, nakshatra, karmic_debt, planet_image, navamsha_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt6) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt6->bind_param("isssssss", $member_id, $birth_date, $birth_time, $zodiac, $nakshatra, $karmic_debt, $planet_img, $navamsha_img);
        $stmt6->execute();
        echo "<div class='alert alert-success' role='alert'>Horoscope saved. Registration complete.</div>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Ship</title>
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
    
.nav-link:hover {
  color: #ffe0f0 !important;
  text-decoration: underline;
}

.navbar-brand:hover {
  color: #fff0f5 !important;
}

/* img */

 .pic{
    height: 250px;
    width: 200px;
  }
  .car{
    width: 200px;
    height: 250px;
    position: relative;
    transform-style: preserve-3d;
    transform: perspective(50px);
    animation: gallery 50s  linear infinite;
    cursor: pointer;
  }
  .car span{
    position: absolute;
    width: 200px;
    height: 250px;
    transform-style: preserve-3d;
    transform: rotateY(calc(var(--i)*45deg)) translateZ(350px);
   -webkit-box-reflect: below 3.5px linear-gradient(transparent, transparent, rgba(3, 3, 3, 0.2));
  }
  .car span img{
    position: absolute;
    border: 6px ridge;
    border-radius: 10px;
    
  }
  @keyframes gallery {
    0%{
      transform: perspective(1000px) rotateY(0deg);
    }
    100%{
      transform: perspective(1000px) rotateY(-360deg);
    }
  }





  @media (max-width: 768px) {
    .col-12{
      margin-left: 0 !important;
      
    }
  .col-6 {
    width: 100% !important;
    flex: 0 0 100%;
    max-width: 100%;
    margin-bottom: 1.5rem;
    margin-left: 8% !important;
  }

  .col-10 {
    margin-left: 0 !important;
    align-items: center;
    justify-content: center;

  }

  section {
    margin-top: 1rem !important;
    padding: 1.5rem 1rem !important;
    width: 100% !important;
  }

  h2.text-center {
    font-size: 1.5rem;
  }

  .form-control, .form-select {
    font-size: 0.9rem;
  }

  .btn {
    font-size: 0.95rem;
  }

  .story-img {
    height: auto !important;
    width: 100% !important;
    margin-top: 1rem;
  }

  .d-flex.justify-content-between,
  .text-end {
    flex-direction: column;
    gap: 0.75rem;
  }

  .d-flex.justify-content-between .btn,
  .text-end .btn {
    width: 100%;
  }


  .col-lg-10 {
    margin-left: 0 !important;
    width: 100% !important;
  }


  .col-md-6 {
    width: 100% !important;
    flex: 0 0 100%;
    max-width: 100%;
  }
  .pic {
    width: 130px;
    height: 170px;
  }

  .car {
    width: 130px;
    height: 170px;
    animation: gallery 40s linear infinite;
  }

  .car span {
    width: 130px;
    height: 170px;
    transform: rotateY(calc(var(--i) * 45deg)) translateZ(220px); /* Closer Z */
  }

  .car span img {
    border: 4px ridge;
    border-radius: 8px;
  }
  .col-12.justify-content-center {
    padding: 2rem 0;
  }
}

   
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap JS (required for toggler to work) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body >
    <div class="container-fluid bg-light bg-opacity-75 ">
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
        <a class="nav-link  text-white fw-semibold me-3"  style="text-decoration: underline;" href="members.php">Member Ship</a>
        <a class="nav-link  text-white fw-semibold me-3"  href="mem.php">Members</a>
        <a class="nav-link text-white fw-semibold me-3" href="package.php">Packages</a>
        <a class="nav-link text-white fw-semibold me-3" href="contact.php">Contact Us</a>
        <a class="nav-link text-white fw-semibold me-3" href="story.php">Stories</a>
      </div>
    </div>
  </div>
</nav>
</div>


<div class="col-12  " >
  <div class="row" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('img/holding-hand.jpg'); 
            background-size: cover; 
            background-position: center;">
    <div class="col-6">
      <div class="col-10" style="margin-left: 17%; border: black;">
<section class="py-5 bg-light bg-opacity-25 border-dark mt-5 " style="border-radius: 10px; color: rgb(0, 13, 13); border: black;">
  <div class="container g-5">
    <h2 class="mb-4 text-center">Member Details Form</h2>
    <form method="POST" action="" enctype="multipart/form-data">

      <div class="row mb-3">

        <div class="col-md-6">
          <label class="form-label">Name</label>
          <input type="text" class="form-control" name="name" placeholder="name" required>
        </div>
        <div class="col-md-6">
           <label class="form-label">Upload Your Photo</label>
                <input type="file" class="form-control" name="photo" accept="image/*" required>
                <small class="text-muted">Supported formats: JPG, PNG. Max size: 2MB.</small>
       </div>
        <div class="col-md-6">
          <label class="form-label">Looking For</label>
          <select class="form-select" name="looking_for" required>
            <option value="">Select option</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Date of Birth</label>
          <input type="date" class="form-control" name="dob" required>
        </div>
      </div>
      


      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Religion</label>
          <input type="text" class="form-control" name="religion" placeholder="Eg: Hindu, Christian" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Gender</label>
          <select class="form-select" name="gender" required>
            <option value="">Select option</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Marital Status</label>
          <select class="form-select" name="marital_status" required>
            <option value="">Select option</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Divorced">Divorced</option>
            <option value="Widowed">Widowed</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Language</label>
          <input type="text" class="form-control" name="language" placeholder="Eg: Tamil, English " required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Profession</label>
          <input type="text" class="form-control" name="profession" placeholder="Eg: Teacher, Engineer" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Country</label>
          <input type="text" class="form-control" name="country" placeholder="Eg: Sri Lanka" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <input type="tel" class="form-control" name="phone" placeholder="Eg: +94 7X XXX XXXX" required>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Smoking Habit</label>
          <select class="form-select" name="smoking">
            <option value="No">No</option>
            <option value="Yes">Yes</option>
            <option value="Occasionally">Occasionally</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Drinking Habit</label>
          <select class="form-select" name="drinking">
            <option value="No">No</option>
            <option value="Yes">Yes</option>
            <option value="Occasionally">Occasionally</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Present Address</label>
        <textarea class="form-control" name="present_address" rows="2" placeholder="Enter full present address" ></textarea>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">City</label>
          <input type="text" class="form-control" name="city" placeholder="Eg: Jaffna" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Zip Code</label>
          <input type="text" class="form-control" name="zip" placeholder="Eg: 40000" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Permanent Address</label>
        <textarea class="form-control" name="permanent_address" rows="2" placeholder="Enter full permanent address"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Permanent City</label>
        <input type="text" class="form-control" name="permanent_city" placeholder="Eg: Batticaloa">
      </div>

      <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
  </div>
</section>
</div>
    </div>


<div class="col-6">
  <div class="col-10">
<!-- Physical Info Section -->
<section id="physical-info " class="py-5 bg-white bg-opacity-25 border-dark mt-5" style="border-radius: 10px; color: rgb(21, 0, 0);">
  <div class="container g-5">
    <h2 class="mb-4 text-center">Physical Information</h2>
    <form method="POST" action="">
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Complexion</label>
          <select class="form-select" name="complexion">
            <option value="">Select option</option>
            <option value="Fair">Fair</option>
            <option value="Wheatish">Wheatish</option>
            <option value="Dark">Dark</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Height (cm)</label>
          <input type="number" class="form-control" name="height" placeholder="Eg: 170">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Weight (kg)</label>
          <input type="number" class="form-control" name="weight" placeholder="Eg: 65">
        </div>
        <div class="col-md-6">
          <label class="form-label">Blood Group</label>
          <select class="form-select" name="blood_group">
            <option value="">Select option</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Eye Color</label>
          <input type="text" class="form-control" name="eye_color" placeholder="Eg: Brown">
        </div>
        <div class="col-md-6">
          <label class="form-label">Hair Color</label>
          <input type="text" class="form-control" name="hair_color" placeholder="Eg: Black">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Any Disability</label>
        <select class="form-select" name="disability">
          <option value="No">No</option>
          <option value="Yes">Yes</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary w-100">Submit</button>
     
    </form>
      <!-- other fields as earlier... -->
 <br>
      <div class="text-end">
        <button type="button" class="btn btn-primary" onclick="showSection('education-section', 'physical-info')">Next</button>
      </div>
  </div>
  <br>
  <img src="img/1c282bd5c77d49d7bae64ed7a01ff0ab.jpg" class="img-fluid rounded shadow-sm story-img" style="height: 590px; width: 100%;" alt="">
</section>
</div>
</div>

<br>
<!-- Education Info Section -->
 <div class="col-12 mt-4">
  
  <div class="col-lg-10 " style="margin-left: 8.5%;">
<section id="education-section" class="py-5 bg-light bg-opacity-25 border-dark" style="display: none; border-radius: 10px; color: azure;">
  <div class="container g-5">
    <h2 class="mb-4 text-center">Educational Information</h2>
    <form method="POST" action="">
      <!-- A/L or O/L Part -->
      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">A/L or O/L</label>
          <select class="form-select" name="al_ol">
            <option value="A/L">A/L</option><option value="O/L">O/L</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">School</label>
          <input type="text" class="form-control" name="school" placeholder="School">
        </div>
        <div class="col-md-4">
          <label class="form-label">Stream</label>
          <input type="text" class="form-control" name="stream" placeholder="stream">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Year</label>
          <input type="number" class="form-control" name="year" placeholder="Eg: 2020">
        </div>
        <div class="col-md-6">
          <label class="form-label">Result</label>
          <input type="text" class="form-control" name="result" placeholder="Eg: A, B, C">
        </div>
      </div>

      <!-- Higher Education Repeating Section -->
      <div id="higher-edu-container">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Institute</label>
            <input type="text" class="form-control" name="institute[]" placeholder="Institute Name">
          </div>
          <div class="col-md-4">
            <label class="form-label">Degree</label>
            <input type="text" class="form-control" name="degree[]" placeholder="Degree Name">
          </div>
          <div class="col-md-4">
            <label class="form-label">Field of Study</label>
            <input type="text" class="form-control" name="field[]" placeholder="Field of Study">
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Reg. Number</label>
            <input type="text" class="form-control" name="regnum[]" placeholder="Registration Number">
          </div>
          <div class="col-md-4">
            <label class="form-label">Start Year</label>
            <input type="number" class="form-control" name="startyear[]" placeholder="Start Year">
          </div>
          <div class="col-md-4">
            <label class="form-label">End Year</label>
            <input type="number" class="form-control" name="endyear[]"  placeholder="End Year">
          </div>
        </div>
      </div>

      <!-- Add New Button -->
      <div class="mb-3 text-end">
        <button type="button" class="btn btn-success" onclick="addHigherEducation()">+ Add New</button>
      </div>
      <br>
      <button type="submit" class="btn btn-primary w-100">Submit</button>
      <br>
      <br>
      <!-- Navigation Buttons -->
      <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" onclick="showSection('physical-info', 'education-section')">Back</button>
        
        <button type="button" class="btn btn-primary" onclick="showSection2('family-section', 'education-section')">Next</button>
      </div>
    </form>
  </div>
   <!-- <br>
  <img src="img/3.jpg" class="img-fluid rounded shadow-sm story-img" alt=""> -->
</section>
 </div>
 </div>
 
<br>

<div class="col-12 mt-2">
  <div class="col-lg-10 " style="margin-left: 8.5%;">
<!-- Family Information Section -->
<section id="family-section" class="py-5 bg-white bg-opacity-25 border-dark" style="display: none; border-radius: 10px; color: azure;">
  <div class="container g-5 ">
    <h2 class="mb-4 text-center">Family Information</h2>
    <form method="POST" action="">
      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Father's Name</label>
          <input type="text" class="form-control" name="father_name" placeholder="Enter father's name">
        </div>
        <div class="col-md-4">
          <label class="form-label">Father's Profession</label>
          <input type="text" class="form-control" name="father_profession" placeholder="Enter father's profession">
        </div>
        <div class="col-md-4">
          <label class="form-label">Father's Contact</label>
          <input type="text" class="form-control" name="father_contact" placeholder="Phone number">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Mother's Name</label>
          <input type="text" class="form-control" name="mother_name" placeholder="Enter mother's name">
        </div>
        <div class="col-md-4">
          <label class="form-label">Mother's Profession</label>
          <input type="text" class="form-control" name="mother_profession" placeholder="Enter mother's profession">
        </div>
        <div class="col-md-4">
          <label class="form-label">Mother's Contact</label>
          <input type="text" class="form-control" name="mother_contact" placeholder="Phone number">
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-6">
          <label class="form-label">Total Brothers</label>
          <input type="number" class="form-control" name="brothers" placeholder="Eg: 2">
        </div>
        <div class="col-md-6">
          <label class="form-label">Total Sisters</label>
          <input type="number" class="form-control" name="sisters" placeholder="Eg: 1">
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Submit</button>
      <br><br>

      <!-- Navigation Buttons -->
      <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" onclick="showSection1('education-section', 'family-section')">Back</button>
        <button type="button" class="btn btn-primary" onclick="showSection3('partner-section', 'family-section')">Next</button>
      </div>
    </form>
  </div>
   <!-- <br>
  <img src="img/3.jpg" class="img-fluid rounded shadow-sm story-img" style="height: 650px;" alt=""> -->
</section>
<br>

<!-- Partner Expectation Section -->
<section id="partner-section" class="py-5 bg-light bg-opacity-25 border-dark" style="display: none; border-radius: 10px; color: azure;">
  <div class="container g-5">
    <h2 class="mb-4 text-center">Partner Expectation</h2>
    <form method="POST" action="">
      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Preferred Country</label>
          <input type="text" class="form-control" name="partner_country" placeholder="Enter country" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Minimum Age</label>
          <input type="number" class="form-control" name="min_age" placeholder="Eg: 25" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Maximum Age</label>
          <input type="number" class="form-control" name="max_age" placeholder="Eg: 35" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Minimum Height (cm)</label>
          <input type="number" class="form-control" name="min_height" placeholder="Eg: 150" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Maximum Height (cm)</label>
          <input type="number" class="form-control" name="max_height" placeholder="Eg: 180" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Marital Status</label>
          <select class="form-select" name="partner_marital_status" required>
            <option value="" selected disabled>Select status</option>
            <option value="Never Married">Never Married</option>
            <option value="Divorced">Divorced</option>
            <option value="Widowed">Widowed</option>
            <option value="Separated">Separated</option>
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4">
          <label class="form-label">Religion</label>
          <input type="text" class="form-control" name="partner_religion" placeholder="Enter religion" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Smoking Habit</label>
          <select class="form-select" name="partner_smoking">
            <option value="" selected disabled>Select option</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
            <option value="Occasionally">Occasionally</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Drinking Habit</label>
          <select class="form-select" name="partner_drinking">
            <option value="" selected disabled>Select option</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
            <option value="Occasionally">Occasionally</option>
          </select>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Submit</button>
      <br>
      <br>
      <!-- Navigation Buttons -->
      <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" onclick="showSection('family-section', 'partner-section')">Back</button>
        <!-- <button type="button" class="btn btn-primary" onclick="alert('horoscope-section', 'partner-section')">Next</button> -->
      </div>
    </form>
  </div>
   <!-- <br>
  <img src="img/3.jpg" class="img-fluid rounded shadow-sm story-img" style="height: 660px;" alt=""> -->
</section>
</div>
</div>
</div>
</div>



<div class="col-12 justify-content-center d-flex align-items-center mt-5" style="border-radius: 10px; background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('img/desktop-wallpaper-hindu-wedding-tamil-wedding.jpg'); 
            background-size: cover; 
            background-position: center;">
      <div class="car">
        <span style="--i:1"><img class="pic" src="img/1c282bd5c77d49d7bae64ed7a01ff0ab.jpg" alt=""></span>
         <span style="--i:2"><img class="pic" src="img/d4ab9fa85e1530032ebcd2afa6a17e44.jpg" alt=""></span>
          <span style="--i:3"><img class="pic" src="img/6ca680122357085.60d84a765717c.jpg" alt=""></span>
           <span style="--i:4"><img class="pic" src="img/R.jpg" alt=""></span>
            <span style="--i:5"><img class="pic" src="img/355a26bc03afc2fe5f425ddbc79b4180.jpg" alt=""></span>
             <span style="--i:6"><img class="pic" src="img/d81b6668b4cd875545b963b9d8541773.jpg" alt=""></span>
              <span style="--i:7"><img class="pic" src="img/fixthephoto-wedding-photo-retouching-services-after_1701303442_wh480.jpg" alt=""></span>
               <span style="--i:8"><img class="pic" src="img/112024-house-on-the-cloud.jpeg" alt=""></span>

      </div>
        <!-- <img src="about.jpg" class="img-fluid rounded" alt="About image"> -->
    </div>


<br><br>
<div class="col-lg-10 " style="margin-left: 8.5%;">
<section class="py-5 bg-dark bg-opacity-25 border-dark" style="border-radius: 10px; color: azure;">
  <div id="horoscope-section" class="container g-5">
    <h2 class="mb-4 text-center">Horoscope Details</h2>
    <form method="POST" action="" enctype="multipart/form-data">

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Birth Date</label>
          <input type="date" class="form-control" name="birth_date" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Birth Time</label>
          <input type="time" class="form-control" name="birth_time" required>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Zodiac Sign</label>
          <input type="text" class="form-control" name="zodiac" placeholder="Eg: Leo, Virgo" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nakshatra</label>
          <input type="text" class="form-control" name="nakshatra" placeholder="Eg: Rohini, Ashwini" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Karmic Debt</label>
        <input type="text" class="form-control" name="karmic_debt" placeholder="Eg: Yes/No or Specific Value" required>
      </div>

      <!-- <div class="mb-3">
        <label class="form-label">Upload Horoscope (PDF/Image)</label>
        <input type="file" class="form-control" accept=".pdf,image/*">
      </div> -->

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Planet Position Image</label>
          <input type="file" class="form-control" name="planet_image" accept="image/*" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Navamsha Position Image</label>
          <input type="file" class="form-control" name="navamsha_image" accept="image/*" readonly>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Submit Horoscope</button>
      
    </form>
  </div>
</section>
    </div> 
    </div>

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



  function showSection(showId, hideId) {
    document.getElementById(showId).style.display = "block";
    document.getElementById(hideId).style.display = "none";
    // window.scrollTo({ top: 0, behavior: 'smooth' });
  }
  function showSection2(showId, hideId) {
    document.getElementById(showId).style.display = "block";
    document.getElementById(hideId).style.display = "none";
    // window.scrollTo({ top: 0, behavior: 'smooth' });
  }
  function showSection3(showId, hideId) {
    document.getElementById(showId).style.display = "block";
    document.getElementById(hideId).style.display = "none";
    // window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function addHigherEducation() {
    const container = document.getElementById('higher-edu-container');
    const clone = container.children[0].cloneNode(true);
    const clone2 = container.children[1].cloneNode(true);
    
    // Clear values
    clone.querySelectorAll('input').forEach(i => i.value = '');
    clone2.querySelectorAll('input').forEach(i => i.value = '');

    container.appendChild(clone);
    container.appendChild(clone2);
  }


  function showSection1(showId, hideId) {
    document.getElementById(hideId).style.display = 'block';
    document.getElementById(showId).style.display = 'none';
    window.scrollTo(0, 0); // Optional: Scroll to top when section changes
  }


</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Members Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .profile-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 20px;
      background: white;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .profile-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .profile-card img {
      width: 100%;
      height: auto;
      border-radius: 10px;
      border: 2px solid #fda101;
    }
    .icon-bar i {
      font-size: 20px;
      margin-right: 15px;
      cursor: pointer;
      transition: transform 0.3s, color 0.3s;
    }
    .icon-bar i:hover {
      transform: scale(1.2);
      color: #fda101;
    }
    .favorite.active {
      color: red;
    }
    .favorite.active ~ .whatsapp-icon {
      display: inline-block !important;
    }
    .whatsapp-icon {
      display: none;
      color: green;
    }
    .table th {
      background-color: #fda101;
      color: white;
      font-weight: bold;
      text-align: center;
    }
    .pagination .page-link {
      border-radius: 30px;
      color: #fda101;
      font-weight: 600;
    }
    .pagination .active .page-link {
      background-color: #fda101;
      border-color: #fda101;
      color: white;
    }
    @media (max-width: 767px) {
      .profile-card .row {
        flex-direction: column;
      }
      .icon-bar {
        text-align: center;
        margin-top: 10px;
      }
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
  </style>
</head>
<body>
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
<nav class="navbar navbar-expand-lg shadow-sm " style="background: linear-gradient(to right, #ec3107, #fda101); border-radius: 10px; ">
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
        <a class="nav-link  text-white fw-semibold me-3"   href="members.php">Member Ship</a>
        <a class="nav-link  text-white fw-semibold me-3" style="text-decoration: underline;" href="mem.php">Members</a>
        <a class="nav-link text-white fw-semibold me-3" href="package.php">Packages</a>
        <a class="nav-link text-white fw-semibold me-3" href="contact.php">Contact Us</a>
        <a class="nav-link text-white fw-semibold me-3" href="story.php">Stories</a>
      </div>
    </div>
  </div>
</nav>
</div>

  <div class="container my-4">
    <div class="row">

      <!-- Filter Section -->
      <div class="col-lg-4 col-md-6 col-12 mb-4" id="filtersForm">
        <h4 class="mb-3">Member Filter</h4>
        <table class="table table-bordered">
          
          <tr><th>Looking for</th></tr>
          <tr><td><select class="form-select" id="f_looking"><option value="">All</option><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></td></tr>
          <tr><th>Marital Status</th></tr>
          <tr><td><select class="form-select" id="f_marital"><option value="">All</option><option value="Single">Single</option><option value="Divorced">Divorced</option><option value="Married">Married</option><option value="Widowed">Widowed</option></select></td></tr>
          <tr><th>Religion</th></tr>
          <tr><td><select class="form-select" id="f_religion"><option value="">All</option><option value="Hindu">Hindu</option><option value="Christian">Christian</option><option value="Islam">Islam</option><option value="Buddhist">Buddhist</option></select></td></tr>
          <tr><th>Country</th></tr>
          <tr><td><select class="form-select" id="f_country"><option value="">All</option><option value="Sri Lanka">Sri Lanka</option><option value="India">India</option></select></td></tr>
          <tr><th>Profession</th></tr>
          <tr><td><input type="text" id="f_profession" class="form-control" placeholder="Profession"></td></tr>
          <tr><th>City</th></tr>
          <tr><td><input type="text" id="f_city" class="form-control" placeholder="City"></td></tr>
          <tr><td><button class="btn btn-sm btn-primary w-100 mt-2" id="btnApply">Apply Filters</button></td></tr>
          <tr><th>Age Range</th></tr>
          <tr><td>
      <select  class="form-select">
        <option value="" selected disabled>Select Age</option>
        <option value="18-25">18-25</option>
        <option value="26-30">26-30</option>
        <option value="31-35">31-35</option>
        <option value="36-40">36-40</option>
        <option value="41-45">41-45</option>
        <option value="46+">46+</option>

       </select>
       </td></tr>
       <tr><th>Cast</th></tr>
          <tr><td><input type="text" class="form-control" placeholder="Cast"></td></tr>
        </table>
      </div>

      <!-- Member Section -->
      <div class="col-lg-8 col-md-6 col-12" id="membersContainer">
        <!-- Cards injected here -->
       <div class="container" id="cardsRoot">
       </div>

  <!-- The modal with all requested details (dynamic) -->
  <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailsModalLabel">Profile Details — <span id="modalName">Y.Thisa</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="row">
            <div class="col-md-4 text-center">
              <img id="modalPhoto" src="img/d.webp" alt="photo" class="img-fluid rounded mb-3">
              <p class="small-muted">Contact: <a href="#" id="modalPhone">+94 77 123 4567</a></p>
              <p class="small-muted">DOB: <span id="modalDob">1999-08-12</span> | Gender: <span id="modalGender">Male</span></p>
            </div>
            <div class="col-md-8">

              <!-- Basic info -->
              <div class="section-title">Basic Information</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Name:</span> <div id="nameVal" class="small-muted">Y.Thisa</div></div>
                <div class="kv"><span class="modal-label">Male/Female:</span> <div id="genderVal" class="small-muted">Male</div></div>
                <div class="kv"><span class="modal-label">DOB:</span> <div id="dobVal" class="small-muted">1999-08-12</div></div>
                <div class="kv"><span class="modal-label">Religion:</span> <div id="religionVal" class="small-muted">Hindu</div></div>
                <div class="kv"><span class="modal-label">Marital Status:</span> <div id="maritalVal" class="small-muted">Single</div></div>
                <div class="kv"><span class="modal-label">Language:</span> <div id="languageVal" class="small-muted">Tamil</div></div>
                <div class="kv"><span class="modal-label">Profession:</span> <div id="professionVal" class="small-muted">Software Engineer</div></div>
                <div class="kv"><span class="modal-label">Country:</span> <div id="countryVal" class="small-muted">Sri Lanka</div></div>
                <div class="kv"><span class="modal-label">City:</span> <div id="cityVal" class="small-muted">Tringo</div></div>
                <div class="kv"><span class="modal-label">Phone Number:</span> <div id="phoneVal" class="small-muted">+94 77 123 4567</div></div>
                <div class="kv"><span class="modal-label">Smoking:</span> <div id="smokingVal" class="small-muted">No</div></div>
                <div class="kv"><span class="modal-label">Drinking:</span> <div id="drinkingVal" class="small-muted">Occasionally</div></div>
                <div class="kv"><span class="modal-label">Present Address:</span> <div id="presentAddressVal" class="small-muted">No. 12, Main Rd</div></div>
                <div class="kv"><span class="modal-label">City/ZIP:</span> <div id="presentCityZipVal" class="small-muted">Tringo / 12345</div></div>
                <div class="kv"><span class="modal-label">Permanent Address:</span> <div id="permAddressVal" class="small-muted">No. 4, Old St</div></div>
                <div class="kv"><span class="modal-label">Permanent City:</span> <div id="permCityVal" class="small-muted">Colombo</div></div>
              </div>

              <!-- Physical info -->
              <div class="section-title">Physical Information</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Complexion:</span> <div id="complexionVal" class="small-muted">Fair</div></div>
                <div class="kv"><span class="modal-label">Height:</span> <div id="heightVal" class="small-muted">5'8" (173 cm)</div></div>
                <div class="kv"><span class="modal-label">Weight:</span> <div id="weightVal" class="small-muted">68 kg</div></div>
                <div class="kv"><span class="modal-label">Blood Group:</span> <div id="bloodVal" class="small-muted">O+</div></div>
                <div class="kv"><span class="modal-label">Eye Color:</span> <div id="eyeVal" class="small-muted">Brown</div></div>
                <div class="kv"><span class="modal-label">Hair Color:</span> <div id="hairVal" class="small-muted">Black</div></div>
                <div class="kv"><span class="modal-label">Disability:</span> <div id="disabilityVal" class="small-muted">None</div></div>
              </div>

              <!-- Education -->
              <div class="section-title">Education Information</div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="table-light"><tr><th>Level</th><th>School / Institute</th><th>Stream</th><th>Year</th><th>Result</th><th>Reg. No</th></tr></thead>
                  <tbody id="educationTable">
                    <!-- rows injected by JS -->
                  </tbody>
                </table>
              </div>

              <!-- Family -->
              <div class="section-title">Family Information</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Father:</span> <div id="fatherVal" class="small-muted">Mr X — Engineer — +94 77 111 2222</div></div>
                <div class="kv"><span class="modal-label">Mother:</span> <div id="motherVal" class="small-muted">Mrs Y — Teacher — +94 77 333 4444</div></div>
                <div class="kv"><span class="modal-label">Brothers:</span> <div id="brothersVal" class="small-muted">1</div></div>
                <div class="kv"><span class="modal-label">Sisters:</span> <div id="sistersVal" class="small-muted">0</div></div>
              </div>

              <!-- Partner expectations -->
              <div class="section-title">Partner Expectations</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Preferred Country:</span> <div id="prefCountryVal" class="small-muted">Sri Lanka</div></div>
                <div class="kv"><span class="modal-label">Age Range:</span> <div id="ageRangeVal" class="small-muted">22 - 28</div></div>
                <div class="kv"><span class="modal-label">Height Range:</span> <div id="heightRangeVal" class="small-muted">5'0" - 5'6"</div></div>
                <div class="kv"><span class="modal-label">Marital Status:</span> <div id="prefMaritalVal" class="small-muted">Single</div></div>
                <div class="kv"><span class="modal-label">Religion:</span> <div id="prefReligionVal" class="small-muted">Hindu</div></div>
                <div class="kv"><span class="modal-label">Smoking:</span> <div id="prefSmokingVal" class="small-muted">No</div></div>
                <div class="kv"><span class="modal-label">Drinking:</span> <div id="prefDrinkingVal" class="small-muted">No</div></div>
              </div>

              <!-- Horoscope -->
              <div class="section-title">Horoscope Details</div>
              <div class="row g-3 align-items-center">
                <div class="col-md-6">
                  <ul class="list-unstyled small-muted">
                    <li><strong>Birth Date:</strong> <span id="h_birthdate">1999-08-12</span></li>
                    <li><strong>Birth Time:</strong> <span id="h_birthtime">07:30</span></li>
                    <li><strong>Zodiac Sign:</strong> <span id="h_zodiac">Leo</span></li>
                    <li><strong>Nakshatra:</strong> <span id="h_nakshatra">Magha</span></li>
                    <li><strong>Karmic Debt:</strong> <span id="h_karmic">None</span></li>
                  </ul>
                </div>
                <div class="col-md-6 text-center">
                  <div class="small-muted">Planet position image</div>
                  <img id="planetImg" src="img/planet-placeholder.png" alt="planet" class="img-fluid rounded" style="max-height:160px">
                  <div class="small-muted mt-2">Navamsha position</div>
                  <img id="navamshaImg" src="img/navamsha-placeholder.png" alt="navamsha" class="img-fluid rounded" style="max-height:160px">
                </div>
              </div>

            </div> <!-- col-md-8 -->
          </div> <!-- row -->

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <a id="waBtn" class="btn btn-success" href="https://wa.me/94771234567" target="_blank"><i class="bi bi-whatsapp"></i> Message on WhatsApp</a>
        </div>
      </div>
    </div>
  </div>

</div>









        <div class="container">
  <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailsModalLabel">Profile Details — <span id="modalName">Y.Thisa</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="row">
            <div class="col-md-4 text-center">
              <img id="modalPhoto" src="img/b.png" alt="photo" class="img-fluid rounded mb-3">
              <p class="small-muted">Contact: <a href="#" id="modalPhone">+94 77 123 4567</a></p>
              <p class="small-muted">DOB: <span id="modalDob">1999-08-12</span> | Gender: <span id="modalGender">Male</span></p>
            </div>
            <div class="col-md-8">

              <!-- Basic info -->
              <div class="section-title">Basic Information</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Name:</span> <div id="nameVal" class="small-muted">Y.Thisa</div></div><br>
                <div class="kv"><span class="modal-label">Male/Female:</span> <div id="genderVal" class="small-muted">Male</div></div><br>
                <div class="kv"><span class="modal-label">DOB:</span> <div id="dobVal" class="small-muted">1999-08-12</div></div><br>
                <div class="kv"><span class="modal-label">Religion:</span> <div id="religionVal" class="small-muted">Hindu</div></div><br>
                <div class="kv"><span class="modal-label">Marital Status:</span> <div id="maritalVal" class="small-muted">Single</div></div><br>
                <div class="kv"><span class="modal-label">Language:</span> <div id="languageVal" class="small-muted">Tamil</div></div><br>
                <div class="kv"><span class="modal-label">Profession:</span> <div id="professionVal" class="small-muted">Software Engineer</div></div><br>
                <div class="kv"><span class="modal-label">Country:</span> <div id="countryVal" class="small-muted">Sri Lanka</div></div><br>
                <div class="kv"><span class="modal-label">City:</span> <div id="cityVal" class="small-muted">Tringo</div></div><br>
                <div class="kv"><span class="modal-label">Phone Number:</span> <div id="phoneVal" class="small-muted">+94 77 123 4567</div></div><br>
                <div class="kv"><span class="modal-label">Smoking:</span> <div id="smokingVal" class="small-muted">No</div></div><br>
                <div class="kv"><span class="modal-label">Drinking:</span> <div id="drinkingVal" class="small-muted">Occasionally</div></div><br>
                <div class="kv"><span class="modal-label">Present Address:</span> <div id="presentAddressVal" class="small-muted">No. 12, Main Rd</div></div><br>
                <div class="kv"><span class="modal-label">City/ZIP:</span> <div id="presentCityZipVal" class="small-muted">Tringo / 12345</div></div><br>
                <div class="kv"><span class="modal-label">Permanent Address:</span> <div id="permAddressVal" class="small-muted">No. 4, Old St</div></div><br>
                <div class="kv"><span class="modal-label">Permanent City:</span> <div id="permCityVal" class="small-muted">Colombo</div></div><br>
              </div>

              <!-- Physical info -->
              <div class="section-title">Physical Information</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Complexion:</span> <div id="complexionVal" class="small-muted">Fair</div></div><br>
                <div class="kv"><span class="modal-label">Height:</span> <div id="heightVal" class="small-muted">5'8" (173 cm)</div></div><br>
                <div class="kv"><span class="modal-label">Weight:</span> <div id="weightVal" class="small-muted">68 kg</div></div><br>
                <div class="kv"><span class="modal-label">Blood Group:</span> <div id="bloodVal" class="small-muted">O+</div></div><br>
                <div class="kv"><span class="modal-label">Eye Color:</span> <div id="eyeVal" class="small-muted">Brown</div></div><br>
                <div class="kv"><span class="modal-label">Hair Color:</span> <div id="hairVal" class="small-muted">Black</div></div><br>
                <div class="kv"><span class="modal-label">Disability:</span> <div id="disabilityVal" class="small-muted">None</div></div><br>
              </div>

              <!-- Education -->
              <div class="section-title">Education Information</div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="table-light"><tr><th>Level</th><th>School / Institute</th><th>Stream</th><th>Year</th><th>Result</th><th>Reg. No</th></tr></thead>
                  <tbody id="educationTable">
                    <!-- rows injected by JS -->
                  </tbody>
                </table>
              </div>

              <!-- Family -->
              <div class="section-title">Family Information</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Father:</span> <div id="fatherVal" class="small-muted">Mr X — Engineer — +94 77 111 2222</div></div><br>
                <div class="kv"><span class="modal-label">Mother:</span> <div id="motherVal" class="small-muted">Mrs Y — Teacher — +94 77 333 4444</div></div><br>
                <div class="kv"><span class="modal-label">Brothers:</span> <div id="brothersVal" class="small-muted">1</div></div><br>
                <div class="kv"><span class="modal-label">Sisters:</span> <div id="sistersVal" class="small-muted">0</div></div><br>
              </div>

              <!-- Partner expectations -->
              <div class="section-title">Partner Expectations</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Preferred Country:</span> <div id="prefCountryVal" class="small-muted">Sri Lanka</div></div><br>
                <div class="kv"><span class="modal-label">Age Range:</span> <div id="ageRangeVal" class="small-muted">22 - 28</div></div><br>
                <div class="kv"><span class="modal-label">Height Range:</span> <div id="heightRangeVal" class="small-muted">5'0" - 5'6"</div></div><br>
                <div class="kv"><span class="modal-label">Marital Status:</span> <div id="prefMaritalVal" class="small-muted">Single</div></div><br>
                <div class="kv"><span class="modal-label">Religion:</span> <div id="prefReligionVal" class="small-muted">Hindu</div></div><br>
                <div class="kv"><span class="modal-label">Smoking:</span> <div id="prefSmokingVal" class="small-muted">No</div></div><br>
                <div class="kv"><span class="modal-label">Drinking:</span> <div id="prefDrinkingVal" class="small-muted">No</div></div><br>
              </div>

              <!-- Horoscope -->
              <div class="section-title">Horoscope Details</div>
              <div class="row g-3 align-items-center">
                <div class="col-md-6">
                  <ul class="list-unstyled small-muted">
                    <li><strong>Birth Date:</strong> <span id="h_birthdate">1999-08-12</span></li>
                    <li><strong>Birth Time:</strong> <span id="h_birthtime">07:30</span></li>
                    <li><strong>Zodiac Sign:</strong> <span id="h_zodiac">Leo</span></li>
                    <li><strong>Nakshatra:</strong> <span id="h_nakshatra">Magha</span></li>
                    <li><strong>Karmic Debt:</strong> <span id="h_karmic">None</span></li>
                  </ul>
                </div>
                <div class="col-md-6 text-center">
                  <div class="small-muted">Planet position image</div>
                  <img id="planetImg" src="img/planet-placeholder.png" alt="planet" class="img-fluid rounded" style="max-height:160px">
                  <div class="small-muted mt-2">Navamsha position</div>
                  <img id="navamshaImg" src="img/navamsha-placeholder.png" alt="navamsha" class="img-fluid rounded" style="max-height:160px">
                </div>
              </div>

            </div> <!-- col-md-8 -->
          </div> <!-- row -->

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <a id="waBtn" class="btn btn-success" href="https://wa.me/94771234567" target="_blank"><i class="bi bi-whatsapp"></i> Message on WhatsApp</a>
        </div>
      </div>
    </div>
  </div>

  <!-- The modal with all requested details -->
  <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailsModalLabel">Profile Details — <span id="modalName">Y.Thisa</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="row">
            <div class="col-md-4 text-center">
              <img id="modalPhoto" src="img/b.jpg" alt="photo" class="img-fluid rounded mb-3">
              <p class="small-muted">Contact: <a href="#" id="modalPhone">+94 77 123 4567</a></p>
              <p class="small-muted">DOB: <span id="modalDob">1999-08-12</span> | Gender: <span id="modalGender">Male</span></p>
            </div>
            <div class="col-md-8">

              <!-- Basic info -->
              <div class="section-title">Basic Information</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Name:</span> <div id="nameVal" class="small-muted">Y.Thisa</div></div><br>
                <div class="kv"><span class="modal-label">Male/Female:</span> <div id="genderVal" class="small-muted">Male</div></div><br>
                <div class="kv"><span class="modal-label">DOB:</span> <div id="dobVal" class="small-muted">1999-08-12</div></div><br>
                <div class="kv"><span class="modal-label">Religion:</span> <div id="religionVal" class="small-muted">Hindu</div></div><br>
                <div class="kv"><span class="modal-label">Marital Status:</span> <div id="maritalVal" class="small-muted">Single</div></div><br>
                <div class="kv"><span class="modal-label">Language:</span> <div id="languageVal" class="small-muted">Tamil</div></div><br>
                <div class="kv"><span class="modal-label">Profession:</span> <div id="professionVal" class="small-muted">Software Engineer</div></div><br>
                <div class="kv"><span class="modal-label">Country:</span> <div id="countryVal" class="small-muted">Sri Lanka</div></div><br>
                <div class="kv"><span class="modal-label">City:</span> <div id="cityVal" class="small-muted">Tringo</div></div><br>
                <div class="kv"><span class="modal-label">Phone Number:</span> <div id="phoneVal" class="small-muted">+94 77 123 4567</div></div><br>
                <div class="kv"><span class="modal-label">Smoking:</span> <div id="smokingVal" class="small-muted">No</div></div><br>
                <div class="kv"><span class="modal-label">Drinking:</span> <div id="drinkingVal" class="small-muted">Occasionally</div></div><br>
                <div class="kv"><span class="modal-label">Present Address:</span> <div id="presentAddressVal" class="small-muted">No. 12, Main Rd</div></div><br>
                <div class="kv"><span class="modal-label">City/ZIP:</span> <div id="presentCityZipVal" class="small-muted">Tringo / 12345</div></div><br>
                <div class="kv"><span class="modal-label">Permanent Address:</span> <div id="permAddressVal" class="small-muted">No. 4, Old St</div></div><br>
                <div class="kv"><span class="modal-label">Permanent City:</span> <div id="permCityVal" class="small-muted">Colombo</div></div><br>
              </div>

              <!-- Physical info -->
              <div class="section-title">Physical Information</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Complexion:</span> <div id="complexionVal" class="small-muted">Fair</div></div><br>
                <div class="kv"><span class="modal-label">Height:</span> <div id="heightVal" class="small-muted">5'8" (173 cm)</div></div><br>
                <div class="kv"><span class="modal-label">Weight:</span> <div id="weightVal" class="small-muted">68 kg</div></div><br>
                <div class="kv"><span class="modal-label">Blood Group:</span> <div id="bloodVal" class="small-muted">O+</div></div><br>
                <div class="kv"><span class="modal-label">Eye Color:</span> <div id="eyeVal" class="small-muted">Brown</div></div><br>
                <div class="kv"><span class="modal-label">Hair Color:</span> <div id="hairVal" class="small-muted">Black</div></div><br>
                <div class="kv"><span class="modal-label">Disability:</span> <div id="disabilityVal" class="small-muted">None</div></div><br>
              </div>

              <!-- Education -->
              <div class="section-title">Education Information</div>
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="table-light"><tr><th>Level</th><th>School / Institute</th><th>Stream</th><th>Year</th><th>Result</th><th>Reg. No</th></tr></thead>
                  <tbody id="educationTable">
                    <!-- rows injected by JS -->
                  </tbody>
                </table>
              </div>

              <!-- Family -->
              <div class="section-title">Family Information</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Father:</span> <div id="fatherVal" class="small-muted">Mr X — Engineer — +94 77 111 2222</div></div><br>
                <div class="kv"><span class="modal-label">Mother:</span> <div id="motherVal" class="small-muted">Mrs Y — Teacher — +94 77 333 4444</div></div><br>
                <div class="kv"><span class="modal-label">Brothers:</span> <div id="brothersVal" class="small-muted">1</div></div><br>
                <div class="kv"><span class="modal-label">Sisters:</span> <div id="sistersVal" class="small-muted">0</div></div><br>
              </div>

              <!-- Partner expectations -->
              <div class="section-title">Partner Expectations</div>
              <div class="d-flex flex-wrap gap-3 my-2">
                <div class="kv"><span class="modal-label">Preferred Country:</span> <div id="prefCountryVal" class="small-muted">Sri Lanka</div></div><br>
                <div class="kv"><span class="modal-label">Age Range:</span> <div id="ageRangeVal" class="small-muted">22 - 28</div></div><br>
                <div class="kv"><span class="modal-label">Height Range:</span> <div id="heightRangeVal" class="small-muted">5'0" - 5'6"</div></div><br>
                <div class="kv"><span class="modal-label">Marital Status:</span> <div id="prefMaritalVal" class="small-muted">Single</div></div><br>
                <div class="kv"><span class="modal-label">Religion:</span> <div id="prefReligionVal" class="small-muted">Hindu</div></div><br>
                <div class="kv"><span class="modal-label">Smoking:</span> <div id="prefSmokingVal" class="small-muted">No</div></div><br>
                <div class="kv"><span class="modal-label">Drinking:</span> <div id="prefDrinkingVal" class="small-muted">No</div></div><br>
              </div>

              <!-- Horoscope -->
              <div class="section-title">Horoscope Details</div>
              <div class="row g-3 align-items-center">
                <div class="col-md-6">
                  <ul class="list-unstyled small-muted">
                    <li><strong>Birth Date:</strong> <span id="h_birthdate">1999-08-12</span></li>
                    <li><strong>Birth Time:</strong> <span id="h_birthtime">07:30</span></li>
                    <li><strong>Zodiac Sign:</strong> <span id="h_zodiac">Leo</span></li>
                    <li><strong>Nakshatra:</strong> <span id="h_nakshatra">Magha</span></li>
                    <li><strong>Karmic Debt:</strong> <span id="h_karmic">None</span></li>
                  </ul>
                </div>
                <div class="col-md-6 text-center">
                  <div class="small-muted">Planet position image</div>
                  <img id="planetImg" src="img/planet-placeholder.png" alt="planet" class="img-fluid rounded" style="max-height:160px">
                  <div class="small-muted mt-2">Navamsha position</div>
                  <img id="navamshaImg" src="img/navamsha-placeholder.png" alt="navamsha" class="img-fluid rounded" style="max-height:160px">
                </div>
              </div>

            </div> <!-- col-md-8 -->
          </div> <!-- row -->

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <a id="waBtn" class="btn btn-success" href="https://wa.me/94771234567" target="_blank"><i class="bi bi-whatsapp"></i> Message on WhatsApp</a>
        </div>
      </div>
    </div>
  </div>

</div>

        <!-- Pagination -->
        <nav>
          <ul class="pagination justify-content-center">
            <li class="page-item disabled"><a class="page-link">Previous</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">Next</a></li>
          </ul>
        </nav>
      </div>
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
    function toggleFavorite(icon) {
      icon.classList.toggle('active');
    }


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

  </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const API_LIST = 'api_list_members.php';
  const API_DETAIL = 'api_member_detail.php';

  const cardsRoot = document.getElementById('cardsRoot');
  const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));

  function buildQuery(params){
    const usp = new URLSearchParams();
    Object.entries(params).forEach(([k,v])=>{ if(v !== undefined && v !== null && v !== '') usp.set(k, v); });
    return usp.toString();
  }

  async function loadMembers(filters={}){
    cardsRoot.innerHTML = '<div class="text-center py-3">Loading...</div>';
    const qs = buildQuery(filters);
    const res = await fetch(`${API_LIST}?${qs}`);
    const json = await res.json();
    const list = json.data || [];
    if (!list.length) { cardsRoot.innerHTML = '<div class="text-center py-3">No members found</div>'; return; }
    cardsRoot.innerHTML = '';
    list.forEach(m => cardsRoot.appendChild(createCard(m)));
  }

  function createCard(m){
    const div = document.createElement('div');
    div.className = 'profile-card mb-4 card p-3';
    div.innerHTML = `
      <div class="row g-3">
        <div class="col-md-4 col-12">
          <img src="${m.photo || 'img/d.webp'}" alt="Profile Photo">
        </div>
        <div class="col-md-8 col-12">
          <h5>${m.name} <small class="text-muted">(ID: ${m.id})</small></h5>
          <p>
            <strong>Looking for:</strong> ${m.looking_for || '-'}<br>
            <strong>Marital Status:</strong> ${m.marital_status || '-'}<br>
            <strong>Religion:</strong> ${m.religion || '-'}<br>
            <strong>Country:</strong> ${m.country || '-'}<br>
            <strong>Profession:</strong> ${m.profession || '-'}<br>
            <strong>City:</strong> ${m.city || '-'}<br>
            <strong>Age:</strong> ${m.age ?? '-'}<br>
            <strong>Language:</strong> ${m.language || '-'}
          </p>
          <div class="icon-bar">
            <i class="bi bi-heart favorite" onclick="toggleFavorite(this)"></i>
            <i class="bi bi-star" title="Add to featured"></i>
            <i class="bi bi-flag" title="Report"></i>
          </div>
          <br>
          <button class="btn btn-primary" data-member-id="${m.id}" onclick="openDetails(${m.id})">More Details</button>
        </div>
      </div>`;
    return div;
  }

  async function openDetails(memberId){
    const res = await fetch(`${API_DETAIL}?member_id=${memberId}`);
    const d = await res.json();
    const m = d.member || {};
    const p = d.physical || {};
    const edu = d.education || [];
    const fam = d.family || {};
    const pe = d.partner || {};
    const h = d.horoscope || {};

    document.getElementById('modalName').textContent = m.name || '';
    document.getElementById('modalPhoto').src = m.photo || 'img/d.webp';
    document.getElementById('modalPhone').href = m.phone ? `tel:${m.phone}` : '#';
    document.getElementById('modalPhone').textContent = m.phone || '';
    document.getElementById('modalDob').textContent = m.dob || '';
    document.getElementById('modalGender').textContent = m.gender || '';

    document.getElementById('nameVal').textContent = m.name || '';
    document.getElementById('genderVal').textContent = m.gender || '';
    document.getElementById('dobVal').textContent = m.dob || '';
    document.getElementById('religionVal').textContent = m.religion || '';
    document.getElementById('maritalVal').textContent = m.marital_status || '';
    document.getElementById('languageVal').textContent = m.language || '';
    document.getElementById('professionVal').textContent = m.profession || '';
    document.getElementById('countryVal').textContent = m.country || '';
    document.getElementById('cityVal').textContent = m.city || '';
    document.getElementById('phoneVal').textContent = m.phone || '';
    document.getElementById('smokingVal').textContent = m.smoking || '';
    document.getElementById('drinkingVal').textContent = m.drinking || '';
    document.getElementById('presentAddressVal').textContent = m.present_address || '';
    document.getElementById('presentCityZipVal').textContent = `${m.city || ''} / ${m.zip || ''}`;
    document.getElementById('permAddressVal').textContent = m.permanent_address || '';
    document.getElementById('permCityVal').textContent = m.permanent_city || '';

    document.getElementById('complexionVal').textContent = p.complexion || '';
    document.getElementById('heightVal').textContent = p.height_cm ? `${p.height_cm} cm` : '';
    document.getElementById('weightVal').textContent = p.weight_kg ? `${p.weight_kg} kg` : '';
    document.getElementById('bloodVal').textContent = p.blood_group || '';
    document.getElementById('eyeVal').textContent = p.eye_color || '';
    document.getElementById('hairVal').textContent = p.hair_color || '';
    document.getElementById('disabilityVal').textContent = p.disability || '';

    const edBody = document.getElementById('educationTable'); edBody.innerHTML = '';
    edu.forEach(e => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${e.level||''}</td><td>${e.school_or_institute||''}</td><td>${e.stream_or_degree||''}</td><td>${e.start_year||''}-${e.end_year||''}</td><td>${e.field||''}</td><td>${e.reg_number||''}</td>`;
      edBody.appendChild(tr);
    });

    document.getElementById('fatherVal').textContent = fam.father_name ? `${fam.father_name} — ${fam.father_profession||''} — ${fam.father_contact||''}` : '';
    document.getElementById('motherVal').textContent = fam.mother_name ? `${fam.mother_name} — ${fam.mother_profession||''} — ${fam.mother_contact||''}` : '';
    document.getElementById('brothersVal').textContent = fam.brothers_count ?? '';
    document.getElementById('sistersVal').textContent = fam.sisters_count ?? '';

    document.getElementById('prefCountryVal').textContent = pe.preferred_country || '';
    document.getElementById('ageRangeVal').textContent = `${pe.min_age||''} - ${pe.max_age||''}`;
    document.getElementById('heightRangeVal').textContent = `${pe.min_height||''} - ${pe.max_height||''}`;
    document.getElementById('prefMaritalVal').textContent = pe.marital_status || '';
    document.getElementById('prefReligionVal').textContent = pe.religion || '';
    document.getElementById('prefSmokingVal').textContent = pe.smoking || '';
    document.getElementById('prefDrinkingVal').textContent = pe.drinking || '';

    document.getElementById('h_birthdate').textContent = h.birth_date || '';
    document.getElementById('h_birthtime').textContent = h.birth_time || '';
    document.getElementById('h_zodiac').textContent = h.zodiac || '';
    document.getElementById('h_nakshatra').textContent = h.nakshatra || '';
    document.getElementById('h_karmic').textContent = h.karmic_debt || '';
    document.getElementById('planetImg').src = h.planet_image || 'img/planet-placeholder.png';
    document.getElementById('navamshaImg').src = h.navamsha_image || 'img/navamsha-placeholder.png';

    document.getElementById('waBtn').href = `https://wa.me/${(m.phone||'').replace(/\D/g,'')}`;
    detailsModal.show();
  }

  // Filters -> API
  document.getElementById('btnApply').addEventListener('click', (e)=>{
    e.preventDefault();
    const filters = {
      looking_for: document.getElementById('f_looking').value,
      marital_status: document.getElementById('f_marital').value,
      religion: document.getElementById('f_religion').value,
      country: document.getElementById('f_country').value,
      profession: document.getElementById('f_profession').value,
      city: document.getElementById('f_city').value
    };
    loadMembers(filters);
  });

  // Initial load
  loadMembers();

  function toggleFavorite(el){
    el.classList.toggle('text-danger');
    el.classList.toggle('bi-heart-fill');
  }
</script>
</body>
</html>

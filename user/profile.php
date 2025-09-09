<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'matrimony');
if ($conn->connect_error) {
  die('DB error');
}

$member_id = (int)($_GET['id'] ?? 0);
if ($member_id <= 0) { die('Invalid member'); }

// Gate: allow only paid users
$allowed = !empty($_SESSION['has_active_package']) && $_SESSION['has_active_package'] === true;
if (!$allowed) {
  header('Location: package.html');
  exit;
}

// Load details via queries (reuse api_member_detail logic)
$member = null; $physical = null; $education = []; $family = null; $partner = null; $horoscope = null;

$stmt = $conn->prepare("SELECT id, name, photo, looking_for, dob, religion, gender, marital_status, language, profession, country, phone, present_address, city, zip, permanent_address, permanent_city FROM members WHERE id = ?");
$stmt->bind_param('i', $member_id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();
if (!$member) { die('Not found'); }

$stmt = $conn->prepare("SELECT complexion, height_cm, weight_kg, blood_group, eye_color, hair_color, disability FROM physical_info WHERE member_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('i', $member_id);
$stmt->execute();
$physical = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("SELECT level, school_or_institute, stream_or_degree, field, reg_number, start_year, end_year FROM education WHERE member_id = ? ORDER BY id DESC");
$stmt->bind_param('i', $member_id);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) { $education[] = $r; }

$stmt = $conn->prepare("SELECT father_name, father_profession, father_contact, mother_name, mother_profession, mother_contact, brothers_count, sisters_count FROM family WHERE member_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('i', $member_id);
$stmt->execute();
$family = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("SELECT preferred_country, min_age, max_age, min_height, max_height, marital_status, religion, smoking, drinking FROM partner_expectations WHERE member_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('i', $member_id);
$stmt->execute();
$partner = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("SELECT birth_date, birth_time, zodiac, nakshatra, karmic_debt, planet_image, navamsha_image FROM horoscope WHERE member_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('i', $member_id);
$stmt->execute();
$horoscope = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Profile</title>
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="card mb-3">
      <div class="row g-0">
        <div class="col-md-3">
          <img class="img-fluid h-100 w-100 object-fit-cover" src="<?php echo htmlspecialchars($member['photo'] ?: 'img/d.webp'); ?>" alt="">
        </div>
        <div class="col-md-9">
          <div class="card-body">
            <h3 class="card-title mb-1"><?php echo htmlspecialchars($member['name']); ?></h3>
            <div class="text-muted">Gender: <?php echo htmlspecialchars($member['gender']); ?> · DOB: <?php echo htmlspecialchars($member['dob']); ?></div>
            <div>Religion: <?php echo htmlspecialchars($member['religion']); ?> · Marital: <?php echo htmlspecialchars($member['marital_status']); ?> · Language: <?php echo htmlspecialchars($member['language']); ?></div>
            <div>Profession: <?php echo htmlspecialchars($member['profession']); ?> · <?php echo htmlspecialchars($member['city'].', '.$member['country']); ?></div>
            <div>Phone: <?php echo htmlspecialchars($member['phone']); ?></div>
            <div class="mt-2">Address: <?php echo htmlspecialchars($member['present_address']); ?></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">Physical</div>
          <div class="card-body">
            <?php if ($physical): ?>
            <div>Complexion: <?php echo htmlspecialchars($physical['complexion']); ?></div>
            <div>Height: <?php echo htmlspecialchars($physical['height_cm']); ?> cm · Weight: <?php echo htmlspecialchars($physical['weight_kg']); ?> kg</div>
            <div>Blood: <?php echo htmlspecialchars($physical['blood_group']); ?> · Eye: <?php echo htmlspecialchars($physical['eye_color']); ?> · Hair: <?php echo htmlspecialchars($physical['hair_color']); ?></div>
            <div>Disability: <?php echo htmlspecialchars($physical['disability']); ?></div>
            <?php else: ?>
            <div class="text-muted">No data</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">Partner Expectations</div>
          <div class="card-body">
            <?php if ($partner): ?>
            <div>Country: <?php echo htmlspecialchars($partner['preferred_country']); ?></div>
            <div>Age: <?php echo htmlspecialchars($partner['min_age']); ?>–<?php echo htmlspecialchars($partner['max_age']); ?></div>
            <div>Height: <?php echo htmlspecialchars($partner['min_height']); ?>–<?php echo htmlspecialchars($partner['max_height']); ?> cm</div>
            <div>Marital: <?php echo htmlspecialchars($partner['marital_status']); ?> · Religion: <?php echo htmlspecialchars($partner['religion']); ?></div>
            <div>Smoking: <?php echo htmlspecialchars($partner['smoking']); ?> · Drinking: <?php echo htmlspecialchars($partner['drinking']); ?></div>
            <?php else: ?>
            <div class="text-muted">No data</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="card my-3">
      <div class="card-header">Education</div>
      <div class="card-body table-responsive">
        <?php if ($education): ?>
        <table class="table table-sm">
          <thead><tr><th>Level</th><th>School/Institute</th><th>Stream/Degree</th><th>Field</th><th>Reg No</th><th>Years</th></tr></thead>
          <tbody>
            <?php foreach ($education as $e): ?>
              <tr>
                <td><?php echo htmlspecialchars($e['level']); ?></td>
                <td><?php echo htmlspecialchars($e['school_or_institute']); ?></td>
                <td><?php echo htmlspecialchars($e['stream_or_degree']); ?></td>
                <td><?php echo htmlspecialchars($e['field']); ?></td>
                <td><?php echo htmlspecialchars($e['reg_number']); ?></td>
                <td><?php echo htmlspecialchars($e['start_year'].'-'.$e['end_year']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="text-muted">No data</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">Family</div>
          <div class="card-body">
            <?php if ($family): ?>
            <div>Father: <?php echo htmlspecialchars($family['father_name']); ?> — <?php echo htmlspecialchars($family['father_profession']); ?> — <?php echo htmlspecialchars($family['father_contact']); ?></div>
            <div>Mother: <?php echo htmlspecialchars($family['mother_name']); ?> — <?php echo htmlspecialchars($family['mother_profession']); ?> — <?php echo htmlspecialchars($family['mother_contact']); ?></div>
            <div>Brothers: <?php echo htmlspecialchars($family['brothers_count']); ?> · Sisters: <?php echo htmlspecialchars($family['sisters_count']); ?></div>
            <?php else: ?>
            <div class="text-muted">No data</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-header">Horoscope</div>
          <div class="card-body">
            <?php if ($horoscope): ?>
            <div>Birth: <?php echo htmlspecialchars($horoscope['birth_date']); ?> <?php echo htmlspecialchars($horoscope['birth_time']); ?></div>
            <div>Zodiac: <?php echo htmlspecialchars($horoscope['zodiac']); ?> · Nakshatra: <?php echo htmlspecialchars($horoscope['nakshatra']); ?></div>
            <div>Karmic: <?php echo htmlspecialchars($horoscope['karmic_debt']); ?></div>
            <div class="row g-2 mt-2">
              <div class="col-6"><img class="img-fluid rounded" src="<?php echo htmlspecialchars($horoscope['planet_image'] ?: 'img/planet-placeholder.png'); ?>" alt=""></div>
              <div class="col-6"><img class="img-fluid rounded" src="<?php echo htmlspecialchars($horoscope['navamsha_image'] ?: 'img/navamsha-placeholder.png'); ?>" alt=""></div>
            </div>
            <?php else: ?>
            <div class="text-muted">No data</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</body>
</html>


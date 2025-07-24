<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$name = $date_of_request = $certificate_type = "";
$age = $cstatus = $gender = "";

if ($id > 0) {
  // First get request info
  $stmt = $conn->prepare("SELECT name, date, certificate_type FROM request WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $date_of_request = date("jS \\of F, Y", strtotime($row['date']));
    $certificate_type = $row['certificate_type'];

    // Try to get resident info by matching name (improve with ID later)
    $res_stmt = $conn->prepare("SELECT age, `c-status`, gender FROM `res-info` WHERE CONCAT(fname, ' ', lname) = ?");
    $res_stmt->bind_param("s", $name);
    $res_stmt->execute();
    $res_result = $res_stmt->get_result();
    if ($res_result && $res_result->num_rows > 0) {
      $res = $res_result->fetch_assoc();
      $age = $res['age'];
      $cstatus = $res['c-status'];
      $gender = $res['gender'];
    }
    $res_stmt->close();
  }
  $stmt->close();
}
$conn->close();

// Build display
$certificate_type_display = strtoupper(str_replace('certificate of ', '', $certificate_type));
if ($certificate_type_display === '') $certificate_type_display = 'CERTIFICATE';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars(ucwords($certificate_type_display)) ?> Certificate</title>
  <link rel="stylesheet" href="../styles/certificate.css">
</head>
<body>
  <div class="certificate-container">
    <div class="header">
      <img src="../images/LipaLogo.png" alt="Barangay Seal" />
      <h1>Republic of the Philippines</h1>
      <h2>Province of Batangas</h2>
      <h2>Lipa City</h2>
      <h2>BARANGAY BUGTONG NA PULO</h2>
      <div class="divider"></div>
    </div>

    <div class="title">
      <h2><u><?= $certificate_type_display ? "CERTIFICATE OF $certificate_type_display" : "CERTIFICATE" ?></u></h2>
    </div>

    <div class="content">
      <p><strong>TO WHOM IT MAY CONCERN:</strong></p>
      <p>
        <?php if ($certificate_type && stripos($certificate_type, 'indigency') !== false): ?>
          This is to certify that <strong><?= $name ? htmlspecialchars($name) : '[Blank]' ?></strong>,
          <?= $cstatus ? htmlspecialchars($cstatus) . ',' : '' ?>
          <?= $age ? htmlspecialchars($age) . ' years old,' : '' ?>
          <?= $gender ? htmlspecialchars($gender) . ' Filipino citizen,' : 'Filipino citizen,' ?>
          is a resident of this barangay and belongs to an indigent family. He/She has no means of livelihood to support his/her medication and hospitalization needs.
        <?php elseif ($certificate_type && stripos($certificate_type, 'residency') !== false): ?>
          This is to certify that <strong><?= $name ? htmlspecialchars($name) : '[Blank]' ?></strong> is a legal resident of this barangay.
        <?php else: ?>
          This is to certify that <strong><?= $name ? htmlspecialchars($name) : '[Blank]' ?></strong> is a bona fide resident of Barangay Bugtong na Pulo, Lipa City, for purposes as may be required.
        <?php endif; ?>
      </p>
      <p>
        <?php if ($certificate_type && stripos($certificate_type, 'indigency') !== false): ?>
          This certification is issued as a requirement for medical assistance, and for whatever legal purpose it may serve him/her.
        <?php elseif ($certificate_type && stripos($certificate_type, 'residency') !== false): ?>
          This certification is issued to confirm that <strong><?= $name ? htmlspecialchars($name) : '[Blank]' ?></strong> resides in Barangay Bugtong na Pulo, Lipa City.
        <?php else: ?>
          This certification is issued upon the request of the above-named resident for whatever legal purpose it may serve.
        <?php endif; ?>
      </p>
      <p>Issued this <strong><?= $date_of_request ? $date_of_request : '[Blank]' ?></strong> at Barangay Bugtong na Pulo, Lipa City, Philippines.</p>
    </div>

    <div class="footer">
      <div class="signature-area">
        <div class="signature">
          <div>Certified Correct:</div>
          <span class="sig-line"></span>
          <div class="sig-name">HON. NIDRALDEN A. USMAN</div>
          <div class="sig-title">Barangay Chairman</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

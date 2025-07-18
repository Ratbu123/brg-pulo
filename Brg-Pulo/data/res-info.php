<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "brg-pulo";
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMsg = "";
$errorMsg = "";

// Only process input if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Always check if each POST value is set
    $fname = isset($_POST['fname']) ? $_POST['fname'] : '';
    $lname = isset($_POST['lname']) ? $_POST['lname'] : '';
    $mname = isset($_POST['mname']) ? $_POST['mname'] : '';
    $age = isset($_POST['age']) ? $_POST['age'] : '';
    $number = isset($_POST['number']) ? $_POST['number'] : '';
    $c_status = isset($_POST['c-status']) ? $_POST['c-status'] : '';
    $dob = isset($_POST['dob']) ? $_POST['dob'] : '';
    $housen = isset($_POST['housen']) ? $_POST['housen'] : '';
    $purok = isset($_POST['purok']) ? $_POST['purok'] : '';

    // Default image if none uploaded
    $profile = "images/sub/usericon.png"; // path relative to project root

    // If image uploaded
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        $uploads_dir = __DIR__ . '/../uploads/';
        if (!file_exists($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        $filename = basename($_FILES["profile"]["name"]);
        $target_file = $uploads_dir . $filename;
        $img_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];

        if (in_array($img_ext, $allowed) && move_uploaded_file($_FILES["profile"]["tmp_name"], $target_file)) {
            // Save relative path for use in <img src="">
            $profile = "uploads/" . $filename;
        }
    }

    // Insert to DB
    $stmt = $conn->prepare("INSERT INTO `res-info` (fname, lname, mname, age, number, `c-status`, dob, profile, housen, purok) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $fname, $lname, $mname, $age, $number, $c_status, $dob, $profile, $housen, $purok);

    if ($stmt->execute()) {
        // Show JS alert then redirect to admin page
        echo "<script>alert('Resident added successfully!'); window.location.href='../brg-pulo/Brg-Pulo/admin.php';</script>";
        exit;
    } else {
        $errorMsg = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Resident</title>
    <link rel="stylesheet" href="../Brg-Pulo/styles/styles.css">
    <style>
        .form-container { background:#fff; border-radius:10px; max-width:650px; margin:36px auto; box-shadow:0 4px 24px #0001; padding:35px 24px 22px 24px;}
        .form-header h2 {text-align:center; color:#043CA7;}
        .form-grid {display:grid; grid-template-columns:170px 1fr; gap:18px 28px;}
        .image-upload-section {text-align:center;}
        #preview-container { width: 120px; height: 120px; margin:0 auto 8px; border-radius:50%; overflow:hidden; background:#f4f4f4;}
        #preview-image { width:100%; height:100%; object-fit:cover;}
        .form-fields-section { display:grid; grid-template-columns:1fr 1fr; gap:12px 15px;}
        .form-fields-section input, .form-fields-section select {padding:8px;border-radius:4px;border:1.5px solid #ccc;}
        .form-fields-section label { font-weight:bold; color:#444;}
        .form-fields-section button[type=submit] {grid-column:1/3;background:#043CA7;color:#fff;font-weight:600;padding:11px 0;margin-top:10px;}
        @media (max-width:700px){
            .form-grid {grid-template-columns:1fr;}
            .form-fields-section {grid-template-columns:1fr;}
        }
        .message.error {background:#f8d7da; color:#721c24; padding:12px; margin-bottom:14px; border-radius:5px; text-align:center;}
        .message.success {background:#d4edda; color:#155724; padding:12px; margin-bottom:14px; border-radius:5px; text-align:center;}
    </style>
</head>
<body>

<div class="form-container">
    <?php if (!empty($errorMsg)): ?>
        <div class="message error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="form-header">
            <h2>Add Resident</h2>
        </div>
        <div class="form-grid">
            <div class="image-upload-section">
                <div id="preview-container">
                    <img id="preview-image" src="/Brg-Pulo/images/sub/usericon.png" alt="Preview">
                </div>
                <input type="file" name="profile" accept="image/*" onchange="previewImage(event)">
            </div>
            <div class="form-fields-section">
                <input type="text" name="fname" placeholder="First Name" required>
                <input type="text" name="lname" placeholder="Last Name" required>
                <input type="text" name="mname" placeholder="Middle Name" required>
                <input type="number" name="age" placeholder="Age" required>
                <input type="text" name="number" placeholder="Contact Number" required>
                <label>Civil Status:
                    <select name="c-status" required>
                        <option value="">Select Status</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Separated">Separated</option>
                    </select>
                </label>
                <label>Date of Birth:
                    <input type="date" name="dob" required>
                </label>
                <input type="text" name="housen" placeholder="House Number" required>
                <label>Purok:
                    <select name="purok" required>
                        <option value="">Select Purok</option>
                        <option value="Purok 1">Purok 1</option>
                        <option value="Purok 2">Purok 2</option>
                        <option value="Purok 3">Purok 3</option>
                        <option value="Purok 4">Purok 4</option>
                        <option value="Purok 5">Purok 5</option>
                    </select>
                </label>
                <button type="submit">Add Resident</button>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const input = event.target;
    const previewImage = document.getElementById('preview-image');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>

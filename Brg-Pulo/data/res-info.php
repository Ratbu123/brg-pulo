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
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $mname = trim($_POST['mname'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $number = trim($_POST['number'] ?? '');
    $c_status = trim($_POST['c-status'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $housen = trim($_POST['housen'] ?? '');
    $purok = trim($_POST['purok'] ?? '');
    $gender = trim($_POST['gender'] ?? '');

    // Default image if none uploaded
    $profile = "images/sub/usericon.png";
    $profileUploaded = false;
    $uploads_dir = __DIR__ . '/../uploads/';
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    // If image uploaded
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === 0) {
        if (!file_exists($uploads_dir)) mkdir($uploads_dir, 0777, true);
        $filename = basename($_FILES["profile"]["name"]);
        $img_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $size = $_FILES["profile"]["size"];
        if (!in_array($img_ext, $allowed)) {
            $errorMsg = "Only image files (jpg, jpeg, png, gif, webp) allowed.";
        } elseif ($size > $maxFileSize) {
            $errorMsg = "Image must be less than 2MB.";
        } else {
            // Prevent file name collision
            $base = pathinfo($filename, PATHINFO_FILENAME);
            $uniqueFilename = $filename;
            $i = 1;
            while (file_exists($uploads_dir . $uniqueFilename)) {
                $uniqueFilename = $base . "_$i.$img_ext";
                $i++;
            }
            if (move_uploaded_file($_FILES["profile"]["tmp_name"], $uploads_dir . $uniqueFilename)) {
                $profile = "uploads/" . $uniqueFilename;
                $profileUploaded = true;
            } else {
                $errorMsg = "Error uploading the image.";
            }
        }
    }

    // Insert to DB
    if (empty($errorMsg)) {
        $stmt = $conn->prepare("INSERT INTO `res-info` (fname, lname, mname, age, number, `c-status`, dob, profile, housen, purok, gender)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $fname, $lname, $mname, $age, $number, $c_status, $dob, $profile, $housen, $purok, $gender);
        if ($stmt->execute()) {
            $successMsg = "Resident added successfully!";
            // Reset POST after success
            $_POST = [];
        } else {
            $errorMsg = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
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
    <?php if (!empty($successMsg)): ?>
        <div class="message success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="form-header">
            <h2>Add Resident</h2>
        </div>
        <div class="form-grid">
            <div class="image-upload-section">
                <div id="preview-container">
                    <img id="preview-image"
                        src="<?php
                            if (!empty($successMsg) || (!isset($_POST['profile']) && empty($_POST))) {
                                echo '/Brg-Pulo/images/sub/usericon.png';
                            } elseif (!empty($_FILES['profile']['tmp_name']) && empty($errorMsg)) {
                                echo isset($profile) ? '/' . $profile : '/Brg-Pulo/images/sub/usericon.png';
                            } else {
                                echo '/Brg-Pulo/images/sub/usericon.png';
                            }
                        ?>"
                        alt="Preview">
                </div>
                <input type="file" name="profile" accept="image/*" onchange="previewImage(event)">
            </div>
            <div class="form-fields-section">
                <input type="text" name="fname" placeholder="First Name" required value="<?= htmlspecialchars($_POST['fname'] ?? '') ?>">
                <input type="text" name="lname" placeholder="Last Name" required value="<?= htmlspecialchars($_POST['lname'] ?? '') ?>">
                <input type="text" name="mname" placeholder="Middle Name" required value="<?= htmlspecialchars($_POST['mname'] ?? '') ?>">
                <input type="number" name="age" placeholder="Age" required value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
                <input type="text" name="number" placeholder="Contact Number" required value="<?= htmlspecialchars($_POST['number'] ?? '') ?>">
                <label>Civil Status:
                    <select name="c-status" required>
                        <option value="">Select Status</option>
                        <option value="Single" <?= (isset($_POST['c-status']) && $_POST['c-status']=='Single')?'selected':''; ?>>Single</option>
                        <option value="Married" <?= (isset($_POST['c-status']) && $_POST['c-status']=='Married')?'selected':''; ?>>Married</option>
                        <option value="Widowed" <?= (isset($_POST['c-status']) && $_POST['c-status']=='Widowed')?'selected':''; ?>>Widowed</option>
                        <option value="Separated" <?= (isset($_POST['c-status']) && $_POST['c-status']=='Separated')?'selected':''; ?>>Separated</option>
                    </select>
                </label>
                <label>Date of Birth:
                    <input type="date" name="dob" required value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                </label>
                <input type="text" name="housen" placeholder="House Number" required value="<?= htmlspecialchars($_POST['housen'] ?? '') ?>">
                <label>Purok:
                    <select name="purok" required>
                        <option value="">Select Purok</option>
                        <option value="Purok 1" <?= (isset($_POST['purok']) && $_POST['purok']=='Purok 1')?'selected':''; ?>>Purok 1</option>
                        <option value="Purok 2" <?= (isset($_POST['purok']) && $_POST['purok']=='Purok 2')?'selected':''; ?>>Purok 2</option>
                        <option value="Purok 3" <?= (isset($_POST['purok']) && $_POST['purok']=='Purok 3')?'selected':''; ?>>Purok 3</option>
                        <option value="Purok 4" <?= (isset($_POST['purok']) && $_POST['purok']=='Purok 4')?'selected':''; ?>>Purok 4</option>
                        <option value="Purok 5" <?= (isset($_POST['purok']) && $_POST['purok']=='Purok 5')?'selected':''; ?>>Purok 5</option>
                    </select>
                </label>
                <label>Gender:
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male" <?= (isset($_POST['gender']) && $_POST['gender']=='Male')?'selected':''; ?>>Male</option>
                        <option value="Female" <?= (isset($_POST['gender']) && $_POST['gender']=='Female')?'selected':''; ?>>Female</option>
                        <option value="Other" <?= (isset($_POST['gender']) && $_POST['gender']=='Other')?'selected':''; ?>>Other</option>
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

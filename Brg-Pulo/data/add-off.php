<?php
$successMsg = "";
$uploadError = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "brg-pulo";

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("<div class='error'>Connection failed: " . $conn->connect_error . "</div>");
    }

    // Trim and sanitize input
    $fname       = trim($_POST['fname'] ?? "");
    $mname       = trim($_POST['mname'] ?? "");
    $lname       = trim($_POST['lname'] ?? "");
    $number      = trim($_POST['number'] ?? "");
    $barangay    = trim($_POST['barangay'] ?? "");
    $position    = trim($_POST['position'] ?? "");
    $age         = isset($_POST['age']) ? (int)$_POST['age'] : 0;
    $dateofbirth = trim($_POST['dateofbirth'] ?? "");
    $address     = trim($_POST['address'] ?? "");
    $gender      = trim($_POST['gender'] ?? "");
    $email       = trim($_POST['email'] ?? "");
    $passwordPlain = $_POST['password'] ?? "";

    // Duplicate email check
    $checkEmail = $conn->prepare("SELECT id FROM `b-official` WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();
    if ($checkEmail->num_rows > 0) {
        $errorMsg = "Email is already registered!";
    }
    $checkEmail->close();

    // Upload logic
    $pictureName = "";
    if (empty($errorMsg)) {
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $pictureName = isset($_FILES["picture"]["name"]) ? basename($_FILES["picture"]["name"]) : "";
        $imageFileType = strtolower(pathinfo($pictureName, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        $maxFileSize = 2 * 1024 * 1024; // 2MB

        if (empty($pictureName)) {
            $uploadError = "Please upload an image file.";
        } elseif ($_FILES["picture"]["size"] > $maxFileSize) {
            $uploadError = "Image must be less than 2MB.";
        } else {
            $check = @getimagesize($_FILES["picture"]["tmp_name"]);
            if ($check === false) {
                $uploadError = "File is not a valid image.";
            } elseif (!in_array($imageFileType, $allowedTypes)) {
                $uploadError = "Only JPG, JPEG, PNG & GIF files are allowed.";
            } else {
                // Prevent duplicate file names
                $baseName = pathinfo($pictureName, PATHINFO_FILENAME);
                $i = 1;
                $finalPictureName = $pictureName;
                while (file_exists($targetDir . $finalPictureName)) {
                    $finalPictureName = $baseName . "_" . $i . "." . $imageFileType;
                    $i++;
                }
                if (!move_uploaded_file($_FILES["picture"]["tmp_name"], $targetDir . $finalPictureName)) {
                    $uploadError = "Error uploading the image.";
                } else {
                    $pictureName = $finalPictureName;
                }
            }
        }
    }

    // Only add if no errors
    if ($uploadError === "" && $errorMsg === "" && $fname && $lname && $email) {
        $picturePathForDB = "data/uploads/" . $pictureName;
        $passwordHashed = password_hash($passwordPlain, PASSWORD_BCRYPT);
        $sql = $conn->prepare("INSERT INTO `b-official`
            (fname, mname, lname, number, barangay, position, age, dateofbirth, address, picture, email, password, gender)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param(
            "ssssssissssss",
            $fname, $mname, $lname, $number, $barangay, $position,
            $age, $dateofbirth, $address, $picturePathForDB, $email, $passwordHashed, $gender
        );
        if ($sql->execute()) {
            $successMsg = "Official added successfully!";
        } else {
            $errorMsg = "Database error: " . $conn->error;
        }
        $sql->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Official</title>
    <link rel="stylesheet" href="/styles/styles.css">
    <style>
        .add-off-form {
            background: #fff;
            border-radius: 10px;
            max-width: 700px;
            margin: 30px auto;
            box-shadow: 0 4px 24px #0001;
            padding: 38px 34px 22px 34px;
        }
        .add-off-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 19px 24px;
        }
        .add-off-form-grid label {
            display: flex;
            flex-direction: column;
            font-weight: bold;
            color: #444;
            font-size: 15px;
        }
        .add-off-form-grid input,
        .add-off-form-grid select {
            margin-top: 4px;
            padding: 10px;
            border-radius: 5px;
            border: 1.5px solid #ccc;
            font-size: 16px;
        }
        .add-off-form-grid input[type="file"] {
            padding: 3px 0 0 0;
        }
        .add-off-form .form-actions {
            margin-top: 22px;
            text-align: center;
            grid-column: span 2;
        }
        .add-off-form button {
            background: #043CA7;
            color: #fff;
            border: none;
            padding: 12px 38px;
            border-radius: 6px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: background .16s;
        }
        .add-off-form button:hover { background: #ffe600; color: #043CA7; }
        .add-off-form .image-preview {
            width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border:2px solid #043CA7; margin-top:6px; background:#f4f4f4;
        }
        .message.success { background:#d4edda; color:#155724; padding:12px; margin-bottom:14px; border-radius:5px; text-align:center; }
        .message.error { background:#f8d7da; color:#721c24; padding:12px; margin-bottom:14px; border-radius:5px; text-align:center; }
        @media (max-width: 700px) {
            .add-off-form { padding: 13px 3vw 8vw 3vw; }
            .add-off-form-grid { grid-template-columns: 1fr; gap: 13px 0; }
        }
    </style>
</head>
<body>

<?php if (!empty($successMsg)): ?>
    <div class="message success"><?= htmlspecialchars($successMsg) ?></div>
<?php endif; ?>
<?php if (!empty($uploadError)): ?>
    <div class="message error"><?= htmlspecialchars($uploadError) ?></div>
<?php endif; ?>
<?php if (!empty($errorMsg)): ?>
    <div class="message error"><?= htmlspecialchars($errorMsg) ?></div>
<?php endif; ?>

<form action="add-off.php" method="POST" enctype="multipart/form-data" class="add-off-form" autocomplete="off">
    <div class="add-off-form-grid">
        <label>First Name:
            <input type="text" name="fname" required value="<?= htmlspecialchars($_POST['fname'] ?? '') ?>">
        </label>
        <label>Middle Name:
            <input type="text" name="mname" required value="<?= htmlspecialchars($_POST['mname'] ?? '') ?>">
        </label>
        <label>Last Name:
            <input type="text" name="lname" required value="<?= htmlspecialchars($_POST['lname'] ?? '') ?>">
        </label>
        <label>Gender:
            <select name="gender" required>
                <option value="">Select gender</option>
                <option value="Male" <?= (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
            </select>
        </label>
        <label>Contact Number:
            <input type="text" name="number" required value="<?= htmlspecialchars($_POST['number'] ?? '') ?>">
        </label>
        <label>Barangay:
            <input type="text" name="barangay" required value="<?= htmlspecialchars($_POST['barangay'] ?? '') ?>">
        </label>
        <label>Position:
            <input type="text" name="position" required value="<?= htmlspecialchars($_POST['position'] ?? '') ?>">
        </label>
        <label>Age:
            <input type="number" name="age" min="1" required value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
        </label>
        <label>Date of Birth:
            <input type="date" name="dateofbirth" required value="<?= htmlspecialchars($_POST['dateofbirth'] ?? '') ?>">
        </label>
        <label>Address:
            <input type="text" name="address" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        </label>
        <label>Email:
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </label>
        <label>Password:
            <input type="password" name="password" required>
        </label>
        <label>Picture:
            <input type="file" name="picture" accept="image/*" onchange="previewOfficialImage(event)" required>
            <img id="officialPreview" class="image-preview" style="display:none;" alt="Preview">
        </label>
    </div>
    <div class="form-actions">
        <button type="submit">Add Official</button>
    </div>
</form>

<script>
function previewOfficialImage(event) {
    const input = event.target;
    const preview = document.getElementById('officialPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = "";
        preview.style.display = 'none';
    }
}

// If file input has value after error (like on validation), show preview (works in modern browsers)
<?php if (!empty($_FILES['picture']['tmp_name'])): ?>
    previewOfficialImage({target: document.querySelector('input[name="picture"]')});
<?php endif; ?>
</script>

</body>
</html>

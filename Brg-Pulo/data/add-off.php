<?php
$successMsg = "";
$uploadError = "";
$errorMsg = "";

// Only process if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "brg-pulo";

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("<div class='error'>Connection failed: " . $conn->connect_error . "</div>");
    }

    // Safely get POST data
    $fname       = isset($_POST['fname']) ? $conn->real_escape_string($_POST['fname']) : "";
    $mname       = isset($_POST['mname']) ? $conn->real_escape_string($_POST['mname']) : "";
    $lname       = isset($_POST['lname']) ? $conn->real_escape_string($_POST['lname']) : "";
    $number      = isset($_POST['number']) ? $conn->real_escape_string($_POST['number']) : "";
    $barangay    = isset($_POST['barangay']) ? $conn->real_escape_string($_POST['barangay']) : "";
    $position    = isset($_POST['position']) ? $conn->real_escape_string($_POST['position']) : "";
    $age         = isset($_POST['age']) ? (int)$_POST['age'] : 0;
    $dateofbirth = isset($_POST['dateofbirth']) ? $conn->real_escape_string($_POST['dateofbirth']) : "";
    $address     = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : "";
    $email       = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : "";
    $passwordPlain = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : "";

    // Upload
    $targetDir = __DIR__ . "/uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $pictureName = isset($_FILES["picture"]["name"]) ? basename($_FILES["picture"]["name"]) : "";
    $targetFile = $targetDir . $pictureName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];

    if (empty($pictureName)) {
        $uploadError = "Please upload an image file.";
    } else {
        $check = @getimagesize($_FILES["picture"]["tmp_name"]);
        if ($check === false) {
            $uploadError = "File is not a valid image.";
        } elseif (!in_array($imageFileType, $allowedTypes)) {
            $uploadError = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (!move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
            $uploadError = "Error uploading the image.";
        }
    }

    if ($uploadError === "" && $fname && $lname) {
        $picturePathForDB = "data/uploads/" . $pictureName;
        $sql = "INSERT INTO `b-official` (
                    fname, mname, lname, number, barangay, position,
                    age, dateofbirth, address, picture, email, password
                ) VALUES (
                    '$fname', '$mname', '$lname', '$number', '$barangay', '$position',
                    $age, '$dateofbirth', '$address', '$picturePathForDB', '$email', '$passwordPlain'
                )";
        if ($conn->query($sql) === TRUE) {
            $successMsg = "Official added successfully!";
        } else {
            $errorMsg = "Database error: " . $conn->error;
        }
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
            max-width: 680px;
            margin: 30px auto;
            box-shadow: 0 4px 24px #0001;
            padding: 35px 30px 22px 30px;
        }
        .add-off-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 22px;
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
            margin-top: 18px;
            text-align: center;
            grid-column: span 2;
        }
        .add-off-form button {
            background: #043CA7;
            color: #fff;
            border: none;
            padding: 12px 32px;
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
        @media (max-width: 600px) {
            .add-off-form { padding: 15px 4vw 8vw 4vw; }
            .add-off-form-grid { grid-template-columns: 1fr; gap: 11px 0; }
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
            <input type="text" name="fname" required>
        </label>
        <label>Middle Name:
            <input type="text" name="mname" required>
        </label>
        <label>Last Name:
            <input type="text" name="lname" required>
        </label>
        <label>Contact Number:
            <input type="text" name="number" required>
        </label>
        <label>Barangay:
            <input type="text" name="barangay" required>
        </label>
        <label>Position:
            <input type="text" name="position" required>
        </label>
        <label>Age:
            <input type="number" name="age" min="1" required>
        </label>
        <label>Date of Birth:
            <input type="date" name="dateofbirth" required>
        </label>
        <label>Address:
            <input type="text" name="address" required>
        </label>
        <label>Email:
            <input type="email" name="email" required>
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
</script>

</body>
</html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete = $conn->query("DELETE FROM `b-official` WHERE id = $id");
    if ($delete) {
        echo "<script>alert('Official deleted successfully!'); window.location='../Brg-Pulo/admin.php';</script>";
    } else {
        echo "Delete failed: " . $conn->error;
    }
    exit();
}

// UPDATE
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $number = $_POST['number'];
    $barangay = $_POST['barangay'];
    $position = $_POST['position'];
    $age = $_POST['age'];
    $dob = $_POST['dateofbirth'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($_FILES['picture']['name']) {
        $target = "data/uploads/" . basename($_FILES['picture']['name']);
        move_uploaded_file($_FILES['picture']['tmp_name'], $target);
        $picture = $target;
        $sql = "UPDATE `b-official` SET fname='$fname', mname='$mname', lname='$lname', number='$number', barangay='$barangay', position='$position', age='$age', dateofbirth='$dob', address='$address', email='$email', password='$password', picture='$picture' WHERE id=$id";
    } else {
        $sql = "UPDATE `b-official` SET fname='$fname', mname='$mname', lname='$lname', number='$number', barangay='$barangay', position='$position', age='$age', dateofbirth='$dob', address='$address', email='$email', password='$password' WHERE id=$id";
    }

    if ($conn->query($sql)) {
        echo "<script>alert('Official updated successfully!'); window.location='../Brg-Pulo/admin.php';</script>";
    } else {
        echo "Update failed: " . $conn->error;
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Barangay Officials</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        h2 {
            color: #043CA7;
            margin: 30px 0 15px 20px;
            font-size: 2rem;
        }
        .officials-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 22px;
            margin: 0 0 40px 0;
            padding-left: 24px;
        }
        .official-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 14px #043CA72a;
            width: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 22px 14px 18px 14px;
            position: relative;
            transition: box-shadow .16s, transform .16s;
            margin-bottom: 18px;
        }
        .official-card:hover {
            box-shadow: 0 8px 22px #043CA742;
            transform: translateY(-3px) scale(1.015);
        }
        .official-photo {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #043CA7;
            margin-bottom: 14px;
            background: #f7f7f7;
        }
        .official-name {
            font-size: 1.12rem;
            font-weight: bold;
            color: #043CA7;
            margin-bottom: 10px;
            text-align: center;
        }
        .view-btn {
            background: #043CA7;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 4px;
            transition: background .16s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .view-btn:hover,
        .view-btn:focus-visible {
            background: #ffe600;
            color: #043CA7;
        }
        @media (max-width: 800px) {
            .officials-grid {
                gap: 11px;
                justify-content: center;
                padding-left: 0;
            }
            .official-card {
                width: 96vw;
                max-width: 340px;
            }
        }

        /* Modal styles */
        #officialModal.modal {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.32);
            z-index: 1500;
            justify-content: center;
            align-items: center;
        }
        #officialModal .modal-content {
            background: #fff;
            padding: 30px 25px 25px 25px;
            border-radius: 15px;
            width: 90vw;
            max-width: 350px;
            box-shadow: 0 10px 40px #043CA780;
            position: relative;
            animation: modalPop .18s cubic-bezier(.34,1.47,.63,1.07);
        }
        @keyframes modalPop {
            0% { transform: scale(.92) translateY(16px); opacity: 0; }
            100% { transform: scale(1) translateY(0); opacity: 1; }
        }
        #officialModal .close-btn {
            position: absolute; top: 11px; right: 16px; font-size: 23px;
            background: none; border: none; color: #bbb;
            cursor: pointer; transition: color .18s;
        }
        #officialModal .close-btn:hover,
        #officialModal .close-btn:focus-visible { color: #043CA7; }
        .official-photo-lg {
            width: 96px; height: 96px; object-fit: cover;
            border-radius: 50%; border: 3px solid #043CA7;
            margin-bottom: 15px;
            display: block; margin-left: auto; margin-right: auto;
        }
        #officialModal h3 { margin-top: 0; color: #043CA7; text-align: center; }
        #officialModal .modal-body p { font-size: 15px; margin-bottom: 8px; }
        .modal-actions {
            margin-top: 18px;
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .modal-actions button, .modal-actions a {
            padding: 7px 18px;
            border-radius: 7px;
            border: none;
            background: #043CA7;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: background .14s;
        }
        .modal-actions button:hover, .modal-actions a:hover {
            background: #ffe600;
            color: #043CA7;
        }
        /* Edit Modal Styles */
        .modal-overlay {
            display:none; position: fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.22); z-index:2000;
        }
        .modal-window {
            display:none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background:#fff; z-index:2010; max-width:350px; width:95vw; padding:20px 18px 15px 18px; border-radius:14px; box-shadow:0 8px 28px #0002;
        }
        .modal-window input[type="text"], .modal-window input[type="number"], .modal-window input[type="email"], .modal-window input[type="date"] {
            width:100%; padding:8px; margin:7px 0 13px 0; border-radius:5px; border:1px solid #ccc; font-size:15px;
        }
        .modal-window label { font-weight:bold; color:#444; font-size:14px; }
        .modal-window button[type="submit"], .modal-window button[type="button"] {
            padding:8px 15px; border-radius:6px; border:none; margin-top:8px;
            background:#043CA7; color:#fff; font-weight:bold; cursor:pointer; transition:background .14s;
        }
        .modal-window button[type="submit"]:hover, .modal-window button[type="button"]:hover {
            background:#ffe600; color:#043CA7;
        }
    </style>
</head>
<body>

<h2>Barangay Officials</h2>

<div class="officials-grid">
<?php
$result = $conn->query("SELECT * FROM `b-official`");
while ($row = $result->fetch_assoc()):
    $fullname = htmlspecialchars($row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']);
    ?>
    <div class="official-card">
        <img src="<?= htmlspecialchars($row['picture']) ?>" class="official-photo" alt="<?= $fullname ?>">
        <div class="official-name"><?= $fullname ?></div>
        <button class="view-btn"
          data-id="<?= $row['id'] ?>"
          data-fname="<?= htmlspecialchars($row['fname']) ?>"
          data-mname="<?= htmlspecialchars($row['mname']) ?>"
          data-lname="<?= htmlspecialchars($row['lname']) ?>"
          data-position="<?= htmlspecialchars($row['position']) ?>"
          data-barangay="<?= htmlspecialchars($row['barangay']) ?>"
          data-number="<?= htmlspecialchars($row['number']) ?>"
          data-age="<?= htmlspecialchars($row['age']) ?>"
          data-dateofbirth="<?= htmlspecialchars($row['dateofbirth']) ?>"
          data-address="<?= htmlspecialchars($row['address']) ?>"
          data-email="<?= htmlspecialchars($row['email']) ?>"
          data-password="<?= htmlspecialchars($row['password']) ?>"
          data-photo="<?= htmlspecialchars($row['picture']) ?>">
          <i class="fas fa-eye"></i> View
        </button>
    </div>
<?php endwhile; ?>
</div>

<!-- Official Details Modal -->
<div class="modal" id="officialModal">
  <div class="modal-content">
    <button class="close-btn" onclick="closeOfficialModal()">&times;</button>
    <div class="modal-body">
      <img id="modalPhoto" class="official-photo-lg" src="" alt="Official Photo">
      <h3 id="modalName"></h3>
      <p><b>Position:</b> <span id="modalPosition"></span></p>
      <p><b>Barangay:</b> <span id="modalBarangay"></span></p>
      <p><b>Contact Number:</b> <span id="modalContact"></span></p>
      <p><b>Age:</b> <span id="modalAge"></span></p>
      <p><b>Date of Birth:</b> <span id="modalDOB"></span></p>
      <p><b>Address:</b> <span id="modalAddress"></span></p>
      <p><b>Email:</b> <span id="modalEmail"></span></p>
      <p><b>Password:</b> <span id="modalPassword"></span></p>
      <div class="modal-actions">
        <button id="editBtn">Edit</button>
        <a id="deleteBtn" href="#">Delete</a>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal (pre-filled form, now in 2 columns) -->
<div id="overlay" class="modal-overlay"></div>
<div id="editModal" class="modal-window">
    <form method="POST" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="id" id="edit-id">
        <div class="edit-form-grid">
            <label>First Name:
                <input type="text" name="fname" id="edit-fname" required>
            </label>
            <label>Middle Name:
                <input type="text" name="mname" id="edit-mname" required>
            </label>
            <label>Last Name:
                <input type="text" name="lname" id="edit-lname" required>
            </label>
            <label>Number:
                <input type="text" name="number" id="edit-number" required>
            </label>
            <label>Barangay:
                <input type="text" name="barangay" id="edit-barangay" required>
            </label>
            <label>Position:
                <input type="text" name="position" id="edit-position" required>
            </label>
            <label>Age:
                <input type="number" name="age" id="edit-age" required>
            </label>
            <label>Date of Birth:
                <input type="date" name="dateofbirth" id="edit-dob" required>
            </label>
            <label>Address:
                <input type="text" name="address" id="edit-address" required>
            </label>
            <label>Email:
                <input type="email" name="email" id="edit-email" required>
            </label>
            <label>Password:
                <input type="text" name="password" id="edit-password" required>
            </label>
            <label>Change Picture:
                <input type="file" name="picture">
            </label>
        </div>
        <button type="submit" name="update">Update</button>
        <button type="button" onclick="closeModal()">Cancel</button>
    </form>
</div>


<script>
function closeOfficialModal() {
    document.getElementById('officialModal').style.display = 'none';
}
// Show details modal
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('modalPhoto').src = this.dataset.photo;
        document.getElementById('modalName').textContent = this.dataset.fname + " " + this.dataset.mname + " " + this.dataset.lname;
        document.getElementById('modalPosition').textContent = this.dataset.position;
        document.getElementById('modalBarangay').textContent = this.dataset.barangay;
        document.getElementById('modalContact').textContent = this.dataset.number;
        document.getElementById('modalAge').textContent = this.dataset.age;
        document.getElementById('modalDOB').textContent = this.dataset.dateofbirth;
        document.getElementById('modalAddress').textContent = this.dataset.address;
        document.getElementById('modalEmail').textContent = this.dataset.email;
        document.getElementById('modalPassword').textContent = this.dataset.password;
        document.getElementById('officialModal').style.display = 'flex';

        // Set Edit/Delete for this id
        document.getElementById('editBtn').onclick = () => openEditModal({
            id: this.dataset.id,
            fname: this.dataset.fname,
            mname: this.dataset.mname,
            lname: this.dataset.lname,
            number: this.dataset.number,
            barangay: this.dataset.barangay,
            position: this.dataset.position,
            age: this.dataset.age,
            dateofbirth: this.dataset.dateofbirth,
            address: this.dataset.address,
            email: this.dataset.email,
            password: this.dataset.password
        });
        document.getElementById('deleteBtn').href = "?delete=" + this.dataset.id;
        document.getElementById('deleteBtn').onclick = function() {
            return confirm('Are you sure you want to delete this official?');
        }
    });
});
// ESC and click-outside for modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeOfficialModal();
        closeModal();
    }
});
document.getElementById('officialModal').addEventListener('click', function(e) {
    if (e.target === this) closeOfficialModal();
});
document.getElementById('overlay').addEventListener('click', function() {
    closeModal();
});
// Edit modal logic as before:
function openEditModal(data) {
    closeOfficialModal();
    document.getElementById('edit-id').value = data.id;
    document.getElementById('edit-fname').value = data.fname;
    document.getElementById('edit-mname').value = data.mname;
    document.getElementById('edit-lname').value = data.lname;
    document.getElementById('edit-number').value = data.number;
    document.getElementById('edit-barangay').value = data.barangay;
    document.getElementById('edit-position').value = data.position;
    document.getElementById('edit-age').value = data.age;
    document.getElementById('edit-dob').value = data.dateofbirth;
    document.getElementById('edit-address').value = data.address;
    document.getElementById('edit-email').value = data.email;
    document.getElementById('edit-password').value = data.password;
    document.getElementById('editModal').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
}
function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}
</script>

</body>
</html>

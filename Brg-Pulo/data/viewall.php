<?php
$conn = new mysqli("localhost", "root", "", "brg-pulo");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM `res-info` WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('Resident deleted successfully.'); window.location.href = 'admin.php';</script>";
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $conn->prepare("UPDATE `res-info` SET fname=?, mname=?, lname=?, age=?, dob=?, `c-status`=?, number=? WHERE id=?");
    $stmt->bind_param(
        "sssssssi",
        $_POST['fname'],
        $_POST['mname'],
        $_POST['lname'],
        $_POST['age'],
        $_POST['dob'],
        $_POST['c_status'],
        $_POST['number'],
        $_POST['edit_id']
    );
    $stmt->execute();
    echo "<script>alert('Resident updated successfully.'); window.location.href = 'admin.php';</script>";
    exit();
}

// If edit is triggered, get resident data
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM `res-info` WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $editData = $result->fetch_assoc();
    }
}

// Fetch all residents
$sql = "SELECT * FROM `res-info` ORDER BY purok, lname, fname";
$result = $conn->query($sql);
$residents = [];
$puroks = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
        if (!in_array($row['purok'], $puroks)) {
            $puroks[] = $row['purok'];
        }
    }
}
?>

<<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f4f6fb;
    margin: 0;
    color: #22223b;
}

.resident-table-container {
    max-width: 1100px;
    margin: 35px auto 0 auto;
    padding: 32px 16px 70px 16px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 4px 32px rgba(0,0,0,0.11);
    min-height: 60vh;
}

.resident-table-container h2 {
    text-align: center;
    margin-bottom: 36px;
    font-size: 2.3rem;
    font-weight: 700;
    color: #24305e;
    letter-spacing: .5px;
}

.purok-box {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    background: linear-gradient(120deg, #7b9acc 40%, #b4c5e4 100%);
    color: #fff;
    padding: 28px 0 18px 0;
    margin: 18px 18px 14px 0;
    border-radius: 14px;
    cursor: pointer;
    width: 190px;
    min-height: 70px;
    font-size: 1.18rem;
    font-weight: 500;
    box-shadow: 0 2px 14px rgba(120,120,160,0.06);
    transition: transform 0.14s cubic-bezier(.4,0,.2,1), box-shadow .2s;
    border: 2px solid #7b9acc20;
}
.purok-box:hover {
    background: linear-gradient(120deg, #537188 70%, #b4c5e4 100%);
    box-shadow: 0 8px 32px #7b9acc33;
    transform: translateY(-4px) scale(1.04);
}

.modal-overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(30, 41, 59, 0.45);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 5000;
    transition: background .3s;
}
.modal-overlay.active { display: flex; }

.modal-content {
    background: #fff;
    padding: 34px 24px 26px 24px;
    width: 96vw;
    max-width: 930px;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 4px 32px rgba(0,0,0,.13);
    animation: modalPop .23s cubic-bezier(.4,0,.2,1);
}
@keyframes modalPop {
    from { transform: scale(.90); opacity: 0;}
    to { transform: scale(1); opacity: 1;}
}

.modal-close {
    position: absolute; top: 18px; right: 26px;
    font-size: 29px;
    color: #47597e;
    cursor: pointer;
    font-weight: 700;
    transition: color .15s;
}
.modal-close:hover { color: #d7263d; }

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 0.3em;
    background: transparent;
    font-size: 1.05rem;
}
th, td {
    padding: 11px 10px;
    text-align: left;
}
thead th {
    background: #eff4fa;
    color: #4d6086;
    font-weight: 700;
    border-bottom: 2px solid #b7c0d6;
}
tbody tr {
    background: #f7faff;
    transition: box-shadow .13s;
    border-radius: 11px;
    box-shadow: 0 1px 10px #e3e9fa10;
}
tbody tr:hover {
    background: #e0e6f7;
    box-shadow: 0 4px 16px #a6bbdc33;
}
tbody td:first-child { width: 60px; }
tbody td img {
    border-radius: 50%;
    box-shadow: 0 2px 7px #a3a3a314;
}
.button, a.button {
    background: linear-gradient(90deg,#667eea 20%, #6dd5ed 100%);
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: 7px 15px;
    margin-right: 5px;
    font-size: 15px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    box-shadow: 0 1px 6px #7b9acc12;
    transition: background .18s;
    outline: none;
}
.button:hover, a.button:hover {
    background: linear-gradient(90deg, #415a99 70%, #6dd5ed 100%);
}

.edit-form input {
    width: 100%;
    margin: 10px 0 18px 0;
    padding: 12px 13px;
    border-radius: 7px;
    border: 1px solid #c4c6d6;
    font-size: 1.06rem;
    outline: none;
    transition: border .13s;
    background: #f6f7fa;
}
.edit-form input:focus {
    border: 1.5px solid #667eea;
    background: #fff;
}
.edit-form h3 {
    margin-bottom: 22px;
    color: #24305e;
    font-size: 1.37rem;
    font-weight: 700;
    letter-spacing: .3px;
}

@media screen and (max-width: 980px) {
    .modal-content, .resident-table-container { padding: 12px 2vw 14px 2vw; }
    .modal-content { max-width: 98vw; }
    table, th, td { font-size: 13px;}
    .resident-table-container { border-radius: 11px; }
    .purok-box { width: 98vw; margin: 7px 0; }
}
@media screen and (max-width: 600px) {
    .modal-content, .resident-table-container { padding: 5vw 1vw 2vw 1vw; }
    .modal-content { border-radius: 12px; }
    .resident-table-container { border-radius: 5px; }
    .purok-box { font-size: 1rem; border-radius: 7px;}
}
::-webkit-scrollbar { width: 7px; background: #e4e8f0; border-radius: 8px; }
::-webkit-scrollbar-thumb { background: #c7d6e8; border-radius: 8px; }
</style>

<div class="resident-table-container">
    <h2>All Residents by Purok</h2>
    <?php foreach ($puroks as $purok): ?>
        <div class="purok-box" onclick="openPurokModal('modal_<?= $purok ?>')">
            <span style="font-size:2.1rem; margin-bottom: 6px;">🏡</span>
            <strong>Purok <?= htmlspecialchars($purok) ?></strong>
        </div>

        <div id="modal_<?= $purok ?>" class="modal-overlay">
            <div class="modal-content">
                <span class="modal-close">&times;</span>
                <h2 style="margin-top:-6px; margin-bottom:19px; font-size:1.36rem; color:#33416b;">
                    Residents in Purok <?= htmlspecialchars($purok) ?>
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>DOB</th>
                            <th>Status</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($residents as $person): ?>
                            <?php if ($person['purok'] == $purok): ?>
                                <tr>
                                    <td>
                                        <img src="<?= $person['profile'] ?: 'images/sub/usericon.png' ?>"
                                             width="40" height="40"
                                             style="border-radius:50%; object-fit:cover; border: 2px solid #bbc8dd;">
                                    </td>
                                    <td><?= htmlspecialchars($person['fname'] . ' ' . $person['mname'] . ' ' . $person['lname']) ?></td>
                                    <td><?= htmlspecialchars($person['age']) ?></td>
                                    <td><?= htmlspecialchars($person['dob']) ?></td>
                                    <td><?= htmlspecialchars($person['c-status']) ?></td>
                                    <td><?= htmlspecialchars($person['number']) ?></td>
                                    <td>
                                        <a href="admin.php?edit=<?= $person['id'] ?>" class="button">Edit</a>
                                        <a href="admin.php?delete=<?= $person['id'] ?>" class="button"
                                           style="background: linear-gradient(90deg,#ee6983 10%, #d7263d 90%);"
                                           onclick="return confirm('Delete this resident?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
function openPurokModal(id) {
    document.getElementById(id).classList.add('active');
}
document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', function () {
        const modal = this.closest('.modal-overlay');
        modal.classList.remove('active');
    });
});
document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
</script>

<?php if ($editData): ?>
<!-- Floating Edit Modal -->
<div class="modal-overlay active" id="editModal">
    <div class="modal-content edit-form">
        <span class="modal-close" onclick="closeEditModal()">&times;</span>
        <h3>Edit Resident</h3>
        <form method="POST" action="admin.php" autocomplete="off">
            <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
            <input type="text" name="fname" value="<?= htmlspecialchars($editData['fname']) ?>" placeholder="First Name" required>
            <input type="text" name="mname" value="<?= htmlspecialchars($editData['mname']) ?>" placeholder="Middle Name" required>
            <input type="text" name="lname" value="<?= htmlspecialchars($editData['lname']) ?>" placeholder="Last Name" required>
            <input type="number" name="age" value="<?= htmlspecialchars($editData['age']) ?>" placeholder="Age" min="0" required>
            <input type="date" name="dob" value="<?= htmlspecialchars($editData['dob']) ?>" required>
            <input type="text" name="c_status" value="<?= htmlspecialchars($editData['c-status']) ?>" placeholder="Civil Status" required>
            <input type="text" name="number" value="<?= htmlspecialchars($editData['number']) ?>" placeholder="Contact Number" required>
            <button type="submit" class="button">💾 Save Changes</button>
        </form>
    </div>
</div>
<script>
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    window.location.href = 'admin.php';
}
</script>
<?php endif; ?>
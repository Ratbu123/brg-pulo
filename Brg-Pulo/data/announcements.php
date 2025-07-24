<?php
// Start the session and connect to the database

$conn = new mysqli("localhost", "root", "", "brg-pulo");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle announcement form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["title"], $_POST["content"])) {
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);

    if ($title !== "" && $content !== "") {
        // Use prepared statements for safety
        $stmt = $conn->prepare("INSERT INTO announcements (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        if ($stmt->execute()) {
            $message = "Announcement posted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Title and Content cannot be empty.";
    }
}


// Fetch announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Announcements</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
        body {
            background: linear-gradient(120deg,#e3f0ff 30%, #fff 80%);
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }
        .container {
            max-width: 580px;
            margin: 40px auto 0 auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 32px #59c3c353, 0 2px 4px #a3b8c5;
            padding: 34px 25px 30px 25px;
        }
        h2 {
            color: #194376;
            text-align: center;
            font-size: 2rem;
            margin-bottom: 22px;
            letter-spacing: .5px;
            font-weight: 800;
        }
        form {
            background: #f4f9fd;
            border-radius: 14px;
            box-shadow: 0 1px 4px #72b8d130;
            padding: 24px 18px 16px 18px;
            margin-bottom: 32px;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 13px 11px;
            margin: 9px 0 18px 0;
            border-radius: 7px;
            border: 1.2px solid #a7bbd6;
            font-size: 1rem;
            background: #f8fbff;
            outline: none;
            transition: border .18s;
        }
        input[type="text"]:focus,
        textarea:focus {
            border-color: #46a6ff;
            background: #fff;
        }
        button {
            background: linear-gradient(90deg,#2196f3 60%, #67d9f9 100%);
            color: white;
            padding: 11px 32px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 1px;
            box-shadow: 0 2px 7px #67d9f955;
            transition: background .14s;
        }
        button:hover {
            background: linear-gradient(90deg,#1768ac 70%, #60d1fb 100%);
        }
        .message {
            margin: 18px 0 18px 0;
            color: #4caf50;
            text-align: center;
            font-weight: 600;
            font-size: 1.08rem;
        }
        .announcement {
            background: linear-gradient(100deg, #f9fdff 50%, #e6f4fa 100%);
            padding: 19px 18px 13px 22px;
            margin-bottom: 23px;
            border-left: 7px solid #2196f3;
            border-radius: 10px;
            box-shadow: 0 2px 9px #2196f325;
            transition: box-shadow .18s;
        }
        .announcement:hover {
            box-shadow: 0 6px 18px #2196f344;
        }
        .announcement h3 {
            margin: 0 0 6px 0;
            color: #1260a0;
            font-size: 1.13rem;
            font-weight: 700;
            letter-spacing: .1px;
        }
        .announcement p {
            margin: 6px 0 0 0;
            color: #222e;
            line-height: 1.5;
        }
        .date {
            font-size: 0.93em;
            color: #7093b4;
            margin-top: 9px;
            letter-spacing: .4px;
        }
        @media (max-width: 650px) {
            .container { max-width: 96vw; padding: 5vw 2vw 6vw 2vw; border-radius: 10px;}
            form { padding: 5vw 3vw 3vw 3vw;}
            h2 { font-size: 1.33rem;}
            .announcement { font-size: 15px;}
        }
        ::selection { background: #aee6ff; }
    </style>
</head>
<body>
<div class="container">

    <h2>Barangay Announcements</h2>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="" autocomplete="off">
        <input type="text" name="title" maxlength="80" placeholder="Announcement Title" required>
        <textarea name="content" rows="4" maxlength="4000" placeholder="Write your announcement here..." required></textarea>
        <div style="text-align:center;">
            <button type="submit">+ Post Announcement</button>
        </div>
    </form>

    <h2 style="font-size:1.14rem; font-weight:700; margin-bottom:18px; margin-top:32px; color:#2b5278; letter-spacing:.6px;">
        Recent Announcements
    </h2>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="announcement">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
            <div class="date">📅 Posted on <?= date("F j, Y, g:i a", strtotime($row['date_posted'])) ?></div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>

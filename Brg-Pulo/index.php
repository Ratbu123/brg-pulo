<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $date = $_POST['date']; // Comes from readonly field

  $stmt = $conn->prepare("INSERT INTO request (name, date) VALUES (?, ?)");
  $stmt->bind_param("ss", $name, $date);

  if ($stmt->execute()) {
    $message = "<p class='success-msg'>Request submitted successfully!</p>";
  } else {
    $message = "<p class='error-msg'>Error: " . $stmt->error . "</p>";
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Brgy. Bugtong na Pulo</title>
  <link rel="stylesheet" href="./styles/indexstyle.css" />
</head>
<body>

  <header class="navbar">
    <div class="container">
      <div class="logo">
        <img src="./images/LipaLogo.png" alt="Barangay Logo" />
        <img src="./images/OfficialSeal.png" alt="Barangay Logo" />
        <span>Brgy. Bugtong na Pulo</span>
      </div>
      <nav>
        <ul>
          <li><a href="#home">Home</a></li>
          <li><a href="#announcements">Announcements</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section id="home" class="hero">
    <div class="overlay">
      <h1>Maligayang Araw mga KABARANGGAY!</h1>
    </div>
  </section>

  <section id="announcements" class="section announcements">
    <h2>Announcements</h2>
    <div class="cards">
      <div class="card yellow">
        <h3>We're Hiring</h3>
        <p>Looking for committed individuals for barangay support roles.</p>
        <button>Learn More</button>
      </div>
      <div class="card red">
        <h3>Hiring Volunteers</h3>
        <p>Join our Fire & Rescue Volunteer Program!</p>
        <button>Apply Now</button>
      </div>
      <div class="card blue">
        <h3>Health Workers Needed</h3>
        <p>Seeking licensed professionals to serve the community.</p>
        <button>Join Now</button>
      </div>
    </div>
    <button class="more-btn">Show more</button>
  </section>

  <section id="services" class="section services">
    <h2>Services</h2>
    <div class="cards">
      <div class="service-card">
        <h3>Emergency Hotline</h3>
        <button onclick="openModal('Emergency Hotline')">View</button>
      </div>
    </div>
  </section>

  <section class="about-welcome">
    <div class="welcome">
      <h2>Welcome to <br> Brgy. Bugtong na Pulo</h2>
      <div class="logos">
        <img src="./images/LipaLogo.png" alt="Logo 1">
        <img src="./images/OfficialSeal.png" alt="Logo 2">
        <img src="./images/LipaLogo.png" alt="Logo 3">
      </div>
    </div>
    <div class="about">
      <h2>ABOUT US</h2>
      <p>Brgy. Bugtong na Pulo is a proud community in Batangas committed to delivering efficient, honest, and compassionate public service for all residents. We aim to lead in innovation and unity for a better tomorrow.</p>
    </div>
  </section>

  <section id="contact" class="section contact">
    <h2>Contact Us</h2>
    <p>For inquiries or assistance, reach out using the details below or the form provided.</p>
    <div class="contact-grid">
      <ul>
        <li>📘 facebook.com/buqtpnp</li>
        <li>📧 barangay@bpulo.gov</li>
        <li>📱 0927-123-4567</li>
      </ul>
      <form>
        <input type="text" placeholder="Your Name" required>
        <input type="email" placeholder="Your Email" required>
        <textarea placeholder="Your Message" required></textarea>
        <button type="submit">Send</button>
      </form>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Barangay Bugtong na Pulo. All Rights Reserved.</p>
  </footer>

  <!-- Floating Modal Form -->
  <div id="formModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <h3 id="formTitle">Service Request Form</h3>

      <?php echo $message; ?>

      <form action="" method="POST">
        <label for="name">Name</label>
      <input type="text" id="name" name="name" required>

      <label for="date">Date of Request</label>
      <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" readonly>

      <label for="certificate">Certificate Type</label>
      <select id="certificate" name="certificate" required>
        <option value="Certificate of indigency">Certificate of Indigency</option>
        <option value="Certificate of residency">Certificate of Residency</option>
      </select>

        <button type="submit">Submit</button>
      </form>
    </div>
  </div>

  <script>
    function openModal(serviceName) {
      document.getElementById('formTitle').innerText = serviceName + " Request Form";
      document.getElementById('formModal').style.display = 'block';
    }

    function closeModal() {
      document.getElementById('formModal').style.display = 'none';
    }

    window.onclick = function(event) {
      const modal = document.getElementById('formModal');
      if (event.target == modal) {
        closeModal();
      }
    }
  </script>

</body>
</html>

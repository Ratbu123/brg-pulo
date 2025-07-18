<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employer Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Main Styles -->
  <link rel="stylesheet" href="../Brg-Pulo/styles/styles.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Hamburger Button (Mobile Only) -->
  <button class="sidebar-toggle" id="sidebarToggle" aria-label="Open Sidebar" aria-controls="sidebar" aria-expanded="false" tabindex="0">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
  </button>

  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar" aria-label="Sidebar Navigation">
      <div class="sidebar-header">
        <img src="images/sub/usericon.png" class="icon" alt="Employer Icon">
        <p>Hello,<br><strong>Admin</strong></p>
      </div>
      <nav aria-label="Main">
        <a class="active" data-section="dashboard" tabindex="0">
          <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a data-section="search" tabindex="0">
          <i class="fas fa-magnifying-glass"></i> Search Residents
        </a>
        <a data-section="populations" tabindex="0">
          <i class="fas fa-users"></i> Populations
        </a>
        <a data-section="add-off" tabindex="0">
          <i class="fas fa-user-plus"></i> Add Officials
        </a>
        <a data-section="officials" tabindex="0">
          <i class="fas fa-user-tie"></i> Brg. Official
        </a>
        <a data-section="request" tabindex="0">
          <i class="fas fa-envelope"></i> Requests
        </a>
        <a data-section="add" tabindex="0">
          <i class="fas fa-house-user"></i> Add Residents
        </a>
        <a data-section="hire" tabindex="0">
          <i class="fas fa-briefcase"></i> Job Posting
        </a>
        <a data-section="announce" tabindex="0">
          <i class="fas fa-bullhorn"></i> Announcement
        </a>
      </nav>
    </aside>

    <!-- Overlay for sidebar (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Top Bar -->
    <header class="topbar" role="banner">
      <div class="spacer"></div>
      <div class="top-icons">
        <button class="notif-btn" aria-label="Notifications">
          <i class="fas fa-bell"></i>
          <!-- Uncomment to show a badge -->
          <!-- <span class="notif-badge">3</span> -->
        </button>
        <button class="logout-btn" onclick="location.href='LogIn.php'">
          <i class="fas fa-arrow-right-from-bracket"></i> Log Out
        </button>
      </div>
    </header>

    <!-- Main Content Sections -->
    <main class="main-content" id="mainContent" tabindex="-1">
      <section id="dashboard" class="content-section">
        <h2>Dashboard</h2>
        <?php include 'data/dashboard.php'; ?>
      </section>
      <section id="search" class="content-section">
        <h2>Search Residents</h2>
        <?php include 'data/search.php'; ?>
      </section>
      <section id="populations" class="content-section">
        <h2>Populations</h2>
        <?php include 'data/viewall.php'; ?>
      </section>
      <section id="add-off" class="content-section">
        <h2>Add Officials</h2>
        <?php include 'data/add-off.php'; ?>
      </section>
      <section id="officials" class="content-section">
        <?php include 'data/brgofficials.php'; ?>
      </section>
      <section id="request" class="content-section">
        <h2>Requests</h2>
        <?php include 'data/cert-list.php'; ?>
      </section>
      <section id="add" class="content-section">
        <?php include 'data/res-info.php'; ?>
      </section>
      <section id="hire" class="content-section">
        <?php include 'data/hiring.php'; ?>
      </section>
      <section id="announce" class="content-section">
        <?php include 'data/announcements.php'; ?>
      </section>
    </main>
  </div>
  <script src="script.js"></script>
</body>
</html>

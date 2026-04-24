<?php
session_start();
require 'config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
  header('Location: donor-login.html');
  exit;
}

// Fetch donor's donations from database
$stmt = $pdo->prepare("SELECT * FROM donations WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$donations = $stmt->fetchAll();

// Count stats
$total = count($donations);
$pending = 0;
$assigned = 0;
$completed = 0;
foreach ($donations as $d) {
  if ($d['status'] === 'pending')                                  $pending++;
  if ($d['status'] === 'assigned' || $d['status'] === 'accepted')  $assigned++;
  if ($d['status'] === 'completed')                                 $completed++;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Donor | FoodBridge</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <header class="site-header">
      <div class="container header-flex">
        <div class="logo">
  <a href="index.php">
    <img src="assets/logo.png" alt="FoodBridge" />
  </a>
</div>
        <nav class="nav">
          <ul class="nav-list">
            <li><a href="index.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <main class="container">
      <section class="dashboard-header">
        <h1>My Donations</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>! View and track your submitted food donations.</p>
      </section>

      <section class="dashboard-stats">
        <div class="stat-card">
          <h3>Total Donations</h3>
          <p class="stat-number"><?php echo $total; ?></p>
        </div>
        <div class="stat-card">
          <h3>Pending</h3>
          <p class="stat-number"><?php echo $pending; ?></p>
        </div>
        <div class="stat-card">
          <h3>Completed</h3>
          <p class="stat-number"><?php echo $completed; ?></p>
        </div>
      </section>

      <section class="dashboard-action">
        <a href="donate.html" class="btn-primary">+ Donate New Food</a>
      </section>

      <section class="donation-list">
        <?php if (empty($donations)): ?>
          <p>You have no donations yet. <a href="donate.html">Submit your first donation!</a></p>
        <?php else: ?>
          <?php foreach ($donations as $donation): ?>
            <div class="donation-card">
              <div class="donation-card-header">
                <span class="donation-id">#<?php echo $donation['id']; ?></span>
                <span class="status <?php echo $donation['status']; ?>"><?php echo ucfirst($donation['status']); ?></span>
              </div>
              <div class="donation-card-body">
                <p><strong>Food:</strong> <?php echo htmlspecialchars($donation['food_type']); ?></p>
                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($donation['quantity']); ?></p>
                <p><strong>Pickup Date:</strong> <?php echo $donation['pickup_date']; ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
    </main>

    <footer class="site-footer">
      <div class="container footer-container">
        <div class="footer-left">
          <p>© 2026 FoodBridge</p>
        </div>
        <div class="footer-center">
          <p>Contact: support@foodbridge.org</p>
        </div>
      </div>
    </footer>
    <script src="script.js"></script>
  </body>
</html>
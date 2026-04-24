<?php
session_start();
require 'config.php';

// Redirect if not admin
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: admin-login.html');
  exit;
}

// Fetch all donations
$stmt = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC");
$donations = $stmt->fetchAll();

// Fetch volunteers for assignment dropdown
$stmt = $pdo->query("
  SELECT u.id, u.first_name, u.last_name, u.phone,
    COUNT(CASE WHEN d.status IN ('assigned', 'accepted') THEN 1 END) as active_count
  FROM users u
  LEFT JOIN assignments a ON a.volunteer_id = u.id
  LEFT JOIN donations d ON d.id = a.donation_id
  WHERE u.role = 'volunteer'
  GROUP BY u.id, u.first_name, u.last_name, u.phone
");
$volunteers = $stmt->fetchAll();

// Count stats
$pending = 0;
$assigned = 0;
$completed = 0;
foreach ($donations as $d) {
  if ($d['status'] === 'pending') $pending++;
  if ($d['status'] === 'assigned' || $d['status'] === 'accepted') $assigned++;
  if ($d['status'] === 'completed') $completed++;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin | FoodBridge</title>
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
        <h1>Donation Management</h1>
        <p>View and assign donations to volunteers</p>
      </section>

      <section class="dashboard-stats">
        <div class="stat-card">
          <h3>Pending</h3>
          <p class="stat-number"><?php echo $pending; ?></p>
        </div>
        <div class="stat-card">
          <h3>Assigned</h3>
          <p class="stat-number"><?php echo $assigned; ?></p>
        </div>
        <div class="stat-card">
          <h3>Completed</h3>
          <p class="stat-number"><?php echo $completed; ?></p>
        </div>
      </section>

      <section class="volunteers-section">
  <h2>Volunteers</h2>
  <?php if (empty($volunteers)): ?>
    <p>No volunteers registered yet.</p>
  <?php else: ?>
    <div class="volunteers-grid">
      <?php foreach ($volunteers as $v): ?>
        <div class="volunteer-card">
          <div class="volunteer-info">
            <strong><?php echo htmlspecialchars($v['first_name'] . ' ' . $v['last_name']); ?></strong>
            <span><?php echo htmlspecialchars($v['phone']); ?></span>
          </div>
          <span class="status <?php echo $v['active_count'] > 0 ? 'assigned' : 'completed'; ?>">
            <?php echo $v['active_count'] > 0 ? 'Busy' : 'Available'; ?>
          </span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

      <section class="donation-list">
        <?php if (empty($donations)): ?>
          <p>No donations submitted yet.</p>
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
                <p><strong>Pickup Location:</strong> <?php echo htmlspecialchars($donation['pickup_address'] . ', ' . $donation['city']); ?></p>
                <p><strong>Pickup Date/Time:</strong> <?php echo $donation['pickup_date'] . ' ' . $donation['pickup_time']; ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($donation['donor_name'] . ' — ' . $donation['donor_phone']); ?></p>
              </div>
              <div class="donation-card-actions">
                <?php if ($donation['status'] === 'pending'): ?>
                  <form class="assign-form" action="assign_volunteer.php" method="POST">
                    <input type="hidden" name="donation_id" value="<?php echo $donation['id']; ?>">
                    <select name="volunteer_id" class="volunteer-select" required>
  <option value="">Assign Volunteer</option>
  <?php foreach ($volunteers as $volunteer): ?>
    <option value="<?php echo $volunteer['id']; ?>">
      <?php echo htmlspecialchars($volunteer['first_name'] . ' ' . $volunteer['last_name']); ?>
      <?php echo $volunteer['active_count'] > 0 ? ' (busy)' : ' (available)'; ?>
    </option>
  <?php endforeach; ?>
</select>
                    <button type="submit" class="btn-small">Assign</button>
                  </form>
                <?php else: ?>
  <button class="btn-small <?php echo $donation['status'] === 'completed' ? 'btn-completed' : 'btn-assigned'; ?>" disabled>
    <?php echo ucfirst($donation['status']); ?>
  </button>
<?php endif; ?>
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
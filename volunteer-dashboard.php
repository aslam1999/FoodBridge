<?php
session_start();
require 'config.php';

// Redirect if not volunteer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'volunteer') {
  header('Location: volunteer-login.html');
  exit;
}

// Fetch volunteer's assignments
$stmt = $pdo->prepare("
  SELECT d.*, a.id as assignment_id, a.donation_id as donation_id, a.assigned_at
  FROM assignments a
  JOIN donations d ON a.donation_id = d.id
  WHERE a.volunteer_id = ?
  ORDER BY a.assigned_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$assignments = $stmt->fetchAll();

// Count stats
$new = 0;
$accepted = 0;
$completed = 0;
foreach ($assignments as $a) {
  if ($a['status'] === 'assigned') $new++;
  if ($a['status'] === 'accepted') $accepted++;
  if ($a['status'] === 'completed') $completed++;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Volunteer | FoodBridge</title>
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
        <h1>My Assignments</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>! View and manage your assigned donation pickups.</p>
      </section>

      <section class="dashboard-stats">
        <div class="stat-card">
          <h3>New Assignments</h3>
          <p class="stat-number"><?php echo $new; ?></p>
        </div>
        <div class="stat-card">
          <h3>Accepted</h3>
          <p class="stat-number"><?php echo $accepted; ?></p>
        </div>
        <div class="stat-card">
          <h3>Completed</h3>
          <p class="stat-number"><?php echo $completed; ?></p>
        </div>
      </section>

      <section class="assignment-list">
        <?php if (empty($assignments)): ?>
          <p>No assignments yet. Check back soon!</p>
        <?php else: ?>
          <?php foreach ($assignments as $a): ?>
            <div class="assignment-card <?php echo $a['status'] === 'completed' ? 'completed-card' : ''; ?>" data-id="<?php echo $a['assignment_id']; ?>">
              <div class="assignment-header">
                <h3>Donation #<?php echo $a['donation_id']; ?></h3>
                <span class="status <?php echo $a['status']; ?>"><?php echo ucfirst($a['status']); ?></span>
              </div>
              <div class="assignment-body">
                <div class="assignment-left">
                  <p><strong>Food:</strong> <?php echo htmlspecialchars($a['food_type']); ?></p>
                  <p><strong>Quantity:</strong> <?php echo htmlspecialchars($a['quantity']); ?></p>
                  <p><strong>Pickup Location:</strong> <?php echo htmlspecialchars($a['pickup_address'] . ', ' . $a['city']); ?></p>
                  <p><strong>Pickup Date & Time:</strong> <?php echo $a['pickup_date'] . ' ' . $a['pickup_time']; ?></p>
                </div>
                <div class="assignment-right">
                  <p><strong>Contact Name:</strong> <?php echo htmlspecialchars($a['donor_name']); ?></p>
                  <p><strong>Contact Phone:</strong> <?php echo htmlspecialchars($a['donor_phone']); ?></p>
                  <?php if ($a['notes']): ?>
                    <div class="notes-box">
                      <p><strong>Notes:</strong></p>
                      <p><?php echo htmlspecialchars($a['notes']); ?></p>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
              <div class="assignment-actions">
                <?php if ($a['status'] === 'assigned'): ?>
                  <button class="btn-primary accept-btn" data-id="<?php echo $a['assignment_id']; ?>">Accept Assignment</button>
                  <button class="btn-secondary decline-btn" data-id="<?php echo $a['assignment_id']; ?>">Decline</button>
                <?php elseif ($a['status'] === 'accepted'): ?>
                  <button class="btn-small complete-btn" data-id="<?php echo $a['assignment_id']; ?>">Mark Pickup Complete</button>
                <?php else: ?>
  <button class="btn-completed" disabled>Completed</button>
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
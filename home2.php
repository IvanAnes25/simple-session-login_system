<?php
session_start();

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: index.php");
    exit;
}

if ($_SESSION["role"] != "admin") {
    header("Location: home.php");
    exit;
}

if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

$username = htmlspecialchars($_SESSION["username"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Homepage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-shell">
        <header class="dashboard-header">
            <div class="brand-wrap">
                <div class="brand-icon">A</div>
                <div>
                    <span class="eyebrow">Admin portal</span>
                    <h1>Session</h1>
                </div>
            </div>
            <a href="home2.php?logout=1" class="logout-btn">Logout</a>
        </header>

        <main class="dashboard-main">
            <section class="welcome-panel">
                <span class="eyebrow">Dashboard overview</span>
                <h2>Welcome, <?php echo $username; ?>!</h2>
                <p>You are logged in as an administrator and have access to protected platform controls.</p>
            </section>

            <div class="stat-grid">
                <article class="stat-card">
                    <span>Admin</span>
                    <strong><?php echo $username; ?></strong>
                </article>

                <article class="stat-card">
                    <span>Role</span>
                    <span class="role-pill">Admin</span>
                </article>

                <article class="stat-card">
                    <span>Access</span>
                    <strong>Full</strong>
                </article>
            </div>
        </main>
    </div>
</body>
</html>
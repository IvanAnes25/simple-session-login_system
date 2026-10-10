<?php
session_start();
require_once "db.php";

if (isset($_SESSION["user_id"]) && isset($_SESSION["role"])) {
    if ($_SESSION["role"] == "admin") {
        header("Location: home2.php");
    } else {
        header("Location: home.php");
    }
    exit;
}

$error = "";
$success = "";
$loginFailed = false;
$username = "";
$page = "login";
if (isset($_GET["page"])) {
    $page = $_GET["page"];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = "";
    if (isset($_POST["action"])) {
        $action = $_POST["action"];
    }

    if (isset($_POST["username"])) {
        $username = trim($_POST["username"]);
    }
    $password = "";
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }
    $confirm = "";
    if (isset($_POST["confirm_password"])) {
        $confirm = $_POST["confirm_password"];
    }

    if ($action == "register") {
        $page = "register";

        if ($username == "" || $password == "" || $confirm == "") {
            $error = "All fields are required.";
        } elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters.";
        } elseif ($password != $confirm) {
            $error = "Passwords do not match.";
        } else {
            $check = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $check->bind_param("s", $username);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "Username already exists.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
                $stmt->bind_param("ss", $username, $hashedPassword);

                if ($stmt->execute()) {
                    $success = "Registration successful. You can now log in.";
                    $page = "login";
                } else {
                    $error = "Registration failed. Please try again.";
                }
                $stmt->close();
            }
            $check->close();
        }
    } elseif ($action == "login") {
        $page = "login";

        if ($username == "" || $password == "") {
            $error = "Please fill in all fields.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($id, $dbUsername, $dbPassword, $dbRole);

            if ($stmt->fetch() && password_verify($password, $dbPassword)) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = $dbUsername;
                $_SESSION["role"] = $dbRole;

                if ($dbRole == "admin") {
                    header("Location: home2.php");
                } else {
                    header("Location: home.php");
                }
                exit;
            } else {
                $loginFailed = true;
            }
            $stmt->close();
        }
    } else {
        $error = "Invalid request.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-page">
        <form class="login-card" method="POST" action="<?php echo $page == "register" ? "index.php?page=register" : "index.php"; ?>">
            <?php if ($page == "register") { ?>
                <?php if ($error != "") { ?>
                    <div class="msg msg-error"><?php echo htmlspecialchars($error); ?></div>
                <?php } ?>

                <h2>Register</h2>
                <label class="form-label" for="registerUsername">Username</label>
                <input id="registerUsername" type="text" name="username" class="form-input" placeholder="Enter your name" required>

                <label class="form-label" for="registerPassword">Password</label>
                <input id="registerPassword" type="password" name="password" class="form-input" placeholder="Enter your password" required>

                <label class="form-label" for="registerConfirm">Confirm Password</label>
                <input id="registerConfirm" type="password" name="confirm_password" class="form-input" placeholder="Confirm your password" required>

                <button class="primary-btn" type="submit">Register</button>
                <p>Already registered? <a href="index.php">Login here</a></p>
                <input type="hidden" name="action" value="register">
            <?php } else { ?>
                <?php if ($error != "") { ?>
                    <div class="msg msg-error"><?php echo htmlspecialchars($error); ?></div>
                <?php } ?>

                <?php if ($success != "") { ?>
                    <div class="msg msg-success"><?php echo htmlspecialchars($success); ?></div>
                <?php } ?>

                <h2>Login</h2>
                <label class="form-label" for="loginUsername">Username</label>
                <input id="loginUsername" type="text" name="username" class="form-input<?php echo $loginFailed ? " form-input-invalid" : ""; ?>" placeholder="Enter your name" value="<?php echo htmlspecialchars($username, ENT_QUOTES, "UTF-8"); ?>" required>

                <label class="form-label" for="loginPassword">Password</label>
                <input id="loginPassword" type="password" name="password" class="form-input<?php echo $loginFailed ? " form-input-invalid" : ""; ?>" placeholder="Enter your password" required>

                <button class="primary-btn" type="submit">Login</button>
                <p>Forgot password? <a href="#">Click here</a></p>
                <p class="register-line">Don't have an account? <a href="index.php?page=register">Register here</a></p>
                <input type="hidden" name="action" value="login">
            <?php } ?>
        </form>
    </div>
</body>
</html>
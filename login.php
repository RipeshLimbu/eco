<?php
require_once 'config/config.php';
require_once 'config/db_connect.php';
session_start();

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: " . BASE_URL . "/admin/dashboard.php");
            break;
        case 'collector':
            header("Location: " . BASE_URL . "/collector/dashboard.php");
            break;
        case 'user':
            header("Location: " . BASE_URL . "/user/das.php");
            break;
    }
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->connect();

    try {
        $username = $conn->real_escape_string(trim($_POST['username']));
        $password = trim($_POST['password']);

        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database query error: " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: " . BASE_URL . "/admin/dashboard.php");
                        break;
                    case 'collector':
                        header("Location: " . BASE_URL . "/collector/dashboard.php");
                        break;
                    case 'user':
                        header("Location: " . BASE_URL . "/user/dashboard.php");
                        break;
                }
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            $error_message = "User not found.";
        }
    } catch (Exception $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }

    $conn->close();
}

$page_title = "Login";
include 'includes/header.php';
?>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .login-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 350px;
    }

    .login-container h3 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
    }

    .btn {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .text-center {
        text-align: center;
        margin-top: 15px;
    }

    .text-center a {
        color: #007bff;
        text-decoration: none;
    }

    .text-center a:hover {
        text-decoration: underline;
    }

    .alert {
        margin-bottom: 15px;
        padding: 10px;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
    }
</style>
<div class="login-container">
    <h3>Login</h3>
    <?php if ($error_message): ?>
        <div class="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo BASE_URL . '/login.php'; ?>">
        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>

    <div class="text-center">
        <a href="<?php echo BASE_URL . '/register.php'; ?>">Don't have an account? Register here</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
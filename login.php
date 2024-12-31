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
            header("Location: " . BASE_URL . "/user/dashboard.php");
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
    /* General Page Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fc;
    margin: 0;
    padding: 0;
}

.container {
    margin-top: 100px;
}

/* Logo Styles */
.login-logo .eco-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 50px;
    color: #5C6BC0;
}

.login-logo .eco-logo i {
    margin-right: 10px;
    font-size: 60px;
    color: #388E3C;
}

.login-logo .logo-text {
    font-size: 45px;
    font-weight: bold;
}

.login-logo .eco {
    color: #388E3C; /* Green */
}

.login-logo .manage {
    color: #5C6BC0; /* Blue */
}

/* Card Styles */
.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: #5C6BC0;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.card-header h3 {
    font-weight: bold;
}

.card-body {
    padding: 40px 30px;
}

/* Input Fields Styles */
.form-group label {
    font-weight: bold;
    color: #333;
}

.form-control {
    border-radius: 5px;
    padding: 10px;
    font-size: 16px;
    margin-bottom: 20px;
}

.form-control:focus {
    border-color: #5C6BC0;
    box-shadow: 0 0 5px rgba(92, 107, 192, 0.5);
}

/* Button Styles */
.btn-primary {
    background-color: #5C6BC0;
    border-color: #5C6BC0;
    font-weight: bold;
}

.btn-primary:hover {
    background-color: #3f4a94;
    border-color: #3f4a94;
}

/* Alert Message Styles */
.alert {
    margin-top: 20px;
}

.alert-danger {
    background-color: #ffdddd;
    border-color: #f5c2c7;
    color: #b71c1c;
}

/* Register Link */
.text-center a {
    color: #5C6BC0;
    font-weight: bold;
}

.text-center a:hover {
    text-decoration: underline;
}

</style>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6 text-center mb-5 login-logo">
            <div class="eco-logo">
                <i class="fas fa-leaf"></i>
                <div class="logo-text">
                    <span class="eco">eco</span><span class="manage">Manage</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center mb-0">Login</h3>
                </div>
                <div class="card-body">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo htmlspecialchars($error_message); ?>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo BASE_URL . '/login.php'; ?>">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?php echo BASE_URL . '/register.php'; ?>" class="btn btn-link">Don't have an account? Register here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

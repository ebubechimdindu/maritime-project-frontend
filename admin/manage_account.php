<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_username'])) {
        $new_username = $_POST['new_username'];
        $sql = "UPDATE admin SET username = ? WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $new_username);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Username updated successfully!";
        }
    }

    if (isset($_POST['update_email'])) {
        $new_email = $_POST['new_email'];
        $sql = "UPDATE admin SET email = ? WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $new_email);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Email updated successfully!";
        }
    }

    if (isset($_POST['update_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password === $confirm_password) {
            $sql = "UPDATE admin SET password = ? WHERE id = 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $new_password);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Password updated successfully!";
            }
        } else {
            $_SESSION['error_message'] = "Passwords do not match!";
        }
    }
}

// Fetch current admin details
$sql = "SELECT username, email FROM admin WHERE id = 1";
$result = $conn->query($sql);
$admin_data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account - NautiGuard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body>
    <div class="admin-container">
        <?php include('includes/admin_sidebar.php'); ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1><i class="fas fa-user-cog"></i> Manage Account</h1>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-user"></i> Update Username</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="current_username" class="form-label">Current Username</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin_data['username']); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="new_username" class="form-label">New Username</label>
                                    <input type="text" class="form-control" id="new_username" name="new_username" required>
                                </div>
                                <button type="submit" name="update_username" class="btn btn-primary">Update Username</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-envelope"></i> Update Email</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="current_email" class="form-label">Current Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($admin_data['email']); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="new_email" class="form-label">New Email</label>
                                    <input type="email" class="form-control" id="new_email" name="new_email" required>
                                </div>
                                <button type="submit" name="update_email" class="btn btn-primary">Update Email</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-lock"></i> Update Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <div class="password-input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <div class="password-input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/admin_footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = event.currentTarget.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>

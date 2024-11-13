<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NautiGuard - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/authstyle.css" rel="stylesheet">
</head>

<body>
    <a href="../index.html" class="btn back-btn">
        <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <div class="login-container">
        <div class="login-header">
            <h2>Welcome to NautiGuard</h2>
            <p class="text-muted">Login to your account</p>
        </div>

        <form action="backend/login_process.php" method="POST">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
            </div>

            <div class="mb-3 text-end">
                <a href="#" class="text-muted" style="text-decoration: none;">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Login <i class="fas fa-sign-in-alt ms-2"></i>
            </button>
        </form>

        <div class="login-footer">
            <p class="text-muted">
                Don't have an account?
                <a href="userSignup.php" style="color: var(--primary-color); text-decoration: none;">
                    Register here
                </a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
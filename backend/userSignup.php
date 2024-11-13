<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NautiGuard - Register</title>
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
            <p class="text-muted">Create your account</p>
        </div>

        <form action="backend/register_process.php" method="POST">
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="Username" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                    <select class="form-control" name="role" required>
                        <option value="">Select Role</option>
                        <option value="incident_coordinator">Incident Coordinator</option>
                        <option value="safety_officer">Safety Officer</option>
                        <option value="emergency_responder">Emergency Responder</option>
                        <option value="vessel_operator">Vessel Operator</option>
                        <option value="port_authority">Port Authority</option>
                        <option value="security_analyst">Security Analyst</option>
                        <option value="compliance_officer">Compliance Officer</option>
                        <option value="risk_assessor">Risk Assessor</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                    <select class="form-control" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100">
                Register <i class="fas fa-user-plus ms-2"></i>
            </button>
        </form>

        <div class="login-footer">
            <p class="text-muted">
                Already have an account?
                <a href="userLogin.php" style="color: var(--primary-color); text-decoration: none;">
                    Login here
                </a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
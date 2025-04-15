<?php
// Session control
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login/');
    exit;
}

require_once 'login/config.php';

// Get user information
$user = $_SESSION['user'];
$currentName = $user['name'];
$currentEmail = $user['email'];

// Name update process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_name'])) {
    $newName = trim($_POST['new_name']);
    
    if (!empty($newName)) {
        try {
            $stmt = $conn->prepare("UPDATE users SET name = ? WHERE google_id = ?");
            $stmt->bind_param("ss", $newName, $user['id']);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $_SESSION['user']['name'] = $newName;
                $success = "Name updated successfully!";
                $currentName = $newName;
            } else {
                $error = "Error updating name.";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please enter a valid name.";
    }
}

// Account deletion process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    try {
        // Delete user from database
        $stmt = $conn->prepare("DELETE FROM users WHERE google_id = ?");
        $stmt->bind_param("s", $user['id']);
        $stmt->execute();
        
        // End session
        session_unset();
        session_destroy();
        
        // Redirect to home page
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $error = "Error deleting account: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Make CountryBalls</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .settings-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .danger-zone {
            border: 1px solid #ff6b6b;
            padding: 15px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .email-field {
            cursor: default;
            background-color: #f8f9fa;
        }
    </style>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/main-style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="container">
        <div class="settings-container">
            <h2 class="text-center mb-4">Account Settings</h2>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="mb-4">
                <h4>Profile Information</h4>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control email-field" value="<?= htmlspecialchars($currentEmail) ?>" readonly onclick="return false;">
                </div>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="new_name" class="form-label">Username</label>
                        <input type="text" class="form-control" id="new_name" name="new_name" 
                               value="<?= htmlspecialchars($currentName) ?>" required>
                    </div>
                    <button type="submit" name="update_name" class="btn btn-primary">
                        Update Name
                    </button>
                </form>
            </div>
            <div class="danger-zone">
                <h4 class="text-danger">Danger Zone</h4>
                <p>Deleting your account will permanently remove all your data.</p>
                
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone!');">
                    <button type="submit" name="delete_account" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Delete My Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        // Prevent email field from being focused
        document.querySelector('.email-field').addEventListener('mousedown', function(e) {
            e.preventDefault();
            return false;
        });
    </script>
</body>
</html>
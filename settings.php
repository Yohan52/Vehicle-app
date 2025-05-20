<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$username =$_SESSION['username'];

$currentquery = "SELECT first_name, last_name, username, email, interests FROM users WHERE id = ?";
$stmtcurrent = $conn->prepare($currentquery);
$stmtcurrent->bind_param("i",$user_id);
$stmtcurrent->execute();
$resultcurrent = $stmtcurrent->get_result();
$user = $resultcurrent->fetch_assoc();
$interests = explode(",", $user['interests']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | AutoEnthusiasts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #f8f9fa;
            --accent-color: #ffc107;
            --dark-color: #343a40;
            --text-light: #6c757d;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .settings-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background-color: white;
        }
        
        .settings-header {
            background: linear-gradient(135deg, var(--primary-color), #218838);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
            background-color: var(--primary-color);
        }
        
        .interest-checkbox {
            display: none;
        }
        
        .interest-label {
            display: inline-block;
            background-color: var(--secondary-color);
            color: var(--dark-color);
            padding: 8px 12px;
            border-radius: 20px;
            margin-right: 8px;
            margin-bottom: 8px;
            border: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .interest-checkbox:checked + .interest-label {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .interest-icon {
            margin-right: 5px;
        }
        
        .save-btn {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .save-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .cancel-btn {
            background-color: var(--text-light);
            border: none;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
            margin-right: 10px;
        }
        
        .cancel-btn:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-car me-2"></i> Auto Enthusiasts
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div id="userDropdown" class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2" style="font-size: 1.25rem;"></i>
                            <span id="usernameDisplayNav"><?= htmlspecialchars($username) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item active" href="settings.html"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Settings Content -->
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="settings-card mb-5">
                    <div class="settings-header">
                        <h3><i class="fas fa-user-cog me-2"></i> Profile Settings</h3>
                        <p class="mb-0">Update your personal information and preferences</p>
                    </div>
                    
                    <div class="p-4">
                        <form id="profileSettingsForm" method="POST" action="update_profile.php">
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-id-card me-2" style="color: var(--primary-color);"></i> Basic Information</h5>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="firstname" value="<?= htmlspecialchars($user['first_name']); ?>"required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="lastname" value="<?= htmlspecialchars($user['last_name']); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>"required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="mb-3"><i class="fas fa-heart me-2" style="color: var(--primary-color);"></i> Vehicle Interests</h5>
                                <p class="text-muted mb-3">Select the types of vehicles you're interested in:</p>
                                
                                <div>
                                    <input type="checkbox" id="interest-cars" class="interest-checkbox" name="interests[]" value="cars" <?= in_array("cars", $interests) ? 'checked' : '' ?>>
                                    <label for="interest-cars" class="interest-label">
                                        <i class="fas fa-car interest-icon"></i>Cars
                                    </label>
                                    
                                    <input type="checkbox" id="interest-bikes" class="interest-checkbox" name="interests[]" value="bikes" <?= in_array("bikes", $interests) ? 'checked' : '' ?>>
                                    <label for="interest-bikes" class="interest-label">
                                        <i class="fas fa-motorcycle interest-icon"></i>Motorcycles
                                    </label>
                                    
                                    <input type="checkbox" id="interest-trucks" class="interest-checkbox" name="interests[]" value="trucks" <?= in_array("trucks", $interests) ? 'checked' : '' ?>>
                                    <label for="interest-trucks" class="interest-label">
                                        <i class="fas fa-truck-pickup interest-icon"></i>Trucks
                                    </label>
                                    
                                    <input type="checkbox" id="interest-classic" class="interest-checkbox" name="interests[]" value="classic" <?= in_array("classic", $interests) ? 'checked' : '' ?>>
                                    <label for="interest-classic" class="interest-label">
                                        <i class="fas fa-history interest-icon"></i>Classic Cars
                                    </label>
                                    
                                    <input type="checkbox" id="interest-ev" class="interest-checkbox" name="interests[]" value="ev" <?= in_array("ev", $interests) ? 'checked' : '' ?>>
                                    <label for="interest-ev" class="interest-label">
                                        <i class="fas fa-bolt interest-icon"></i>Electric Vehicles
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn cancel-btn text-white" id="cancelBtn">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn save-btn text-white">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2025 AutoEnthusiasts. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
             // Cancel button handler
            document.getElementById('cancelBtn').addEventListener('click', function() {
                window.location.href = 'dashboard.php';
            }); 
        });
    </script>
</body>
</html>
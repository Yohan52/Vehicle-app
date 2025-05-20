<?php
session_start();
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Enthusiasts - Vehicle Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #f8f9fa;
            --accent-color: #ffc107;
            --dark-color: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .hero-section {
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80') no-repeat center center;
            background-size: cover;
            padding: 8rem 0;
            margin-bottom: 3rem;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .category-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .category-card .card-img-top {
            height: 180px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .category-card:hover .card-img-top {
            transform: scale(1.05);
        }
        
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
        
        footer {
            background-color: var(--dark-color);
        }
        
        .footer-links a {
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: var(--primary-color) !important;
        }
        
        .social-icon {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }
        
        .social-icon:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .hero-content, .category-card {
            animation: fadeIn 0.8s ease-out forwards;
        }
        
        .category-card:nth-child(1) { animation-delay: 0.2s; }
        .category-card:nth-child(2) { animation-delay: 0.4s; }
        .category-card:nth-child(3) { animation-delay: 0.6s; }
        .category-card:nth-child(4) { animation-delay: 0.8s; }

        /* User dropdown styles */
        .dropdown-menu-dark {
            background-color: #343a40;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dropdown-menu-dark .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.2s;
        }

        .dropdown-menu-dark .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .dropdown-menu-dark .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.1);
        }

        .user-icon {
            font-size: 1.25rem;
            color: black;
            transition: all 0.2s;
        }

        .user-icon:hover {
            color: var(--primary-color);
        }
        
    </style>
</head>
<body class="<?php echo isset ($_SESSION['username']) ? 'logged-in' : ''; ?>">
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>                    
                </ul>
                <div class="d-flex">
                    <!-- Login/Register buttons (shown when not logged in) -->
                    <div id="guestButtons">
                        <a href="login.html" class="btn btn-outline-light me-2">Login</a>
                        <a href="register.html" class="btn btn-primary">Register</a>
                    </div>
                    
                    <!-- User dropdown (shown when logged in) -->
                    <div id="userDropdown" class="dropdown d-none">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2" style="font-size: 1.25rem;"></i>
                            <span id="usernameDisplay"><?= $_SESSION['username']; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-user-circle me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Background Image -->
    <header class="hero-section text-white">
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <div class="col-12 text-center hero-content">
                    <h1 class="display-4 fw-bold mb-3">Welcome to Auto Enthusiasts</h1>
                    <p class="lead mb-4">Join our community of passionate vehicle lovers and experts</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="categories.php" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-list me-2"></i>Browse Categories
                        </a>
                        <a href="register.html" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-user-plus me-2"></i>Join Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container my-5">
        <div class="row">
            <!-- Popular Categories -->
            <div class="col-md-8">
                <h2 class="mb-4"><i class="fas fa-list me-2"></i> Popular Categories</h2>
                <div class="row">
                     <?php
                     $sql = "
                        SELECT c.category_id, c.name, c.description, c.icon, c.threads_count,
                            (SELECT COUNT(*) FROM replies r
                            INNER JOIN  threads t ON r.thread_id = t.id
                            WHERE t.category_id = c.category_id) AS reply_count
                        FROM categories c
                        LIMIT 4";
                     
                     $result = $conn->query($sql);

                     if($result->num_rows > 0):
                     while($row = $result->fetch_assoc()):
                     ?>
                    <!-- Categories -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 category-card">
                            <img src="images/<?php echo htmlspecialchars($row['name']); ?>.jpg" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas <?php echo htmlspecialchars($row['icon']); ?> me-2"></i><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <a href="threads.php?category=<?php echo $row['category_id']; ?>" class="btn btn-outline-primary">View Threads</a>
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted"><?php echo htmlspecialchars($row['threads_count']); ?> threads • <?= number_format($row['reply_count']) ?> replies</small>
                            </div>
                        </div>
                    </div>
                     <?php
                        endwhile;
                     else:
                        echo "<p>NO category found.</p>";
                     endif;
                     ?>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-md-4">
                
                 <?php
                    $sqlrecentthreads = "SELECT t.id, t.title, c.name AS category, c.icon, COUNT(r.id)AS reply_count
                                         FROM threads t
                                         LEFT JOIN replies r ON r.thread_id = t.id
                                         JOIN categories c ON t.category_id = c.category_id
                                         GROUP BY t.id
                                         ORDER BY reply_count DESC
                                         LIMIT 4
                                         ";
                    $resultrecentthreads = $conn->query($sqlrecentthreads);
                 ?>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-fire me-2"></i> Hot Threads</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php if ($resultrecentthreads && $resultrecentthreads->num_rows > 0): ?>
                                <?php while ($thread = $resultrecentthreads->fetch_assoc()): ?>
                                    <li class="list-group-item">
                                        <a href="thread.php?id=<?= $thread['id'] ?>" class="text-decoration-none d-block mb-1"><?= htmlspecialchars($thread['title']) ?></a>
                                        <small class="text-muted"><i class="fas <?= htmlspecialchars($thread['icon']) ?> me-1"></i><?= htmlspecialchars($thread['category']) ?> • <?= $thread['reply_count'] ?> Replies</small>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li class="list-group-item">No Recent Threads Found.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Forum Stats -->
                <div class="card mb-4">
                    <?php
                    $thredscount = $conn->query("SELECT COUNT(*) AS count FROM threads")->fetch_assoc()['count'];
                    $replycount = $conn->query("SELECT COUNT(*) AS count FROM replies")->fetch_assoc()['count'];
                    $memberscount = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
                    $newestuserresult = $conn->query("SELECT username FROM users ORDER BY created_at DESC LIMIT 1");
                    $newestuserrow = $newestuserresult ? $newestuserresult->fetch_assoc() : null;
                    $newestuser = $newestuserrow && isset($newestuserrow['username']) ? $newestuserrow['username'] : 'N/A';
                    ?>
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Forum Stats</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-comments me-2"></i><strong>Threads:</strong> <?= $thredscount ?></li>
                            <li class="mb-2"><i class="fas fa-reply me-2"></i><strong>Replies:</strong> <?= $replycount ?></li>
                            <li class="mb-2"><i class="fas fa-users me-2"></i><strong>Members:</strong> <?= $memberscount ?></li>
                            <li><i class="fas fa-user-plus me-2"></i><strong>Newest Member:</strong> <?= htmlspecialchars($newestuser) ?></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-link me-2"></i> Quick Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="register.html" class="btn btn-outline-success"><i class="fas fa-user-plus me-2"></i>Register</a>
                            <a href="login.html" class="btn btn-outline-primary"><i class="fas fa-sign-in-alt me-2"></i>Login</a>
                            <a href="categories.php" class="btn btn-outline-info"><i class="fas fa-list me-2"></i>All Categories</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="mb-3"><i class="fas fa-car me-2"></i>About AutoEnthusiasts</h5>
                    <p>Your premier destination for vehicle discussions, tips, and enthusiast community since 2015.</p>
                    <div class="mt-3">
                        <a href="#" class="social-icon me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5 class="mb-3">Explore</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="index.php" class="text-white">Home</a></li>
                        <li class="mb-2"><a href="categories.php" class="text-white">Categories</a></li>    
                        
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5 class="mb-3">Account</h5>
                    <ul class="list-unstyled footer-links">
                        <li class="mb-2"><a href="login.html" class="text-white">Login</a></li>
                        <li class="mb-2"><a href="register.html" class="text-white">Register</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="mb-2"><a href="dashboard.php" class="text-white">My Profile</a></li>
                        <li><a href="settings.php" class="text-white">Settings</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="mb-3">Newsletter</h5>
                    <p>Subscribe to get updates on new threads and events</p>
                    <form class="mt-3">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Your email" aria-label="Your email">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <small>&copy; 2025 AutoEnthusiasts. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small>
                        <a href="terms.html" class="text-white me-3">Terms</a>
                        <a href="privacy.html" class="text-white">Privacy</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Show/hide UI based on body class set by PHP
        const isLoggedIn = document.body.classList.contains('logged-in');
        const guestButtons = document.getElementById('guestButtons');
        const userDropdown = document.getElementById('userDropdown');

        if (isLoggedIn) {
            guestButtons?.classList.add('d-none');
            userDropdown?.classList.remove('d-none');
        } else {
            guestButtons?.classList.remove('d-none');
            userDropdown?.classList.add('d-none');
        }

        // Animate cards on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.category-card, .card').forEach(card => {
            observer.observe(card);
        });
    });
</script>

</body>
</html>
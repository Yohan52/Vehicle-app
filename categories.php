<?php
session_start();
?>
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Forum - Categories</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
        }


        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        
        .category-card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 15px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .category-icon {
            font-size: 1.5rem;
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .search-box {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
        }
        
        .search-btn {
            border-radius: 8px;
        }
        
        .sidebar-card {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .sidebar-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .badge-custom {
            background-color: var(--accent-color);
            color: white;
        }
        
        .latest-text {
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        footer {
            background-color: var(--dark-bg);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-top: 2rem;
            }
        }
    </style>


</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-car me-2"></i>Vehicle Forum
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                </ul>
                <div class="d-flex">
                     <?php if (isset($_SESSION['username'])): ?>
                        <div id="userDropdown" class="dropdown">
                            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-2" style="font-size: 1.25rem;"></i>
                                <span id="usernameDisplay"><?= htmlspecialchars($_SESSION['username']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="dropdownUser">
                                <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-user-circle me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                     <?php else: ?>
                        <div id="guestButtons">
                            <a href="login.html" class="btn btn-outline-light me-2">Login</a>
                            <a href="register.html" class="btn btn-primary">Register</a>
                        </div>
                     <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>


    <main class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <h2 class="mb-4"><i class="fas fa-list me-2"></i> All Vehicle Categories</h2>
                
                <!-- Search Box -->
                <div class="card search-box mb-4">
                    <div class="card-body">
                        <form id="searchForm" class="d-flex">
                            <input id="searchInput" class="form-control me-2" type="search" placeholder="Search categories..." aria-label="Search">
                            <button class="btn btn-primary search-btn" type="submit"><i class="fas fa-search me-1"></i> Search</button>
                        </form>
                    </div>
                </div>
                
                <!-- Categories List -->
                <div class="list-group mt-4" id="categoriesList">
                    
                </div>
                
                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center mt-4">
                    <div class="spinner-border text-primary" role="status">
                        
                    </div>
                    <p class="mt-2">Loading categories...</p>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-md-4 sidebar">
                <div class="card sidebar-card mb-4">
                    <div class="card-body">
                        <h5 class="sidebar-title"><i class="fas fa-fire me-2"></i> Popular Threads</h5>
                        <ul class="list-group list-group-flush">
                            <?php
                            require 'db.php';

                            $pt = "SELECT t.id, t.title, c.name AS category, COUNT(r.id) AS reply_count
                                   FROM threads t
                                   JOIN categories c ON t.category_id = c.category_id
                                   LEFT JOIN replies r ON t.id =r.thread_id
                                   GROUP BY t.id, t.title, c.name
                                   ORDER BY reply_count DESC
                                   LIMIT 3";
                            $result = $conn->query($pt);
                            while ($row =$result->fetch_assoc()) {
                                echo'<li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="fw-bold">' . htmlspecialchars($row['title']) .'</div>
                                            <small class="text-muted">' . htmlspecialchars($row['category']) . '</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">' . htmlspecialchars($row['reply_count']) . '</span>
                                    </li>';
                            }
                            $conn->close();
                            ?>
                            
                        </ul>
                    </div>
                </div>
                
                <div class="card sidebar-card">
                    <div class="card-body">
                        <h5 class="sidebar-title"><i class="fas fa-info-circle me-2"></i> Forum Statistics</h5>
                        <ul class="list-group list-group-flush">
                            <?php
                            require 'db.php';
                            $totalcategories = $conn->query("SELECT COUNT(*) AS total FROM categories")->fetch_assoc()['total'];
                            $totalthreads = $conn->query("SELECT COUNT(*) AS total FROM threads")->fetch_assoc()['total'];
                            $totalmembers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
                            $newestuserresult = $conn->query("SELECT username FROM users ORDER BY created_at DESC LIMIT 1");
                            $newestuserrow = $newestuserresult ? $newestuserresult->fetch_assoc() : null;
                            $newestuser = $newestuserrow && isset($newestuserrow['username']) ? $newestuserrow['username'] : 'N/A';
                            echo '
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Categories
                                <span class="badge badge-custom rounded-pill">'. $totalcategories .'</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Threads
                                <span class="badge badge-custom rounded-pill">'. $totalthreads .'</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Total Members
                                <span class="badge badge-custom rounded-pill">'. $totalmembers .'</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Newest Member
                                <span class="badge badge-custom rounded-pill">'. htmlspecialchars($newestuser) .'</span>
                            </li>';

                            $conn->close();
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-car me-2"></i>Vehicle Forum</h5>
                    <p>Your premier destination for all vehicle-related discussions, tips, and community support.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="categories.php" class="text-white">Categories</a></li>
                        <li><a href="#" class="text-white">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                    <p class="mt-2">© 2025 Vehicle Forum. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // DOM elements
        const categoriesList = document.getElementById('categoriesList');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');

        let allCategories = [];

        // Display categories
        function displayCategories(categoriesToDisplay) {
            categoriesList.innerHTML = '';
            
            if (categoriesToDisplay.length === 0) {
                categoriesList.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No categories found matching your search.
                    </div>
                `;
                return;
            }
            
            categoriesToDisplay.forEach(category => {
                const html = `
                    <a href = "threads.php?category=${category.category_id}" class="list-group-item list-group-item-action category-card">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><i class="fas ${category.icon} category-icon"></i>${category.name}</h5>
                            <span class="badge bg-primary rounded-pill">${category.threads_count} Threads</span>
                        </div>
                        <p class="mb-1">${category.description}</p>
                        <small class="latest-text"><i class="fas fa-comment me-1"></i>Latest: "${category.latest_thread_title}"</small>
                    </a>`;
                categoriesList.insertAdjacentHTML('beforeend', html);
            });
        }

        // Filter categories based on search input
        function filterCategories(term) {
            return allCategories.filter(c => 
                c.name.toLowerCase().includes(term) || 
                c.description.toLowerCase().includes(term) 
            );
        }

        // Load categories from the backend
        fetch('get_categories.php')
            .then(res => res.json())
            .then(data => {
                allCategories = data;
                loadingSpinner.style.display = 'none';
                displayCategories(allCategories);
            })
            .catch(err => {
                console.error('Failed to load categories:', err);
                loadingSpinner.innerHTML = '<div class="alert alert-danger">Error loading categories.</div>';
            });

        // Handle search form submission
        searchForm.addEventListener('submit', e => {
            e.preventDefault();
            const term = searchInput.value.trim().toLowerCase();
            displayCategories(filterCategories(term));
        });

        // Handle search input changes (live search)
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.trim().toLowerCase();
            if (term === '') {
                displayCategories(allCategories);
            } else if(term.length) {;
                displayCategories(filterCategories(term));
            }
        });

        
    </script>


</body>
</html>        

<?php
session_start();
include 'db.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;

$sql = "SELECT threads.*, users.username
    FROM threads 
    JOIN users ON threads.user_id = users.id
    WHERE threads.category_id = ?
    ORDER BY threads.created_at DESC
    
    ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cars Discussion | AutoEnthusiasts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #f8f9fa;
            --accent-color: #ffc107;
            --text-dark: #343a40;
            --text-light: #6c757d;
            --border-color: #e9ecef;
        }

        body {
            background-color: #f8f9fa;
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
        
        .breadcrumb {
            background-color: transparent;
            padding: 0.75rem 0;
            font-size: 0.9rem;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
        
        .category-header {
            background-color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }
        
        .btn-new-thread {
            background-color: var(--primary-color);
            border: none;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-new-thread:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.25);
        }
        
        .filter-card {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            background-color: white;
        }
        
        .form-select, .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            transition: all 0.3s;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.15);
        }
        
        .btn-search {
            border-radius: 0 8px 8px 0;
        }
        
        .thread-list {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }
        
        .thread-item {
            padding: 1.25rem;
            border-left: none;
            border-right: none;
            transition: all 0.2s;
            background-color: white;
            border-bottom: 1px solid var(--border-color);
        }
        
        .thread-item:hover {
            transform: translateX(5px);
            background-color: #f8f9fa;
        }
        
        .thread-item:first-child {
            border-top: none;
        }
        
        .thread-title {
            color: var(--text-dark);
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .thread-title:hover {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .thread-meta {
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .thread-meta a {
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .thread-meta a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }
        
        .thread-excerpt {
            color: var(--text-dark);
            margin: 0.75rem 0;
            line-height: 1.6;
        }
        
        .thread-stats {
            text-align: right;
            min-width: 100px;
        }
        
        .badge-replies {
            background-color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .page-link {
            color: var(--primary-color);
            border-radius: 8px;
            margin: 0 0.25rem;
            border: none;
            padding: 0.5rem 1rem;
        }
        
        .page-link:hover {
            color: #218838;
            background-color: #e9ecef;
        }
        
        /* Modal styles */
        .modal-content {
            border-radius: 12px;
            border: none;
        }
        
        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }
        
        .modal-footer {
            border-top: 1px solid var(--border-color);
        }
        
        .btn-cancel {
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        
        .btn-post {
            background-color: var(--primary-color);
            border: none;
            border-radius: 8px;
        }
        
        .btn-post:hover {
            background-color: #218838;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .thread-item {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }
        
        .thread-item:nth-child(1) { animation-delay: 0.1s; }
        .thread-item:nth-child(2) { animation-delay: 0.2s; }
        .thread-item:nth-child(3) { animation-delay: 0.3s; }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        /* No threads message */
        .no-threads {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .thread-stats {
                text-align: left;
                margin-top: 1rem;
            }
            
            .breadcrumb {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
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

    <main class="container my-4">
        <div class="row">
            <?php
                $stmtcategory = $conn->prepare("SELECT name, icon FROM categories WHERE category_id = ?");
                $stmtcategory->bind_param("i",$category_id);
                $stmtcategory->execute();
                $stmtcategory->bind_result($category_name, $category_icon);
                $stmtcategory->fetch();
                $stmtcategory->close();
            ?>
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="categories.php"><i class="fas fa-list me-1"></i>Categories</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fas <?= $category_icon ?> me-1"></i><?= $category_name ?></li>
                    </ol>
                </nav>

             
                <!-- Category Header -->
                <div class="category-header">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0"><i class="fas <?= $category_icon ?> me-2"></i><?= $category_name ?> Discussion</h2>
                        <button class="btn btn-new-thread btn-primary" data-bs-toggle="modal" data-bs-target="#newThreadModal">
                            <i class="fas fa-plus me-2"></i>New Thread
                        </button>
                    </div>
                </div>
            
                <!-- Thread Filter -->
                <div class="card filter-card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <select class="form-select">
                                    <option selected>Sort by: Newest</option>
                                    <option>Sort by: Oldest</option>
                                    <option>Sort by: Most Replies</option>
                                    <option>Sort by: Most Views</option>
                                    <option>Sort by: Most Likes</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search in this category...">
                                    <button class="btn btn-outline-secondary btn-search" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Threads List -->
                <div class="list-group thread-list mb-4">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $thread_id = (int)$row['id'];
                            $thread_title = htmlspecialchars($row['title']);
                            $thread_content = htmlspecialchars(mb_strimwidth($row['content'], 0, 150, '...'));
                            $created_at = date('M d, Y', strtotime($row['created_at']));
                            $views = (int)$row['view_count'];
                            $username = htmlspecialchars($row['username']);
                            $userid = (int)$row['user_id'];

                            // Last reply info
                            $stmtlastreply = $conn->prepare("
                                SELECT u.username, r.created_at, u.id
                                FROM replies r
                                JOIN users u ON r.user_id = u.id
                                WHERE r.thread_id = ?
                                ORDER BY r.created_at DESC
                                LIMIT 1
                            ");
                            $stmtlastreply->bind_param("i", $thread_id);
                            $stmtlastreply->execute();
                            $stmtlastreply->bind_result($last_reply_user_raw, $last_reply_time_raw, $last_reply_user_id_raw);
                            $stmtlastreply->fetch();
                            $stmtlastreply->close();

                            $last_reply_user = $last_reply_user_raw ? htmlspecialchars($last_reply_user_raw) : null;
                            $last_reply_time = $last_reply_time_raw ? date('M d, Y H:i', strtotime($last_reply_time_raw)) : null;
                            $last_reply_user_id = $last_reply_user_id_raw ? intval($last_reply_user_id_raw) : null;

                            // Reply count
                            $stmtReplies = $conn->prepare("SELECT COUNT(*) FROM replies WHERE thread_id = ?");
                            $stmtReplies->bind_param("i", $thread_id);
                            $stmtReplies->execute();
                            $stmtReplies->bind_result($reply_count);
                            $stmtReplies->fetch();
                            $stmtReplies->close();
                    ?>
                        <div class="list-group-item list-group-item-action thread-item" 
                            role="button" 
                            aria-label="View thread: <?= $thread_title ?>" 
                            onclick="window.location.href='thread.php?id=<?= $thread_id ?>';" 
                            style="cursor: pointer;">

                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="thread-title mb-2"><?= $thread_title ?></h5>
                                    
                                    <div class="thread-meta mb-2 text-muted small">
                                        Started by 
                                        <a href="user_profile.php?id=<?= urlencode($userid) ?>"> <?= $username ?> </a>    •  <?= $created_at ?>

                                        <?php if ($last_reply_user): ?>
                                        <span class="d-none d-md-inline">
                                            • Last reply:  
                                            <a href="user_profile.php?id=<?= urlencode($last_reply_user_id) ?>"><?= $last_reply_user ?></a> 
                                            <?= $last_reply_time ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <p class="thread-excerpt mb-0"><?= $thread_content ?></p>
                                </div>
                                
                                <div class="thread-stats ms-3 text-end">
                                    <span class="badge badge-replies rounded-pill"><?= $reply_count ?> replies</span>
                                    <div class="thread-meta mt-1"><?= number_format($views) ?> views</div>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    } else {
                        echo '<div class="no-threads"><p>No threads available.</p></div>';
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <nav aria-label="Thread pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!--Category info-->
                <div class="card mb-4">
                    <?php
                    // getting description from category table
                    
                    $stmtcat = $conn->prepare("SELECT description FROM categories WHERE category_id = ?");
                    $stmtcat->bind_param("i", $category_id);
                    $stmtcat->execute();
                    $stmtcat->bind_result($category_description);
                    $stmtcat->fetch();
                    $stmtcat->close();

                    //getting total threads for that category
                    $stmtthreadcount = $conn->prepare("SELECT COUNT(*) FROM threads WHERE category_id = ?");
                    $stmtthreadcount->bind_param("i", $category_id);
                    $stmtthreadcount->execute();
                    $stmtthreadcount->bind_result($total_threads);
                    $stmtthreadcount->fetch();
                    $stmtthreadcount->close();

                    //getting total replies for that category
                    $stmtreplycount = $conn->prepare("SELECT COUNT(*) FROM replies WHERE thread_id IN (SELECT id FROM threads WHERE category_id = ?)");
                    $stmtreplycount->bind_param("i", $category_id);
                    $stmtreplycount->execute();
                    $stmtreplycount->bind_result($total_replies);
                    $stmtreplycount->fetch();
                    $stmtreplycount->close();

                    //getting active memebers count
                    $stmtmembber = $conn->prepare("
                        SELECT COUNT(DISTINCT user_id)
                        FROM replies
                        WHERE thread_id IN (SELECT id FROM threads WHERE category_id = ?)
                    ");
                    $stmtmembber->bind_param("i", $category_id);
                    $stmtmembber->execute();
                    $stmtmembber->bind_result($active_members);
                    $stmtmembber->fetch();
                    $stmtmembber->close();
                    ?>
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Category Info</h5>
                    </div>
                    <div class="card-body">
                        <p><?= htmlspecialchars($category_description) ?></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-comments me-2"></i>Total Threads</span>
                                <span class="badge bg-primary rounded-pill"><?= number_format($total_threads) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-reply me-2"></i>Total Replies</span>
                                <span class="badge bg-primary rounded-pill"><?= number_format($total_replies) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-users me-2"></i>Active Members</span>
                                <span class="badge bg-primary rounded-pill"><?= number_format($active_members) ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!--popular threads-->
                <div class="card mb-4">
                    <?php
                    $stmtpopularthreads = $conn->prepare("
                        SELECT t.id, t.title, COUNT(r.id) as reply_count
                        FROM threads t
                        LEFT JOIN REPLIES r ON r.thread_id = t.id
                        WHERE t.category_id = ?
                        GROUP BY t.id
                        ORDER BY reply_count DESC
                        LIMIT 3
                    ");
                    $stmtpopularthreads->bind_param("i",$category_id);
                    $stmtpopularthreads->execute();
                    $resultpopularthreads = $stmtpopularthreads->get_result();
                    $popular_threads = $resultpopularthreads->fetch_all(MYSQLI_ASSOC);
                    ?>

                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Popular Threads</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach ($popular_threads as $thread): ?>
                                <a href="thread.php?id=<?= $thread['id'] ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between">
                                        <span><?= htmlspecialchars($thread['title']) ?> </span>
                                        <span class="badge bg-primary"><?= $thread['reply_count'] ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!--top contibutors-->
                <div class="card">
                    <?php
                    $stmtcontributors = $conn->prepare("
                        SELECT u.id, u.username, COUNT(t.id) as post_count
                        FROM users u
                        LEFT JOIN threads t ON t.user_id = u.id
                        GROUP BY u.id
                        ORDER BY post_count DESC
                        LIMIT 3
                        ");
                    $stmtcontributors->execute();
                    $top_users = $stmtcontributors->get_result()->fetch_all(MYSQLI_ASSOC);
                    ?>
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top Contributors</h5>
                    </div>
                    
                    <div class="card-body">
                        <?php foreach ($top_users as $user): ?>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-user-circle fa-2x me-3"></i>
                            <div>
                                <a href="user_profile.php?id=<?= $user['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($user['username']) ?></a>
                                <div class="small text-muted"><?= number_format($user['post_count']) ?> posts</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- New Thread Modal -->
    <div class="modal fade" id="newThreadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Create New Thread</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="create_thread.php" method="POST" id="newThreadForm">
                        <div class="mb-4">
                            <label for="threadTitle" class="form-label">Thread Title</label>
                            <input type="text" class="form-control" id="threadTitle" name="title" required placeholder="What's your thread about?">
                        </div>
                        <div class="mb-4">
                            <label for="threadContent" class="form-label">Content</label>
                            <textarea class="form-control" id="threadContent" rows="8" name="content" required placeholder="Write your question or discussion topic here..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="threadTags" class="form-label">Tags (comma separated)</label>
                            <input type="text" class="form-control" id="threadTags" name="tags" placeholder="e.g., electric, sedan, 2025">
                            <div class="form-text">Add up to 5 tags to help others find your thread</div>
                        </div>
                        <div id="threadFormError" class="alert alert-danger d-none" role="alert">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-cancel" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" form="newThreadForm" class="btn btn-primary btn-post">
                                <i class="fas fa-paper-plane me-2"></i>Post Thread
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // New thread form submission
            const newThreadForm = document.getElementById('newThreadForm');
            const errBox = document.getElementById('threadFormError');
            
            newThreadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitButton = this.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;

                const category_id = new URLSearchParams(window.location.search).get('category');
                if (category_id) {
                    formData.append('category_id', category_id);
                    
                }

                errBox.classList.add('d-none');
                errBox.textContent = '';

                // Show loading state
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Posting...';
                submitButton.disabled = true;

                
                fetch ('create_thread.php',{
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        submitButton.innerHTML = '<i class="fas fa-check-circle me-2"></i>Posted!';
                        setTimeout(function() {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('newThreadModal'));
                            modal.hide();

                            // Reset form
                            newThreadForm.reset();
                            submitButton.innerHTML = originalButtonText;
                            submitButton.disabled = false;
                        },1000);
                    } else {
                        submitButton.innerHTML = originalButtonText;
                        submitButton.disabled = false;
                        
                        errBox.textContent = data.error || 'An unknown error occurred while posting the thread.';
                        errBox.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error: ', error);
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;
                    
                    errBox.textContent = 'A network or server error occurred.';
                    errBox.classList.remove('d-none');
                });
            });
    
            // Search functionality
            const searchInput = document.querySelector('.filter-card input[type="text"]');
            const searchButton = document.querySelector('.filter-card .btn-search');
            
            searchButton.addEventListener('click', function() {
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    alert('Search would be implemented here for: ' + searchTerm);
                }
            });
            
            // Allow search on Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchButton.click();
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
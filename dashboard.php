<?php
include 'db.php';
session_start();
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | AutoEnthusiasts</title>
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
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), #218838);
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2000&q=80') no-repeat center center;
            background-size: cover;
            opacity: 0.15;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-color);
            border: 3px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .profile-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .interest-badge {
            background-color: var(--secondary-color);
            color: var(--dark-color);
            padding: 8px 12px;
            border-radius: 20px;
            margin-right: 8px;
            margin-bottom: 8px;
            display: inline-block;
            border: 1px solid #dee2e6;
        }
        
        .interest-icon {
            margin-right: 5px;
            color: var(--primary-color);
        }
        
        .stats-item {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .stats-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: var(--text-light);
        }
        
        .edit-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .edit-btn:hover {
            background-color: white;
            color: var(--primary-color);
            text-decoration: none;
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <div id="userDropdown" class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2" style="font-size: 1.25rem;"></i>
                            <span id="usernameDisplayNav"><?= htmlspecialchars($username) ?></span>
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

    <!-- Profile Content -->
    <main class="container my-5">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <?php
                    $profile = "SELECT first_name, last_name, username, email, interests, created_at FROM users WHERE id = ? ";
                    $stmt = $conn->prepare($profile);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    ?>
                <!-- Profile Header -->
                <div class="profile-header p-4 text-center mb-4">                   
                    <a href="settings.php" class="edit-btn" title="Edit Profile">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <div class="profile-avatar mx-auto mb-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?> </h3>
                    <p class="mb-2"><i class="fas fa-at me-2"></i><span><?= htmlspecialchars($user['username']); ?></span></p>
                    <p class="mb-0"><i class="fas fa-envelope me-2"></i><span><?= htmlspecialchars($user['email']); ?></span></p>
                </div>
                
                <!-- Vehicle Interests -->
                <div class="profile-card p-4 mb-4">
                    <?php
                    $interesticons = ['cars' => 'fa-car',
                                      'bikes' => 'fa-motorcycle',
                                      'trucks' => 'fa-truck-pickup',
                                      'classic' => 'fa-history',
                                      'ev' => 'fa-bolt' ];
                    $interests = explode(',',$user['interests']);
                    ?>
                    <h5 class="mb-3"><i class="fas fa-heart me-2" style="color: var(--primary-color);"></i> Vehicle Interests</h5>
                    <div id="profileInterests">
                       <?php foreach ($interests as $interest): ?>
                            <?php $trimmed = trim($interest); ?>
                            <span class="interest-badge">
                                <i class="fas <?= $interesticons[$trimmed] ?? 'fas-star'; ?> interest-icon"></i>
                                <?= ucfirst($trimmed); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Member Since -->
                <div class="profile-card p-4">
                    <h5 class="mb-3"><i class="fas fa-calendar-alt me-2" style="color: var(--primary-color);"></i> Member Since</h5>
                    <p><i class="fas fa-clock me-2"></i><?= date('M d, Y',strtotime($user['created_at'])); ?></p>
                </div>
            </div>
            
            <div class="col-lg-8">
                <!-- Profile Stats -->
                <div class="profile-card p-4 mb-4">
                    <?php
                    $threadsquery = "SELECT COUNT(*) as thread_count FROM threads WHERE user_id = ?";
                    $stmtthreads = $conn->prepare($threadsquery);
                    $stmtthreads->bind_param("i", $user_id);
                    $stmtthreads->execute();
                    $resultthread = $stmtthreads->get_result();
                    $threaddata = $resultthread->fetch_assoc();
                    $threadscreated = $threaddata['thread_count'];

                    $repliesquery = "SELECT COUNT(*) as reply_count From replies WHERE user_id = ?";
                    $stmtreplies = $conn->prepare($repliesquery);
                    $stmtreplies->bind_param("i",$user_id);
                    $stmtreplies->execute();
                    $resultreplies = $stmtreplies->get_result();
                    $replydata = $resultreplies->fetch_assoc();
                    $repliesposted = $replydata['reply_count'];
                    ?>
                    <h5 class="mb-4"><i class="fas fa-chart-line me-2" style="color: var(--primary-color);"></i> Your Activity</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="stats-item">
                                <div class="stats-number"><?= $threadscreated ?></div>
                                <div class="stats-label">Threads Created</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stats-item">
                                <div class="stats-number"><?= $repliesposted ?></div>
                                <div class="stats-label">Replies Posted</div>
                            </div>
                        </div>
                        <!--
                        <div class="col-md-4 mb-3">
                            <div class="stats-item">
                                <div class="stats-number">42</div>
                                <div class="stats-label">Likes Received</div>
                            </div>
                        </div>
                       -->
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="profile-card p-4">
                    <h5 class="mb-4"><i class="fas fa-history me-2" style="color: var(--primary-color);"></i> Recent Activity</h5>
                    <div class="list-group">
                        <?php
                                $replyquery = "SELECT r.content, r.created_at, t.title, t.id AS thread_id
                                    FROM replies r
                                    JOIN threads t ON r.thread_id = t.id
                                    WHERE r.user_id = ? 
                                    ORDER BY r.created_at DESC
                                    LIMIT 1";
                                $stmt = $conn->prepare($replyquery);
                                $stmt->bind_param("i",$user_id);
                                $stmt->execute();
                                $resultreply = $stmt->get_result();
                                $recentreply = $resultreply->fetch_assoc();

                                $recentthreadquery = "SELECT id, title, created_at, content FROM threads WHERE user_id=?
                                                      ORDER BY created_at DESC
                                                      LIMIT 1";
                                $stmtrecentthread = $conn->prepare($recentthreadquery);
                                $stmtrecentthread->bind_param("i",$user_id);
                                $stmtrecentthread->execute();
                                $resultrecentthread = $stmtrecentthread->get_result();
                                $recentthread = $resultrecentthread->fetch_assoc();

                        ?>
                        <?php if ($recentreply): ?>
                            <a href="thread.php?id=<?= $recentreply['thread_id']; ?>" class="list-group-item list-group-item-action">                            
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Replied to: <?= htmlspecialchars($recentreply['title']); ?></h6>
                                    <small><?= date('m/d/Y',strtotime($recentreply['created_at'])); ?></small>
                                </div>
                                <p class="mb-1"><?= htmlspecialchars(substr(strip_tags($recentreply['content']), 0, 85)); ?>...</p>
                            </a>
                        <?php else: ?>
                            <p class="text-muted list-group-item">No recent replies found.</p>
                        <?php endif; ?>

                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Liked: Motorcycle maintenance tips</h6>
                                <small>1 day ago</small>
                            </div>
                            <p class="mb-1">Great tips for chain maintenance!</p>
                        </a>
                        <?php if ($recentthread): ?>
                            <a href="thread.php?id=<?= $recentthread['id']; ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Created thread:<?= htmlspecialchars($recentthread['title']); ?></h6>
                                    <small><?= date('m/d/Y',strtotime($recentthread['created_at'])); ?></small>
                                </div>
                                <p class="mb-1"><?= htmlspecialchars(substr(strip_tags($recentthread['content']), 0, 85)); ?>...</p>
                            </a>
                        <?php else: ?>
                            <p class="text-muted list-group-item">No threads created yet.</p>
                        <?php endif; ?>
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
    
</body>
</html>
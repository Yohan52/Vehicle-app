<?php
session_start();
include 'db.php';

function parseMarkdown($text) {
    // Convert **bold** to <strong>
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    // Convert *italic* to <em>
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
    // Convert newlines to <br>
    $text = nl2br($text);
    return $text;
}

if (isset($_GET['id'])){
    $thread_id = $_GET['id'];

    if ($thread_id > 0) {
        $stmt = $conn->prepare("UPDATE threads SET view_count = view_count + 1 WHERE id = ?");
        $stmt->bind_param('i',$thread_id);
        $stmt->execute();
        $stmt->close();
    }

    $sql = "SELECT t.title, t.content, u.username, u.id AS user_id, t.tags, t.view_count, t.created_at, c.name AS category_name, t.category_id
            FROM threads t
            JOIN users u ON t.user_id = u.id
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $thread_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt-> num_rows > 0){
            $stmt->bind_result($title, $content, $username, $creatorid, $tags, $view_count, $created_at, $category_name, $categoryid);
            $stmt->fetch();
        } else {
            echo "thread not found!";
            exit;
        }
    } else {
        echo "Error: ". $conn->error;
        exit;
    }
} else {
    echo "Invalid thread ID!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> | AutoEnthusiasts</title>
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
    
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }
    
        .card-header {
            background-color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
        }
        
        .thread-header {
            background-color: white;
            border-radius: 10px 10px 0 0;
        }
    
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            margin-left: 0.5rem;
        }
        
        .badge-primary {
            background-color: var(--primary-color);
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 2px solid var(--border-color);
            transition: all 0.3s;
        }
        
        .user-avatar:hover {
            transform: scale(1.05);
            border-color: var(--primary-color);
        }
        
        .reply-avatar {
            width: 50px;
            height: 50px;
        }
        
        .post-content {
            line-height: 1.7;
            color: var(--text-dark);
        }
        
        .post-content strong, .post-content b {
            color: #000 !important;
            font-weight: 800 !important;
        }

        .post-content em, .post-content i {
            font-style: italic !important;
        }

        .post-content p {
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }
        
        .post-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.85rem;
            border-radius: 20px;
            transition: all 0.2s;
        }
        
        .post-actions .btn:hover {
            transform: translateY(-2px);
        }
        
        .like-btn:hover, .like-btn.active {
            color: #dc3545;
            border-color: #dc3545;
        }
    
        .reply-btn:hover {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .nested-reply {
            border-left: 3px solid var(--border-color);
            padding-left: 1rem;
            margin-left: 1.5rem;
            transition: all 0.3s;
        }
        
        .nested-reply:hover {
            border-left-color: var(--primary-color);
        }
    
        .reply-form textarea {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            transition: all 0.3s;
        }
        
        .reply-form textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.15);
        }
        
        .formatting-toolbar .btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 0.5rem;
            transition: all 0.2s;
        }
        
        .formatting-toolbar .btn:hover {
            background-color: var(--border-color);
            transform: scale(1.1);
        }
        
        .post-time {
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .post-time:hover {
            color: var(--text-dark);
        }
        
        .user-name {
            font-weight: 600;
            color: var(--text-dark);
            transition: all 0.2s;
        }
        
        .user-name:hover {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .highlight-post {
            animation: highlight 2s ease-out;
            border-left: 3px solid var(--primary-color);
        }
        
        @keyframes highlight {
            0% { background-color: rgba(40, 167, 69, 0.2); }
            100% { background-color: transparent; }
        }
        
        .jump-to-reply {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .jump-to-reply:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        
        .vote-count {
            min-width: 30px;
            display: inline-block;
            text-align: center;
            font-weight: 600;
        }
        
        /* Animation for new replies */
        .new-reply {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                        <li class="breadcrumb-item"><a href="categories.php"><i class="fas fa-list me-1"></i>Categories</a></li>
                        <li class="breadcrumb-item"><a href="threads.php?category=<?= $categoryid ?>"><i class="fas fa-car me-1"></i><?= htmlspecialchars($category_name); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($title); ?></li>
                    </ol>
                </nav>

            
                <!-- Thread Header -->
                <div class="card thread-header mb-3">
                    <?php
                    $reply_count_sql = "SELECT COUNT(*) AS total_replies FROM replies WHERE thread_id = ? ";
                    if($count_stmt = $conn->prepare($reply_count_sql)) {
                        $count_stmt->bind_param('i',$thread_id);
                        $count_stmt->execute();
                        $count_result = $count_stmt->get_result();
                        $reply_data = $count_result->fetch_assoc();
                        $reply_count = $reply_data['total_replies'];
                        $count_stmt->close();
                    } else {
                        $reply_count = 0;
                    }
                    ?>
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h1 class="h3 mb-0"><i class="fas fa-bolt text-warning me-2"></i><?php echo htmlspecialchars($title); ?></h1>
                            <div>
                                <span class="badge bg-primary"><i class="fas fa-comments me-1"></i><?= $reply_count ?> replies</span>
                                <span class="badge bg-secondary"><i class="fas fa-eye me-1"></i><?php echo htmlspecialchars($view_count); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Original Post -->
                <div class="card mb-4" id="original-post">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <i class="fas fa-user-circle fa-2x me-3"></i> 
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <a href="user_profile.php?id=<?= $creatorid ?>" class="user-name text-decoration-none"><?php echo htmlspecialchars($username); ?></a>
                                    <small class="post-time" title="Posted on"><i class="far fa-clock me-1"></i><?php echo date('M d, Y', strtotime($created_at)); ?></small>
                                </div>
                                <div class="post-content mb-3">
                                    <p><?php echo parseMarkdown(htmlspecialchars($content)); ?></p>
                                </div>
                                <div class="post-actions">
                                    <button class="btn btn-sm btn-outline-primary like-btn me-2">
                                        <i class="fas fa-thumbs-up me-1"></i>
                                        <span class="vote-count">0</span>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary reply-btn me-2">
                                        <i class="fas fa-reply me-1"></i>Reply
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-share me-1"></i>Share
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Replies Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Replies</h5>
                    </div>
                    <div class="card-body">
                        <!-- Reply -->
                        <?php
                        $reply_sql = "SELECT r.id, r.content, r.created_at, u.username,r.user_id
                                      FROM replies r
                                      JOIN users u ON r.user_id = u.id
                                      WHERE r.thread_id = ?
                                      ORDER BY r.created_at ASC";
                        if($reply_stmt = $conn->prepare($reply_sql)) {
                            $reply_stmt->bind_param('i', $thread_id);
                            $reply_stmt->execute();
                            $reply_result = $reply_stmt->get_result();
                    
                            while($reply = $reply_result->fetch_assoc()) {
                                echo '<div class="d-flex mb-4" id="reply-' . $reply['id'] . '">
                                        <div class="flex-shrink-0 me-3">
                                            <i class="fas fa-user-circle fa-2x me-3"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <a href="user_profile.php?id=' . urlencode($reply['user_id']) . '" class="user-name text-decoration-none">' .htmlspecialchars($reply['username']) . '</a>
                                                <small class="post-time" title="' .date('M d, Y h:i A', strtotime($reply['created_at'])) .'"><i class="far fa-clock me-1"></i>' .date('M d, Y', strtotime($reply['created_at'])) . '</small>
                                            </div>
                                            <div class="post-content mb-3">
                                                <p>' . parseMarkdown(htmlspecialchars($reply['content'])) . '</p>
                                          </div>
                                            <div class="post-actions">
                                                <button class="btn btn-sm btn-outline-primary like-btn me-2">
                                                    <i class="fas fa-thumbs-up me-1"></i>
                                                    <span class="vote-count">0</span>
                                                </button>
                                                
                                            </div>
                                        </div>
                                    </div>';
                            }
                        }
                        ?>
                        <!-- New Reply Form -->
                        <div class="card mt-4" id="reply-form">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-reply me-2"></i>Post a Reply</h5>
                            </div>
                            <div class="card-body">                            
                                <form action="post_reply.php?id=<?php echo $thread_id; ?>" method="POST" id="newReplyForm">
                                    <div class="mb-3">
                                        <textarea class="form-control" name="content" id="replyTextarea" rows="5" placeholder="Write your reply..." required></textarea>
                                    </div>

                                    
                                    <div id="reply_error" class="alert alert-danger d-none mt-2"> </div>
                                    

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="formatting-toolbar">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Bold" onclick="formatText('bold')">
                                                <i class="fas fa-bold"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" title="Italic" onclick="formatText('italic')">
                                                <i class="fas fa-italic"></i>
                                            </button>
                                        </div>
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-paper-plane me-2"></i>Post Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thread Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php
                            $reply_sql = "SELECT created_at FROM replies WHERE thread_id = ? ORDER BY created_at DESC LIMIT 1";
                            if ($reply_stmt = $conn->prepare($reply_sql)) {
                                $reply_stmt->bind_param('i', $thread_id);
                                $reply_stmt->execute();
                                $reply_result = $reply_stmt->get_result();
                                $last_reply = $reply_result->fetch_assoc();
                                $last_reply_time = $last_reply ? date("M j, Y g:i A", strtotime($last_reply['created_at'])) : 'No replies yet';
                                $reply_stmt->close();
                            } else {
                                $last_reply_time = 'Error loading last reply';
                            }
                            ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-user me-2"></i>Started by</span>
                                <a href="user_profile.php?id=<?= $creatorid ?>" class="text-decoration-none"><?php echo htmlspecialchars($username); ?></a>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-calendar me-2"></i>Created</span>
                                <span><?php echo date('M d, Y', strtotime($created_at)); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-sync me-2"></i>Last reply</span>
                                <span><?= htmlspecialchars($last_reply_time) ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-tag me-2"></i>Tags</span>
                                <div>
                                    <?php
                                    $tags = explode(',', $tags);
                                    foreach ($tags as $tag){
                                        echo'<span class="badge bg-primary me-1">' .htmlspecialchars(trim($tag)) . '</span>';
                                    }
                                    ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Participants-->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Participants</h5>
                    </div>
                    <div class="card-body">
                        
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-user-circle fa-2x me-3"></i>
                            <div>
                                <a href="user_profile.php?id=<?= $creatorid ?>" class="text-decoration-none"><?php echo htmlspecialchars($username); ?></a>
                                <div class="small text-muted">Thread starter</div>
                            </div>
                        </div>
                        <?php

                        $repliers = "SELECT u.username, u.id, COUNT(r.id) AS reply_count
                                     FROM replies r
                                     JOIN users u ON r.user_id = u.id
                                     Where r.thread_id = ?
                                     GROUP BY r.user_id
                                     ORDER BY reply_count DESC";

                        $repliers_stmt = $conn->prepare($repliers);
                        $repliers_stmt->bind_param("i", $thread_id);
                        $repliers_stmt->execute();
                        $repliers_result = $repliers_stmt->get_result();
                        ?>
                        <?php if ($repliers_result->num_rows > 0): ?>
                            <?php while ($row = $repliers_result->fetch_assoc()): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-user-circle fa-2x me-3"></i>
                                    <div>
                                        <a href="user_profile.php?id=<?= $row['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($row['username']) ?></a>
                                        <div class="small text-muted">
                                            <?= $row['reply_count'] ?> <?= $row['reply_count'] == 1 ? 'reply' : 'replies' ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-mutd">No participants yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Jump to Reply Button -->
    <a href="#reply-form" class="jump-to-reply bg-primary text-white">
        <i class="fas fa-reply"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Text formatting functions
        function formatText(format) {
            const textarea = document.getElementById('replyTextarea');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);
            let before, after;
            
            switch(format) {
                case 'bold':
                    before = after = '**';
                    break;
                case 'italic':
                    before = after = '*';
                    break;
                default:
                    before = after = '';
            }
            
            const newText = textarea.value.substring(0, start) + before + selectedText + after + textarea.value.substring(end);
            textarea.value = newText;
            
            // Restore cursor position
            if (selectedText.length > 0) {
                textarea.selectionStart = start + before.length;
                textarea.selectionEnd = end + before.length;
            } else {
                textarea.selectionStart = textarea.selectionEnd = start + before.length;
            }
            
            textarea.focus();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Like button functionality
            document.querySelectorAll('.like-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const countElement = this.querySelector('.vote-count');
                    let count = parseInt(countElement.textContent);
                    
                    if (this.classList.contains('active')) {
                        // Unlike
                        this.classList.remove('active');
                        count--;
                    } else {
                        // Like
                        this.classList.add('active');
                        count++;
                    }
                    
                    countElement.textContent = count;
                    
                    // Visual feedback
                    this.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 200);
                });
            });
            
            // Reply button functionality
            document.querySelectorAll('.reply-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Scroll to reply form
                    document.getElementById('reply-form').scrollIntoView({ behavior: 'smooth' });
                    
                    // Highlight the form briefly
                    const form = document.getElementById('reply-form');
                    form.classList.add('highlight-post');
                    setTimeout(() => {
                        form.classList.remove('highlight-post');
                    }, 2000);
                    
                    // Focus the textarea
                    document.getElementById('replyTextarea').focus();
                });
            });
            
            // Form submission
            document.getElementById('newReplyForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const errorDiv = document.getElementById('reply_error');
                errorDiv.classList.add('d-none');

                let formData = new FormData(this);
                formData.append('thread_id', <?php echo json_encode($thread_id); ?>);

                fetch('post_reply.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'success') {
                        errorDiv.textContent = data.message;
                        errorDiv.classList.remove('d-none');
                        return;
                    } else {
                        const replyText = this.querySelector('textarea').value.trim();
                        if (replyText) {
                            // Simple client-side markdown parsing for the preview
                            const formattedText = replyText
                                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                                .replace(/\n/g, '<br>');
                            
                            const newReply = document.createElement('div');
                            newReply.className = 'd-flex mb-4 new-reply';
                            newReply.innerHTML = `
                                <div class="flex-shrink-0 me-3">
                                <i class="fas fa-user-circle fa-2x me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <a href="#" class="user-name text-decoration-none">You</a>
                                        <small class="post-time"><i class="far fa-clock me-1"></i>Just now</small>
                                    </div>
                                    <div class="post-content mb-3">
                                        <p>${formattedText}</p>
                                    </div>
                                    <div class="post-actions">
                                        <button class="btn btn-sm btn-outline-primary like-btn me-2">
                                            <i class="fas fa-thumbs-up me-1"></i>
                                            <span class="vote-count">0</span>
                                        </button>
                                        
                                    </div>
                                </div>
                            `;
                            document.querySelector('#reply-form').parentNode.insertBefore(newReply, document.querySelector('#reply-form'));// Insert before the reply form
                            this.querySelector('textarea').value = '';// Clear the form

                            // Add event listeners to new buttons
                            newReply.querySelector('.like-btn').addEventListener('click', function() {
                                const countElement = this.querySelector('.vote-count');
                                let count = parseInt(countElement.textContent);
                        
                                if (this.classList.contains('active')) {
                                    this.classList.remove('active');
                                    count--;
                                } else {
                                    this.classList.add('active');
                                    count++;
                                }
                        
                                countElement.textContent = count;
                            });
                            
                             // Update reply count
                            const replyCountBadge = document.querySelector('.badge.bg-primary');
                            const currentCount = parseInt(replyCountBadge.textContent);
                            replyCountBadge.textContent = currentCount + 1;

                            // Show success message
                            const successAlert = document.createElement('div');
                            successAlert.className = 'alert alert-success alert-dismissible fade show mt-3';
                            successAlert.innerHTML = `
                                <i class="fas fa-check-circle me-2"></i>Your reply has been posted successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            `;
                            this.parentNode.insertBefore(successAlert, this.nextSibling);

                        }
                    }
                    
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("An error occurred while posting your reply. Please try again.");
                    
                });
            });
            
            // Highlight any anchor-linked post
            if (window.location.hash) {
                const targetPost = document.querySelector(window.location.hash);
                if (targetPost) {
                    targetPost.classList.add('highlight-post');
                    setTimeout(() => {
                        targetPost.classList.remove('highlight-post');
                    }, 3000);
                }
            }
        });
    </script>
</body>
</html>
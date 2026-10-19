<?php
session_start();

if (file_exists('db_connect.php')) {
    require_once 'db_connect.php';
} elseif (file_exists('db.php')) {
    require_once 'db.php';
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$message = "";
$message_type = "";

if (isset($_GET['action']) && $_GET['action'] === 'delete_post' && isset($_GET['id'])) {
    $post_id = intval($_GET['id']);

    $img_stmt = $conn->prepare("SELECT image FROM community_posts WHERE id = ?");
    $img_stmt->bind_param("i", $post_id);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();
    if ($img_row = $img_result->fetch_assoc()) {
        if (!empty($img_row['image']) && file_exists("uploads/" . $img_row['image'])) {
            unlink("uploads/" . $img_row['image']);
        }
    }

    $conn->query("DELETE FROM community_comments WHERE post_id = $post_id");
    $conn->query("DELETE FROM community_likes WHERE post_id = $post_id");

    $del_stmt = $conn->prepare("DELETE FROM community_posts WHERE id = ?");
    $del_stmt->bind_param("i", $post_id);
    if ($del_stmt->execute()) {
        $_SESSION['flash_message'] = "Post deleted successfully.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Failed to delete post.";
        $_SESSION['flash_type'] = "error";
    }
    header("Location: manage_posts.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete_comment' && isset($_GET['comment_id'])) {
    $comment_id = intval($_GET['comment_id']);

    $del_comment = $conn->prepare("DELETE FROM community_comments WHERE id = ?");
    $del_comment->bind_param("i", $comment_id);
    if ($del_comment->execute()) {
        $_SESSION['flash_message'] = "Comment removed successfully.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Failed to remove comment.";
        $_SESSION['flash_type'] = "error";
    }
    header("Location: manage_posts.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $sql = "SELECT community_posts.*, users.first_name, users.last_name, users.email 
            FROM community_posts 
            JOIN users ON community_posts.user_id = users.id 
            WHERE community_posts.title LIKE ? OR community_posts.content LIKE ? OR users.first_name LIKE ?
            ORDER BY community_posts.id DESC";
    $search_param = "%" . $search . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $posts = $stmt->get_result();
} else {
    $posts = $conn->query("
        SELECT community_posts.*, users.first_name, users.last_name, users.email
        FROM community_posts
        JOIN users ON community_posts.user_id = users.id
        ORDER BY community_posts.id DESC
    ");
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts - FoodFusion Admin</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <nav>
        <div class="navbar">
            <div class="head-left">
                <h2>Food<span class="span_text">Fusion</span></h2>
            </div>

            <button class="hamburger-menu" id="hamburgerBtn" onclick="toggleMenu()">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="nav-links" id="navLinks">
                <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active-nav' : ''; ?>">Home</a>
                <a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active-nav' : ''; ?>">About Us</a>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="manage_posts.php" class="<?php echo ($current_page == 'manage_posts.php') ? 'active-nav' : ''; ?>">Manage Posts</a>
                    <div class="dropdown">
                        <a href="#" class="<?php echo ($current_page == 'admin_recipes.php' || $current_page == 'admin_resources.php') ? 'active-nav' : ''; ?>">Manage Resources <i class="fa-solid fa-chevron-down nav-arrow"></i></a>
                        <div class="dropdown-content">
                            <a href="admin_recipes.php" class="<?php echo ($current_page == 'admin_recipes.php') ? 'active-nav' : ''; ?>">Recipes Collection</a>
                            <a href="admin_resources.php" class="<?php echo ($current_page == 'admin_resources.php') ? 'active-nav' : ''; ?>">Resources</a>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active-nav' : ''; ?>">Contact Us</a>
            </div>

            <div class="user-area">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="welcome-text">
                        Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>
                        <span class="admin-badge"><i class="fa-solid fa-shield-halved"></i> Admin</span>
                    </span>
                    <a href="logout.php" class="logout-link" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="main-wrapper" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">

        <div class="receipe_title_box" style="text-align: left; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h2 class="receipe_title" style="font-size: 28px; font-weight: 700; color:#1a1633;">Community <span class="span_text">Post Moderation</span></h2>
                    <p class="receipe_description" style="margin-top: 5px; color: #6c757d;">Review, edit, and moderate user submissions and community feedback.</p>
                </div>
            </div>
        </div>

        <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px;">
            <form method="GET" action="manage_posts.php" style="display: flex; gap: 12px; flex-wrap: wrap;">
                <input type="text" name="search" placeholder="Search by title, content, or author..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; min-width: 250px; padding: 10px 15px; border: 1px solid #e9ecef; border-radius: 8px; font-size: 14px;">
                <button type="submit" style="background: #ff6b6b; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    <i class="fa-solid fa-magnifying-glass"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="manage_posts.php" style="background: #6c757d; color: white; border: none; padding: 10px 15px; border-radius: 8px; text-decoration: none; font-size: 14px; display: flex; align-items: center;">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="feed-container" style="display: flex; flex-direction: column; gap: 25px;">
            <?php if ($posts && $posts->num_rows > 0): ?>
                <?php while ($row = $posts->fetch_assoc()): ?>
                    <?php
                    $post_id = $row['id'];

                    $likeStmt = $conn->prepare("SELECT COUNT(*) AS total_likes FROM community_likes WHERE post_id = ?");
                    $likeStmt->bind_param("i", $post_id);
                    $likeStmt->execute();
                    $likeData = $likeStmt->get_result()->fetch_assoc();

                    $commentStmt = $conn->prepare("
                        SELECT community_comments.*, users.first_name, users.last_name 
                        FROM community_comments 
                        JOIN users ON community_comments.user_id = users.id 
                        WHERE community_comments.post_id = ? 
                        ORDER BY community_comments.id DESC
                    ");
                    $commentStmt->bind_param("i", $post_id);
                    $commentStmt->execute();
                    $commentResult = $commentStmt->get_result();
                    ?>

                    <div class="post-card" style="background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e9ecef;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                            <div class="post-header" style="display: flex; align-items: center; gap: 12px;">
                                <div class="author-avatar" style="width: 42px; height: 42px; border-radius: 50%; background: #1a1633; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                    <?php echo strtoupper(substr($row['first_name'], 0, 1)); ?>
                                </div>
                                <div class="author-info">
                                    <h4 style="margin: 0; font-size: 16px; color: #1a1633;"><?php echo htmlspecialchars($row['first_name'] . ' ' . (isset($row['last_name']) ? $row['last_name'] : '')); ?></h4>
                                    <span style="font-size: 12px; color: #6c757d;"><?php echo htmlspecialchars($row['email']); ?></span>
                                </div>
                            </div>
                            <div>
                                <button onclick="confirmDeletePost(<?php echo $post_id; ?>)" style="background: #ef4444; color: white; border: none; padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer;">
                                    <i class="fa-solid fa-trash-can"></i> Delete Post
                                </button>
                            </div>
                        </div>

                        <h3 class="post-title" style="font-size: 18px; color: #1a1633; margin-bottom: 10px;"><?php echo htmlspecialchars($row['title']); ?></h3>

                        <?php if (!empty($row['image'])): ?>
                            <div style="margin-bottom: 15px;">
                                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Post Image" style="max-height: 350px; width: auto; border-radius: 8px; object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <div class="post-content" style="color: #4b5563; line-height: 1.6; margin-bottom: 15px; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <p style="margin: 0;"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                        </div>

                        <div style="display: flex; gap: 20px; font-size: 13px; color: #6c757d; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e9ecef;">
                            <span><i class="fa-solid fa-thumbs-up" style="color: #ff6b6b;"></i> <strong><?php echo $likeData['total_likes']; ?></strong> Likes</span>
                            <span><i class="fa-solid fa-comments" style="color: #3b82f6;"></i> <strong><?php echo $commentResult->num_rows; ?></strong> Comments</span>
                        </div>

                        <div class="comments-section" style="background: #fafafa; padding: 15px; border-radius: 8px;">
                            <h5 style="margin: 0 0 12px 0; font-size: 14px; color: #1a1633;"><i class="fa-regular fa-comments"></i> Managed Comments</h5>

                            <?php if ($commentResult->num_rows > 0): ?>
                                <div class="comment-list" style="display: flex; flex-direction: column; gap: 10px;">
                                    <?php while ($comment = $commentResult->fetch_assoc()): ?>
                                        <div style="display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 10px 12px; border-radius: 6px; border: 1px solid #e9ecef;">
                                            <div>
                                                <strong style="font-size: 13px; color: #1a1633;"><?php echo htmlspecialchars($comment['first_name']); ?>:</strong>
                                                <span style="font-size: 13px; color: #4b5563; margin-left: 6px;"><?php echo htmlspecialchars($comment['comment']); ?></span>
                                            </div>
                                            <button onclick="confirmDeleteComment(<?php echo $comment['id']; ?>)" style="background: transparent; color: #ef4444; border: none; font-size: 13px; cursor: pointer;" title="Remove Comment">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <p style="font-size: 13px; color: #6c757d; margin: 0;">No comments on this post yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="background: #fff; padding: 40px; text-align: center; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <i class="fa-solid fa-folder-open" style="font-size: 40px; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <h3 style="color: #1a1633; margin-bottom: 8px;">No Posts Found</h3>
                    <p style="color: #6c757d; margin: 0;">No community posts match your current filter or query criteria.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <footer class="modern-footer">
        <div class="footer-container">
            <div class="footer-brand">
                <h2 class="footer-logo">Food<span class="span_text">Fusion</span></h2>
                <p class="footer-desc">
                    FoodFusion is a modern recipe-sharing platform that helps users discover,
                    create, and share delicious recipes from around the world.
                </p>
                <div class="social-links">
                    <a href="#" class="social-icon" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon" aria-label="Telegram"><i class="fab fa-telegram"></i></a>
                    <a href="#" class="social-icon" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="social-icon" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>

            <div class="footer-contact-wrapper">
                <h3 class="footer-heading">Get in Touch</h3>
                <div class="contact-card">
                    <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                    <div class="contact-info">
                        <span>Email Us</span>
                        <p>foodfusion@gmail.com</p>
                    </div>
                </div>
                <div class="contact-card">
                    <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
                    <div class="contact-info">
                        <span>Call Us</span>
                        <p>+959 67843321</p>
                    </div>
                </div>
            </div>

            <div class="footer-newsletter">
                <h3 class="footer-heading">Stay Updated</h3>
                <p>Subscribe to get new recipes and cooking updates directly in your inbox.</p>
                <form action="#" method="POST" class="newsletter-form" onsubmit="event.preventDefault();">
                    <div class="input-group">
                        <input type="email" placeholder="Enter your email" required class="newsletter-input">
                        <button type="submit" class="newsletter-btn">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 FoodFusion. All rights reserved.</p>
            <p class="developer-credit">Coded by Hein</p>
        </div>
    </footer>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <script>
            Swal.fire({
                icon: '<?php echo $_SESSION['flash_type']; ?>',
                title: 'Notification',
                text: '<?php echo htmlspecialchars($_SESSION['flash_message']); ?>',
                confirmButtonColor: '#1a1633'
            });
        </script>
        <?php
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>

    <script>
        function confirmDeletePost(id) {
            Swal.fire({
                title: 'Delete Post?',
                text: "This will permanently remove the post, images, and associated comments.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_posts.php?action=delete_post&id=' + id;
                }
            });
        }

        function confirmDeleteComment(commentId) {
            Swal.fire({
                title: 'Remove Comment?',
                text: "Are you sure you want to delete this comment?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_posts.php?action=delete_comment&comment_id=' + commentId;
                }
            });
        }
    </script>

    <script>
        function toggleMenu() {
            const navLinks = document.getElementById("navLinks");
            const hamburgerBtn = document.getElementById("hamburgerBtn");

            navLinks.classList.toggle("active");

            const icon = hamburgerBtn.querySelector("i");
            if (navLinks.classList.contains("active")) {
                icon.classList.remove("fa-bars");
                icon.classList.add("fa-xmark");
            } else {
                icon.classList.remove("fa-xmark");
                icon.classList.add("fa-bars");
            }
        }
    </script>

</body>

</html>
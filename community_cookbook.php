<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['like'])) {
    $post_id = $_GET['like'];

    $sql = "INSERT IGNORE INTO community_likes 
            (post_id, user_id)
            VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();

    header("Location: community_cookbook.php");
    exit();
}

if (isset($_POST['add_comment'])) {
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];

    $sql = "INSERT INTO community_comments 
            (post_id, user_id, comment)
            VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
    $stmt->execute();

    header("Location: community_cookbook.php");
    exit();
}

$posts = $conn->query("
    SELECT community_posts.*, users.first_name
    FROM community_posts
    JOIN users ON community_posts.user_id = users.id
    ORDER BY community_posts.id DESC
");

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Cookbook - FoodFusion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="recipes.php" class="<?php echo ($current_page == 'recipes.php') ? 'active-nav' : ''; ?>">Recipe Collection</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'member'): ?>
                    <a href="recipes.php" class="<?php echo ($current_page == 'recipes.php') ? 'active-nav' : ''; ?>">Recipe Collection</a>
                    <a href="community_cookbook.php" class="<?php echo ($current_page == 'community_cookbook.php') ? 'active-nav' : ''; ?>">Community Cookbook</a>
                    <div class="dropdown">
                        <a href="#" class="<?php echo ($current_page == 'educational_resources.php' || $current_page == 'culinary_resources.php') ? 'active-nav' : ''; ?>">Resources <i class="fa-solid fa-chevron-down nav-arrow"></i></a>
                        <div class="dropdown-content">
                            <a href="educational_resources.php" class="<?php echo ($current_page == 'educational_resources.php') ? 'active-nav' : ''; ?>">Educational Resources</a>
                            <a href="culinary_resources.php" class="<?php echo ($current_page == 'culinary_resources.php') ? 'active-nav' : ''; ?>">Culinary Resources</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="community_cookbook.php" class="<?php echo ($current_page == 'community_cookbook.php') ? 'active-nav' : ''; ?>">Community Cookbook</a>
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
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                            <span class="admin-badge"><i class="fa-solid fa-shield-halved"></i> Admin</span>
                        <?php endif; ?>
                    </span>
                    <a href="logout.php" class="logout-link" title="Logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </a>
                <?php else: ?>
                    <button class="nav-btn btn-outline" onclick="showLogin()">
                        <i class="fa-solid fa-right-from-bracket"></i> Login
                    </button>
                    <button class="nav-btn btn-solid" onclick="showRegister()">
                        <i class="fa-solid fa-user-plus"></i> Register
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="main-wrapper">
        <div class="community-hero">
            <h2 class="receipe_title">Community <span class="span_text">Cookbook</span></h2>
            <p class="receipe_description">
                View recipes, cooking tips, and culinary experiences shared by FoodFusion members.
            </p>
            <div class="community-nav">
                <a href="community_cookbook.php" class="btn-active">Community Feed</a>
                <a href="my_wall.php" class="btn-wall">My Wall</a>
            </div>
        </div>

        <div class="feed-container">
            <?php while ($row = $posts->fetch_assoc()): ?>
                <div class="post-card">
                    <div class="post-header">
                        <div class="author-avatar">
                            <?php echo strtoupper(substr($row['first_name'], 0, 1)); ?>
                        </div>
                        <div class="author-info">
                            <h4><?php echo htmlspecialchars($row['first_name']); ?></h4>
                        </div>
                    </div>

                    <h3 class="post-title"><?php echo htmlspecialchars($row['title']); ?></h3>

                    <?php if (!empty($row['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="post-image" alt="Post Image">
                    <?php endif; ?>

                    <div class="post-content">
                        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                    </div>

                    <?php
                    $post_id = $row['id'];

                    $likeStmt = $conn->prepare("
                        SELECT COUNT(*) AS total_likes
                        FROM community_likes
                        WHERE post_id = ?
                    ");
                    $likeStmt->bind_param("i", $post_id);
                    $likeStmt->execute();
                    $likeData = $likeStmt->get_result()->fetch_assoc();

                    $commentStmt = $conn->prepare("
                        SELECT community_comments.*, users.first_name
                        FROM community_comments
                        JOIN users ON community_comments.user_id = users.id
                        WHERE community_comments.post_id = ?
                        ORDER BY community_comments.id DESC
                    ");
                    $commentStmt->bind_param("i", $post_id);
                    $commentStmt->execute();
                    $commentResult = $commentStmt->get_result();
                    ?>

                    <div class="post-actions">
                        <a href="community_cookbook.php?like=<?php echo $post_id; ?>" class="like-btn">
                            <i class="fa-regular fa-thumbs-up"></i> Like
                        </a>
                        <span class="likes-counter"><strong><?php echo $likeData['total_likes']; ?></strong> Likes</span>
                    </div>

                    <div class="comments-section">
                        <h5><i class="fa-regular fa-comments"></i> Comments</h5>

                        <div class="comment-list">
                            <?php while ($comment = $commentResult->fetch_assoc()): ?>
                                <div class="comment-item">
                                    <span class="comment-user"><?php echo htmlspecialchars($comment['first_name']); ?>:</span>
                                    <span class="comment-text"><?php echo htmlspecialchars($comment['comment']); ?></span>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <form method="POST" class="comment-form">
                            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                            <input type="text" name="comment" class="comment-input" placeholder="Write a comment..." required>
                            <button type="submit" name="add_comment" class="comment-submit">Post</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
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

        <!-- Copyright Bar -->
        <div class="footer-bottom">
            <p>&copy; 2026 FoodFusion. All rights reserved.</p>
            <p class="developer-credit">Coded by Hein</p>
        </div>
    </footer>

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
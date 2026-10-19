<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['submit_post'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imageName = "";

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $tmpName = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmpName, "uploads/" . $imageName);
    }

    $sql = "INSERT INTO community_posts 
            (user_id, title, content, image)
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $title, $content, $imageName);
    $stmt->execute();

    header("Location: my_wall.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT *
    FROM community_posts
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wall - FoodFusion</title>
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
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'member'): ?>
                    <a href="recipes.php">Recipe Collection</a>
                    <a href="community_cookbook.php">Community Cookbook</a>
                    <a href="culinary_resources.php">Culinary Resources</a>
                    <a href="educational_resources.php">Educational Resources</a>
                <?php endif; ?>

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="community_cookbook.php">Community Cookbook</a>
                    <div class="dropdown">
                        <a href="#">Manage Resources ▼</a>
                        <div class="dropdown-content">
                            <a href="admin_recipes.php">Recipes Collection</a>
                            <a href="admin_resources.php">Resources</a>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="contact.php">Contact Us</a>
            </div>

            <span class="user-area">
                Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>
                <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i></a>
            </span>
        </div>
    </nav>

    <div class="main-wrapper">
        <div class="wall-hero">
            <h2>My <span class="span_text">Wall</span></h2>
            <p>Create, update, edit, and manage your culinary posts.</p>

            <div class="community-nav">
                <a href="community_cookbook.php" class="btn-outline">Community Feed</a>
                <a href="my_wall.php" class="btn-active">My Wall</a>
            </div>
        </div>

        <div class="wall-container">
            <div class="create-post-card">
                <h3><i class="fa-solid fa-pen-to-square"></i> Create New Post</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="text" name="title" class="form-input" placeholder="Give your recipe or tip a title..." required>
                    </div>
                    <div class="form-group">
                        <textarea name="content" class="form-input" placeholder="Share your recipe details, ingredients, or culinary tip..." required></textarea>
                    </div>
                    
                    <div class="upload-zone">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <p id="file-label">Click or Drag photo here to attach</p>
                        <input type="file" name="image" accept="image/*" onchange="document.getElementById('file-label').innerText = this.files[0].name">
                    </div>

                    <button type="submit" name="submit_post" class="btn-submit">Publish Post</button>
                </form>
            </div>

            <h3 class="section-heading">My Timeline</h3>

            <?php if ($posts->num_rows > 0): ?>
                <?php while ($row = $posts->fetch_assoc()): ?>
                    <div class="post-card">
                        <h3 class="post-title"><?php echo htmlspecialchars($row['title']); ?></h3>

                        <?php if (!empty($row['image'])): ?>
                            <div class="post-image-container">
                                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="post-image" alt="Post Image">
                            </div>
                        <?php endif; ?>

                        <div class="post-content">
                            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                        </div>

                        <div class="post-actions">
                            <a href="edit_post.php?id=<?php echo $row['id']; ?>" class="action-link btn-edit">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                            <a href="delete_post.php?id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Delete this post?')" 
                               class="action-link btn-delete">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-posts">
                    <i class="fa-regular fa-folder-open" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>You haven't posted anything on your wall yet.</p>
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
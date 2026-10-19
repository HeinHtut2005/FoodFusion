<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("
    SELECT * FROM resources
    WHERE resource_category = 'Culinary'
    ORDER BY id DESC
");
$stmt->execute();
$resources = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culinary Resources - FoodFusion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .top-back-bar {
            padding: 20px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #111827;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #22c55e;
            border-color: #22c55e;
            color: #ffffff;
        }
    </style>
</head>

<body>

    <!-- Back Navigation Bar -->
    <header class="top-back-bar">
        <div class="head-left">
            <h2>Food<span class="span_text">Fusion</span></h2>
        </div>
        <a href="index.php" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Home
        </a>
    </header>

    <div class="main-wrapper">
        <!-- Hero Section -->
        <div class="culinary-hero">
            <h2 class="receipe_title">Culinary <span class="span_text">Resources</span></h2>
            <p class="receipe_description">
                Elevate your cooking skills! Download high-quality recipe cards, watch expert culinary video tutorials, and master pro kitchen hacks curated by FoodFusion.
            </p>
        </div>

        <!-- Resources Grid Container -->
        <div class="resources-container">
            <?php if ($resources->num_rows > 0): ?>
                <div class="resources-grid">
                    <?php while ($row = $resources->fetch_assoc()): ?>
                        <div class="resource-card">
                            <div class="card-badge">
                                <?php if ($row['file_type'] == 'mp4'): ?>
                                    <i class="fa-solid fa-video"></i> Video Tutorial
                                <?php elseif ($row['file_type'] == 'pdf'): ?>
                                    <i class="fa-solid fa-file-pdf"></i> PDF Guide
                                <?php else: ?>
                                    <i class="fa-solid fa-utensils"></i> Culinary Card
                                <?php endif; ?>
                            </div>

                            <div class="card-body">
                                <h3 class="resource-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                                <p class="resource-desc"><?php echo htmlspecialchars($row['description']); ?></p>

                                <?php if ($row['file_type'] == 'mp4'): ?>
                                    <div class="video-preview-wrapper">
                                        <video controls preload="metadata">
                                            <source src="uploads/resources/<?php echo htmlspecialchars($row['file_name']); ?>" type="video/mp4">
                                            Your browser does not support HTML5 video.
                                        </video>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-footer">
                                <a href="uploads/resources/<?php echo htmlspecialchars($row['file_name']); ?>" download class="download-btn">
                                    <i class="fa-solid fa-download"></i> Download Resource
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-resources">
                    <i class="fa-solid fa-folder-open"></i>
                    <p>No culinary resources available at the moment. Check back soon!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer Section -->
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

</body>

</html>
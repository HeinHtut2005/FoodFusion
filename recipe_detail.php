<?php
session_start();
include "db.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: recipes.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();

if (!$recipe) {
    header("Location: recipes.php");
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - FoodFusion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .detail-container {
            max-width: 900px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }

        .detail-img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .detail-title {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 15px;
        }

        .detail-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .detail-box {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #22c55e;
        }

        .detail-box h3 {
            font-size: 18px;
            color: #1a1633;
            margin-bottom: 10px;
        }

        .detail-box p {
            color: #4b5563;
            line-height: 1.7;
            white-space: pre-line;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #ffffff;
            background: #1a1633;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .back-link:hover {
            background: #22c55e;
        }
    </style>
</head>

<body>

    <nav>
        <div class="navbar">
            <div class="head-left">
                <h2>Food<span class="span_text">Fusion</span></h2>
            </div>

            <div class="nav-links">
                <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active-nav' : ''; ?>">Home</a>
                <a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active-nav' : ''; ?>">About Us</a>
                <a href="recipes.php" class="<?php echo ($current_page == 'recipes.php') ? 'active-nav' : ''; ?>">Recipe Collection</a>
                <a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active-nav' : ''; ?>">Contact Us</a>
            </div>

            <div class="user-area">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="welcome-text">
                        Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>
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
        <div class="detail-container">
            <a href="recipes.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Recipes</a>

            <?php if (!empty($recipe['image'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="detail-img">
            <?php else: ?>
                <img src="images/recipe-placeholder.jpg" alt="Recipe Image" class="detail-img">
            <?php endif; ?>

            <h1 class="detail-title"><?php echo htmlspecialchars($recipe['title']); ?></h1>

            <div class="detail-meta">
                <span class="meta-pill"><i class="fa-solid fa-utensils"></i> <?php echo htmlspecialchars($recipe['cuisine_type']); ?></span>
                <span class="meta-pill"><i class="fa-solid fa-leaf"></i> <?php echo htmlspecialchars($recipe['dietary_preference']); ?></span>
                <span class="meta-pill"><i class="fa-solid fa-layer-group"></i> <?php echo htmlspecialchars($recipe['difficulty']); ?></span>
            </div>

            <div class="detail-box">
                <h3>Description</h3>
                <p><?php echo htmlspecialchars($recipe['description']); ?></p>
            </div>

            <?php if (!empty($recipe['ingredients'])): ?>
                <div class="detail-box">
                    <h3>Ingredients</h3>
                    <p><?php echo htmlspecialchars($recipe['ingredients']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($recipe['instructions'])): ?>
                <div class="detail-box">
                    <h3>Instructions</h3>
                    <p><?php echo htmlspecialchars($recipe['instructions']); ?></p>
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
            </div>
        </div>
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
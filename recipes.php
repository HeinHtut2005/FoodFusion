<?php
session_start();
include "db.php";

$search = $_GET['search'] ?? "";

$sql = "SELECT * FROM recipes
        WHERE title LIKE ?
        OR cuisine_type LIKE ?
        OR dietary_preference LIKE ?
        OR difficulty LIKE ?
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);
$keyword = "%$search%";
$stmt->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
$stmt->execute();
$result = $stmt->get_result();
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Collection - FoodFusion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .recipe-card {
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
        }

        .recipe-detail-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 600px;
            max-height: 85vh;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            z-index: 1001;
            overflow-y: auto;
            padding: 25px;
        }

        .recipe-detail-popup.active {
            display: block;
        }

        .recipe-detail-header {
            position: relative;
            margin-bottom: 20px;
        }

        .recipe-detail-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .recipe-detail-title {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 10px;
        }

        .recipe-detail-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .recipe-detail-section {
            margin-bottom: 10px;
        }

        .recipe-detail-section h4 {
            font-size: 16px;
            font-weight: 600;
            color: #1a1633;
            margin-bottom: 8px;
            border-bottom: 2px solid #22c55e;
            display: inline-block;
            padding-bottom: 3px;
        }

        .recipe-detail-section p {
            color: #4b5563;
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-line;
        }

        .recipe-detail-popup .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.9);
            color: #111827;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .recipe-detail-popup .close-btn:hover {
            background: #ffffff;
            transform: scale(1.1);
        }

    </style>
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

    <div id="modalOverlay" class="modal-overlay" onclick="closeAll()"></div>

    <div id="registerForm" class="popup">
        <button type="button" class="close-btn" onclick="closeAll()">&times;</button>
        <div class="popup-header">
            <h2>Join Food<span class="span_text">Fusion</span></h2>
            <p>Create an account to explore & share recipes</p>
        </div>

        <form action="register.php" method="POST" class="popup-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" name="first_name" placeholder="First Name" class="popup_input" required>
                </div>
                <div class="form-group">
                    <input type="text" name="last_name" placeholder="Last Name" class="popup_input" required>
                </div>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" class="popup_input" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" class="popup_input" required>
            </div>

            <button type="submit" class="submit-btn">Create Account</button>
        </form>
    </div>

    <div id="loginForm" class="popup">
        <button type="button" class="close-btn" onclick="closeAll()">&times;</button>

        <div class="popup-header">
            <h2>Welcome <span class="span_text">Back</span></h2>
            <p>Log in to access your saved recipes & resources</p>
        </div>

        <form action="login.php" method="POST" class="popup-form">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" class="popup_input" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" class="popup_input" required>
            </div>

            <a href="forgot_password.php">Forgot Password?</a>

            <button type="submit" class="submit-btn">Login</button>
        </form>
    </div>

    <div id="recipeDetailModal" class="recipe-detail-popup">
        <button type="button" class="close-btn" onclick="closeAll()">&times;</button>
        <div class="recipe-detail-header">
            <img id="detailImg" src="" alt="Recipe Image" class="recipe-detail-img">
            <h2 id="detailTitle" class="recipe-detail-title"></h2>
            <div class="recipe-detail-meta">
                <span class="meta-pill" id="detailCuisine"></span>
                <span class="meta-pill" id="detailDiet"></span>
                <span class="meta-pill" id="detailDifficulty"></span>
            </div>
        </div>

        <div class="recipe-detail-section">
            <h4>Description</h4>
            <p id="detailDescription"></p>
        </div>
    </div>

    <div class="main-wrapper">
        <div class="recipes-hero">
            <h2 class="receipe_title">Recipe <span class="span_text">Collection</span></h2>

            <p class="receipe_description">
                Explore delicious recipes from around the world. Search by cuisine type, dietary preference, or difficulty level!
            </p>

            <form method="GET" class="recipe-search-form">
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>

                    <input
                        type="text"
                        name="search"
                        placeholder="Search recipe, cuisine, diet, or difficulty..."
                        value="<?php echo htmlspecialchars($search); ?>">

                    <button type="submit" class="search-btn">Search</button>
                </div>
            </form>
        </div>

        <div class="recipes-container">
            <?php if ($result->num_rows > 0): ?>

                <div class="recipe-grid">

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <?php
                        $image_src = !empty($row['image']) ? "uploads/" . htmlspecialchars($row['image']) : "images/recipe-placeholder.jpg";
                        $row_json = htmlspecialchars(json_encode([
                            'title' => $row['title'] ?? '',
                            'image' => $image_src,
                            'cuisine' => $row['cuisine_type'] ?? 'N/A',
                            'diet' => $row['dietary_preference'] ?? 'N/A',
                            'difficulty' => $row['difficulty'] ?? 'N/A',
                            'description' => $row['description'] ?? 'No description available.'
                        ]), ENT_QUOTES, 'UTF-8');
                        ?>

                        <div class="recipe-card" onclick="openRecipeModal(<?php echo $row_json; ?>)">

                            <div class="recipe-image-wrapper">

                                <img
                                    src="<?php echo $image_src; ?>"
                                    alt="<?php echo htmlspecialchars($row['title']); ?>">

                                <span class="difficulty-tag">
                                    <?php echo htmlspecialchars($row['difficulty']); ?>
                                </span>

                            </div>

                            <div class="recipe-card-content">

                                <h3 class="recipe-card-title">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </h3>

                                <div class="recipe-meta-pills">

                                    <span class="meta-pill">
                                        <i class="fa-solid fa-utensils"></i>
                                        <?php echo htmlspecialchars($row['cuisine_type']); ?>
                                    </span>

                                    <span class="meta-pill">
                                        <i class="fa-solid fa-leaf"></i>
                                        <?php echo htmlspecialchars($row['dietary_preference']); ?>
                                    </span>

                                </div>

                                <p class="recipe-card-desc">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </p>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            <?php else: ?>

                <div class="no-resources">
                    <i class="fa-solid fa-utensils"></i>
                    <p>No recipes found matching your search term. Try searching for something else!</p>
                </div>

            <?php endif; ?>
        </div>
    </div>

    <footer class="modern-footer">

        <div class="footer-container">

            <div class="footer-brand">

                <h2 class="footer-logo">
                    Food<span class="span_text">Fusion</span>
                </h2>

                <p class="footer-desc">
                    FoodFusion is a modern recipe-sharing platform that helps users discover,
                    create, and share delicious recipes from around the world.
                </p>

                <div class="social-links">
                    <a href="#" class="social-icon" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>

                    <a href="#" class="social-icon" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>

                    <a href="#" class="social-icon" aria-label="Telegram">
                        <i class="fab fa-telegram"></i>
                    </a>

                    <a href="#" class="social-icon" aria-label="TikTok">
                        <i class="fab fa-tiktok"></i>
                    </a>

                    <a href="#" class="social-icon" aria-label="Twitter">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                </div>

            </div>

            <div class="footer-contact-wrapper">

                <h3 class="footer-heading">Get in Touch</h3>

                <div class="contact-card">

                    <div class="contact-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>

                    <div class="contact-info">
                        <span>Email Us</span>
                        <p>foodfusion@gmail.com</p>
                    </div>

                </div>

                <div class="contact-card">

                    <div class="contact-icon">
                        <i class="fa-solid fa-phone"></i>
                    </div>

                    <div class="contact-info">
                        <span>Call Us</span>
                        <p>+959 67843321</p>
                    </div>

                </div>

            </div>

            <div class="footer-newsletter">

                <h3 class="footer-heading">Stay Updated</h3>

                <p>
                    Subscribe to get new recipes and cooking updates directly in your inbox.
                </p>

                <form
                    action="#"
                    method="POST"
                    class="newsletter-form"
                    onsubmit="event.preventDefault();">

                    <div class="input-group">

                        <input
                            type="email"
                            placeholder="Enter your email"
                            required
                            class="newsletter-input">

                        <button
                            type="submit"
                            class="newsletter-btn">

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

    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>

    <script>
        function showRegister() {
            closeAll();
            document.getElementById("registerForm").classList.add("active");
            document.getElementById("modalOverlay").classList.add("active");
        }

        function showLogin() {
            closeAll();
            document.getElementById("loginForm").classList.add("active");
            document.getElementById("modalOverlay").classList.add("active");
        }

        function openRecipeModal(data) {
            closeAll();
            document.getElementById("detailImg").src = data.image;
            document.getElementById("detailTitle").innerText = data.title;
            document.getElementById("detailCuisine").innerHTML = '<i class="fa-solid fa-utensils"></i> ' + data.cuisine;
            document.getElementById("detailDiet").innerHTML = '<i class="fa-solid fa-leaf"></i> ' + data.diet;
            document.getElementById("detailDifficulty").innerHTML = '<i class="fa-solid fa-layer-group"></i> ' + data.difficulty;
            document.getElementById("detailDescription").innerText = data.description;

            document.getElementById("recipeDetailModal").classList.add("active");
            document.getElementById("modalOverlay").classList.add("active");
        }

        function closeAll() {
            document.getElementById("registerForm").classList.remove("active");
            document.getElementById("loginForm").classList.remove("active");
            document.getElementById("recipeDetailModal").classList.remove("active");
            document.getElementById("modalOverlay").classList.remove("active");
        }

        var swiper = new Swiper(".mySwiper", {
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                dynamicBullets: true,
            },
            loop: true,
            autoplay: {
                delay: 3000
            },
            grabCursor: true,
        });
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($_SESSION['login_error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                html: '<p style="margin-bottom: 12px; font-size: 15px; color: #4b5563;"><?php echo htmlspecialchars($_SESSION['login_error']); ?></p>' +
                    '<?php if (isset($_SESSION['login_attempts'])): ?>' +
                    '<div style="background-color: #ffe4e6; color: #e11d48; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 14px; display: inline-block;">' +
                    '<i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i><?php echo htmlspecialchars($_SESSION['login_attempts']); ?>' +
                    '</div>' +
                    '<?php endif; ?>',
                confirmButtonColor: '#1a1633',
                confirmButtonText: 'Try Again'
            });
        </script>
        <?php
        unset($_SESSION['login_error']);
        unset($_SESSION['login_attempts']);
        ?>
    <?php endif; ?>

</body>

</html>
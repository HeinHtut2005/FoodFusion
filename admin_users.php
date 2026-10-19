<?php
session_start();

if (file_exists('db_connect.php')) {
    require_once 'db_connect.php';
} elseif (file_exists('db.php')) {
    require_once 'db.php';
}

$total_users = 0;
$total_recipes = 0;
$total_reports = 0;
$total_resources = 0;
$recent_recipes = [];

if (isset($conn) && $conn) {
    try {
        $user_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
        if ($user_res) {
            $total_users = mysqli_fetch_assoc($user_res)['total'];
        }
    } catch (Exception $e) {
        $total_users = 0;
    }

    try {
        $recipe_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM recipes");
        if ($recipe_res) {
            $total_recipes = mysqli_fetch_assoc($recipe_res)['total'];
        }
    } catch (Exception $e) {
        $total_recipes = 0;
    }

    try {
        $report_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM reports");
        if ($report_res) {
            $total_reports = mysqli_fetch_assoc($report_res)['total'];
        }
    } catch (Exception $e) {
        $total_reports = 0;
    }

    try {
        $resource_res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM resources");
        if ($resource_res) {
            $total_resources = mysqli_fetch_assoc($resource_res)['total'];
        }
    } catch (Exception $e) {
        $total_resources = 0;
    }

    try {
        $recent_query = "SELECT id AS recipe_id, title FROM recipes ORDER BY id DESC LIMIT 6";
        $recent_res = mysqli_query($conn, $recent_query);
        if ($recent_res && mysqli_num_rows($recent_res) > 0) {
            while ($row = mysqli_fetch_assoc($recent_res)) {
                $recent_recipes[] = $row;
            }
        }
    } catch (Exception $e) {
        $recent_recipes = [];
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html>

<head>
    <title>FoodFusion - <?php echo (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') ? 'Admin Control Center' : 'Home'; ?></title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />

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

                <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
                    <a href="about.php" class="<?php echo ($current_page == 'about.php') ? 'active-nav' : ''; ?>">About Us</a>
                <?php endif; ?>

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
                    <a href="admin_users.php" class="<?php echo ($current_page == 'admin_users.php') ? 'active-nav' : ''; ?>">Manage Users</a>
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

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>

        <div class="admin-hero-card" data-aos="fade-up">
            <div class="admin-hero-text">
                <div class="admin-badge-pill">
                    <i class="fa-solid fa-user-shield"></i> System Administration
                </div>
                <h1 class="admin-hero-title">
                    Control & Manage <br>
                    <span class="highlight">FoodFusion Platform</span>
                </h1>
                <p class="admin-hero-desc">
                    Monitor community uploads, manage culinary resources, oversee user activities, and handle content moderations efficiently.
                </p>
                <div class="admin-btn-group">
                    <a href="admin_users.php" class="admin-btn admin-btn-primary">
                        <i class="fa-solid fa-users-gear"></i> Manage Users
                    </a>
                    <a href="admin_recipes.php" class="admin-btn admin-btn-secondary">
                        <i class="fa-solid fa-utensils"></i> Manage Recipes
                    </a>
                </div>
            </div>
            <div class="admin-hero-image">
                <img src="images/food_receipes_bg3.png" alt="Admin Dashboard Illustration">
            </div>
        </div>

        <section class="admin-dashboard-section" data-aos="fade-up">
            <div class="receipe_title_box">
                <h2 class="receipe_title index">Platform Overview</h2>
                <p class="receipe_description index">Key real-time statistics and administrative metrics for FoodFusion.</p>
            </div>

            <div class="admin-stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-users"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($total_users); ?></h3>
                        <p>Total Registered Users</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-recipes"><i class="fa-solid fa-book-bookmark"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($total_recipes); ?></h3>
                        <p>Active Recipes</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-pending"><i class="fa-solid fa-folder-tree"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($total_resources); ?></h3>
                        <p>Learning Resources</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon icon-reports"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="stat-info">
                        <h3><?php echo number_format($total_reports); ?></h3>
                        <p>Reported Items</p>
                    </div>
                </div>
            </div>

            <div class="admin-table-container" style="margin-top: 40px; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="font-size: 20px; font-weight: 600; color: #1a1633;"><i class="fa-solid fa-clock-rotate-left" style="color: #ff6b6b; margin-right: 8px;"></i> Recent Recipe Submissions</h3>
                    <a href="admin_recipes.php" style="color: #ff6b6b; font-weight: 600; text-decoration: none; font-size: 14px;">View All Recipes →</a>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px; text-align: left;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                                <th style="padding: 12px;">ID</th>
                                <th style="padding: 12px;">Recipe Title</th>
                                <th style="padding: 12px;">Submitted Date</th>
                                <th style="padding: 12px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_recipes)): ?>
                                <?php foreach ($recent_recipes as $recipe): ?>
                                    <tr style="border-bottom: 1px solid #f1f3f5;">
                                        <td style="padding: 12px; font-weight: 600;">#<?php echo $recipe['recipe_id']; ?></td>
                                        <td style="padding: 12px;"><?php echo htmlspecialchars($recipe['title']); ?></td>
                                        <td style="padding: 12px; color: #6c757d;"><?php echo isset($recipe['created_at']) ? date('M d, Y', strtotime($recipe['created_at'])) : 'N/A'; ?></td>
                                        <td style="padding: 12px; text-align: center;">
                                            <a href="admin_recipes.php?id=<?php echo $recipe['recipe_id']; ?>" style="background: #1a1633; color: #fff; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">Manage</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="padding: 20px; text-align: center; color: #6c757d;">No recipes found in the database.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-tools-grid" style="margin-top: 40px;">
                <div class="admin-tool-card">
                    <i class="fa-solid fa-users-gear tool-icon"></i>
                    <h4>User Account Management</h4>
                    <p>Manage user profiles, view registered members, adjust permissions, or remove accounts.</p>
                    <a href="admin_users.php" class="tool-btn">Manage Users →</a>
                </div>

                <div class="admin-tool-card">
                    <i class="fa-solid fa-pen-to-square tool-icon"></i>
                    <h4>Recipe Collection Management</h4>
                    <p>Review submitted recipes from members, edit existing content, or remove outdated entries.</p>
                    <a href="admin_recipes.php" class="tool-btn">Open Manager →</a>
                </div>

                <div class="admin-tool-card">
                    <i class="fa-solid fa-layer-group tool-icon"></i>
                    <h4>Culinary & Educational Resources</h4>
                    <p>Upload new educational guides, manage resource categories, and update downloadable files.</p>
                    <a href="admin_resources.php" class="tool-btn">Manage Resources →</a>
                </div>
            </div>

            <div style="margin-top: 40px; background: #f8f9fa; border-radius: 12px; padding: 25px; border: 1px solid #e9ecef;">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 15px; color: #1a1633;"><i class="fa-solid fa-sliders" style="margin-right: 8px;"></i> Administrative System Utilities</h3>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <a href="admin_users.php" class="tool-btn" style="background: #8b5cf6; color: white; display: inline-block; width: auto;"><i class="fa-solid fa-users"></i> Manage Users</a>
                    <a href="admin_recipes.php?action=add" class="tool-btn" style="background: #22c55e; color: white; display: inline-block; width: auto;"><i class="fa-solid fa-plus"></i> Add New Recipe</a>
                    <a href="admin_resources.php?action=add" class="tool-btn" style="background: #3b82f6; color: white; display: inline-block; width: auto;"><i class="fa-solid fa-file-circle-plus"></i> Upload Resource Guide</a>
                    <a href="contact.php" class="tool-btn" style="background: #64748b; color: white; display: inline-block; width: auto;"><i class="fa-solid fa-headset"></i> Support Inquiries</a>
                </div>
            </div>
        </section>

    <?php else: ?>

        <div class="header_top">
            <div class="header__container" id="home">

                <div class="header__content">

                    <div class="receipe_box">
                        <i class="fa-solid fa-kitchen-set"></i>
                        <p>FoodFusion Receipes</p>
                    </div>

                    <h1 data-aos="fade-up" data-aos-duration="1000">Welcome to Food<span class="span_text">Fusion</span>
                        <br><span class="header_span"> Recipe Sharing Platform</span>
                    </h1>
                    <p class="section__description" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                        FoodFusion is a cooking platform where users can explore recipes,
                        join the community, and learn culinary skills.
                    </p>

                    <div class="rating_box" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                        <div class="star">
                            <p>⭐</p>
                            <p>⭐</p>
                            <p>⭐</p>
                            <p>⭐</p>
                            <p>⭐</p>
                        </div>
                        <div class="rating_text">
                            <p>4.9 | 500+ happy celebration</p>
                        </div>
                    </div>

                    <div class="box_container" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                        <div class="duration_box">
                            <i class="fa-solid fa-stopwatch"></i>
                            <p>1 Hour</p>
                        </div>

                        <div class="duration_box share">
                            <i class="fa-solid fa-utensils"></i>
                            <p>Receipe Sharing</p>
                        </div>
                    </div>

                    <div data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500">
                        <button class="btn_receipe" onclick="window.location.href='recipes.php'">View Recipes ></button>
                    </div>

                </div>

                <div class="header__image" data-aos="fade-left" data-aos-duration="1000">
                    <img src="images/food_receipes_bg3.png" class="header_img" alt="header" />
                </div>
            </div>
        </div>

        <div class="receipe_title_box">
            <h2 class="receipe_title index" data-aos="fade-up" data-aos-duration="1000">Discover Delicious Recipes</h2>
            <p class="receipe_description index" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">Explore a wide collection of recipes from different cuisines,
                ranging from quick everyday meals to special occasion dishes.</p>
        </div>

        <div class="receipe_container">
            <div class="receipe_training" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                <div class="receipe_icon_container">
                    <div class="receipe_icon">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                </div>
                <h4>Step-by-Step Receipes</h4>
                <p class="receipe_des">Cook with ease using simple, easy-to-follow instructions.
                    From preparing ingredients to serving the final dish,
                    every step is explained clearly to help you achieve great results.</p>
            </div>
            <div class="receipe_training" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                <div class="receipe_icon_container">
                    <div class="receipe_icon">
                        <i class="fa-regular fa-compass"></i>
                    </div>
                </div>
                <h4>Find Recipes Your Way</h4>
                <p class="receipe_des">Search recipes by ingredients, cuisine, meal type, dietary preference,
                    or cooking time to quickly find dishes that match your needs.</p>
            </div>
            <div class="receipe_training" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500">
                <div class="receipe_icon_container">
                    <div class="receipe_icon">
                        <i class="fa-regular fa-heart"></i>
                    </div>
                </div>
                <h4>Save Your Favorites</h4>
                <p class="receipe_des">Bookmark recipes you love and organize them into your personal collection,
                    making it easy to revisit your favorite meals whenever you need inspiration.</p>
            </div>
            <div class="receipe_training" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
                <div class="receipe_icon_container">
                    <div class="receipe_icon">
                        <i class="fa-solid fa-bowl-food"></i>
                    </div>
                </div>
                <h4>Fresh Ideas Every Day</h4>
                <p class="receipe_des">Discover new recipes and seasonal dishes regularly.
                    There's always something exciting to cook,
                    helping you keep your meals creative and delicious.</p>
            </div>
        </div>

        <section class="featured_recipes_section">
            <div class="receipe_title_box" data-aos="fade-up">
                <h2 class="receipe_title index">Trending Recipes</h2>
                <p class="receipe_description index">Check out the most loved dishes from our culinary community this week.</p>
            </div>

            <div class="featured_grid">
                <div class="recipe_card" data-aos="fade-up" data-aos-delay="100">
                    <img src="images/0fb780bf1d5bfdaca9c981aa334f9a61.jpg" alt="Spicy Noodles" class="recipe_card_img">
                    <div class="recipe_card_body">
                        <span class="recipe_tag tag_asian">Asian Fusion</span>
                        <h3 class="recipe_card_title">Spicy Garlic Ramen</h3>
                        <p class="recipe_card_text">Rich savory broth topped with a soft-boiled egg and fresh herbs.</p>
                        <div class="recipe_card_footer">
                            <span class="recipe_time"><i class="fa-regular fa-clock"></i> 25 mins</span>
                            <a href="recipes.php" class="recipe_link">Read More →</a>
                        </div>
                    </div>
                </div>

                <div class="recipe_card" data-aos="fade-up" data-aos-delay="200">
                    <img src="images/1b103c0c8e8d91cfa1bafa22c435fe04.jpg" alt="Italian Pasta" class="recipe_card_img">
                    <div class="recipe_card_body">
                        <span class="recipe_tag tag_italian">Italian</span>
                        <h3 class="recipe_card_title">Creamy Tuscan Chicken Pasta</h3>
                        <p class="recipe_card_text">Sun-dried tomatoes, spinach, and tender chicken in garlic cream sauce.</p>
                        <div class="recipe_card_footer">
                            <span class="recipe_time"><i class="fa-regular fa-clock"></i> 35 mins</span>
                            <a href="recipes.php" class="recipe_link">Read More →</a>
                        </div>
                    </div>
                </div>

                <div class="recipe_card" data-aos="fade-up" data-aos-delay="300">
                    <img src="images/c016144cdc4eb275ea33b2c90c5681e1.jpg" alt="Healthy Bowl" class="recipe_card_img">
                    <div class="recipe_card_body">
                        <span class="recipe_tag tag_healthy">Healthy</span>
                        <h3 class="recipe_card_title">Avocado Quinoa Power Bowl</h3>
                        <p class="recipe_card_text">Nutritious bowl filled with roasted veggies, quinoa, and lemon dressing.</p>
                        <div class="recipe_card_footer">
                            <span class="recipe_time"><i class="fa-regular fa-clock"></i> 15 mins</span>
                            <a href="recipes.php" class="recipe_link">Read More →</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta_section" data-aos="zoom-in">
            <h2 class="cta_title">Ready to share your culinary creations?</h2>
            <p class="cta_text">Join thousands of home cooks and professional chefs. Create your own cookbook and inspire food lovers around the globe!</p>
            <button class="cta_btn" onclick="showRegister()">Join FoodFusion Today</button>
        </section>
    <?php endif; ?>

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

        function closeAll() {
            document.getElementById("registerForm").classList.remove("active");
            document.getElementById("loginForm").classList.remove("active");
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

    <div id="cookieBanner" class="cookie-banner">
        <div class="cookie-content">
            <div class="cookie-text">
                <i class="fa-solid fa-cookie-bite cookie-icon"></i>
                <p>We use cookies to enhance your experience and improve our services. By continuing to visit this site, you agree to our use of cookies.</p>
            </div>
            <div class="cookie-actions">
                <button class="cookie-btn btn-accept" onclick="acceptCookies()">Accept All</button>
                <button class="cookie-btn btn-decline" onclick="declineCookies()">Decline</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (!localStorage.getItem("cookieConsent")) {
                setTimeout(() => {
                    document.getElementById("cookieBanner").classList.add("show");
                }, 1000);
            }
        });

        function acceptCookies() {
            localStorage.setItem("cookieConsent", "accepted");
            closeCookieBanner();
        }

        function declineCookies() {
            localStorage.setItem("cookieConsent", "declined");
            closeCookieBanner();
        }

        function closeCookieBanner() {
            const banner = document.getElementById("cookieBanner");
            banner.classList.remove("show");
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
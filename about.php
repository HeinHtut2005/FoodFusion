<?php session_start(); ?>
<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html>

<head>
    <title>FoodFusion - About Us</title>

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

    <div id="modalOverlay" class="modal-overlay" onclick="closeAll()"></div>

    <div id="registerForm" class="popup">
        <button type="button" class="close-btn" onclick="closeAll()">&times;</button>
        <div class="popup-header">
            <h2>Join <span class="span_text">FoodFusion</span></h2>
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

            <button type="submit" class="submit-btn">Login</button>
        </form>
    </div>

    <div class="about">
        <div class="about_container">
            <div class="about_box">
                <div class="about_image">
                    <div class="about_bgbox"></div>
                    <img src="images/poster1-removebg-preview.png" class="about_img" alt="about_poster">
                </div>
            </div>
            <div class="about_box para">
                <div>
                    <h3 class="about_title">About Food<span class="span_text">Fusion</span></h3> <br>
                    <p>Welcome to FoodFusion, where cooking meets creativity.
                        Our platform is dedicated to helping food lovers discover, prepare,
                        and share delicious recipes from around the world.
                        Whether you're a beginner learning the basics or an experienced cook exploring new cuisines,
                        FoodFusion provides easy-to-follow recipes, helpful cooking tips,
                        and meal inspiration for every occasion. We believe that great food brings people together,
                        and our mission is to make cooking enjoyable, accessible, and rewarding for everyone.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <div class="stats-section">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-utensils"></i></div>
                <h3 class="stat-number">12,500+</h3>
                <p class="stat-label">Curated Recipes</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <h3 class="stat-number">85,000+</h3>
                <p class="stat-label">Active Home Chefs</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-earth-americas"></i></div>
                <h3 class="stat-number">45+</h3>
                <p class="stat-label">Global Cuisines</p>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-award"></i></div>
                <h3 class="stat-number">100%</h3>
                <p class="stat-label">Tested & Verified</p>
            </div>
        </div>
    </div>

    <div class="values-section">
        <div class="about_title_box">
            <h2 class="about_title_sec">Our Core <span class="span_text">&nbsp;Values</span></h2>
        </div>
        <div class="values-container">
            <div class="value-card">
                <div class="value-icon"><i class="fa-solid fa-seedling"></i></div>
                <h4>Sustainability First</h4>
                <p>We emphasize eco-conscious cooking by promoting seasonal, locally sourced produce and zero-waste kitchen tips.</p>
            </div>
            <div class="value-card">
                <div class="value-icon"><i class="fa-solid fa-people-group"></i></div>
                <h4>Community Spirit</h4>
                <p>Food brings people together. Our interactive forums and shared cookbooks encourage collaborative culinary learning.</p>
            </div>
            <div class="value-card">
                <div class="value-icon"><i class="fa-solid fa-mortar-pestle"></i></div>
                <h4>Authentic Technique</h4>
                <p>We preserve traditional cooking methods while adapting them for modern kitchens, ensuring real flavors every time.</p>
            </div>
        </div>
    </div>

    <div>
        <div class="about-chef-container">
            <div class="about-chef-description">
                <div class="about-chef">
                    <i class="fa-solid fa-user-tie"></i>
                    <p>Our Chef</p>
                </div>
                <h2>About Our Master Chef</h2>
                <p><i class="fa-solid fa-circle-check"></i> 20 years of experience in restaurant in United Kingdom</p>
                <p><i class="fa-solid fa-circle-check"></i> Any kind of recipe for customer</p>
                <p class="about-headchef">Guided by Head Chef Xavier Cruz, our kitchen brings together authentic flavors,
                    fresh local ingredients, and modern culinary art.
                    With a passion for perfection in every bite,
                    Chef Alex turns everyday dining into an extraordinary experience.
                </p>
                <button class="btn_receipe about">View More</button>
            </div>
            <div class="about-chef-card">
                <div class="about-chef-image">
                    <img src="images/chef5.jpg" alt="Chef Xavier Cruz">
                </div>
                <div class="about-chef-info">
                    <h3>Head Chef Xavier Cruz</h3>
                    <div class="about-chef-info-des">
                        <p>Senior Chef</p>
                        <p>Style: Fire-Grilled Specialties</p>
                        <p class="chef-bg-info">Background: With over 30 years of experience leading high-energy kitchens across Spain,
                            Mexico, and the U.S., Chef Xavier Cruz brings dynamic energy and intense flavor profiles to FoodFusion.
                            Known for his mastery of open-flame cooking, slow-roasted meats, and vibrant,
                            herb-infused marinades, Xavier crafts dishes that are as visually striking as they are rich in taste.</p>
                    </div>
                </div>
            </div>
            <div class="about-chef-card">
                <div class="about-chef-image">
                    <img src="images/chef1.jpg" alt="Chef Julian Laurent">
                </div>
                <div class="about-chef-info">
                    <h3>Chef Julian Laurent</h3>
                    <div class="about-chef-info-des">
                        <p>Senior Chef</p>
                        <p>Style: Modern European Fusion</p>
                        <p class="chef-bg-info">Background: With over 20 years of culinary mastery,
                            Chef Julian Laurent leads the kitchen with a philosophy centered on precision,
                            bold flavors, and artistic presentation. Having trained under world-renowned culinary masters,
                            Gabriel brings a refined technique to every dish while keeping flavor approachable and deeply satisfying.</p>
                    </div>
                </div>
            </div>
            <div class="about-chef-card">
                <div class="about-chef-image">
                    <img src="images/chef3.jpg" alt="Chef Gabriel Sterling">
                </div>
                <div class="about-chef-info">
                    <h3>Chef Gabriel Sterling</h3>
                    <div class="about-chef-info-des">
                        <p>Senior Chef</p>
                        <p>Style: Classical French Techniques</p>
                        <p class="chef-bg-info">Background: Chef Gabriel Sterling learned the craft in classic Parisian kitchens,
                            developing a deep passion for balanced taste and flawless technique.
                            His meticulous attention to detail shines through in both his savory dishes and exquisite dessert creations,
                            making every plate a balance of texture, aroma, and color.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="timeline-section">
        <div class="about_title_box">
            <h2 class="about_title_sec">Our Culinary <span class="span_text">&nbsp;Journey</span></h2>
        </div>
        <div class="timeline-container">
            <div class="timeline-item">
                <div class="timeline-year">2021</div>
                <div class="timeline-content">
                    <h4>Platform Launch</h4>
                    <p>FoodFusion began as a small digital blog sharing 50 home-tested recipes.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2023</div>
                <div class="timeline-content">
                    <h4>Community Cookbook</h4>
                    <p>Expanded to allow home cooks globally to share their heritage recipes.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2026</div>
                <div class="timeline-content">
                    <h4>Masterclass Integration</h4>
                    <p>Partnered with world-class chefs to introduce video modules and interactive resources.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="about_title_box">
        <h2 class="about_title_sec">Why Choose Our <span class="span_text">&nbsp;Recipes</span></h2>
    </div>

    <div class="about_container_recipe">
        <div class="about_training">
            <div class="about_icon_container">
                <div class="about_icon">
                    <i class="fa-solid fa-plate-wheat"></i>
                </div>
            </div>
            <h4>Fresh Ingredients</h4>
            <p class="about_des">We use fresh, high-quality ingredients in every recipe to ensure delicious flavors,
                balanced nutrition, and satisfying meals for you and your family.</p>
        </div>
        <div class="about_training">
            <div class="about_icon_container">
                <div class="about_icon">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </div>
            <h4>Easy Step-by-Step Guides</h4>
            <p class="about_des">Our recipes include clear,
                detailed instructions that make cooking simple and enjoyable,
                even for beginners.</p>
        </div>
        <div class="about_training">
            <div class="about_icon_container">
                <div class="about_icon">
                    <i class="fa-regular fa-clock"></i>
                </div>
            </div>
            <h4>Quick & Time-Saving</h4>
            <p class="about_des">From 15-minute meals to family dinners,
                our recipes are designed to help you prepare delicious food without spending hours in the kitchen.</p>
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
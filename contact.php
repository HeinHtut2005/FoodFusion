<?php
session_start();
include "db.php";

$message_status = "";
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$is_admin) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        $message_status = "Thank you, " . $name . "! Your message has been sent successfully.";
    } else {
        $message_status = "Error sending message. Please try again later.";
    }
    $stmt->close();
}

if ($is_admin && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    $stmt_delete = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt_delete->bind_param("i", $delete_id);
    if ($stmt_delete->execute()) {
        $_SESSION['admin_msg'] = "Message deleted successfully.";
    } else {
        $_SESSION['admin_err'] = "Failed to delete message.";
    }
    $stmt_delete->close();
    header("Location: contact.php");
    exit();
}

if ($is_admin) {
    $sql = "SELECT * FROM contact_messages ORDER BY id DESC";
    $result = $conn->query($sql);
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_admin ? 'Contact Messages - Admin Dashboard' : 'Contact Us'; ?> - FoodFusion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-contact-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .admin-header {
            margin-bottom: 25px;
        }

        .admin-header h1 {
            font-size: 28px;
            color: #1a1633;
        }

        .messages-table-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .admin-table th,
        .admin-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .admin-table th {
            background-color: #1a1633;
            color: #ffffff;
            font-weight: 600;
        }

        .admin-table tr:hover {
            background-color: #f9fafb;
        }

        .badge-inquiry {
            background: #e0f2fe;
            color: #0369a1;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .btn-delete {
            color: #ef4444;
            background: #fee2e2;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: #ffffff;
        }

        .empty-msg {
            text-align: center;
            padding: 40px;
            color: #6b7280;
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
                    <a href="manage_posts.php" class="<?php echo ($current_page == 'manage_posts.php') ? 'active-nav' : ''; ?>">Manage Posts</a>
                    <div class="dropdown">
                        <a href="#" class="<?php echo ($current_page == 'admin_recipes.php' || $current_page == 'admin_resources.php') ? 'active-nav' : ''; ?>">Manage Resources <i class="fa-solid fa-chevron-down nav-arrow"></i></a>
                        <div class="dropdown-content">
                            <a href="admin_recipes.php" class="<?php echo ($current_page == 'admin_recipes.php') ? 'active-nav' : ''; ?>">Recipes Collection</a>
                            <a href="admin_resources.php" class="<?php echo ($current_page == 'admin_resources.php') ? 'active-nav' : ''; ?>">Resources</a>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active-nav' : ''; ?>">
                    <?php echo $is_admin ? 'Contact Messages' : 'Contact Us'; ?>
                </a>
            </div>

            <div class="user-area">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="welcome-text">
                        Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>
                        <?php if ($is_admin): ?>
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

    <?php if ($is_admin): ?>

        <div class="main-wrapper admin-contact-container">
            <div class="admin-header">
                <h1>User <span class="span_text">Contact Messages</span></h1>
                <p>View and manage inquiry messages submitted by website visitors.</p>
            </div>

            <div class="messages-table-card">
                <?php if ($result && $result->num_rows > 0): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sender</th>
                                <th>Email</th>
                                <th>Inquiry Type</th>
                                <th>Message</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            while ($row = $result->fetch_assoc()):
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>">
                                            <?php echo htmlspecialchars($row['email']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge-inquiry">
                                            <?php echo htmlspecialchars($row['subject']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
                                    <td>
                                        <a href="contact.php?action=delete&id=<?php echo $row['id']; ?>"
                                            class="btn-delete"
                                            onclick="return confirm('Are you sure you want to delete this message?');">
                                            <i class="fa-solid fa-trash-can"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-msg">
                        <i class="fa-solid fa-inbox fa-3x" style="margin-bottom: 10px; color: #9ca3af;"></i>
                        <p>No contact messages found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>

        <section class="contact-hero">
            <h1>Get in <span class="span_text">Touch</span></h1>
            <p>Have questions about recipes, feedback, or need help with your account? We'd love to hear from you!</p>
        </section>

        <section class="contact-container">
            <div class="contact-form-card">
                <h3>Send Us a Message</h3>

                <?php if (!empty($message_status)): ?>
                    <div class="success-alert">
                        <i class="fa-solid fa-circle-check"></i> <?php echo $message_status; ?>
                    </div>
                <?php endif; ?>

                <form action="contact.php" method="POST" class="contact-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Your Email</label>
                            <input type="email" id="email" name="email" placeholder="john@example.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Inquiry Type</label>
                        <select id="subject" name="subject" required>
                            <option value="" disabled selected>Select an option</option>
                            <option value="General Question">General Inquiry</option>
                            <option value="Recipe Submission">Recipe Submission Issue</option>
                            <option value="Technical Support">Technical Support</option>
                            <option value="Feedback">Platform Feedback</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Send Message <i class="fa-solid fa-paper-plane"></i></button>
                </form>
            </div>
        </section>

        <section class="faq-section">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-card">
                    <h4><i class="fa-solid fa-circle-question"></i> How can I share my own recipe?</h4>
                    <p>Register for a free account, log in, and visit the Community Cookbook section to submit your recipes.</p>
                </div>
                <div class="faq-card">
                    <h4><i class="fa-solid fa-circle-question"></i> Is FoodFusion free to use?</h4>
                    <p>Yes! Browsing, saving, and sharing recipes on FoodFusion is completely free for all users.</p>
                </div>
            </div>
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

    <?php if (isset($_SESSION['admin_msg'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo htmlspecialchars($_SESSION['admin_msg']); ?>',
                confirmButtonColor: '#1a1633'
            });
        </script>
        <?php unset($_SESSION['admin_msg']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['admin_err'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo htmlspecialchars($_SESSION['admin_err']); ?>',
                confirmButtonColor: '#1a1633'
            });
        </script>
        <?php unset($_SESSION['admin_err']); ?>
    <?php endif; ?>

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
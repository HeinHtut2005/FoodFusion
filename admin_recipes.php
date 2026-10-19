<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied. Admin only.");
}

/* ADD RECIPE */
if (isset($_POST['add'])) {

    $title = $_POST['title'];
    $cuisine = $_POST['cuisine_type'];
    $dietary = $_POST['dietary_preference'];
    $difficulty = $_POST['difficulty'];
    $description = $_POST['description'];

    $imageName = $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmpName, "uploads/" . $imageName);

    $sql = "INSERT INTO recipes 
            (title, cuisine_type, dietary_preference, difficulty, description, image)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $title, $cuisine, $dietary, $difficulty, $description, $imageName);
    $stmt->execute();

    header("Location: admin_recipes.php");
    exit();
}

$recipes = $conn->query("SELECT * FROM recipes ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Recipes | FoodFusion</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="admin-body">

    <!-- Top Navigation Bar -->
    <nav class="admin-navbar">

        <div class="nav-container">
            <div class="header-left-admin">
                <h2>Food<span class="span_text">Fusion</span></h2>
                <div><span class="admin-badge">Admin Panel</span></div>
            </div>
            <div class="admin-nav-links">
                <a href="index.php"><i class="fa-solid fa-house"></i> View Site</a>
                <a href="logout.php" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </div>
    </nav>

    <div class="admin-container">

        <div class="admin-header">
            <h1>Manage <span class="span_text">Recipes</span></h1>
            <p>Add new recipes to the collection or update existing ones.</p>
        </div>

        <div class="admin-card">
            <h3><i class="fa-solid fa-plus-circle"></i> Add New Recipe</h3>

            <form method="POST" enctype="multipart/form-data" class="modern-form">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Recipe Title</label>
                        <input type="text" name="title" placeholder="e.g. Italian Pepperoni Pizza" required>
                    </div>

                    <div class="form-group">
                        <label>Cuisine Type</label>
                        <input type="text" name="cuisine_type" placeholder="e.g. Italian, Asian, Mexican" required>
                    </div>

                    <div class="form-group">
                        <label>Dietary Preference</label>
                        <select name="dietary_preference" required>
                            <option value="">Select Diet</option>
                            <option value="Vegetarian">Vegetarian</option>
                            <option value="Vegan">Vegan</option>
                            <option value="Halal">Halal</option>
                            <option value="Gluten-Free">Gluten-Free</option>
                            <option value="Keto">Keto</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Difficulty Level</label>
                        <select name="difficulty" required>
                            <option value="">Select Difficulty</option>
                            <option value="Easy">Easy</option>
                            <option value="Medium">Medium</option>
                            <option value="Hard">Hard</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Briefly describe the recipe and preparation details..." required></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Recipe Image</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="image" id="recipe-image" required>
                    </div>
                </div>

                <button type="submit" name="add" class="submit-btn-admin">
                    <i class="fa-solid fa-plus-circle"></i> Add Recipe
                </button>
            </form>
        </div>

        <div class="recipe-list-section">
            <h2>Recipe Collection</h2>

            <div class="admin-recipe-grid">
                <?php while ($row = $recipes->fetch_assoc()): ?>
                    <div class="recipe-admin-card">
                        <div class="card-img-container">
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <span class="difficulty-badge <?php echo strtolower($row['difficulty']); ?>">
                                <?php echo htmlspecialchars($row['difficulty']); ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>

                            <div class="tag-group">
                                <span class="tag"><i class="fa-solid fa-globe"></i> <?php echo htmlspecialchars($row['cuisine_type']); ?></span>
                                <span class="tag highlight"><i class="fa-solid fa-leaf"></i> <?php echo htmlspecialchars($row['dietary_preference']); ?></span>
                            </div>

                            <p class="recipe-desc"><?php echo htmlspecialchars($row['description']); ?></p>

                            <div class="card-actions">
                                <a href="edit_recipe.php?id=<?php echo $row['id']; ?>" class="btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="delete_recipe.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this recipe?')" class="btn-delete">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

    </div>

</body>

</html>
<?php
session_start();
include "db.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied. Admin only.");
}

$id = $_GET['id'];

$sql = "SELECT * FROM recipes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();

if(isset($_POST['update'])) {

    $title = $_POST['title'];
    $cuisine = $_POST['cuisine_type'];
    $dietary = $_POST['dietary_preference'];
    $difficulty = $_POST['difficulty'];
    $description = $_POST['description'];

    if(!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $tmpName = $_FILES['image']['tmp_name'];

        move_uploaded_file($tmpName, "uploads/" . $imageName);

        $sql = "UPDATE recipes 
                SET title=?, cuisine_type=?, dietary_preference=?, difficulty=?, description=?, image=?
                WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $title, $cuisine, $dietary, $difficulty, $description, $imageName, $id);

    } else {

        $sql = "UPDATE recipes 
                SET title=?, cuisine_type=?, dietary_preference=?, difficulty=?, description=?
                WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $title, $cuisine, $dietary, $difficulty, $description, $id);
    }

    $stmt->execute();

    header("Location: admin_recipes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe | FoodFusion Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">

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
        
        <!-- Section Header -->
        <div class="admin-header">
            <h1>Edit <span class="span_text">Recipe</span></h1>
            <p>Modify recipe details, dietary preferences, or image.</p>
        </div>

        <!-- Edit Recipe Card Form -->
        <div class="admin-card">
            <h3><i class="fa-solid fa-pen-to-square"></i> Update Recipe Information</h3>

            <form method="POST" enctype="multipart/form-data" class="modern-form">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Recipe Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Cuisine Type</label>
                        <input type="text" name="cuisine_type" value="<?php echo htmlspecialchars($recipe['cuisine_type']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Dietary Preference</label>
                        <select name="dietary_preference" required>
                            <option value="">Select Diet</option>
                            <option value="Vegetarian" <?php if($recipe['dietary_preference'] == 'Vegetarian') echo 'selected'; ?>>Vegetarian</option>
                            <option value="Vegan" <?php if($recipe['dietary_preference'] == 'Vegan') echo 'selected'; ?>>Vegan</option>
                            <option value="Halal" <?php if($recipe['dietary_preference'] == 'Halal') echo 'selected'; ?>>Halal</option>
                            <option value="Gluten-Free" <?php if($recipe['dietary_preference'] == 'Gluten-Free') echo 'selected'; ?>>Gluten-Free</option>
                            <option value="Keto" <?php if($recipe['dietary_preference'] == 'Keto') echo 'selected'; ?>>Keto</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Difficulty Level</label>
                        <select name="difficulty" required>
                            <option value="Easy" <?php if($recipe['difficulty'] == "Easy") echo "selected"; ?>>Easy</option>
                            <option value="Medium" <?php if($recipe['difficulty'] == "Medium") echo "selected"; ?>>Medium</option>
                            <option value="Hard" <?php if($recipe['difficulty'] == "Hard") echo "selected"; ?>>Hard</option>
                        </select>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo htmlspecialchars($recipe['description']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Recipe Image</label>
                    <div class="current-img-preview" style="margin-bottom: 12px;">
                        <span style="display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 6px;">Current Image:</span>
                        <img src="uploads/<?php echo htmlspecialchars($recipe['image']); ?>" alt="Recipe Image" style="width: 150px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1;">
                    </div>
                    <div class="file-upload-wrapper">
                        <input type="file" name="image">
                    </div>
                    <small style="color: #64748b; font-size: 0.8rem; margin-top: 4px; display: block;">Leave blank if you do not want to change the image.</small>
                </div>

                <div class="form-actions" style="margin-top: 20px;">
                    <button type="submit" name="update" class="submit-btn-admin">
                        <i class="fa-solid fa-floppy-disk"></i> Update Recipe
                    </button>
                    <a href="admin_recipes.php" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>

</body>
</html>
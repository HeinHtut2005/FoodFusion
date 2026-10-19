<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied. Admin only.");
}

if (isset($_POST['add_resource'])) {
    $resource_category = $_POST['resource_category'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validate category and inputs
    if (!in_array($resource_category, ['Culinary', 'Educational'], true)) {
        die("Invalid resource category.");
    }

    if ($title === '' || $description === '') {
        die("Title and description are required.");
    }

    // Check if file was uploaded without errors
    if (!isset($_FILES['resource_file']) || $_FILES['resource_file']['error'] === UPLOAD_ERR_NO_FILE) {
        die("Please select a file to upload.");
    }

    if ($_FILES['resource_file']['error'] !== UPLOAD_ERR_OK) {
        die("File upload failed with error code: " . $_FILES['resource_file']['error']);
    }

    $originalName = basename($_FILES['resource_file']['name']);
    $fileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Allowed file extensions
    $allowedTypes = ['pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'webp', 'mp4'];
    if (!in_array($fileType, $allowedTypes, true)) {
        die("Unsupported file type.");
    }

    // Ensure upload directory exists
    $uploadDir = __DIR__ . '/uploads/resources/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        die("Unable to create upload directory.");
    }

    // Generate safe unique filename
    $fileName = uniqid('resource_', true) . '.' . $fileType;
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['resource_file']['tmp_name'], $targetPath)) {
        die("Failed to move uploaded file.");
    }

    // Database insertion using prepared statements
    $sql = "INSERT INTO resources 
            (resource_category, title, description, file_name, file_type)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("sssss", $resource_category, $title, $description, $fileName, $fileType);

    if (!$stmt->execute()) {
        die("Database insert failed: " . $stmt->error);
    }

    header("Location: admin_resources.php?success=1");
    exit();
}

$resources = $conn->query("SELECT * FROM resources ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Resources | FoodFusion</title>
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
            <h1>Manage <span class="span_text">Resources</span></h1>
            <p>Upload educational and culinary PDFs, documents, or guides for users.</p>
        </div>

        <!-- Add Resource Form Card -->
        <div class="admin-card">
            <h3><i class="fa-solid fa-cloud-arrow-up"></i> Upload New Resource</h3>

            <form method="POST" enctype="multipart/form-data" class="modern-form">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Resource Category</label>
                        <select name="resource_category" required>
                            <option value="">Select Resource Category</option>
                            <option value="Culinary">Culinary Resources</option>
                            <option value="Educational">Educational Resources</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Resource Title</label>
                        <input type="text" name="title" placeholder="e.g. Basic Cooking Knife Techniques PDF" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Write a summary about this resource file..." required></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Resource File (PDF, DOCX, ZIP, etc.)</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="resource_file" required>
                    </div>
                </div>

                <button type="submit" name="add_resource" class="submit-btn-admin">
                    <i class="fa-solid fa-upload"></i> Upload Resource
                </button>
            </form>
        </div>

        <!-- Resource List Section -->
        <div class="recipe-list-section">
            <h2><i class="fa-solid fa-folder-open"></i> Uploaded Resources List</h2>

            <div class="admin-resource-grid">
                <?php while ($row = $resources->fetch_assoc()): ?>
                    <?php
                    $ext = strtolower($row['file_type']);
                    $iconClass = "fa-file";
                    if (in_array($ext, ['pdf'])) {
                        $iconClass = "fa-file-pdf";
                    } elseif (in_array($ext, ['doc', 'docx'])) {
                        $iconClass = "fa-file-word";
                    } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $iconClass = "fa-file-image";
                    } elseif (in_array($ext, ['zip', 'rar'])) {
                        $iconClass = "fa-file-zipper";
                    }
                    ?>

                    <div class="resource-admin-card">
                        <div class="resource-card-header">
                            <div class="file-icon-box">
                                <i class="fa-solid <?php echo $iconClass; ?>"></i>
                            </div>
                            <span class="category-badge">
                                <?php echo htmlspecialchars($row['resource_category']); ?>
                            </span>
                        </div>

                        <div class="resource-card-body">
                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p class="resource-desc"><?php echo htmlspecialchars($row['description']); ?></p>

                            <div class="file-info">
                                <i class="fa-solid fa-paperclip"></i>
                                <span class="file-name" title="<?php echo htmlspecialchars($row['file_name']); ?>">
                                    <?php echo htmlspecialchars($row['file_name']); ?>
                                </span>
                            </div>

                            <div class="card-actions">
                                <a href="uploads/resources/<?php echo htmlspecialchars($row['file_name']); ?>" download class="btn-download">
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                                <a href="edit_resource.php?id=<?php echo $row['id']; ?>" class="btn-edit">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>
                                <a href="delete_resource.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this resource?')" class="btn-delete">
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
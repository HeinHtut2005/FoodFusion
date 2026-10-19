
<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. Admin only.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid resource ID.");
}

$stmt = $conn->prepare("SELECT * FROM resources WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resource = $stmt->get_result()->fetch_assoc();

if (!$resource) {
    die("Resource not found.");
}

if (isset($_POST['update'])) {
    $category = $_POST['resource_category'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!in_array($category, ['Culinary', 'Educational'], true)) {
        die("Invalid resource category.");
    }

    if ($title === '' || $description === '') {
        die("Title and description are required.");
    }

    $fileName = $resource['file_name'];
    $fileType = $resource['file_type'];

    if (
        isset($_FILES['resource_file']) &&
        $_FILES['resource_file']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        if ($_FILES['resource_file']['error'] !== UPLOAD_ERR_OK) {
            die("File upload failed. Please try again.");
        }

        $originalName = basename($_FILES['resource_file']['name']);
        $newFileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowedTypes = [
            'pdf', 'doc', 'docx', 'zip', 'rar',
            'jpg', 'jpeg', 'png', 'webp', 'mp4'
        ];

        if (!in_array($newFileType, $allowedTypes, true)) {
            die("Unsupported file type.");
        }

        $uploadDir = __DIR__ . '/uploads/resources/';

        if (!is_dir($uploadDir) &&
            !mkdir($uploadDir, 0755, true)) {
            die("Unable to create upload directory.");
        }

        $newFileName = uniqid('resource_', true) . '.' . $newFileType;

        if (!move_uploaded_file(
            $_FILES['resource_file']['tmp_name'],
            $uploadDir . $newFileName
        )) {
            die("Unable to upload the new file.");
        }

        $fileName = $newFileName;
        $fileType = $newFileType;
    }

    $sql = "UPDATE resources
            SET resource_category = ?,
                title = ?,
                description = ?,
                file_name = ?,
                file_type = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssi",
        $category,
        $title,
        $description,
        $fileName,
        $fileType,
        $id
    );

    if (!$stmt->execute()) {
        die("Update failed: " . $stmt->error);
    }

    header("Location: admin_resources.php?updated=1");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resource | FoodFusion Admin</title>
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
            <h1>Edit <span class="span_text">Resource</span></h1>
            <p>Update existing resource details or replace the uploaded file.</p>
        </div>

        <!-- Edit Resource Form Card -->
        <div class="admin-card">
            <h3><i class="fa-solid fa-pen-to-square"></i> Update Resource Information</h3>
            
            <form method="POST" enctype="multipart/form-data" class="modern-form">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Resource Category</label>
                        <select name="resource_category" required>
                            <option value="Culinary" <?php if($resource['resource_category'] == "Culinary") echo "selected"; ?>>
                                Culinary Resources
                            </option>
                            <option value="Educational" <?php if($resource['resource_category'] == "Educational") echo "selected"; ?>>
                                Educational Resources
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Resource Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($resource['title']); ?>" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo htmlspecialchars($resource['description']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Upload New File (Optional - Leave blank to keep existing file)</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="resource_file">
                    </div>
                    <p class="current-file-text">
                        <i class="fa-solid fa-paperclip"></i> Current file: 
                        <strong><?php echo htmlspecialchars($resource['file_name']); ?></strong>
                    </p>
                </div>

                <div class="form-actions">
                    <button type="submit" name="update" class="submit-btn-admin">
                        <i class="fa-solid fa-floppy-disk"></i> Update Resource
                    </button>
                    <a href="admin_resources.php" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>

</body>
</html>
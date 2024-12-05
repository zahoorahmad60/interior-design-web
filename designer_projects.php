<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "interior_design1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve POST data
$designerID = isset($_POST['designer_id']) ? (int)$_POST['designer_id'] : null;
$title = isset($_POST['title']) ? $_POST['title'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';
$dateCreated = date('Y-m-d');

$image = '';
if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $image = basename($_FILES['image']['name']);
    $targetDirectory = "uploads/";
    $targetFile = $targetDirectory . $image;

    // Move the uploaded file to the target directory
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        echo "Error uploading the file.";
        exit();
    }
}

// Check if DesignerID exists in interior_designer table
$checkDesignerQuery = "SELECT DesignerID FROM interior_designer WHERE DesignerID = ?";
$stmt = $conn->prepare($checkDesignerQuery);
$stmt->bind_param("i", $designerID);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // DesignerID exists, proceed with the insertion
    $insertProjectQuery = "INSERT INTO projects (DesignerID, Title, Description, Image, Date_Created) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertProjectQuery);
    $stmt->bind_param("issss", $designerID, $title, $description, $image, $dateCreated);

    if ($stmt->execute()) {
        // Redirect to project.php after successful insertion
        header("Location: projects.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Error: DesignerID does not exist.";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Add Project</title>
</head>
<body>
<?php include "header.php"; ?>
    <div class="form__container">
        <h2>Add New Project</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="designer_id" value="<?php echo $_GET['designer_id']; ?>">
            <div class="form__group">
                <label for="title">Project Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form__group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form__group">
                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image" required>
            </div>
            <div class="form__group">
                <input type="submit" value="Add Project">
            </div>
        </form>
    </div>
</body>
</html>

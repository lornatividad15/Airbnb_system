<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $description = $_POST['description'];

    if (isset($_FILES['images']) && count($_FILES['images']['name']) > 0) {
        $imageFiles = $_FILES['images'];
        $targetDir = "Images/";

        $coverImageIndex = -1;
        for ($i = 0; $i < count($imageFiles['name']); $i++) {
            if ($imageFiles['error'][$i] == 0) {
                $coverImageIndex = $i;
                break;
            }
        }

        if ($coverImageIndex === -1) {
            echo "<script>alert('Please upload at least one valid image.');</script>";
        } else {
            $coverImage = basename($imageFiles['name'][$coverImageIndex]);
            $coverImagePath = $targetDir . $coverImage;

            if (move_uploaded_file($imageFiles['tmp_name'][$coverImageIndex], $coverImagePath)) {
                $sql = "INSERT INTO condos (name, image_path, description, city, address_details) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssss", $name, $coverImage, $description, $city, $address);
                    if (mysqli_stmt_execute($stmt)) {
                        $condo_id = mysqli_insert_id($conn); 

                        for ($i = 0; $i < count($imageFiles['name']); $i++) {
                            if ($imageFiles['error'][$i] != 0) {
                                continue; 
                            }

                            $imageName = basename($imageFiles['name'][$i]);
                            $tmpName = $imageFiles['tmp_name'][$i];
                            $imagePath = $targetDir . $imageName;

                            if ($i != $coverImageIndex) {
                                if (!move_uploaded_file($tmpName, $imagePath)) {
                                    continue;
                                }
                            }

                            $insertImageSQL = "INSERT INTO condo_images (condo_id, image_path) VALUES (?, ?)";
                            $imgStmt = mysqli_prepare($conn, $insertImageSQL);
                            mysqli_stmt_bind_param($imgStmt, "is", $condo_id, $imageName);
                            mysqli_stmt_execute($imgStmt);
                            mysqli_stmt_close($imgStmt);
                        }

                        echo "<script>alert('Condo and images added successfully!'); window.location.href='admin_page.php';</script>";
                        exit;
                    } else {
                        echo "<script>alert('Failed to add condo.');</script>";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    echo "<script>alert('Failed to prepare statement.');</script>";
                }
            } else {
                echo "<script>alert('Failed to upload cover image.');</script>";
            }
        }
    } else {
        echo "<script>alert('Please upload at least one image.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Condo</title>
  <link rel="stylesheet" href="CSS/add_condo.css" />
</head>
<body>

<header>
  <a href="Main Page.php" class="logo">
    <img src="Images/logo-light-transparent.png" alt="Site Logo">
  </a>
</header>

<main>
  <h1>Add New Condo</h1>

  <form id="addCondoForm" method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label for="name">Condo Name</label>
      <input type="text" id="name" name="name" placeholder="Enter condo name" required>
    </div>

    <div class="form-group">
      <label for="city">City</label>
      <select id="city" name="city" required>
        <option value="">-- Select City --</option>
        <option value="Tagaytay">Tagaytay</option>
        <option value="Cavite City">Cavite City</option>
        <option value="Dasmariñas">Dasmariñas</option>
        <option value="Bacoor">Bacoor</option>
        <option value="Imus">Imus</option>
        <option value="Alfonso">Alfonso</option>
        <option value="General Trias">General Trias</option>
        <option value="Silang">Silang</option>
        <option value="Tanza">Tanza</option>
        <option value="Trece Martires">Trece Martires</option>
      </select>
    </div>

    <div class="form-group">
      <label for="address">Address Details</label>
      <input type="text" id="address" name="address" placeholder="Street, Barangay, etc." required>
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description" placeholder="Enter condo description" rows="4" required></textarea>
    </div>

    <div class="form-group">
      <label for="images">Upload Images</label>
      <input type="file" id="images" name="images[]" accept="image/*" multiple required>
    </div>

    <button type="submit">Add Condo</button>
    <a href="admin_page.php" class="back-link">← Back to Admin Page</a>
  </form>
</main>

</body>
</html>
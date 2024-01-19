<?php
session_start();
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$loggedInUserId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

include("config.php");
$con = mysqli_connect($server, $username, $password, $database);

if ($con->connect_error) {
    die("conection failed: " . $con->conect_error);
}

$userCheckQuery = "SELECT * FROM user WHERE id = '$loggedInUserId'";
$userCheckResult = $con->query($userCheckQuery);

$categoryQuery = "SELECT * FROM categories";
$categoryResult = $con->query($categoryQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if 'category' and 'title' keys exist in $_POST
    if (isset($_POST['category']) && isset($_POST['title'])) {
        // Fetch form data
        $desiredCategoryId = intval($_POST['category']);
        $title = $con->real_escape_string($_POST['title']);

        // Check if the selected category exists
        $categoryCheckQuery = "SELECT * FROM categories WHERE category_id = '$desiredCategoryId'";
        $categoryCheckResult = $con->query($categoryCheckQuery);

        if ($categoryCheckResult->num_rows > 0) {
            // Insert into posts table
            $insertQuery = "INSERT INTO posts (post_title, category_id, user_id) 
                            VALUES ('$title', '$desiredCategoryId', '$loggedInUserId')";
            if ($con->query($insertQuery) === TRUE) {
                echo "Post added successfully";
            } else {
                echo "Error: " . $insertQuery . "<br>" . $con->error;
            }
        } else {
            echo "Selected category does not exist";
        }
        
    } elseif (isset($_FILES["image"])) {
        // Handle profile picture upload
        $name = $_FILES["image"]["name"];
        $tmpname = $_FILES["image"]["tmp_name"];
        $type = $_FILES["image"]["type"];
        $file_extension = pathinfo($name, PATHINFO_EXTENSION);

        if ($type === 'image/png' && $file_extension === 'png') {
            $upload = move_uploaded_file($tmpname, "upload-images/" . $name);

            if ($upload) {
                // Update user's profile picture field
                $updateQuery = "UPDATE user SET profile_picture = '$name' WHERE id = '$loggedInUserId'";
                $updateResult = $con->query($updateQuery);

                if ($updateResult) {
                    echo "Profile picture updated successfully";
                } else {
                    echo "Failed to update profile picture";
                }
            } else {
                echo "Failed to upload the file.";
            }
        } else {
            echo "Only PNG files are allowed.";
        }
    } else {
        echo "Category and title not provided";
    }
}

// Function to get the total number of comments for a post
function getTotalCommentCount($postId) {
    global $con;

    // Escape the input to prevent SQL injection
    $postId = mysqli_real_escape_string($con, $postId);

    $sql = "SELECT COUNT(*) AS total_comments FROM comments WHERE post_id = $postId";
    $result = $con->query($sql);
    $row = $result->fetch_assoc();

    return $row['total_comments'];
}

// Fetch user details
$userQuery = "SELECT * FROM user WHERE id = '$loggedInUserId'";
$userResult = $con->query($userQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

<style>
    .container {
    display: flex;
    flex-direction: row;
    flex: column;
    margin-left: 26px;
    margin-top: 70px;
    margin-right: 26px;
    justify-content: space-evenly;
}
.post-icons {
    display: flex;
    justify-content: space-between;
    margin-top: 10px; 
}

.post-icons img {
    width: 25px;
    height: auto;
}

#roundCorners {
    border-radius: 20px;
    width: 500px;
    height: 200px;
    background-color: rgb(226, 226, 235);
    padding-left: 35px;
    padding-top: 20px;
    max-height: 2000px;
    margin-bottom: 20px;
    margin-right: 26px; 
}


      #round{
        border-radius: 20px;
        width: 300px;
        height: 390px;
        background-color: rgb(226, 226, 235);
        padding-left: 35px;
        }
        
        .search-box{
            background: white;
            width: 430px;
            height: 40px;
            border-radius: 20px;
            display: flex;
            align-items: center;            
        }

        .search-box input{
            width: 100%;
            background: transparent;
            outline: none;
            border: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        select, input {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

 .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    justify-content: center;
    align-items: center;
}



.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    /* Remove display and flex-direction properties */
}

/* Add these styles to center the modal content horizontally and vertically */
.modal-content {
    margin: auto;
}

/* Add this style to set a maximum width for the modal content */
.modal-content {
    max-width: 600px; /* Adjust as needed */
}
input[type="submit"] {
    background-color: #4caf50;
    color: #fff;
    cursor: pointer;
    padding: 5px 10px; /* Adjust padding for smaller size */
}

input[type="submit"]:hover {
    background-color: #45a049;
}

/* Adjusted styles for smaller buttons and increased post container width */
.post-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 80%; /* Adjust the width as needed */
}

.post {
    height: 120px;
    border-radius: 20px;
    width: 500px; /* Adjust the width as needed */
    margin-bottom: 20px;
    margin-left: 250px;
    background-color: rgb(226, 226, 235);
    padding-left: 60px;
    position: relative; /* Added to position the buttons */
}

/* Added styles for the edit and delete buttons */
.post form {
    position: absolute;
    top: 10px; /* Adjust top position as needed */
    right: 10px; /* Adjust right position as needed */
    display: flex;
}

.post form button {
    margin-left: 5px;
}
.blueDot{
    position: relative;

}
.blueDot::after{
    content: '';
    width: 10px;
    height: 8px;
    border: 2px solid #fff;
    border-radius: 50%;
    left: 0%;
    top: 0%;
    background: blue;
    position: absolute;
}


.profile-card {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin: 20px;
            overflow: hidden;
            width: 300px;
            text-align: center;
            padding: 20px;
        }

        .profile-image {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .profile-name {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-title {
            color: #888;
            margin-bottom: 15px;
        }

        .profile-info {
            text-align: left;
            margin-bottom: 15px;
        }

        .profile-info p {
            margin: 5px 0;
        }

        .profile-link {
            color: #0366d6;
            text-decoration: none;
            font-weight: bold;
        }

.post-actions {
    display: flex;
    align-items: center;
    margin-top: 10px;
}

.post-actions a {
    text-decoration: none;
    color: #0366d6;
    margin-right: 10px;
    font-weight: bold;
}

.post-actions h5 {
    margin: 0;
    color: #888;
}

.post-actions img {
    width: 20px;
    height: auto;
    margin-right: 5px;
}

/* Add hover effect for links */
.post-actions a:hover {
    text-decoration: underline;
}

/* Adjusted styles for smaller buttons and increased post container width */
.post-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 80%; /* Adjust the width as needed */
}

.post {
    height: 120px;
    border-radius: 20px;
    width: 500px; /* Adjust the width as needed */
    margin-bottom: 20px;
    margin-left: 250px;
    background-color: rgb(226, 226, 235);
    padding-left: 60px;
    position: relative; /* Added to position the buttons */
}

/* Added styles for the edit and delete buttons */
.post form {
    position: absolute;
    top: 10px; /* Adjust top position as needed */
    right: 10px; /* Adjust right position as needed */
    display: flex;
}

.post form button {
    margin-left: 5px;
}

/* Updated profile-link color for consistency */
.profile-link {
    color: #0366d6;
    text-decoration: none;
    font-weight: bold;
}

/* Add hover effect for profile-link */
.profile-link:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <?php 
    include "navbar.php";
    ?>
<h2>Add a Post</h2>

<div class="container">
<div class="profile-card">
    <?php
    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
        // Display user profile details
        echo "<div class='profile-section'>";
        echo "<form action='post form.php' method='post' enctype='multipart/form-data'>";
        echo "<label for='image'>Change Profile Picture:</label>";
        echo "<input type='file' name='image' id='image' accept='image/*'>";
        echo "<input type='submit' value='Upload'>";
        echo "</form>";

        // Display the user's profile picture
        if (!empty($user['profile_picture'])) {
            echo "<img src='upload-images/{$user['profile_picture']}' alt='Profile Picture' class='profile-image'>";
        } else {
            echo "<p>No profile picture available</p>";
        }

        echo "<div class='user-details'>";
        echo "<h3>{$user['name']}</h3>";
        echo "<p>Email: {$user['email']}</p>";
        echo "</div>";
        echo "</div>";
    } else {
        // Handle the case where user details are not found
        echo "User details not found";
    }
    ?>
    <div class="profile-links">
        <a class="profile-link" href="my_posts.php?user_id=<?php echo $loggedInUserId; ?>">My Posts</a>
    </div>
</div>

    <div id="roundCorners">
        <div class="search-box" onclick="openModal()">
            <img src="Images\icons8-create-30.png">
            <input type="text" style="font-size: 15px;" placeholder="   Start a post" id="searchInput">
        </div>
        <div class="post-icons">
            <div class="col">
                <img src="Images\icons8-image-25.png"><br>Photo
            </div>
            <div class="col">
                <img src="Images\icons8-next-25.png"><br>Video
            </div>
            <div class="col">
                <img src="Images\icons8-documents-25.png"><br>Document
            </div>
            <div class="col">
                <img src="Images\icons8-newspaper-icon-25.png"><br>Write article
            </div>
        </div>
    </div>
    <div id="round">
            <h4 style="padding-left: 30px;">Website News</h4><br><br>
            <div class="blueDot" ></div><br><br>
            <div class="blueDot"></div><br><br>
            <div class="blueDot"></div><br><br>
            <div class="blueDot"></div><br><br>
            <div class="blueDot"></div><br><br>
            <div class="blueDot"></div><br><br>
        </div>
</div>

<div class="post-container">
    <?php
    $postsQuery = "SELECT * FROM posts";
    $postsResult = $con->query($postsQuery);

    if ($postsResult === false) {
        echo "Error fetching posts: " . $con->error;
    }

    while ($post = $postsResult->fetch_assoc()) {
        echo "<div class='post'>";
        echo "<h3>{$post['post_title']}</h3>";

        $categoryId = $post['category_id'];
        $categoryNameQuery = "SELECT category_name FROM categories WHERE category_id = $categoryId";
        $categoryNameResult = $con->query($categoryNameQuery);

        if ($categoryNameResult->num_rows > 0) {
            $category = $categoryNameResult->fetch_assoc();
            echo "<h3>{$category['category_name']}</h3>";
        } else {
            echo "<h3>Category Not Found</h3>";
        }

        // Display links for each post
        echo "<div class='post-actions'>";

        // Check if the post was created by the logged-in user
        if ($post['user_id'] == $loggedInUserId) {
            // Show delete button only for posts created by the logged-in user
            echo "<a href='Delete.php?post_id={$post['id']}'>Delete</a>&nbsp;&nbsp;";
        }

        // Display links for each post
        echo "<a href='comments.php?post_id={$post['id']}'>Comments</a>&nbsp;&nbsp";
        echo "<h5>Total Comments: " . getTotalCommentCount($post['id']) . "</h5>&nbsp;&nbsp";
        

        echo "</div>";

        echo "</div>";
    }

    ?>


<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">WRITE A POST</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="post form.php" method="post">
            <label for="category">Select Category:</label>
            <select name="category" id="category" required>
                <?php
                // Display category options for the form
                $categoryResult->data_seek(0);
                while ($category = $categoryResult->fetch_assoc()) {
                    echo "<option value='{$category['category_id']}'>{$category['category_name']}</option>";
                }
                ?>
            </select>
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>

            <input type="submit" value="Submit">
        </form>
    </div>
</div>


<script>
    function openModal() {
        var modal = document.getElementById('editModal');
        modal.style.display = 'block';
    }

    function closeModal() {
        var modal = document.getElementById('editModal');
        modal.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var closeButton = document.querySelector('.btn-close');
        closeButton.addEventListener('click', closeModal);
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
<?php
$con->close();
?>




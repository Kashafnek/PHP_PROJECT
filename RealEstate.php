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

// Function to get the total number of comments for a post
function getTotalCommentCount($postId) {
    global $con;

    // Escape the input to prevent SQL injection
    $postId = mysqli_real_escape_string($con, $postId);

    $sql = "SELECT COUNT(*) AS total_comments FROM comments WHERE post_id = $postId";
    $result = $con->query($sql);

    if ($result === false) {
        die("Error executing query: " . $con->error);
    }

    $row = $result->fetch_assoc();

    return $row['total_comments'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        .navbar {
            background-color: #333;
            padding: 10px;
            color: white;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        .post-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin: 20px;
        }

        .post {
            background-color: #fff;
            border: 1px solid #ddd;
            margin: 10px;
            padding: 15px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .post h3 {
            color: #333;
        }

        .post p {
            color: #666;
        }

        .post-actions {
            margin-top: 10px;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php 
    include "navbar.php";
    ?>

    <h2>All Posts</h2>

    <div class="post-container">
        <?php
        // Check if categoryId is provided in the URL
        if (isset($_GET['categoryId'])) {
            $categoryId = mysqli_real_escape_string($con, $_GET['categoryId']);

            // Fetch posts with user information and category name for the specified category
            $postsByCategoryQuery = "SELECT posts.*, user.name as username, categories.category_name 
                                    FROM posts 
                                    JOIN user ON posts.user_id = user.id 
                                    JOIN categories ON posts.category_id = categories.category_id
                                    WHERE posts.category_id = $categoryId";
            $postsByCategoryResult = $con->query($postsByCategoryQuery);

            if ($postsByCategoryResult === false) {
                die("Error fetching posts: " . $con->error);
            }

            while ($post = $postsByCategoryResult->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h3>{$post['post_title']}</h3>";
                echo "<p>Posted by: {$post['username']}</p>";
                echo "<p>Category: {$post['category_name']}</p>";

                // Display links for each post
                echo "<div class='post-actions'>";
                echo "<a href='comments.php?post_id={$post['id']}'>Comments</a>&nbsp;&nbsp";
                echo "<h5>Total Comments: " . getTotalCommentCount($post['id']) . "</h5>&nbsp;&nbsp";
                echo "</div>";

                echo "</div>";
            }
        } else {
            echo "Please provide a category ID in the URL.";
        }
        ?>
    </div>

    <?php
    $con->close();
    ?>
</body>
</html>

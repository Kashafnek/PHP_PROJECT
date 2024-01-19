<?php
session_start();
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

include("config.php");
$conn = mysqli_connect($server, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = isset($_GET['user_id']) ? $_GET['user_id'] : null;

$postsQuery = "SELECT * FROM posts WHERE user_id = '$userId'";
$postsResult = $conn->query($postsQuery);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <style>
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

        .post-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        .post {
            height: 120px;
            border-radius: 20px;
            width: 500px;
            margin-bottom: 20px;
            background-color: rgb(226, 226, 235);
            padding-left: 60px;
            position: relative;
        }

        .post h3 {
            margin-top: 10px;
        }

        .post form {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
        }

        .post form button {
            margin-left: 5px;
        }
    </style>
</head>
<body>
<?php 
    include "navbar.php";
    ?>
    <h2>User Posts</h2>

    <div class="post-container">
        <?php
        while ($post = $postsResult->fetch_assoc()) {
            echo "<div class='post'>";  
            echo "<h3>{$post['post_title']}</h3>";
            echo "</div>";
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>

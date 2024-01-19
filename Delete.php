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
    die("conection failed: " . $con->connect_error);
}

// Check if the post ID is set in the URL
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    // Delete comments associated with the post
    $deleteCommentsQuery = "DELETE FROM comments WHERE post_id = '$post_id'";
    $con->query($deleteCommentsQuery);

    // Now, delete the post
    $deletePostQuery = "DELETE FROM posts WHERE id = '$post_id' AND user_id = '$loggedInUserId'";
    
    if ($con->query($deletePostQuery) === TRUE) {
        header("location:/PORTAL_PHP/post form.php");
    } else {
        echo "Error deleting post: " . $con->error;
    }
}

// Close the conection
$con->close();
?>

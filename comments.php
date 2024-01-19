<?php
session_start();
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$postId = isset($_GET['post_id']) ? $_GET['post_id'] : (isset($_POST['form_post_id']) ? $_POST['form_post_id'] : null);
$loggedInUserId = isset($_SESSION['id']) ? $_SESSION['id'] : null;

include("config.php");
$con = mysqli_connect($server, $username, $password, $database) or die(mysqli_error($con));

$userQuery = "SELECT * FROM user WHERE id = '$loggedInUserId'";

// Fetch user information
$userResult = $con->query($userQuery);
$user = $userResult->fetch_assoc();

$insert = false;

function displayComment($comment, $postId, $indent = 0)
{
    global $con, $loggedInUserId;

    // Apply styling for indentation and comment container
    echo "<div class='comment-container' style='margin-left: " . ($indent * 20) . "px;'>";

    // Fetch user information
    $userQuery = "SELECT * FROM user WHERE id = '{$comment['user_id']}'";
    $userResult = $con->query($userQuery);
    $user = $userResult->fetch_assoc();

    // Display comment
    echo "<p>{$user['name']} said: {$comment['comment']} 
    [<a href='javascript:void(0);' onclick='toggleReplyForm({$postId}, {$comment['id']})'>Reply</a>]</p>";

    // Form for replying to a comment
    echo "<form id='replyForm_{$comment['id']}_{$postId}' style='display:none;' method='post' action='comments.php?reply={$comment['id']}&post_id={$postId}'>";
    echo "<input type='hidden' name='user_id' value='{$loggedInUserId}'>"; 
    echo "<label for='comment'>Comment:</label>";
    echo "<textarea name='comment' required></textarea><br>";
    echo "<input type='hidden' name='parent_id' value='{$comment['id']}'>";
    echo "<input type='hidden' name='post_id' value='{$postId}'>";
    echo "<input type='submit' name='submit_reply' value='Submit'>";
    echo "</form>";

    // Recursively display replies
    foreach ($comment['replies'] as $reply) {
        displayComment($reply, $postId, $indent + 1);
    }

    // Close comment-container
    echo "</div>";
}

// Function to fetch replies for a comment
function fetchReplies($postId, $parentCommentId)
{
    global $con;

    $replies = array();

    $sql = "SELECT * FROM comments WHERE post_id = $postId AND parent_id = $parentCommentId ORDER BY created_at DESC";
    $result = $con->query($sql);

    while ($row = $result->fetch_assoc()) {
        // Recursively fetch replies for each reply
        $row['replies'] = fetchReplies($postId, $row['id']);
        $replies[] = $row;
    }

    return $replies;
}

// Function to display comments
function displayComments($postId)
{
    global $con;

    // Fetch top-level comments (parent_id = 0)
    $sql = "SELECT * FROM comments WHERE post_id = $postId AND parent_id = 0 ORDER BY created_at DESC";
    $result = $con->query($sql);

    while ($row = $result->fetch_assoc()) {
        // Fetch replies for each top-level comment
        $row['replies'] = fetchReplies($postId, $row['id']);

        // Display top-level comment
        displayComment($row, $postId);
    }
}

// Function to retrieve a comment for editing
function getCommentForEditing($commentId) {
    global $con;

    $sql = "SELECT * FROM comments WHERE id = $commentId";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

// Handle form submission for adding a new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $user['name']; 
    $comment = isset($_POST['comment']) ? mysqli_real_escape_string($con, $_POST['comment']) : '';
    $parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : ''; 
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : null;

    if (isset($post_id) && !is_null($post_id)) {
        $sqlInsert = "INSERT INTO comments (user_id, comment, parent_id, post_id) VALUES ('$loggedInUserId', '$comment', $parent_id, $post_id)";
        $result = $con->query($sqlInsert);
    
        if (!$result) {
            // Print the SQL error for debugging
            echo "Error: " . $con->error;
        } else {
            // Redirect after successful comment insertion
            header("location:/PORTAL_PHP/comments.php?post_id={$post_id}");
            exit();
        }
    }
}

// Display comments
if ($insert) {
    echo "";
}

// Handle like link click
if (isset($_POST['like'])) {
    $like_id = $_POST['like'];
    $sql = "INSERT INTO likes (comment_id) VALUES ($like_id)";
    $con->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Comment System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h2 {
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }

        .comment-container {
            margin-left: 20px;
            margin-bottom: 20px;
        }

        .comment {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            margin-bottom: 10px;
        }

        .comment a {
            text-decoration: none;
            color: #007bff;
            margin-right: 10px;
        }

        .comment a:hover {
            text-decoration: underline;
        }

        .comment p {
            margin: 0; /* Remove default paragraph margin */
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .like-comment {
            color: #007bff;
        }

        .like-comment:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>
    <h2>Comments</h2>

    <?php
    // Display error message if deletion failed
    if (isset($_GET['error']) && $_GET['error'] === 'delete_failed') {
        echo "<p style='color: red;'>Comments with replies cannot be deleted.</p>";
    }

    // Display comments
    if ($postId !== null) {
        displayComments($postId);
    }
    ?>

    <script>
        // JavaScript function to toggle the visibility of the reply form
        function toggleReplyForm(postId, commentId) {
            console.log("toggleReplyForm called with postId: " + postId + " and commentId: " + commentId);
            var replyForm = document.getElementById('replyForm_' + commentId + '_' + postId);
            replyForm.style.display = (replyForm.style.display === 'none' || replyForm.style.display === '') ? 'block' : 'none';
        }
    </script>

    <h2>Add a Comment</h2>
    <form method="post" action="comments.php">
        <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
        <input type="hidden" name="user_id" value="<?php echo $loggedInUserId; ?>">
        <label for="comment">Comment:</label>
        <textarea name='comment' required></textarea><br>
        <input type="hidden" name="parent_id" value="0"> 
        <input type="submit" value="Submit">
        <input type="hidden" name="form_post_id" value="<?php echo $postId; ?>">
    </form>
</body>
</html>

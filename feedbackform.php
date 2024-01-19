<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

include("config.php");
$insert = false;

$con = mysqli_connect($server, $username, $password, $database);

if (!$con) {
    die("Connection Failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $a = $_POST["name"];
    $b = $_POST["email"];
    $c = $_POST["feedbackType"];
    $d = $_POST["feedback"];

    $sql = "INSERT INTO feedback_form (name, email, feed_type, feedback) 
            VALUES ('$a', '$b', '$c', '$d')";
    $result = mysqli_query($con, $sql);

    if ($result) {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                 <strong>Thank You!!</strong> Your feedback has been sent successfully.
                 <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close' style='top: 0;'></button>
               </div>";
    } else {
        // Print detailed error message
        echo "Error: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
       

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            margin: 20px;
            margin-left: 400px;
            justify-content: center;
            align-items: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .alert {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 1000;
        }
    </style>
</head>
<body>
<?php 
    include "navbar.php";
    ?>

<form action="feedbackform.php" method="post">
        <label for="name">Your Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Your Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="feedbackType">Feedback Type:</label>
        <select id="feedbackType" name="feedbackType" required>
            <option value="compliment">Compliment</option>
            <option value="suggestion">Suggestion</option>
            <option value="issue">Issue</option>
        </select>

        <label for="comment">Your Feedback:</label>
        <textarea id="comment" name="feedback" rows="4" required></textarea>

        <button type="submit">Submit Feedback</button>
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>

<?php
session_start();

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $a = $_POST["name"];
    $b = $_POST["contact_no"];
    $c = $_POST["email"];
    $d = $_POST["subject"];
    $e = $_POST["message"];
   
    $sql = "INSERT INTO contact_form (name, contact_no, email, subject, message) 
        VALUES ('$a', '$b', '$c', '$d', '$e')";

    $result = mysqli_query($con, $sql);

        if ($result) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                     We wil contact you as soon as possible.<strong>Thank You!!</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close' style='top: 0;'></button>
                  </div>";
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
        .container {
            border: 2px solid #3498db; 
            border-radius: 10px; 
            padding: 20px;
            margin-top: 50px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        form {
            border: 2px solid #3498db; 
            border-radius: 10px; 
            padding: 20px;
            margin-top: 20px;
            max-width: 100%;
            margin-left: auto; 
            margin-right: auto; 
        }

        h1 {
            color: #3498db; 
        }

        label {
            color: #3498db; 
        }

        textarea,
        input {
            border: 1px solid #3498db; 
            border-radius: 5px;
            margin-bottom: 10px;
            width: 100%; 
            height: 30px;
        }

        button {
            background-color: #3498db; 
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            width: 100%; 
        }

        button:hover {
            background-color: #2980b9; 
        }
    </style>
</head>
<body>
<?php 
    include "navbar.php";
    ?>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <h1 class="mb-3">Contact Us</h1>
                <form action="contactform.php" method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact" class="form-label">Contact No</label>
                            <input type="text" class="form-control" id="contact" name="contact_no" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject">
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-dark fw-bold">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>


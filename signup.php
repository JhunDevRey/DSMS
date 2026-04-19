<?php

include "connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $access_level = $_POST['access_level'];

    $sql = "INSERT INTO `users`(`username`, `password`, `access_level`) VALUES ('$username', '$password', '$access_level')";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH">
    <link rel="stylesheet" href="css/signup2.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>DSMS Sign Up Page</title>
</head>

<body>
    <br><br><br>
    <h1 class="page-title">Digital Student Management System</h1>
    <div class="main">
        <p class="sign" align="center">Sign Up</p>
        <form class="form1" id="signupForm">
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input class="un" type="text" name="username" placeholder="Username" required >
            </div>

            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input class="pass" type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <div class="input-group">
                <i class="fa fa-key"></i>
                <input class="email" type="text" name="access_level" placeholder="Access Level" required>
            </div>

            <button class="submit" type="submit" name="submit">Sign in</button>

            <p class="option"><a href="login.php">Already have an account?</a></p>
        </form>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            fetch('signup.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Congratulations!',
                            text: 'You have successfully registered!',
                            icon: 'success'
                        }).then(function() {
                            window.location.href = 'login.php';
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Registration failed: ' + data.error,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error with your request.',
                        icon: 'error'
                    });
                });
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
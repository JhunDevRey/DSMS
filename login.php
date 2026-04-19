<?php
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND password ='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['access_level'] = $user['access_level'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login2.css">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>DSMS Login Page</title>
</head>

<body>
    <br><br><br>
    <h1 class="page-title">Digital Student Management System</h1><br>
    <div class="main">
        <p class="sign" align="center">Log In</p>
        <form class="form1" id="loginForm">
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input class="un" type="text" name="username" placeholder="Username" required >
            </div>

            <div class="input-group">
                <i class="fa fa-lock"></i>
                <input class="pass" type="password" id="password" name="password" placeholder="Password" required>
                <i class="fa fa-eye toggle-password" id="togglePassword"></i>
            </div>

            <button class="submit" type="submit" name="submit">Sign in</button>

            <p class="option"><a href="signup.php">Create an account</a></p>
            <p class="option"><a href="forgotpass.php">Forgot Password?</a></p>
        </form>
    </div>

    <!-- Eye Icon for Password Toggle -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            this.classList.toggle('fa-eye-slash');
        });
    </script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            fetch('login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'You have successfully logged in!',
                            icon: 'success',
                        }).then(function() {
                            window.location.href = 'dashboard.php';
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.error,
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
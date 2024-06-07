<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
    <link rel="icon" href="favicon.ico" type="image/ico">

</head>
<body id="login-body">
    <?php 
        $username = "name";
    ?>
    <section id="main">
        <div id="login">
            <form action="logic/login-logic.php" method="post">
                <div id="input-field">
                    <input type="text" placeholder=Username  name="username" id="email-login" class="login-input" requiired>
                    <br>
                    <input type="password" placeholder="Password" name="password" id="password-login" class="login-input" required>
                </div>
                <div id="button-container">
                    <button id="login-button" type="submit" name="login">Login</button>
                </div>
                <p id="register-display">Don't have an account? Click <span id="here-login"  class="here">here</span> to register</p>
            </form>
        </div>

        <div id="register">
            <form action="logic/register-logic.php" method= "post">
                <div id="input-field">
                    <input type="text" placeholder="Username" id="register-username" name="reg-username" class="register-input" required>
                    <br>
                    <input type="text" placeholder= "Name" id= "register-name" name = "reg-name" class = "register-input" required>
                    <br>
                    <input type="password" placeholder="Password" id="register-password" name= "reg-password" class="register-input" required>
                </div>
                <div id="register-button-container">
                    <button id="register-button">Register</button>
                </div>
                <p id="login-display">Already have an account? Click <span id="here-register" class="here">here</span> to login</p>
            </form>
        </div>
    </section>
    <script src="scripts/login.js"></script>
</body>
</html>
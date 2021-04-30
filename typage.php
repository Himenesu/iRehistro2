<?php require_once "controllerUserData.php"; ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>

        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

            <script
                src="https://kit.fontawesome.com/64d58efce2.js"
                crossorigin="anonymous"
            ></script>

                <link rel="stylesheet" href="style5.css" />
                    <title> iRegister | Registered </title>
    </head>   
<body>
<div class="container">
    <div class="forms-container">   
        <div class="signin-signup">
            <form action="login.php" method="POST">

            <div class="wrapper">
                <div class="form">
                    <div class="inputfield">
                        <div class="title">
                            <h2><strong>iRehistro</strong></h2>
                        </div>
                        <h2>You successfully registered!</h2><br>
                        <p> Please patiently wait for an email for further notice. The email that will receive contains a copy of your registration form. Thank you for your understanding. Have a great day!</p><br>
                    </div> 
                    <div class="inputfield"> 
                            <input class="btn" type="submit" name="homebtn" value="Ok">
                    </div>
                </div>            
            </div>

            </form>

        </div>
    </div>
</div>
</body>
</html>

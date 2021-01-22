<!DOCTYPE HTML>
<?php
    /*setting cookies locally*/
    $cookie_name = "username";
    $cookie_value = 'nobody';
    setcookie($cookie_name, $cookie_value, time() - (3600), "/"); // 3600=1hr 
    $_COOKIE['username'] = $cookie_value;
    
    $cookie_name = "token";
    $cookie_value = "empty";
    setcookie($cookie_name, $cookie_value, time() - (3600), "/"); // 3600=1hr 
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>logout</title>
        <link href="res/css/stylesheet.css?version=1" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div class="navbar-image" id="navbar-image">
            <img src="res/images/dpi-logo-cropped.png">
        </div>
        <div id="navbar">
            <ul>
            <?php 
                if(count($_COOKIE) == 0) {
                    echo '
                        <li><a href="index.php">Home</a></li>
                        <li><a href="projects.php">Projects</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="newsletter.php">Contact</a></li>
                        <li><a href="login.php">Login</a></li>
                    ';
                }
                else {
                    echo '
                        <li><a href="index.php">Home</a></li>
                        <li><a href="projects.php">Projects</a></li>
                        <li><a href="resources.php">Resources</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="changepassword.php">Change Password</a></li>
                    ';
                }
            ?>
            </ul>
        </div>

        <div class="title">
        logout
        </div>
        <div class="php-redirect">
            You've been logged out.
        </div>
    </body>
</html>

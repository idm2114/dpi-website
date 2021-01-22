<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Reset Password</title>
        <link href="res/css/stylesheet.css?version=1" rel="stylesheet" type="text/css" />
        <!--links for favicon-->
        <link rel="apple-touch-icon" sizes="180x180" href="res/images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="res/images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="res/images/favicon/favicon-16x16.png">
        <link rel="manifest" href="res/images/favicon/site.webmanifest">
    </head>
    <body>

        <div class="navbar-image" id="navbar-image">
            <img src="res/images/dpi-logo-cropped.png">
        </div>
        <div id="navbar">
            <ul>
            <?php
            ini_set('display_errors',1);
            if(count($_COOKIE) > 0) {
                echo '<li><a href="index.php">Home</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="resources.php">Resources</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="logout.php">Logout</a></li>';
            }
            else {
                echo '<li><a href="index.php">Home</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="newsletter.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a class="active" href="resetpassword.php">Reset Password</a></li>';
            }
            ?>
            </ul>
        </div>
<?php 
    if (!isset($_POST['submit'])) {
        $host = "localhost";
        $port = "5432"; $dbname = "dpi";
        $user = "postgres";
        $password = "cdpi1754psql";
        $connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} ";
        $dbconn = pg_connect($connection_string);

        $sql = "SELECT username, expiring FROM reset_password WHERE link = '".$_SERVER['QUERY_STRING']."';";
        $result = pg_query($dbconn,$sql);
        $username = null;
        $expiring = null;

        echo '
            <div class="title">
            reset password
            </div>

            <div class="change-passwd-container">
            <h1> Update password for DPI account</h1>';

        if (!$result) {
            echo '<div class="php-redirect">Something went wrong.</div>';
            exit;
        }

        while ($row = pg_fetch_row($result)) { 
            $username = $row[0];
            $expiring = $row[1];
        }
        $expiring = strtotime($expiring);

        $date = new DateTime();
        $timestamp = $date->format(DateTime::ATOM);

        if ($timestamp > $expiring) {
            echo '<div class="php-redirect">This link has expired. Please request a new one if you still need to reset your password</div>';
            exit;
        }
        echo '
            <form action="';
            echo'" method="post">
      
                <div class="form-group">
                  <label for="newpass">new password:</label>
                  <input type="password" class="form-control" id="newpass" placeholder="******" name="newpass">
                </div>
                <div class="hidden-form-group">
                  <input type="hidden" class="form-control" id="username" name="username" value="';
                  echo $username;
                echo '">
                </div>
                <input type="submit" name="submit" class="btn btn-primary" value="Submit">
              </form>
            <p id = "error"></p>
            </div>

            <div class="php-redirect">';
        }
        else {
            echo ' 
            <div class="php-redirect">
            ';
            $host = "localhost";
            $port = "5432"; $dbname = "dpi";
            $user = "postgres";
            $password = "cdpi1754psql";
            $connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} ";
            $dbconn = pg_connect($connection_string);

            $username = $_POST['username'];
            $password = $_POST['newpass'];

            $sql = "SELECT L.salt FROM Login L WHERE L.username = '".$username."'";
            $result= pg_query($dbconn,$sql);
            if (!$result) {
                echo "<h1> It appears your username doesn't exist in our database.</h1>";
                echo '<a href="changepassword.php">Click here to redirect</a>';
                exit;
            }
            $salt = null;
            while ($row = pg_fetch_row($result)) { 
                $salt = $row[0];
            }

            $computehash = hash('sha256', $salt.$_POST["newpass"]);
            $sql = "UPDATE Login SET hash = '".$computehash."' WHERE username = '".$username."'";
            $result=pg_query($sql);
            if (!$result) {
                echo '<h1> password change failed.</h1>';
                echo '<a href="changepassword.php">Click here to redirect</a>';
                exit;
            }

            $shellscript = "sudo /var/www/chpasswd"; 
            $cmd = $shellscript." ".$username." ".$password;
            exec($cmd, $output, $status);
            if ($status != 0) {
                echo '<h1> linux password change failed.</h1>';
                echo '<a href="changepassword.php">Click here to redirect</a>';
            }
            else {
                echo "<h1>Your password has been updated successfully</h1>";
                echo '<a href="index.php">Click here to redirect</a>';
            }
        }
?>
        </div>
    </body>
</html>

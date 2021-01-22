<?php
    if(count($_COOKIE)==0) {
        header("Location: http://3.23.38.146/index.php");
        exit;
    }
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Change Password</title>
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
                <li><a class="active" href="changepassword.php">Change Password</a></li>
                <li><a href="logout.php">Logout</a></li>';
            }
            else {
                echo '<li><a href="index.php">Home</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="newsletter.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>';
            }
            ?>
            </ul>
        </div>
<?php 
    ini_set('display_errors',1);
    if (!isset($_POST['submit'])) {
    echo '
        <div class="title">
        change password
        </div>

		<div class="change-passwd-container">
		<h1> Update your DPI account password </h1>
        ';
    echo '
		<form action="';
        echo $_SERVER["PHP_SELF"];
        echo'" method="post">
  
		    <div class="form-group">
		      <label for="username">username:</label>
		      <input type="text" class="form-control" id="username" placeholder="i_<3_DPI" name="username" required>
		    </div>
		    
		    <div class="form-group">
		      <label for="oldpass">old password:</label>
		      <input type="password" class="form-control" id="oldpass" placeholder="******" name="oldpass">
		    </div>
		    
		    <div class="form-group">
		      <label for="newpass">new password:</label>
		      <input type="password" class="form-control" id="newpass" placeholder="******" name="newpass">
		    </div>
		     
		    <input type="submit" name="submit" class="btn btn-primary" value="Submit">
		  </form>
		<p id = "error"></p>
        <a href="forgotpassword.php">Forgot your password?</a>
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

        $sql = "SELECT L.salt FROM Login L WHERE L.username = '".$_POST["username"]."';";
        $result = pg_query($dbconn,$sql);
        $salt = null;
        while ($row = pg_fetch_row($result)) { 
            $salt = $row[0];
        }

        $computehash = hash('sha256', $salt.$_POST["oldpass"]);

        $sql = "SELECT L.hash FROM Login L WHERE L.username = '".$_POST['username']."'";
        $result= pg_query($dbconn,$sql);
        if (!$result) {
            echo "<h1> It appears your username doesn't exist in our database.</h1>";
            echo '<a href="changepassword.php">Click here to redirect</a>';
            exit;
        }
        $hashval = null;
        while ($row = pg_fetch_row($result)) { 
            $hashval = $row[0];
        }

        if ($computehash != $hashval) {
            echo '<h1> password change failed.</h1>';
            echo '<a href="changepassword.php">Click here to redirect</a>';
            exit;
        }

        $computehash = hash('sha256', $salt.$_POST["newpass"]);
        $sql = "UPDATE Login SET hash = '".$computehash."' WHERE username = '".$_POST['username']."'";
        $result=pg_query($sql);
        if (!$result) {
            echo '<h1> password change failed.</h1>';
            echo '<a href="changepassword.php">Click here to redirect</a>';
            exit;
        }

        $shellscript = "sudo /var/www/chpasswd"; 
        $cmd = $shellscript." ".$_POST['username']." ".$_POST['newpass'];
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

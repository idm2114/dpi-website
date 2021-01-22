<html> 
	<head>
		<title> password recovery </title>
        <link rel="stylesheet" href="res/css/stylesheet.css" />
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
                <li><a href="index.php">Home</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="resources.php">Resources</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a class="active" href="changepassword.php">Change Password</a></li>
            </ul>
        </div>

        <div class="title">
        reset password
        </div>
        <?php
        if(!isset($_POST['submit'])) { 
            echo '
            <div class="change-passwd-container">
            <h1> recover your DPI password </h1>
            <h3> Fill out this form, and we will send you a password recovery link. </h3>
            <form action="'; 
            echo $_SERVER[PHP_SELF]; 
            echo '" method="post">
      
                <div class="form-group">
                  <label for="username">username:</label>
                  <input type="text" class="form-control" id="username" placeholder="Enter username" name="username" required>
                </div>
                
                <div class="form-group">
                  <label for="oldpass">email address:</label>
                  <input type="text" class="form-control" id="email" placeholder="abc@columbia.edu" name="email">
                </div>
                
                <input type="submit" name="submit" class="btn btn-primary" value="Submit">
              </form>
              <a href="index.php">return to home page</a>
            </div>
            ';
        }
        else {
            echo '<div class="php-redirect">';
            $host = "localhost";
            $port = "5432"; $dbname = "dpi";
            $user = "postgres";
            $password = "cdpi1754psql";
            $connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} ";
            $dbconn = pg_connect($connection_string);

            $sql = "SELECT L.email FROM Login L WHERE L.username = '".$_POST["username"]."';";
            $result = pg_query($dbconn,$sql);
            $email = null;
            while ($row = pg_fetch_row($result)) { 
                $email = $row[0];
            }
            if ($email != $_POST['email']) {
                echo '<h1>password recovery failed</h1>';
                echo "<p>The email you provided doesn't match our records for the email associated with this account.</p>";
                echo '<a href="index.php">Click here to redirect</a>';
            }
            else {

                $permitted_chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $link = substr(str_shuffle($permitted_chars), 0, 16);

                # timestamp 1day from today (link expires 24hours after being issued) 
                $date = new DateTime();
                $date->add(new DateInterval('P1D'));
                $timestamp = $date->format(DateTime::ATOM);

                /*inserting token into tokens database*/
                $sql = "UPDATE reset_password SET link = '".$link."' WHERE username = '".$_POST['username']."', expiring = '".$timestamp."' WHERE username = '".$_POST['username']."'";
                $result= pg_query($dbconn,$sql);
                if (!$result) {
                    $sql = "INSERT INTO reset_password(username,email,expiring,link) VALUES  ('".$_POST['username']."','".$email."','".$timestamp."','".$link."')";
                    $result= pg_query($dbconn,$sql);
                }

                $shellscript = "sudo /var/www/forgotpasswd"; 
                $cmd = $shellscript." ".$link." ".$_POST['email'];
                exec($cmd, $output, $status);
                if ($status != 0) {
                    echo '<h1>password recovery failed</h1>';
                    echo '<a href="index.php">Click here to redirect</a>';
                }
                else {
                    echo "<h1>password recovery email sent</h1>";
                    echo '<a href="index.php">Click here to redirect</a>';
                }
            }
            echo '</div>';
        }
    ?>
	</body>
</html>

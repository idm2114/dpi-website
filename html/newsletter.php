<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Contact Us</title>
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
                <li><a href="index.php">Home</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a class="active" href="newsletter.php">Contact</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
        
        <div class="title">
            contact us
        </div>
        <?php 
        if (!isset($_POST['submit'])) {
            echo '
            <div class="change-passwd-container">
            <h1>Sign up for our DPI newsletter to receive notifications about events, beta testing opportunities, and more!</h1>';
            echo '<form action="';
            echo $_SERVER['PHP_SELF']; 
            echo '" method="post">
                <div class="form-group">
                  <label for="firstname">first name:</label>
                  <input type="text" class="form-control" id="firstname" placeholder="John" name="firstname" required>
                </div>
                
                <div class="form-group">
                  <label for="lastname">last name:</label>
                  <input type="text" class="form-control" id="lastname" placeholder="Doe" name="lastname">
                </div>
                
                <div class="form-group">
                  <label for="email">email:</label>
                  <input type="text" class="form-control" id="email" placeholder="johndoe@columbia.edu" name="email">
                </div>
                 
                <input type="submit" name="submit" class="btn btn-primary" value="Submit">
              </form>
		</div> ';
        }
        else {
            echo '<div class="php-redirect">';
            $host = "localhost";
            $port = "5432"; $dbname = "dpi";
            $user = "postgres";
            $password = "cdpi1754psql";
            $connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} ";
            $dbconn = pg_connect($connection_string);

            $sql = "INSERT INTO listserv(email, firstname,lastname) VALUES ('".$_POST['email']."', '".$_POST['firstname']."', '".$_POST['lastname']."')"; 
            $result=pg_query($sql);

            if(!$result) {
                echo '<h1> Something went wrong... </h1>';
                echo "<a href='newsletter.php'>click here to try again</a>";

            }
            else {
                echo '<h1> Thanks for signing up '.$_POST['firstname'].'!</h1>';
                echo "<a href='index.php'>return home</a>";
            }
        }
    ?>
        </div>
    </body>
</html>

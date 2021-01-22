<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Login</title>
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
                if(count($_COOKIE) == 0) {
                    echo '
                        <li><a href="index.php">Home</a></li>
                        <li><a href="projects.php">Projects</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="newsletter.php">Contact</a></li>
                        <li><a class="active" href="login.php">Login</a></li>
                    ';
                }
                else {
                    echo '
                        <li><a href="index.php">Home</a></li>
                        <li><a href="projects.php">Projects</a></li>
                        <li><a href="resources.php">Resources</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="changepassword.php">Change Password</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    ';
                }
            ?>
            </ul>
        </div>

        <div class="title">
        login 
        </div>
        <?php
            if(!isset($_POST['submit'])) {
                echo '
                    <div class="change-passwd-container">
                    <h1> Enter your username and password </h1>
                    <form action="';
                echo $_SERVER['PHP_SELF'];
                echo '" method="post">
                        <div class="form-group">
                          <label for="username">username:</label>
                          <input type="text" class="form-control" id="username" placeholder="i_<3_DPI" name="username" required>
                        </div>
                        
                        <div class="form-group">
                          <label for="newpass">password:</label>
                          <input type="password" class="form-control" id="password" placeholder="******" name="password">
                        </div>
                        <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                      </form>
                    <a href="forgotpassword.php">Forgot your password?</a>
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

                $sql = "SELECT L.salt FROM Login L WHERE L.username = '".$_POST["username"]."';";
                $result = pg_query($dbconn,$sql);
                if (!$result) {
                    echo "<h1> It appears your username doesn't exist in our database.</h1>";
                    echo '<a href="login.php">Click here to redirect</a>';
                    exit;
                }
                    
                $salt = null;
                while ($row = pg_fetch_row($result)) { 
                    $salt = $row[0];
                }

                $computehash = hash('sha256', $salt.$_POST["password"]);

                $sql = "SELECT L.hash FROM Login L WHERE L.username = '".$_POST['username']."'";
                $result= pg_query($dbconn,$sql);
                $hashval = null;
                while ($row = pg_fetch_row($result)) { 
                    $hashval = $row[0];
                }

                if ($computehash != $hashval) {
                    echo '<h1> login failed: invalid username/password.</h1>';
                    echo '<a href="login.php">Click here to redirect</a>';
                    exit;
                }
                else {
                    /*setting cookies locally*/
                    $cookie_name = "username";
                    $cookie_value = $_POST['username'];
                    setcookie($cookie_name, $cookie_value, time() + (86400), "/"); // 86400 = 1 day
                    $_COOKIE['username'] = $cookie_value;
                    
                    $cookie_name = "token";
                    $permitted_chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                    $token = substr(str_shuffle($permitted_chars), 0, 64);
                    $cookie_value = $token;
                    setcookie($cookie_name, $cookie_value, time() + (86400), "/"); // 86400 = 1 day
                    $_COOKIE['token'] = $cookie_value;

                    $date = new DateTime();
                    $timestamp = $date->format(DateTime::ATOM);

                    /*inserting token into tokens database*/
                    $sql = "UPDATE tokens SET token = '".$token."' WHERE username = '".$_POST['username']."', timestamp = '".$timestamp."' WHERE username = '".$_POST['username']."'";
                    $result= pg_query($dbconn,$sql);
                    if (!$result) {
                        $sql = "INSERT INTO tokens(username,token,timestamp) VALUES  ('".$_POST['username']."','".$token."','".$timestamp."')";
                        $result= pg_query($dbconn,$sql);
                    }
                    header('Location: http://3.23.38.146/projects.php');
                    exit();

                    # echo "Welcome ".$_POST['username']."!";
                }
            }
?>
        </div>
    </body>
</html>

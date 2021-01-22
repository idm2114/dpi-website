<html> 
	<head>
		<title> DPI homepage</title>
	</head>
    <link rel="stylesheet" href="res/css/stylesheet.css?version=2">
        <!--links for favicon-->
        <link rel="apple-touch-icon" sizes="180x180" href="res/images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="res/images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="res/images/favicon/favicon-16x16.png">
        <link rel="manifest" href="res/images/favicon/site.webmanifest">
	<body>
        <div class="navbar-image" id="navbar-image">
            <img src="res/images/dpi-logo-cropped.png">
        </div>
        <div id="navbar">
            <ul>
            <?php 
                if(count($_COOKIE) == 0) {
                    echo '
                        <li><a class="active" href="index.php">Home</a></li>
                        <li><a href="projects.php">Projects</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="newsletter.php">Contact</a></li>
                        <li><a href="login.php">Login</a></li>
                    ';
                }
                else {
                    echo '
                        <li><a class="active" href="index.php">Home</a></li>
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

        <div class="landing-container">
            <img src="res/images/dpi-logo.png"> 
            <h1> We're remodeling! Come back soon! </h1>
        </div>
	</body>
</html>

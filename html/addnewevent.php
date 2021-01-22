<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title>Add New Event</title>
        <link href="res/css/stylesheet.css?version=2" rel="stylesheet" type="text/css" />
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
            if(count($_COOKIE) > 0) {
                echo '<li><a href="index.php">Home</a></li>
                <li><a href="projects.php">Projects</a></li>
                <li><a href="resources.php">Resources</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="changepassword.php">Change Password</a></li>
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
    if (!isset($_POST['submit'])) {
    echo '
        <div class="title">
        add new event
        </div>

		<div class="new-event-container">
		<h1>Describe your new event here:</h1>
        ';
    echo '
		<form action="';
        echo $_SERVER["PHP_SELF"];
        echo'" method="post">
  
		    <div class="form-group">
		      <label for="event-name">event name:</label>
		      <input type="text" class="form-control" id="event-name" placeholder="New Meeting" name="event-name" required>
		    </div>
		    
		    <div class="form-group">
		      <label for="event-host">event host:</label>
		      <input type="text" class="form-control" id="event-host" placeholder="DPI" name="event-host" required>
		    </div>
		    
		    <div class="form-group">
		      <label for="event-location">location (if online enter "online"):</label>
		      <input type="text" class="form-control" id="event-location" placeholder="online" name="event-location">
		    </div>
		    <div class="form-group">
		      <label for="event-link">link to event:</label>
		      <input type="url" class="form-control" id="event-link" placeholder="http://abc.com" name="event-link">
		    </div>
		    <div class="form-group">
		      <label for="event-date">date of event (MM/DD/YYYY):</label>
		      <input type="date" class="form-control" id="event-date" pattern="\d{1,2}/\d{1,2}/\d{4}" name="event-date" required>
		    </div>
		    <div class="form-group">
		      <label for="event-time">time of event (HH:MM):</label>
		      <input type="time" class="form-control" id="event-time" pattern="\d{1,2}:\d{2}" name="event-time" required>
		    </div>
		    <div class="form-group">
		      <label for="event-description">description:</label>
		      <input type="text" class="form-control" id="event-description" placeholder="This is what our event will be about." name="event-description">
		    </div>
		    <div class="form-group">
		      <label for="event-members">members only:</label>
		      <label for="event-members">Yes</label>
              
		      <input type="radio" class="form-control" id="event-members" name="radio" checked="checked" value="1">
		      <label for="event-members">No</label>
		      <input type="radio" class="form-control" id="event-members" name="radio" checked="checked" value="0">
		    </div>
             <br>
		    <input type="submit" name="submit" class="btn btn-primary" value="Add event to calendar">
		  </form>
          <br><br>
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

        $combinedatetime = $_POST["event-date"]." 00:".$_POST["event-time"];
        echo $combinedatetime; 
        exit;
        $eventdatetime= date(DATE_ISO8601, strtotime($combinedatetime));

        $sql = "INSERT INTO events E(name,link,location,host,description,date,membersonly) VALUES ('".$_POST["event-name"]."','".$_POST["event-link"]."','".$_POST["event-location"]."','".$_POST["event-host"]."','".$_POST["event-description"]."','".$eventdatetime."','".$_POST["event-members"]."');";
        $result = pg_query($dbconn,$sql);
        if (!$result) {
            echo '<h1>something went wrong</h1>';
        }
        else {
            echo '<h1>event added successfully</h1>';
        }
    }
?>
        </div>
    </body>
</html>

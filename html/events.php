<html> 
	<head>
		<title> DPI upcoming events </title>
        <link rel="stylesheet" href="res/css/stylesheet.css?version=4" />
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
                        <li><a class="active" href="events.php">Events</a></li>
                        <li><a href="newsletter.php">Contact</a></li>
                        <li><a href="login.php">Login</a></li>
                    ';
                }
                else {
                    echo '
                        <li><a href="index.php">Home</a></li>
                        <li><a href="projects.php">Projects</a></li>
                        <li><a href="resources.php">Resources</a></li>
                        <li><a class="active" href="events.php">Events</a></li>
                        <li><a href="changepassword.php">Change Password</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    ';
                }
            ?>
            </ul>
        </div>
        <div class="title-small">
            our events 
        </div>
    <div class="split">
        <div class="calendar-dayofweek" id="calendar-dayofweek"></div>
        <div class="calendar-name" id="calendar-name"></div>
        <div class="calendar-host" id="calendar-host"></div>
        <div class="calendar-link" id="calendar-link"></div>
        <div class="calendar-location" id="calendar-location"></div>
        <div class="calendar-date" id="calendar-date"></div>
        <div class="calendar-text" id="calendar-text"></div>
    </div>
    <div class="split" style="width: 45vw;">
        <div class="calendar-container">
            <div class="calendar">
			<header>				
                <?php
				echo '<h2>';
                echo date('F Y');
                echo'</h2>';

                echo'
			</header>
			<table>
				<thead>
					<tr>
						<td>Mo</td>
						<td>Tu</td>
						<td>We</td>
						<td>Th</td>
						<td>Fr</td>
						<td>Sa</td>
						<td>Su</td>
					</tr>
				</thead>
				<tbody>
                    ';
                ini_set('display_errors', 1);
                $host = "localhost";
                $port = "5432"; $dbname = "dpi";
                $user = "postgres";
                $password = "cdpi1754psql";
                $connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} ";
                $dbconn = pg_connect($connection_string);


                $tz = 'America/New_York';
                $timestamp = time();
                $dt = new DateTime("now", new DateTimeZone($tz));
                $dt->setTimestamp($timestamp); 

                
                $day0 = date("01-m-Y");
                $first = new DateTime($day0, new DateTimeZone($tz));
                $first->setTimestamp($timestamp);
                $firstdaynum = $first->format("w")-1; 
                $days = $first->format("t");

                $dt = new DateTime("now", new DateTimeZone($tz)); 
                $dt->setTimestamp($timestamp); 
                $current = $dt->format("d");

                for($i = 1; $i <= 42; $i++) {
                    /* checking if any events exist in cal for that day */
                    $minquerytime = date('Y-m-d', strtotime("+".$i - $firstdaynum -1 - $current." days"));
                    $minquery = new DateTime($minquerytime, new DateTimeZone($tz));
                    $minquery= $minquery->format('Y-m-d');
                    $maxquerytime = date('Y-m-d', strtotime("+".($i - $firstdaynum - $current)." days"));
                    $maxquery = new DateTime($maxquerytime, new DateTimeZone($tz));
                    $maxquery= $maxquery->format('Y-m-d');
                    $sql = "SELECT E.name,E.link,E.location,E.host,E.description,E.membersonly FROM Events E WHERE E.date >= '".$minquery."'::date AND E.date < '".$maxquery."'::date";
                    $result = pg_query($dbconn,$sql);
                    $hasEvent = False;

                    if ($result) {
                        while ($row = pg_fetch_row($result)) { 
                            $hasEvent = True;
                        }
                    }

                    if ((($i - 1) % 7) == 0) {
                        echo'<tr>';
                    }

                    if ($i <= $firstdaynum) {
                        if ($hasEvent == True) {
                            echo '<td class="prev-month calendar-event" onclick="show(\''.$minquerytime.'\')">';
                        }
                        else {
                            echo '<td class="prev-month">';
                        }
                        /* getting days in last month */
                        $lastmonthdays = date("t", strtotime("last month"));
                        echo $lastmonthdays - ($firstdaynum - $i); 
                    }
                    else if (($i-$firstdaynum) == $current) {
                        if ($hasEvent == True) {
                            echo '<td class="calendar-event current-day" onclick="show(\''.$minquerytime.'\')">';
                        }
                        else {
                            echo '<td class="current-day">';
                        }
                        echo $i-$firstdaynum;
                    }
                    else if (($i-$firstdaynum) > $days) {
                        if ($hasEvent == True) {
                            echo '<td class="calendar-event next-month" onclick="show(\''.$minquerytime.'\')">';
                        }
                        else {
                            echo '<td class="next-month">';
                        }
                        echo ($i-$firstdaynum) - $days;
                    }
                    else {
                        if ($hasEvent == True) {
                            echo '<td class="calendar-event" onclick="show(\''.$minquerytime.'\')">';
                        }
                        else {
                            echo '<td>';
                        }
                        echo $i-$firstdaynum;
                    }
                    echo '</td>
                    ';
                    if (($i % 7) == 0) {
                        echo'</tr>';
                    }
                }
				echo '</tbody>
                </table>
            </div> <!-- end calendar -->
        </div> <!-- end container -->
        </div>';
        ?>
        <script>
            function show(x) {
            <?php

                $sql = "SELECT E.name,E.link,E.location,E.host,E.description,E.membersonly,((E.date AT TIME ZONE 'UTC') AT TIME ZONE 'EST') FROM Events E";
                $result = pg_query($dbconn,$sql);

                $name = null;
                $link = null;
                $location = null;
                $host = null;
                $description = null;
                $membersonly = null;
                $date = null;
                $time = null;
                if ($result) {
                    while ($row = pg_fetch_row($result)) { 
                        $name = $row[0];
                        $link = $row[1];
                        $location = $row[2];
                        $host = $row[3];
                        $description = $row[4];
                        $membersonly = $row[5];
                        $date = date('Y-m-d',strtotime($row[6]));
                        $time = date('H:i',strtotime($row[6]));
                        $dayofweek= date('l, F d',strtotime($row[6]));

                        echo 'if (x == "';
                        echo $date;
                        echo '") {';
                        print("\n"); 

                        if ($membersonly==1 && count($_COOKIE) == 0) {
                            echo 'document.getElementById("calendar-text").innerHTML = "This event is for DPI members only. If you are a member, please sign in to view the event desription.";';
                            print("\n");
                        }
                        else {
                            echo 'document.getElementById("calendar-text").innerHTML = "';
                            echo $description;
                            echo '";';
                            print("\n");
                        }
                        echo 'document.getElementById("calendar-name").innerHTML = "';
                        echo $name;
                        echo '";';
                        echo 'document.getElementById("calendar-dayofweek").innerHTML = "';
                        echo $dayofweek;
                        echo '";';
                        echo 'document.getElementById("calendar-host").innerHTML = "hosted by: ';
                        echo $host;
                        echo '";
                            document.getElementById("calendar-location").innerHTML = "location: ';
                        echo $location;
                        if ($location == "online") {
                            echo " <a href='";
                            echo $link;
                            echo '\'>(here) </a>';
                        }
                        echo '";
                            document.getElementById("calendar-date").innerHTML = "time: ';
                        echo $time;

                        echo ' EST"; }';
                        print("\n"); 
                    }
                }
                echo '}';
        ?>
        </script>
</body>
</html>

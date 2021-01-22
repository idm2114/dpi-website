<html> 
	<head>
		<title> DPI current projects </title>
        <link rel="stylesheet" href="res/css/stylesheet.css?version=1" />
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
                        <li><a class="active" href="projects.php">Projects</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="newsletter.php">Contact</a></li>
                        <li><a href="login.php">Login</a></li>
                    ';
                }
                else {
                    echo '
                        <li><a href="index.php">Home</a></li>
                        <li><a class="active" href="projects.php">Projects</a></li>
                        <li><a href="resources.php">Resources</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="changepassword.php">Change Password</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    ';
                }
            ?>
            </ul>
        </div>

        <div class="split">
            <div class="title">
                our projects
            </div>
            <div class="projects-container">
            <?php 
                $host = "localhost";
                $port = "5432"; $dbname = "dpi";
                $user = "postgres";
                $password = "cdpi1754psql";
                $connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} ";
                $dbconn = pg_connect($connection_string);
                $sql = "SELECT P.name, P.members FROM Projects P";
                $result = pg_query($dbconn,$sql);
                if (!$result) {
                    exit;
                }
                $name = null;
                $members = null;
                while ($row = pg_fetch_row($result)) { 
                    $name = $row[0];
                    $members = $row[1];
                    echo '<div class="projects-title" onmouseover="show(';
                    echo "'";
                    echo $name;
                    echo "'";
                    echo ')">';
                    echo $name;
                    echo '</div>';
                }
            ?>
            </div>
        </div>
        <div class="split">
            <div class="projects-members" id="projects-members"></div>
            <div class="projects-text" id="projects-text"></div>
        </div>
        <script>
            function show(x) {
        <?php 
            /* function for parsing postgres array into php array */
            function pg_array_parse($literal)
            {
                if ($literal == '') return;
                preg_match_all('/(?<=^\{|,)(([^,"{]*)|\s*"((?:[^"\\\\]|\\\\(?:.|[0-9]+|x[0-9a-f]+))*)"\s*)(,|(?<!^\{)(?=\}$))/i', $literal, $matches, PREG_SET_ORDER);
                $values = [];
                foreach ($matches as $match) {
                    $values[] = $match[3] != '' ? stripcslashes($match[3]) : (strtolower($match[2]) == 'null' ? null : $match[2]);
                }
                return $values;
            }

            $dbconn = pg_connect($connection_string);
            $sql = "SELECT P.name, P.members, P.description FROM Projects P"; 
            $result = pg_query($dbconn,$sql);
            if (!$result) {
                exit;
            }
            $name = null;
            $description = null;
            print("\n");
            while ($row = pg_fetch_row($result)) { 
                $name = $row[0];
                $description = $row[2];
                $members = $row[1];
                echo 'if (x == "';
                echo $name;
                echo '") {';
                print("\n"); 
                echo 'document.getElementById("projects-text").innerHTML = "';
                echo $description;
                echo '";
                    document.getElementById("projects-members").innerHTML = "members: ';
                $members = pg_array_parse($members);
                for($i = 0; $i < count($members); $i++) {
                    echo $members[$i]; 
                    if ($i != count($members)-1) {
                        echo ", "; 
                    }
                }
                echo '"; }';
                print("\n"); 
            }
            echo '} </script>';
        ?>
	</body>
</html>

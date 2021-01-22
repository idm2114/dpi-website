<html> 
	<head>
		<title> DPI resources </title>
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
                        <li><a class="active" href="resources.php">Resources</a></li>
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
                our resources 
            </div>
            <div class="projects-container">
            <?php 
                $host = "localhost";
                $port = "5432"; $dbname = "dpi";
                $user = "postgres";
                $password = "cdpi1754psql";
                $connection_string = "host={$host} port={$port} dbname={$dbname} user={$user} password={$password} ";
                $dbconn = pg_connect($connection_string);
                $sql = "SELECT DISTINCT R.type FROM Resources R";
                $result = pg_query($dbconn,$sql);
                if (!$result) {
                    exit;
                }
                $type = null;
                while ($row = pg_fetch_row($result)) { 
                    $type = $row[0];
                    echo '<div class="projects-title" onmouseover="show(';
                    echo "'";
                    echo $type;
                    echo "'";
                    echo ')">';
                    echo $type;
                    echo '</div>';
                }
            ?>
            </div>
        </div>
        <div class="split" style="padding-left: 5vw; width: 35vw;">
            <div class="resources-table" id="resources-table">
        </div>
        <script>
            function show(x) {
        <?php 
            ini_set('display_errors', 1);
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
            
            $arr = array("frontend","backend","product design","misc");
            foreach ($arr as &$value) {
                print("\n");
                echo 'if (x == "';
                echo $value;
                echo '") {';
                print("\n"); 

                $sql = "SELECT R.link, R.type, R.mediatype, R.description, R.date, R.name FROM resources R WHERE R.type = '".$value."'"; 
                $result = pg_query($dbconn,$sql);
                if (!$result) {
                    exit;
                }

                $link = null;
                $type = null;
                $time = null;
                $mediatype = null;
                $description = null;
                $name = null;

                echo 'document.getElementById("resources-table").innerHTML = "';
                echo '<table>';
                echo '<th class=\'resources-th\'>Link</th>';
                echo '<th class=\'resources-th\'>Description</th>';

                while ($row = pg_fetch_row($result)) { 
                    $link = $row[0];
                    $type = $row[1];
                    $mediatype = $row[2];
                    $description = $row[3];
                    $time = date('m/d/Y',strtotime($row[4]));
                    $name = $row[5];

                    echo '<tr class=\'resources-tr\'><td class=\'resources-td\'><a href=\'';
                    echo $link;
                    echo '\'>';
                    echo $name;
                    echo '</a></td>';

                    echo '<td class=\'resources-td\'>';
                    echo $description;
                    echo '</td>';

                    echo '</tr>';
                }
                echo '</table>"; }';    
                print("\n");
            }
            echo '} </script>';
        ?>
	</body>
</html>

<?php
    session_start();

    require_once('../../LayoutHTML/header.php');
    require_once('../../LayoutHTML/footer.php');
    require_once('../../03_PERSISTENCE/MySQL/Report1SQL.php');
    require_once('../../03_PERSISTENCE/MongoDB/MongoDBWriter.php');

    $mongoDBWriter = new MongoDBWriter();
    $_SESSION["allMongoDBUsers"] = $mongoDBWriter->getAllUserFromMongoDB();
    $allMongoDBUsers = $_SESSION["allMongoDBUsers"];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
    <div class="my-div">
        <h1><font color=#42f5b3> User </font> MongoDB-Data</h1>

        <?php
            if ($_SESSION["migrationToMongoStarted"] == false) {
                echo "<p> <font color=#4287f5><b>" . "...not yet converted to MongoDB, go to "."<a href='../../01_UI/UI_Main.php' style='text-decoration: none;'> <b>Profile</b> </a>"." and hit the yellow button..." . "</b></font> </p>";
            } else {

                
                //* All Users
                if ($allMongoDBUsers !== null) {
                    foreach ($allMongoDBUsers as $element) {
                        echo "<pre>";
                            print_r($element);
                        echo "</pre>";
                    }
                }

            }

        ?>
    </div>

    
    <footer>
    <?php
    if ($_SESSION["migrationToMongoStarted"] == false) {
        echo '<img src="../../LayoutHTML/Logo/runningOnMySQL.png" height="110"  alt="MySQL Logo" class="footer-logo">';
    } else {
        echo '<img src="../../LayoutHTML/Logo/runningOnMongoDB.png" height="110" alt="MongoDB Logo" class="footer-logo">';
    }
    ?>
    <button onclick="scrollToTop()" class="logout-button">Back to Top</button>
    </footer>

    <script>
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        }
    </script>
    
</body>
</html>
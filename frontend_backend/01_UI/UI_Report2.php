<?php
    session_start();

    require_once('../LayoutHTML/header.php');
    require_once('../LayoutHTML/footer.php');
    require_once('../03_PERSISTENCE/MySQL/Report2SQL.php');
    require_once('../03_PERSISTENCE/MongoDB/Report2MongoDB.php');
    require_once('../03_PERSISTENCE/MongoDB/MongoDBWriter.php');

    $selectedDate = date("Y-m-d H:i:s");
    
    //* MySQL-Part
    if (!$_SESSION["migrationToMongoStarted"]) { 
        $selectedUser = $_SESSION["selectedUser"];
        
        $report2Query = new Report2SQL();
        $listRecipeQuery = $report2Query->calulateTopListRecipe_Report2($selectedUser,$selectedDate);

        if (isset($_POST['refreshReport'])) {
            if (isset($_POST['selectedNewDate']) && $_POST['selectedNewDate'] !== "") {
                $selectedDate = $_POST['selectedNewDate'];
            }
            $listRecipeQuery = $report2Query->calulateTopListRecipe_Report2($selectedUser, $selectedDate);
        }
    }

//* Mongo-Migration
    else {
        $actualMongoDBUser = $_SESSION["actualMongoDBUser"];
        $mongoDBWriter = new MongoDBWriter();
        $actualMongoDBUser = $mongoDBWriter->getActualStatus($actualMongoDBUser->username);
        $report2MongoQuery = new Report2MongoDB();
        $listRecipeQuery = $report2MongoQuery->calulateTopListRecipe_Report2($actualMongoDBUser, $selectedDate);
        if (isset($_POST['refreshReport'])) {
            if (isset($_POST['selectedNewDate']) && $_POST['selectedNewDate'] !== "") {
                $selectedDate = $_POST['selectedNewDate'];
            }
            $listRecipeQuery = $report2MongoQuery->calulateTopListRecipe_Report2($actualMongoDBUser, $selectedDate);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="my-div"> 

        
        <h1>Report 2: Most successful ListRecipe since <font color=#f56342><?php echo $selectedDate?></font></h1>

        <ul>
            <?php
            $rankingCounter = 0;
            foreach ($listRecipeQuery as $listRecipe) {
                $rankingCounter++;
                $sNN = $listRecipe[0];
                $listName = $listRecipe[1];
                echo "<li style='list-style: none;'>$rankingCounter) <font color=#4287f5><b>  $listName </b></font>   ($sNN)</li>";
            }
            ?>
        </ul>

        <form method="post">
            choose creationDate (everything created before will be considered): 
            <br>
            <input type="date" name="selectedNewDate">
            <br>
            <input type="submit" name="refreshReport" value="refresh report" class='green-button'>
        </form>

    </div>


    <footer>
        <?php
        if ($_SESSION["migrationToMongoStarted"] == false) {
            echo '<img src="../LayoutHTML/Logo/runningOnMySQL.png" height="110"  alt="MySQL Logo" class="footer-logo">';
        } else {
            echo '<img src="../LayoutHTML/Logo/runningOnMongoDB.png" height="110" alt="MongoDB Logo" class="footer-logo">';
        }
        ?>
    </footer>
    
</body>

</html>
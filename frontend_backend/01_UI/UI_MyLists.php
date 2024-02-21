<?php


session_start();
require_once('../LayoutHTML/header.php');
require_once('../03_PERSISTENCE/MySQL/MySQLDataBaseQuery.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBWriter.php');


//* MySQL-Part
if (!$_SESSION["migrationToMongoStarted"]) {
    $query = new MySQLDataBaseQuery();
    $selectedUser = $_SESSION["selectedUser"];
    $managedListRecipes = $query->retrieveAllMyListRecipes($selectedUser);
}

//* Mongo-Migration
else {
    $actualMongoDBUser = $_SESSION["actualMongoDBUser"];
    $mongoDBWriter = new MongoDBWriter();
    $actualMongoDBUser = $mongoDBWriter->getActualStatus($actualMongoDBUser->username);
    $managedListRecipesMongo = array();
    foreach ($actualMongoDBUser->listRecipes as $listRecipe) {
        $managedListInfoBundle = array();
        array_push($managedListInfoBundle, $listRecipe->listName);
        array_push($managedListInfoBundle, $listRecipe->editedDate);
        $managedListRecipesMongo[] = $managedListInfoBundle;
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

            <?php
                if ($_SESSION["migrationToMongoStarted"] == false) {
                    echo '<h1>My Lists</h1>';
                    echo '<ul>';
                    $rankingCounter = 0;
                    foreach ($managedListRecipes as $listRecipe) {
                        $rankingCounter++;
                        $listName = $listRecipe[0];
                        echo "<li style='list-style: none;'>$rankingCounter) <font color=#4287f5><b>  $listName </b></font></li>";
                    }
                    echo '</ul>';
                } else {
                    echo '<h1>My Lists</h1>';
                    echo '<ul>';
                    $rankingCounter = 0;
                    foreach ($managedListRecipesMongo as $listRecipe) {
                        $rankingCounter++;
                        $listName = $listRecipe[0];
                        echo "<li style='list-style: none;'>$rankingCounter) <font color=#4287f5><b>  $listName </b></font></li>";
                    }
                    echo '</ul>';
                }
            ?>

    </div>


    <footer>
        <?php
        require_once('../LayoutHTML/footer.php');
        if ($_SESSION["migrationToMongoStarted"] == false) {
            echo '<img src="../LayoutHTML/Logo/runningOnMySQL.png" height="110"  alt="MySQL Logo" class="footer-logo">';
        } else {
            echo '<img src="../LayoutHTML/Logo/runningOnMongoDB.png" height="110" alt="MongoDB Logo" class="footer-logo">';
        }
        ?>
    </footer>

</body>

</html>
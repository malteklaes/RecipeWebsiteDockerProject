<?php

session_start();
//* main controller
require_once('../LayoutHTML/header.php');
require_once('../03_PERSISTENCE/MySQL/MySQLDataBaseQuery.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBConnector.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBInserter.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBMigrator.php');

if (!isset($_SESSION["selectedUser"])) {
    header("Location: ../../index.php");
    exit;
}

//* MySQL-Part
if (!$_SESSION["migrationToMongoStarted"]) {
    //* stored user
    $selectedUser = $_SESSION["selectedUser"];


    //* MySQL database (controller)
    $query = new MySQLDataBaseQuery();
    $user = $query->retrieveUserDataByUserID($selectedUser);
    $userName = $user[0];
    $userEmail = $user[1];


}
//* MongoDB-Part
if (isset($_POST['convertToMongoDBButton']) && !$_SESSION["migrationToMongoStarted"]) {
    $_SESSION["migrationToMongoStarted"] = true;
    //* here begin data-migration
    $mongoDBMigrator = new MongoDBMigrator();
    $mongoDBMigrator->startMigration($userName);
    $userArray = $mongoDBMigrator->getUserArray();
    $commentArray = $mongoDBMigrator->getCommentArray();
    $_SESSION["allMongoDBComments"] = $commentArray;
    $recipeArray = $mongoDBMigrator->getRecipeArray();
    $_SESSION["allMongoDBRecipes"] = $recipeArray;
    $getActualUser = $mongoDBMigrator->getActualUser();
    $_SESSION["actualMongoDBUser"] = $getActualUser;
    $listRecipeArray = $mongoDBMigrator->getListRecipeArray();
    $_SESSION["actualMongoDBListRecipes"] = $listRecipeArray;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mainPage</title>
    <link rel="stylesheet" type="text/css" href="style.css">


</head>

<body>

    <div class="my-div">


        <h1>Main Page</h1>

        <?php
        require_once('../LayoutHTML/footer.php');
        if ($_SESSION["migrationToMongoStarted"] == false) {
            echo "<h2>hello <font color=#E15A5A><b> $userName </b></font></h2>";
        } else {
            echo "<h2>hello <font color=#E15A5A><b>" . $_SESSION["actualMongoDBUser"]->username . "</b></font></h2>";
        }
        ?>

        <?php
        if ($_SESSION["migrationToMongoStarted"] == false) {
            echo "(".$userEmail.")<br> <br>";
        } else {
            echo "(". $_SESSION["actualMongoDBUser"]->email.")<br> <br>";
        }
        ?>

        <?php
        if ($_SESSION["migrationToMongoStarted"] == false) {
            echo "(UID: ".$selectedUser.")";
        } else {
            echo "(oid: ". $_SESSION["actualMongoDBUser"]->_id->__toString().")";
        }
        ?>
        

        <form method="post">
            <input type="submit" name="convertToMongoDBButton" value="Convert to MongoDB" class="yellow-button">
        </form>

        <br>

        <div>
            <button class="logout-button" onclick="window.location.href='../../index.php'">Logout</button>
        </div>

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
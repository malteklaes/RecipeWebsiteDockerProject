<?php


session_start();
require_once('../LayoutHTML/header.php');
require_once('../03_PERSISTENCE/MySQL/MySQLDataBaseWrite.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBWriter.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBEntity/ListRecipeMongo.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBEntity/RecipeMongo.php');

//* MongoDB data
$actualMongoDBUser = null;
$actualMongoDBUser = $_SESSION["actualMongoDBUser"];

$selectedListRecipeMongo = null;
if(isset($_SESSION["selectedListRecipeMongo"]) && $_SESSION["selectedListRecipeMongo"] !== null){
    $selectedListRecipeMongo = $_SESSION["selectedListRecipeMongo"];
}

//* MySQL data
if (!$_SESSION["migrationToMongoStarted"]) {
    $selectedUser = $_SESSION["selectedUser"];

    if (isset($_POST["CreateList"])) {
        $listRecipeName = $_POST['name'];
        $listRecipeDescription = $_POST['description'];
        $privateStatus = isset($_POST['privateStatus']) && $_POST['privateStatus'] === 'yes';
      
        $writer = new MySQLDataBaseWrite();
        $writer->writeNewRecipeEntryToList($_SESSION["selectedListRecipe"][1], $_SESSION["postID"]);
        $writer->writeNewListRecipe($_SESSION["selectedListRecipe"][1], $_SESSION["selectedUser"], $listRecipeName, 0, $listRecipeDescription, $privateStatus, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));

        header('Location: UI_MyRecipes.php');
        exit;
    }
} 
else {
    //* Mongo-Migration
    if (isset($_POST["CreateList"])) {
        $listRecipeName = $_POST['name'];
        $listRecipeDescription = $_POST['description'];
        $privateStatus = isset($_POST['privateStatus']) && $_POST['privateStatus'] === 'yes';
        $wholeRecipe = $_POST['wholeRecipe'];
        
        
        if (isset($_SESSION["selectedListRecipeMongo"])) {
            $selectedListRecipeMongo = $_SESSION["selectedListRecipeMongo"];
            $writer = new MongoDBWriter();
            $writer->insertListRecipeToMongo($listRecipeName, $listRecipeDescription, $privateStatus, $selectedListRecipeMongo, $actualMongoDBUser);
            $_SESSION["actualMongoDBListRecipes"] = $writer->getAllListRecipesFromMongoDBUpdate();
            $_SESSION["allMongoDBRecipes"] = $writer->getAllRecipesFromMongoDB();
            $allMongoDBRecipes = $_SESSION["allMongoDBRecipes"];

            
            header('Location: UI_MyRecipes.php');
            exit;
        } else {
            echo "The session variable 'selectedListRecipeMongo' was not set.";
        }

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

            
            <h1>Create new List Recipe</h1>

            
            <form action="" method="post">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="description">Description :</label>
                <textarea id="description" name="description" required></textarea><br><br>
                <p>(please no apostrophe!)</p>

        

                <fieldset style="border:none">
                    <legend>Private?</legend>
                    <input type="radio" id="privateStatus-yes" name="privateStatus" value="yes" required>
                    <label for="privateStatus-yes">Yes</label>
                    <input type="radio" id="privateStatus-no" name="privateStatus" value="no" checked required>
                    <label for="privateStatus-no">No</label>
                </fieldset>

                <input name="CreateList" class="green-button" type="submit" value="Create List">
                <button name="cancel-button" class="cancel-button" onclick="window.location.href='UI_MyRecipes.php'">Cancel</button>
            </form>

          
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
<?php

session_start();
require_once('../LayoutHTML/header.php');
require_once('../03_PERSISTENCE/MySQL/MySQLDataBaseQuery.php');
require_once('../03_PERSISTENCE/MySQL/MySQLDataBaseWrite.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBConnector.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBWriter.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBEntity/RecipeMongo.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBEntity/CommentMongo.php');
require_once('../03_PERSISTENCE/MySQL/Report1SQL.php');


//* MySQL-Part
if (!$_SESSION["migrationToMongoStarted"]) { 
    $mongoDBController = new MongoDBConnector();

    $selectedUser = $_SESSION["selectedUser"];


    $writer = new MySQLDataBaseWrite();

    if (isset($_POST["CreateRecipe"])) {
        $recipeName = $_POST['name'];
        $recipeDescription = $_POST['description'];
        $recipeCategory = $_POST['category-dropdown'];
        $recipeComment = (empty($_POST['comments'])) ? null : $_POST['comments'];
        $writer->writeRecipeDataWithListID($_SESSION["listRecipeListID"], $_SESSION["selectedUser"], $recipeName, $recipeDescription, $recipeCategory, 0, $recipeComment, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));


        header('Location: UI_MyRecipes.php');
        exit();
    }
} 
//* Mongo-Migration
else {
    $actualMongoDBUser = $_SESSION["actualMongoDBUser"]->username;

    $mongoDBWriter = new MongoDBWriter();
    $categories = $mongoDBWriter->getAllCategoriesFromMongoDB();
    $mongoDBWriter->queryUser($actualMongoDBUser->username);
    $newRecipe = null;
    if (isset($_POST["CreateRecipe"])) {
        //* RECIPE
        $recipeName = $_POST['name'];
        $recipeDescription = $_POST['description'];
        $recipeCategory = $_POST['category-dropdown'];
        $recipeComment = array();
        $recipeComment[] = new CommentMongo($_POST['comments'], $actualMongoDBUser, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));
        $newRecipe = new RecipeMongo($recipeName, $recipeDescription, $recipeCategory, 0, $recipeComment, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));




        //* USER

        $mongoDBWriter->insertUserToMongoDB($newRecipe, $_SESSION["actualMongoDBUser"]);
        $resultU = array();
        $resultU = $mongoDBWriter->getAllUserFromMongoDB();

        $getActualUser = $mongoDBWriter->getActualStatus($actualMongoDBUser);
        $_SESSION["actualMongoDBUser"] = $getActualUser;


        //* insert data
        $mongoDBWriter->insertRecipe($newRecipe);
        $resultA = array();
        $resultA = $mongoDBWriter->getAllRecipesFromMongoDB();
       

        //* update recipe-MongoSession
        $_SESSION["allMongoDBRecipes"] = $mongoDBWriter->getAllRecipesFromMongoDB();
        $allMongoDBRecipes = $_SESSION["allMongoDBRecipes"];

        //* older stuff
        header('Location: UI_MyRecipes.php');
        exit();

   
    }
}
if (!$_SESSION["migrationToMongoStarted"]) {
    $report1Query = new Report1SQL();
    $categories = $report1Query->retrieveAllCategories();
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

       
        <h1>Add new recipe: <?php echo $_SESSION["listRecipeName"]  ?></h1>

        <form action="" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="description">Description :</label>
            <textarea id="description" name="description" required></textarea><br><br>
            <p>(please separate ingredients by comma)</p>


            <label for="category-dropdown">Category:</label>
            <select id="category-dropdown" name="category-dropdown">
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="comments">Comment :</label>
            <textarea id="comments" name="comments"></textarea><br><br>

        


            <input type="submit" value="Create Recipe" class="green-button" name="CreateRecipe">
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
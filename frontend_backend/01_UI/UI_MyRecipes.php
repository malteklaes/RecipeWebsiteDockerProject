<?php

session_start();

//* main controller
require_once('../LayoutHTML/header.php');
require_once('../LayoutHTML/footer.php');
require_once('../03_PERSISTENCE/MySQL/MySQLDataBaseQuery.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBConnector.php');



//* MySQL-Part
if (!$_SESSION["migrationToMongoStarted"]) {
    $mongoDBController2 = new MongoDBConnector();

    $selectedUser = $_SESSION["selectedUser"];

    $query = new MySQLDataBaseQuery();
    $user = $query->retrieveUserDataByUserID($selectedUser);
    $allRecipes = $query->retrieveUserDataByList($selectedUser);
}

//* Mongo-Migration
else {
    $actualMongoDBUser = $_SESSION["actualMongoDBUser"];
    $allMongoDBRecipes = $_SESSION["allMongoDBRecipes"];
    $allRecipesMongoDB = array();
    foreach ($actualMongoDBUser->recipes as $recipe) {
        if (is_object($recipe)) {
            $recipeInfoBundle = array();
            array_push($recipeInfoBundle, $recipe->recipeName);
            array_push($recipeInfoBundle, $recipe->rating);
            array_push($recipeInfoBundle, $recipe->recipeDescription);
            array_push($recipeInfoBundle, $recipe->category);
            array_push($recipeInfoBundle, $recipe->creationDate);
            array_push($recipeInfoBundle, $recipe->editedDate);
            $allRecipesMongoDB[] = $recipeInfoBundle;
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

        
        
        <?php
        if ($_SESSION["migrationToMongoStarted"] == false) {
            echo '<h1>My Recipes <?php echo $listRecipeName ?></h1>';
            echo '<ul>';
            foreach ($allRecipes as $recipe) {
                $name = $recipe[0];
                $rating = $recipe[1];
                $postID = $recipe[2];
                $stars = '';
                for ($i = 1; $i <= $rating; $i++) {
                    $stars .= "&#10025; ";
                }
                $_SESSION["postID"] = $postID;
                echo "<li><a href='UI_ShowRecipeDetails.php?name=$name&rating=$rating&postID=$postID'>$name <font color=#b5b524><b>  $stars </b></font></a></li>";
            }
            echo '</ul>';
            echo "<a href='UI_NewRecipe.php?name=$name&&listID=$listRecipeListID'><button class='green-button'>+ Add New Recipe</button></a>";
        } else {
            echo '<h1>My Recipes <?php echo $listRecipeName ?></h1>';
            echo '<ul>';
            foreach ($allRecipesMongoDB as $recipe) {
                $name = $recipe[0];
                $rating = $recipe[1];
                $recipeDescription = $recipe[2];
                $category = $recipe[3];
                $creationDate = $recipe[4];
                $editedDate = $recipe[5];
                $stars = '';
                for ($i = 1; $i <= $rating; $i++) {
                    $stars .= "&#10025; ";
                }
                $_SESSION["postID"] = $postID;
                echo "<li><a href='UI_ShowRecipeDetails.php?name=$name&rating=$rating&recipeDescription=$recipeDescription&category=$category&creationDate=$creationDate&editedDate=$editedDate'>
                $name <font color=#b5b524><b>   $stars </b></font></a></li>";
            }
            echo '</ul>';
            echo "<a href='UI_NewRecipe.php?name=$name&&listName=$name'><button class='green-button'>+ Add New Recipe</button></a>";
        }
        ?>

      

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
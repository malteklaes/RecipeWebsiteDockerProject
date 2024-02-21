<?php
session_start();
require_once('../LayoutHTML/header.php');
require_once('../03_PERSISTENCE/MySQL/MySQLDataBaseQuery.php');
require_once('../03_PERSISTENCE/MySQL/MySQLDataBaseWrite.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBConnector.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBDocumentSearcher.php');
require_once('../03_PERSISTENCE/MongoDB/MongoDBWriter.php');

$_SESSION["selectedListRecipeMongo"] = null;
$name = $_GET['name'];
$rating = $_GET['rating'];
//* MySQL-Part
if (!$_SESSION["migrationToMongoStarted"]) {
    $query = new MySQLDataBaseQuery();
    $mongoDBController = new MongoDBConnector();

    $selectedUser = $_SESSION["selectedUser"];
    //* [A] every data which will be shown
    $postID = $_GET['postID'];

    //* [B] bring in all necessary data to show
    $recipeDetails = $query->retrieveRecipeDetailsByPostID($postID, $_SESSION["selectedUser"]);
    $recipeCreator = $recipeDetails[3];
    $recipeIngredients = explode(",", $recipeDetails[1]);
    $recipeCreatorTime = $recipeDetails[4];
    $recipeComments = (($recipeDetails[5] === "") || ($recipeDetails[5] !== null) === false) ? array("no comments") : $recipeDetails[5];
    $stars = "&#10025";
    $space = "&nbsp &nbsp &nbsp &nbsp";


    //* [C] main use case 2: when new listrecipe should be created
    $allListRecipes = $query->retrieveAllMyListRecipesAndNewList($selectedUser);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["selectListRecipe"])) {
            $selectedListRecipe = unserialize($_POST["listRecipe-dropdown"]);

            $_SESSION["selectedListRecipe"] = $selectedListRecipe;
            if ($selectedListRecipe[0] === "new ListRecipe") {
                echo "make new recipe";
                header("Location: /01_UI/UI_CreateNewListRecipe.php");
                exit;
            } else {
                echo " RICHTIGE postID: " . $_SESSION["postID"] . " , " . $postID;
                $writer = new MySQLDataBaseWrite();
                $writer->writeNewRecipeEntryToList($selectedListRecipe[1], $selectedListRecipe[2]);
                header("Location: /01_UI/UI_MyRecipes.php");
                exit;
            }
        }
    }
} else {
    //* Mongo-Migration
    $mongoDBDocumentsearcher = new MongoDBDocumentSearcher();
    $actualMongoDBUser = $_SESSION["actualMongoDBUser"];


    $allListRecipesByUser = $actualMongoDBUser->listRecipes;
    $allListRecipesMongo = $mongoDBDocumentsearcher->getAllListRecipesFromMongoDB();

    $getActualUser = $actualMongoDBUser;
    //* all important recipe details data
    $recipeCreatorMongo = $actualMongoDBUser->username;
    $recipeNameMongo = $name;
    $ratingMongo = $rating;
    $recipeDescriptionMongo = explode(",", $_GET['recipeDescription']);
    $categoryMongo = $_GET['category'];
    $creationDateMongo = $_GET['creationDate'];
    $editedDateMongo = $_GET['editedDate'];

    //* recipe to be stored into a listRecipe
    $storeRecipe = array();
    array_push($storeRecipe, $recipeNameMongo);
    array_push($storeRecipe, $recipeDescriptionMongo);
    array_push($storeRecipe, $categoryMongo);
    array_push($storeRecipe, $ratingMongo);
    array_push($storeRecipe, $creationDateMongo);
    array_push($storeRecipe, $editedDateMongo);




    //* retrieve all comments of own recipe
    $allRecipesCommentsMongoDB = array();
    foreach ($actualMongoDBUser->recipes as $recipe) {
        if ($recipe->recipeName === $recipeNameMongo) {
            foreach ($recipe->comment as $comment) {
                $commentInfoBundle = array();
                array_push($commentInfoBundle, $comment->commentContent);
                array_push($commentInfoBundle, $comment->ownerName);
                array_push($commentInfoBundle, $comment->editedDate);
                $allRecipesCommentsMongoDB[] = $commentInfoBundle;
            }
        }
    }
    //* [C] main use case 2: when new listrecipe should be created
    //$allListRecipes = $query->retrieveAllMyListRecipesAndNewList($selectedUser);
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["selectListRecipeMongo"])) {
            $selectedListRecipe = unserialize($_POST["listRecipe-dropdown"]);

            $_SESSION["selectedListRecipeMongo"] = $selectedListRecipe;
            $recipeToBeAddedToListRecipe = $selectedListRecipe[7];

            
            
                if ($selectedListRecipe[0] === "new ListRecipe") {
                    echo "make new recipe";
                    header("Location: /01_UI/UI_CreateNewListRecipe.php");
                    exit;
                } else {
                    $writer = new MongoDBWriter();
                    $writer->insertRecipeToList($recipeToBeAddedToListRecipe, $selectedListRecipe);
                    $_SESSION["actualMongoDBListRecipes"] = $writer->getAllListRecipesFromMongoDBUpdate();
                    header("Location: /01_UI/UI_MyRecipes.php");
                    exit;
                }
            
            
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php $name ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="my-div">
        <?php if ($_SESSION["migrationToMongoStarted"] == false) : ?>
            <h1>Recipe for: <font color=#42f5b3> <?php echo  $name ?> </b></font> <?php echo "$space <font color=#b5b524><b>  ($rating) $stars </b></font><br>" ?> </h1>
            <h3>created by: <font color=#f56342><b><?php echo  $recipeCreator ?></b></font> <br>(on <?php echo  $recipeCreatorTime ?>)</h3>

            <br>
            <h3>Ingredients</h3>
            <ul>
                <?php
                foreach ($recipeIngredients as $recipe) {
                    echo "<li>$recipe</a></li>";
                }
                ?>
            </ul>

            <br>
            <h4>Comments: </h4>
            <ul>
                <?php
                if ($recipeComments[0] !== "no comments") {
                    foreach ($recipeComments as $comment) {
                        $commentContent = "\"" . $comment[0] . "\"";
                        $commentCreator = $comment[1];
                        $commentTime = $comment[2];
                        if (strlen($commentContent) === 2) {
                            echo "<li> <font color=#4287f5><b>...no comments...</b></font>";
                        } else {
                            echo "<li> <font color=#4287f5><b>$commentContent</b></font>";
                            echo "<br>(by: $commentCreator on $commentTime)</a></li>";
                        }
                    }
                } else {
                    echo "<font color=#4287f5><b>...$recipeComments[0]...</b></font>";
                }
                ?>
            </ul>

            <br>
            <h4>Add recipe to My List Recipe: </h4>

            <form method="POST" action="<?php echo  $_SERVER["PHP_SELF"]; ?>">
                <label for="listRecipe-dropdown">choose List Recipe:</label>
                <select id="listRecipe-dropdown" name="listRecipe-dropdown">
                    <?php foreach ($allListRecipes as $listRecipe) : ?>

                        <option value="<?php
                                        array_push($listRecipe, $postID);
                                        echo htmlspecialchars(serialize($listRecipe));
                                        ?>" <?php if ($listRecipe[0] === "new ListRecipe") : ?> style="color: #cc7325; font-weight: bold;" <?php endif; ?>>
                            <?php echo $listRecipe[0]; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" name="selectListRecipe" value="Add">
            </form>

        <?php else : ?>
            <h1>Recipe for: <font color=#42f5b3> <?php echo  $name ?> </b></font> <?php echo "$space <font color=#b5b524><b>  ($rating) $stars </b></font><br>" ?> </h1>
            <h3>created by: <font color=#f56342><b><?php echo  $recipeCreatorMongo ?></b></font> <br>(on <?php echo  $creationDateMongo ?>)</h3>

            <br>
            <h3>Ingredients</h3>
            <ul>
                <?php
                foreach ($recipeDescriptionMongo as $recipe) {
                    echo "<li>$recipe</a></li>";
                }
                ?>
            </ul>

            <br>
            <h4>Comments: </h4>
            <ul>
                <?php
                if ($allRecipesCommentsMongoDB[0] !== "no comments") {
                    foreach ($allRecipesCommentsMongoDB as $comment) {
                        $commentContent = "\"" . $comment[0] . "\"";
                        $commentCreator = $comment[1];
                        $commentTime = $comment[2];
                        if (strlen($commentContent) === 2) {
                            echo "<li> <font color=#4287f5><b>...no comments...</b></font>";
                        } else {
                            echo "<li> <font color=#4287f5><b>$commentContent</b></font>";
                            echo "<br>(by: $commentCreator on $commentTime)</a></li>";
                        }
                    }
                } else {
                    echo "<font color=#4287f5><b>...$recipeComments[0]...</b></font>";
                }
                ?>
            </ul>

            <br>
            <h4>Add recipe to My List Recipe: </h4>

            <form method="POST" action="<?php echo  $_SERVER["PHP_SELF"]; ?>">
                <label for="listRecipe-dropdown">choose List Recipe:</label>
                <select id="listRecipe-dropdown" name="listRecipe-dropdown">
                    <?php echo "hello" ?>
                    <?php foreach ($allListRecipesMongo as $listRecipeMongo) : ?>
                        <option value="<?php
                                        $newList[] = "iwas";
                                        array_push($listRecipeMongo, $storeRecipe);
                                        array_push($listRecipeMongo, $allRecipesCommentsMongoDB);
                                        echo htmlspecialchars(serialize($listRecipeMongo));
                                        ?>" <?php if ($listRecipeMongo[0] === "new ListRecipe") : ?> style="color: #cc7325; font-weight: bold;" <?php endif; ?>>
                            <?php echo $listRecipeMongo[0]; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" name="selectListRecipeMongo" value="Add">
            </form>

        <?php endif; ?>
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
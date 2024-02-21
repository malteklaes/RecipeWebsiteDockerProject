<?php
    session_start();

    require_once('../LayoutHTML/header.php');
    require_once('../LayoutHTML/footer.php');
    require_once('../03_PERSISTENCE/MySQL/Report1SQL.php');
    require_once('../03_PERSISTENCE/MongoDB/Report1MongoDB.php');
    
    

    //* MySQL-Part
    if (!$_SESSION["migrationToMongoStarted"]) { 
        $selectedUser = $_SESSION["selectedUser"];
        $report1Query = new Report1SQL();
        $categories = $report1Query->retrieveAllCategories();
        if (isset($_POST["chooseCategory"])) {
            $category = $_POST["category-dropdown"];
            $recipeQuery = $report1Query->calculateTopRecipes_Report1($selectedUser, $category);
        } 
        else {
            $category = 'All categories';
            $recipeQuery = $report1Query->calculateTopRecipes_Report1($selectedUser, null);
        }
    }

    //* Mongo-Migration
    else {
        $actualMongoDBUser = $_SESSION["actualMongoDBUser"];
        $report1QueryMongo = new Report1MongoDB;
        $categories = $report1QueryMongo->retrieveAllCategories();
        if (isset($_POST["chooseCategory"])) {
            $category = $_POST["category-dropdown"];
            $recipeQuery = $report1QueryMongo->calulateTopRecipe_Report1($actualMongoDBUser, $category);
        } 
        else {
            $category = 'All categories';
            $recipeQuery = $report1QueryMongo->calulateTopRecipe_Report1($actualMongoDBUser, null);
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

        
        <h1>Report 1: Top rated recipes -  
        <font color=#f56342><?php 
            if ($category != 'All categories') {
                echo "by category ";
            }
            echo "$category" 
            ?> </font>
        </h1>
        
       
        <ul>
            <?php
            foreach (array_slice($recipeQuery, 0, 10) as $recipe) {
                $rankingCounter++;
                $newRating = round($recipe[0], 3);
                $listRecipeNumber = $recipe[1];
                $recipeName = $recipe[2];
                echo "<li style='list-style: none;'>$rankingCounter) <font color=#4287f5><b>  $recipeName </b></font> 
                    ($newRating, $listRecipeNumber)</li>";
            }
            
            ?>
        </ul>

    

        <h1>Choose category</h1>

        <form method="POST" action="<?php echo  $_SERVER["PHP_SELF"]; ?>">
            <label for="category-dropdown">Category:</label>
            <select id="category-dropdown" name="category-dropdown">
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" name="chooseCategory" value="choose" class='green-button'>
        </form>

        <div id="button-list" class="button-list">
            <form method="post">
            <input type="submit" name="Others" value="All categories" class='green-button'>
            </form>
        </div>

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
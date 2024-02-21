<?php

session_start();

require_once('MySQLDataBaseConnector.php');
require_once('MySQLEntity/MySQLEntityHelper/ListRecipeIDSQL.php');
require_once('MySQLEntity/MySQLEntityHelper/UserIDSQL.php');

   

    class MySQLDataBaseQuery{

    private $dbConnector;

        function __construct(){
        $this->dbConnector = new MySQLDataBaseConnector();
        
        }

        

        public function retrieveUserIDsFromTableData($dataBaseConnection){
            $userIDsArray = array();
            $table_name = "user";
            $query = "SELECT * FROM user";
            $stmt = $dataBaseConnection->query($query);
            $output_database_pdo = "\n<strong>$table_name - pdo</strong>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($userIDsArray, $row['userID']);
                $output_database_pdo .= "<p>".$row['userID']."</p>";
                $output_database_pdo .= "<p>".$row['username']."</p>";
                $output_database_pdo .= "<p>".$row['email']."</p>";
                $output_database_pdo .= "<p>".$row['pwd']."</p>";
                $output_database_pdo .= "<p>".$row['registrationDate']."</p>";
                $output_database_pdo .= "<hr>";
            }
            return $userIDsArray;
        }

        public function retrieveUserUserNamesFromTableData($dataBaseConnection){
            $userBamesArray = array();
            $query = "SELECT * FROM user";
            $stmt = $dataBaseConnection->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($userBamesArray, $row['username']);
            }
            return $userBamesArray;
        }

        public function retrieveUserDataByUserID($userID){
            $userIDsArray = array();
            $sql = "SELECT * FROM user WHERE userID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            

            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($userData) {
                array_push($userIDsArray, $userData['username']);
                array_push($userIDsArray, $userData['email']);
                array_push($userIDsArray, $userData['usepwdrname']);
                array_push($userIDsArray, $userData['registrationDate']);
            } else {
                echo "No data was found for the specified userID in the MySQL database.";
            }

            return $userIDsArray;
        }

        public function retrieveUserDataByList2($userID){
            $listRecipeArray = array();
            $recipesArray = array();
            $sql = "SELECT recipe.recipeName, recipe.rating, recipe.postID, listRecipe.listName, listRecipe.listID
                    FROM recipe
                    JOIN listRecipe ON recipe.ownerUserID = listRecipe.ownerID
                    WHERE recipe.ownerUserID = :userID";

            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

     

            //* collect recipes
            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                 $recipePair = array();
                array_push($recipePair, $userData['recipeName']);
                array_push($recipePair, $userData['rating']);
                array_push($recipePair, $userData['postID']);
                $recipesArray[] = $recipePair;
            }

            $stmt->execute();
            //* collect further data
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($userData) {
                array_push($listRecipeArray, $userData['rating']);
                array_push($listRecipeArray, $userData['listName']);
                array_push($listRecipeArray, $userData['listID']);
            } else {
            echo "<br> nothing found in Recipe<br>";
            }
            
            array_push($listRecipeArray, $recipesArray);
        return $listRecipeArray;
        

        }

        public function retrieveUserDataByList($userID) {
            $listRecipeArray = array();
            $recipesArray = array();
            $sql = "SELECT * FROM recipe WHERE ownerUserID = :userID";

            $dataBaseConnection = new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();



            //* collect recipes
            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipePair = array();
                array_push($recipePair, $userData['recipeName']);
                array_push($recipePair, $userData['rating']);
                array_push($recipePair, $userData['postID']);
                $recipesArray[] = $recipePair;
            }

            array_push($listRecipeArray, $recipesArray);
            return $recipesArray;
        }

    /**
     * Summary of retrieveRecipeDetailsByPostID
     * @param mixed $postID
     * @param mixed $userID
     * @return array<string>
     */
    public function retrieveRecipeDetailsByPostID($postID, $userID){
        $recipeDetailsArray = array();
        $commentsArray = array();
        $dataBaseConnection = new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
        
        //* [A] joins two tables "recipe" and "comment", where recipe's postID is equivalent 
        $sql = "SELECT recipe.recipeName, recipe.recipeDescription, recipe.ownerUserID, comment.commentContent, comment.ownerUserID, comment.editedDate
                FROM recipe
                JOIN comment ON recipe.postID = comment.recipeIDReference
                WHERE recipe.postID = :postID";

        

        $stmt = $dataBaseConnection->prepare($sql);
        $stmt->bindParam(':postID', $postID);
        $stmt->execute();

       //* [B1] if there are no comments
        if($stmt->fetch(PDO::FETCH_ASSOC) === false){
            //* new sql statement for only the recipe
            $sql = "SELECT * FROM recipe WHERE recipe.postID = :postID";
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($userData) {
                $ownerUserID = $userData['ownerUserID'];
                $ownerUserName = $this->retrieveUserDataByUserID($ownerUserID);
                array_push($recipeDetailsArray, $userData['recipeName']);
                array_push($recipeDetailsArray, $userData['recipeDescription']);
                array_push($recipeDetailsArray, $userData['ownerUserID']);
                array_push($recipeDetailsArray, $ownerUserName[0]);
                
                array_push($recipeDetailsArray, $userData['editedDate']);
            } 
        } 
        //* [B2] if there are recipes with comments
        else {
            $stmt->execute();
            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $commentPair = array();
                array_push($commentPair, $userData['commentContent']);
                array_push($commentPair, $this->retrieveUserDataByUserID($userData['ownerUserID'])[0]);
                array_push($commentPair, $userData['editedDate']);
                $commentsArray[] = $commentPair;
            }

            $stmt->execute();
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($userData) {
                $ownerUserID = $userData['ownerUserID'];
                $ownerUserName = $this->retrieveUserDataByUserID($userID);

                array_push($recipeDetailsArray, $userData['recipeName']);
                array_push($recipeDetailsArray, $userData['recipeDescription']);
                array_push($recipeDetailsArray, $userData['ownerUserID']);
                array_push($recipeDetailsArray, $ownerUserName[0]);
                array_push($recipeDetailsArray, $userData['editedDate']);
            } else {
                echo "<br> nothing found in Recipe <br>";
            }

            array_push($recipeDetailsArray, $commentsArray);
        }
        
      
        return $recipeDetailsArray;
    }


    public function retrieveUserLikedListRecipes($userID) {
        $listRecipesArray = array();
        $sql = "SELECT l.listID, l.ownerID, l.listName, l.likesAmount, l.listDescription, l.privateStatus, l.creationDate, l.editedDate
            FROM userLikesListRecipe ul
            JOIN listRecipe l ON ul.listID = l.listID
            WHERE ul.userID = :userID AND l.privateStatus = 0";
        
        //* connect to db and send sql
        $dataBaseConnection = new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
        $stmt = $dataBaseConnection->prepare($sql);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();



        //* collect listRecipes
        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $listRecipesBundle = array();
            if($userData['ownerID'] === $userID){
                array_push($listRecipesBundle, $userData['listName'] . " (myList)");
            } else {
                array_push($listRecipesBundle, $userData['listName']);
            }
            array_push($listRecipesBundle, $userData['listID']);
            $listRecipesArray[] = $listRecipesBundle;
        }
        return $listRecipesArray;
    }


    /**
     * Summary of retrieveAllPublicListRecipes
     * @return array<string, string> (array<listName, listID>)
     */
    public function retrieveAllPublicListRecipes() {
        $listRecipesArray = array();
        
        $sql = "SELECT * FROM listRecipe WHERE privateStatus = 0";
        
        //* connect to db and send sql
        $dataBaseConnection = new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
        $stmt = $dataBaseConnection->prepare($sql);
        $stmt->execute();

        //* collect listRecipes
        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $listRecipesBundle = array();
            array_push($listRecipesBundle, $userData['listName']);
            array_push($listRecipesBundle, $userData['listID']);
            $listRecipesArray[] = $listRecipesBundle;
        }
        $newListID = (new ListRecipeIDSQL())->getListRecipeID();
        
        array_push($listRecipesArray, array("new ListRecipe", $newListID));
        return $listRecipesArray;
    }

    public function retrieveAllMyListRecipes($userID) {
        $listRecipesArray = array();
        $sql = "SELECT * FROM listRecipe WHERE ownerID = :userID";
        
        //* connect to db and send sql
        $dataBaseConnection = new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
        $stmt = $dataBaseConnection->prepare($sql);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        
        //* collect listRecipes
        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $listRecipesBundle = array();
            array_push($listRecipesBundle, $userData['listName']);
            array_push($listRecipesBundle, $userData['listID']);
            $listRecipesArray[] = $listRecipesBundle;
        }
        
        return $listRecipesArray;
    }

    public function retrieveAllMyListRecipesAndNewList($userID) {
        $listRecipesArray = array();
        $sql = "SELECT * FROM listRecipe WHERE ownerID = :userID";
        
        //* connect to db and send sql
        $dataBaseConnection = new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
        $stmt = $dataBaseConnection->prepare($sql);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        
        //* collect listRecipes
        while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $listRecipesBundle = array();
            array_push($listRecipesBundle, $userData['listName']);
            array_push($listRecipesBundle, $userData['listID']);
            $listRecipesArray[] = $listRecipesBundle;
        }
        $newListID = (new ListRecipeIDSQL())->getListRecipeID();
        
        array_push($listRecipesArray, array("new ListRecipe", $newListID));
        return $listRecipesArray;
    }


  
    }




    

?>
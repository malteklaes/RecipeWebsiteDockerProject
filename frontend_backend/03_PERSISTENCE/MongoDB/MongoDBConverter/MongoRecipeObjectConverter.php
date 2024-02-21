<?php

    require_once(__DIR__.'/../MongoDBEntity/UserMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/CommentMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/RecipeMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/ListRecipeMongo.php');



    class MongoRecipeObjectConverter {

        private $recipesArray;

        function __construct() {
            $this->recipesArray = array();
            $this->collectAllRecipes();
        }

        /**
         * creates all user-objects like this
         * (1) connect to MySQL-db and retrieves array with all users
         * (2) fills $this->usersArray with all those users  
         * @return void
         */
        private function collectAllRecipes(){
            $recipes = $this->collectAllRecipesFromMySQL();
            foreach($recipes as $recipe){     
                $collectUsernamesAndRatingForRecipe = $recipe[7];
                $containedInListRecipe = $recipe[8];
                $newRecipe = new RecipeMongo($recipe[0], $recipe[1], $recipe[2], $recipe[3], $recipe[4], $recipe[5], $recipe[6]);
                $newRecipe->addUserRated($collectUsernamesAndRatingForRecipe);
                $newRecipe->addListRecipes($containedInListRecipe);
                array_push($this->recipesArray, $newRecipe);
            }
        }

        /**
         * Summary of collectAllRecipesFromMySQL
         * @return array
         */
        private function collectAllRecipesFromMySQL(){
            $recipes = array();
            $sql = "SELECT * FROM recipe";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->execute();

            //* collect recipes
            while ($recipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipeInfoBundle = array();
                array_push($recipeInfoBundle, $recipeData['recipeName'] );
                array_push($recipeInfoBundle, $recipeData['recipeDescription'] );
                array_push($recipeInfoBundle, $recipeData['category'] );
                array_push($recipeInfoBundle, $recipeData['rating'] );
                array_push($recipeInfoBundle, $this->getAllRecipes_GETComments($recipeData['postID']));
                array_push($recipeInfoBundle, $recipeData['creationDate'] );
                array_push($recipeInfoBundle, $recipeData['editedDate'] );
                array_push($recipeInfoBundle, $this->collectUsernamesAndRatingForRecipe($recipeData['postID']));
                array_push($recipeInfoBundle, $this->getAllRecipesListNamesContainRecipe($recipeData['postID']));
                $recipes[] = $recipeInfoBundle;
            }
            
            return $recipes;
        }


        /**
         * Summary of getAllRecipesListNamesContainRecipe
         * @param mixed $postID
         * @return array<string> (array<listname>)
         */
        private function getAllRecipesListNamesContainRecipe($postID){
            $recipesInListRecipe = array();
            
            $sql = "SELECT * FROM listContainsOfRecipe WHERE recipeID = :postID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();
            
            while ($recipesData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipesInListRecipe[] = $this->getAllRecipesListNames_GETListName($recipesData['listID']);
            }
            return $recipesInListRecipe;
        }

        private function getAllRecipesListNames_GETListName($listID){
            $sql = "SELECT * FROM listRecipe WHERE listID = :listID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':listID', $listID);
            $stmt->execute();

            if($listRecipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $listRecipeData['listName'];
            }
            return null;
        }

        /**
         * collect all information about rating for a single recipe (username and what given rating)
         * @param mixed $postID
         * @return array<string, int> (array<username, rating>)
         */
        public function collectUsernamesAndRatingForRecipe($postID){
            $ratedRecipesOfUser = array();
            $sql = "SELECT * FROM userRatesRecipe WHERE postID = :postID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();
            
            while ($ratedRecipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ratingInfoBundle = array();
            array_push($ratingInfoBundle, $this->collectUsernamesAndRatingForRecipe_GETuserName($ratedRecipeData['userID']));
            array_push($ratingInfoBundle, $ratedRecipeData['rating']);
                $ratedRecipesOfUser[] = $ratingInfoBundle;
            }
            return $ratedRecipesOfUser;
        }

        /**
         * retrieves username by given userID
         * @param mixed $userID
         * @return null|string (null|username)
         */
        private function collectUsernamesAndRatingForRecipe_GETuserName($userID){
            $sql = "SELECT * FROM user WHERE userID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            if($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $userData['username'];
            }
            return null;
        }

        /**
         * helper function for getAllRecipes
         * @param mixed $postID
         * @return array<CommentMongo>
         */
        private function getAllRecipes_GETComments($postID){
            $recipesComments = array();
            $sql = "SELECT * FROM comment WHERE recipeIDReference = :postID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();
            while ($commentData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $comment = new CommentMongo($commentData['commentContent'], $this->collectAllCommentsFromMySQL_GETownerName($commentData['ownerUserID']), $commentData['creationDate'],$commentData['editedDate']);
                $recipesComments[] = $comment;
            }
            return $recipesComments;
        }

        private function collectAllCommentsFromMySQL_GETownerName($userID){
            $sql = "SELECT * FROM user WHERE userID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            if($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                return $userData['username'];
            }
            return null;
        }


        public function getRecipesArray() {
        	return $this->recipesArray;
        }
    }

?>
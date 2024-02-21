<?php

    require_once(__DIR__.'/../MongoDBEntity/UserMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/RecipeMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/ListRecipeMongo.php');

    class MongoListRecipeObjectConverter {

        private $ListRecipeArray;

        function __construct() {
            $this->ListRecipeArray = array();
            $this->collectAllListRecipes();
        }

        public function collectAllListRecipes() {
            $listRecipes = array();
            $sql = "SELECT * FROM listRecipe";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->execute();
            
            while ($listRecipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipes  =$this->getAllRecipesListID($listRecipeData['listID']);
                $userLikeAmount = $this->countLikeAmountPerList($listRecipeData['listID']);
                $newListRecipe = new ListRecipeMongo($listRecipeData['listName'], $listRecipeData['listDescription'], 
                $listRecipeData['privateStatus'], $recipes, $userLikeAmount, $listRecipeData['creationDate'], $listRecipeData['editedDate'] );
                array_push($this->ListRecipeArray, $newListRecipe);
            }
            
            return $listRecipes;
        }

        /**
         * Summary of getListRecipeArray
         * @return array<ListRecipeMongo>
         */
        public function getListRecipeArray() {
        	return $this->ListRecipeArray;
        }

        private function getAllRecipesListID($listID){
            $recipesInListRecipe = array();
            
            $sql = "SELECT * FROM listContainsOfRecipe WHERE listID = :listID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':listID', $listID);
            $stmt->execute();
            
            while ($recipesData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipesInListRecipe[] = $this->getAllRecipes_GETRecipe($recipesData['recipeID']);
            }
            return $recipesInListRecipe;
        }
        
       
        private function getAllRecipes_GETRecipe($postID){
            $sql = "SELECT * FROM recipe WHERE postID = :postID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();

            if($recipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipe = new RecipeMongo($recipeData['recipeName'],$recipeData['recipeDescription'],
                $recipeData['category'], $recipeData['rating'],$this->getAllCommentsByPostID($recipeData['postID']), 
                $recipeData['creationDate'],$recipeData['editedDate']);
                return $recipe;
            }
            return null;
        }

        private function countLikeAmountPerList($listID){
            $sql = "SELECT * FROM userLikesListRecipe WHERE listID = :listID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':listID', $listID);
            $stmt->execute();

            $likeCounter = 0;
            while ($recipesData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $likeCounter++;
            }
            return $likeCounter;
        }


        private function collectAllUsers($listID){
            $users = $this->collectAllUsersFromMySQL($listID);
            foreach($users as $user){
                $userLiked = new UserMongo($user[1], $user[2], $user[3], $user[4], 
                $user[5], $user[6], $user[7], $user[8], $user[9], $user[10]); 
            }
            return $userLiked;
        }

        public function collectAllUsersFromMySQL($listID) {
            $users = array();

            $sql = "SELECT user.userID as userID, user.username as username, user.email as email,
            user.pwd as pwd, user.registrationDate as registrationDate FROM UserLikesListRecipe JOIN user 
            ON UserLikesListRecipe.userID = user.userID 
            WHERE UserLikesListRecipe.listID = :listID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $listID);
            $stmt->execute();

            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $userData = array();
                array_push($userInfoBundle, $userData['userID'] );
                array_push($userInfoBundle, $userData['username'] );
                array_push($userInfoBundle, $userData['email'] );
                array_push($userInfoBundle, $userData['pwd'] );
                array_push($userInfoBundle, $userData['registrationDate'] );
                //* here fill all other arrays for user
                array_push($userInfoBundle, $this->getAllComments($userData['userID']));
                array_push($userInfoBundle, $this->getAllRecipes($userData['userID']));
                array_push($userInfoBundle, $this->getAllListRecipes($userData['userID']));
                array_push($userInfoBundle, $this->getAllLikedListRecipes($userData['userID']));
                array_push($userInfoBundle, $this->getAllRatedRecipes($userData['userID']));
                array_push($userInfoBundle, $this->getAllFollowedUser($userData['userID']));

                $users[] = $userInfoBundle;
            }

            return $users;

        }

        /**
         * help function to collect all comments belong to a recipe
         * @param mixed $postID
         * @return array<CommentMongo>
         */
        public function getAllCommentsByPostID($postID){
            $commentsOfUser = array();
            
            $sql = "SELECT * FROM comment WHERE recipeIDReference = :postID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();
            
            while ($commentData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $comment = new CommentMongo($commentData['commentContent'],$this->collectAllCommentsFromMySQL_GETownerName($commentData['ownerUserID']),$commentData['creationDate'],$commentData['editedDate']);
                $commentsOfUser[] = $comment;
            }
            return $commentsOfUser;
        }

        public function getAllComments($userID){
            $commentsOfUser = array();
            
            $sql = "SELECT * FROM comment WHERE ownerUserID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
            
            while ($commentData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $comment = new CommentMongo($commentData['commentContent'],$this->collectAllCommentsFromMySQL_GETownerName($commentData['ownerUserID']),$commentData['creationDate'],$commentData['editedDate']);
                $commentsOfUser[] = $comment;
            }
            return $commentsOfUser;
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

        private function getAllRecipes($userID){
            $recipesOfUser = array();
            
            $sql = "SELECT * FROM recipe WHERE ownerUserID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
            while ($recipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipe = new RecipeMongo($recipeData['recipeName'],$recipeData['recipeDescription'],
                $recipeData['category'], $recipeData['rating'], $this->getAllRecipes_GETComments($recipeData['postID']), 
                $recipeData['creationDate'],$recipeData['editedDate']);
                $recipesOfUser[] = $recipe;
            }
            return $recipesOfUser;
        }

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


        private function getAllListRecipes($userID){
            $listRecipesOfUser = array();
            
            $sql = "SELECT * FROM listRecipe WHERE ownerID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            while ($listRecipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipes  =$this->getAllRecipesListID($listRecipeData['listID']);
                $userLikeAmount = $this->countLikeAmountPerList($listRecipeData['listID']);
                $listRecipe = new ListRecipeMongo($listRecipeData['listName'],$listRecipeData['listDescription']
                ,$listRecipeData['privateStatus'],$recipes, $userLikeAmount, $listRecipeData['creationDate'],$listRecipeData['editedDate']);
                $listRecipesOfUser[] = $listRecipe;
            }
            return $listRecipesOfUser;
        }

        private function getAllLikedListRecipes($userID){
            $likedListRecipesOfUser = array();
            
            $sql = "SELECT * FROM userLikesListRecipe WHERE userID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            while ($likedListRecipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $likedListRecipesOfUser[] = $this->getAllLikedListRecipes_GETListRecipe($likedListRecipeData['listID']);
            }
            return $likedListRecipesOfUser;
        }
        
        private function getAllLikedListRecipes_GETListRecipe($listID){
            $sql = "SELECT * FROM listRecipe WHERE listID = :listID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':listID', $listID);
            $stmt->execute();

            if($listRecipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipes  =$this->getAllRecipesListID($listRecipeData['listID']);
                $userLikeAmount = $this->countLikeAmountPerList($listRecipeData['listID']);
                $listRecipe = new ListRecipeMongo($listRecipeData['listName'],$listRecipeData['listDescription']
                ,$listRecipeData['privateStatus'],$recipes, $userLikeAmount,$listRecipeData['creationDate'],$listRecipeData['editedDate']);
                return $listRecipe;
            }
            return null;
        }

        private function getAllRatedRecipes($userID){
            $ratedRecipesOfUser = array();
            
            $sql = "SELECT * FROM userRatesRecipe WHERE userID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
            
            while ($ratedRecipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ratedRecipesOfUser[] = $this->getAllRatedRecipes_GETRecipe($ratedRecipeData['postID']);
            }
            return $ratedRecipesOfUser;
        }
        
        private function getAllRatedRecipes_GETRecipe($postID){
            $sql = "SELECT * FROM recipe WHERE postID = :postID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();

            if($recipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $recipe = new RecipeMongo($recipeData['recipeName'],$recipeData['recipeDescription'],
                $recipeData['category'], $recipeData['rating'],$this->getAllRecipes_GETComments($recipeData['postID']), 
                $recipeData['creationDate'],$recipeData['editedDate']);
                return $recipe;
            }
            return null;
        }
        
        private function getAllFollowedUser($userID){
            $followedUsers = array();
            
            $sql = "SELECT * FROM userFollow WHERE user1ID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            while ($allFollowedUsers = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $followedUsers[] = $this->getAllFollowedUser_GETUser($allFollowedUsers['user2ID']);
            }
            return $followedUsers;
        }

        private function getAllFollowedUser_GETUser($user2ID){
            $sql = "SELECT * FROM user WHERE userID = :user2ID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':user2ID', $user2ID);
            $stmt->execute();
            if($followedUserData = $stmt->fetch(PDO::FETCH_ASSOC)) {
               $user = new UserMongo($followedUserData['username'],
                $followedUserData['email'],$followedUserData['pwd'], $followedUserData['registrationDate'],
                $this->getAllComments($followedUserData['userID']), 
                $this->getAllRecipes($followedUserData['userID']),  
                $this->getAllListRecipes($followedUserData['userID']),  
                $this->getAllLikedListRecipes($followedUserData['userID']), 
                $this->getAllRatedRecipes($followedUserData['userID']),
                $this->getAllFollowedUserOnlyNames($followedUserData['userID']));
                return $user;
            }
            return null;
        }

        private function getAllFollowedUserOnlyNames($userID){
            $followedUsers = array();
            
            $sql = "SELECT * FROM userFollow WHERE user1ID = :userID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
            while ($allFollowedUsers = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $followedUsers[] = $this->getAllFollowedUserOnlyNames_GETUserName($allFollowedUsers['user2ID']);
            }
            return $followedUsers;
        }


        private function getAllFollowedUserOnlyNames_GETUserName($user2ID){
            $sql = "SELECT * FROM user WHERE userID = :user2ID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':user2ID', $user2ID);
            $stmt->execute();
            if($followedUserData = $stmt->fetch(PDO::FETCH_ASSOC)) {
               $user = new UserMongo($followedUserData['username'], null, null, null, null, null, null, null, null, null);
                return $user;
            }
            return null;
        }


        
    }
?>
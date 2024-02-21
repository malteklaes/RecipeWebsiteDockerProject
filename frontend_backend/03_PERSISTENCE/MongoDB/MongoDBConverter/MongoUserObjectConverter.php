<?php

    require_once(__DIR__.'/../MongoDBEntity/UserMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/CommentMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/RecipeMongo.php');
    require_once(__DIR__.'/../MongoDBEntity/ListRecipeMongo.php');



    class MongoUserObjectConverter{


        private $usersArray;
        private $commentsArray;

    

        /**
         * sense: do automation right of the beginning, there should be only 
         * *GETTERS
         * in this class
         */
        function __construct() {
            $this->usersArray = array();
            $this->commentsArray = array();
            $this->collectAllUsers();
        }
    
    
        //* USER migration to objects -------------------------------------------------------------------------------------
        /**
         * creates all user-objects like this
         * (1) connect to MySQL-db and retrieves array with all users
         * (2) fills $this->usersArray with all those users  
         * @return void
         */
        private function collectAllUsers(){
            $users = $this->collectAllUsersFromMySQL();
            foreach($users as $user){
                array_push($this->usersArray, new UserMongo($user[1], $user[2], $user[3], $user[4], 
                $user[5], $user[6], $user[7], $user[8], $user[9], $user[10]));
            }
        }


        
        private function collectAllUsersFromMySQL(){
            $users = array();
            $sql = "SELECT * FROM user";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->execute();

            //* collect recipes
            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $userInfoBundle = array();
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
         * publishs result of this class to outer world
         * @return array<UserMongo>
         */
        public function getUsersArray() {
        	return $this->usersArray;
        }

        //* help functions to retrieve other data for user -------------------------------------------------------------------------------------
        
        
        //* get COMMENTS ###########################################
        /**
         * rertieves all comments of this user (userID)
         * @param mixed $userID (not null)
         * @return array<CommentMongo>
         */
        private function getAllComments($userID){
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
        
        //* get RECIPES ###########################################
        /**
         * rertieves all recipes of this user (userID)
         * @param mixed $userID (not null)
         * @return array<RecipeMongo>
         */
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
        
        //* get LISTRECIPES ###########################################
        
        /**
         * rertieves all ListRecipe of this user (userID) (which are also managed by this user)
         * @param mixed $userID (not null)
         * @return array<ListRecipeMongo>
         */
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
        //* get LIKED-LISTRECIPES ###########################################
        
        /**
         * rertieves all liked ListRecipes of this user (userID)
         * @param mixed $userID (not null)
         * @return array<ListRecipeMongo>
         */
        private function getAllLikedListRecipes($userID){
            $likedListRecipesOfUser = array();
            
            $sql = "SELECT l.listID, l.ownerID, l.listName, l.likesAmount, l.listDescription, l.privateStatus, l.creationDate, l.editedDate
                FROM userLikesListRecipe ul
                JOIN listRecipe l ON ul.listID = l.listID
                WHERE ul.userID = :userID AND l.privateStatus = 0";


            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();

            while ($likedListRecipeData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $likedListRecipesOfUser[] = $this->getAllLikedListRecipes_GETListRecipe($likedListRecipeData['listID']);
            }
            return $likedListRecipesOfUser;
        }
        
        /**
         * helper function for getAllLikedListRecipes
         * @param mixed $listID (not null)
         * @return ListRecipeMongo|null
         */
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
                ,$listRecipeData['privateStatus'], $recipes, $userLikeAmount, $listRecipeData['creationDate'],$listRecipeData['editedDate']);
                return $listRecipe;
            }
            return null;
        }
        
        //* get RATED-RECIPES ###########################################
        
        /**
         * rertieves all rated recipes b< this user (userID)
         * @param mixed $userID (not null)
         * @return array<RecipeMongo>
         */
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
        
        /**
         * helper function for getAllRatedRecipes
         * @param mixed $postID (not null)
         * @return RecipeMongo|null
         */
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
        
        //* get FOLLOWED USER ###########################################
        /**
         * rertieves all user this user (userID) followes
         * @param mixed $userID (not null)
         * @return array<UserMongo>
         */
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

        /**
         * helper function for getAllFollowedUser
         * @param mixed $userID (not null) 
         * @return mixed (UserMongo)
         */
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

        /**
         * prevents endless recursion in followed in cyclic relationships
         * @param mixed $userID (not null)
         * @return array<UserMongo>
         */
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

        /**
         * helper function for  getAllFollowedUserOnlyNames
         * @param mixed $userID (not null) 
         * @return UserMongo (UserMongo-objects are not completely filled due to prohibit endless recursion)
         */
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
            return new UserMongo(null, null, null, null, null, null, null, null, null, null);
        }


    }


?>
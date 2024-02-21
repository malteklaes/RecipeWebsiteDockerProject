<?php

    require_once('MySQLDataBaseCreator.php');
    require_once('MySQLDataBaseInserter.php');
    require_once('MySQLDataBaseQuery.php');

    class MySQLController {

        //* internal class variables
        private $sqlCreator;
        private $creatorSQLConnection;
        private $sqlInserter;
        private $sqlDataBaseQuery;

        //* created example data (each type is an array)
        private $users;
        private $recipes;
        private $comments;
        private $listRecipes;
        private $isRandomDataAvailable;

        private $userLikesListRecipe;
        private $userRatesRecipe;
        private $listContainsOfRecipe;
        private $userFollow;


        public function __construct() {
            //* [1] create all database schemas: CREATOR
        	$this->sqlCreator = new MySQLDataBaseCreator();
            $this->creatorSQLConnection = $this->sqlCreator->getSQLConn();
            //* [2] initialize database schemas with (example) data: INIT
            $this->sqlInserter = new MySQLDataBaseInserter();
            //* [3] initialize database query
            $this->sqlDataBaseQuery = new MySQLDataBaseQuery();
            //* [4] set up data status
            $this->isRandomDataAvailable = false;
            //* [5] example data
            $this->users = array();
            $this->recipes = array();
            $this->comments = array();
            $this->listRecipes = array();

            $this->userLikesListRecipe = array();
            $this->userRatesRecipe = array();
            $this->listContainsOfRecipe = array();
            $this->userFollow = array();

        }

        /**
         * main function to coordinate the whole 
         * procedure of creating random sample data
         * needs these (sub-)functions:
         * (1) initData($amountOfUser)
         *     (1b) initUserData(), initRecipeData(), initCommentData(), initListRecipeData()
         * (2) wireData()
         * (3) insertDataIntoDataBase()
         * @param mixed $amountOfUser (not null)
         * @return void
         */
        public function initDataBase($amountOfUser) {
            $this->initData($amountOfUser);
            //* wireData
            $this->wireData();
            //* after this, insertData
            $this->insertDataIntoDataBase();
            $this->isRandomDataAvailable = true;
        }

        /**
         * wires all created data in terms of inserting ownership (IDs) and reference-ID into 
         * each entity-arrays
         * @return void
         */
        private function wireData(){
            //* [1] insert random recipeID into a COMMENT  
            foreach ($this->comments as $comment) {
                $randomRecipe = $this->recipes[array_rand($this->recipes)];
                $comment->addRecipeIDReference($randomRecipe->getPostID());
                $comment->addOwnerUserID($this->users[array_rand($this->users)]->getUserID());
            }
            //* [2] insert random userID into a RECIPE  
            foreach ($this->recipes as $recipe) {
                $recipe->addOwnerUserID($this->users[array_rand($this->users)]->getUserID());
            }
            //* [3] insert random recipeID into a LISTRECIPE
            $userCounter = 0;  
            foreach ($this->listRecipes as $listRecipe) {
                $randomUser = $this->users[$userCounter];
                $listRecipe->addOwnerUserID($randomUser->getUserID());
                $userCounter++;
            }
            //* [4] create and insert  USERLIKESLISTRECIPE
            for ($i = 0; $i < 50; $i++) {
                array_push($this->userLikesListRecipe, new UserLikesListRecipe($this->users[array_rand($this->users)]->getUserID(), $this->listRecipes[array_rand($this->listRecipes)]->getListID()));
            }
            //* [5] create and insert  USERRATESRECIPE
            for ($i = 0; $i < 200; $i++) {
                array_push($this->userRatesRecipe, new UserRatesRecipeSQL($this->users[array_rand($this->users)]->getUserID(), $this->recipes[array_rand($this->recipes)]->getPostID(), rand(1,5)));
            }
            //* [6] create and insert  LISTCONTAINSOFRECIPE
            for ($i = 0; $i < 10; $i++) {
                array_push($this->listContainsOfRecipe, new ListContainsOfRecipeSQL($this->listRecipes[array_rand($this->listRecipes)]->getListID(), $this->recipes[array_rand($this->recipes)]->getPostID()));
            } 
            //* [6] create and insert  USERFOLLOW
            for ($i = 0; $i < 10; $i++) {
                $randomUser1ID = $this->users[array_rand($this->users)]->getUserID();
                $randomUser2ID = $this->users[array_rand($this->users)]->getUserID();
                while($randomUser1ID === $randomUser2ID){
                    $randomUser2ID = $this->users[array_rand($this->users)]->getUserID();
                }
                array_push($this->userFollow, new UserFollow($randomUser1ID, $randomUser2ID));
            }     
        
        }

        /**
         * help function to calculate after wireData() the average Rating for each recipe
         * @param mixed $ratedRecipes
         * @return array<array> (like array[ array[PID123, 2.5], array[PID456, 3.7]])
         */
        private function calculateAverageRating($ratedRecipes) {
            $ratings = [];
            foreach ($ratedRecipes as $ratedRecipe) {
                $productId = $ratedRecipe->getPostID();
                $rating = $ratedRecipe->getRating();
                
                //* If the product has already been rated, update the rating
                if (isset($ratings[$productId])) {
                    $ratings[$productId]['totalRating'] += $rating;
                    $ratings[$productId]['count']++;
                } else {
                    //* If the product is rated for the first time, add it to the array
                    $ratings[$productId] = [
                        'totalRating' => $rating,
                        'count' => 1
                    ];
                }
            }
            $averageRatings = []; //* Array for storing average ratings
            
            //* Calculate the average rating for each product
            foreach ($ratings as $productId => $data) {
                $averageRating = $data['totalRating'] / $data['count'];
                $averageRatings[] = [$productId, $averageRating];
            }
            return $averageRatings;
        }

      

        /**
         * insert initialized and wired data finally into MySQL-database
         * @return void
         */
        private function insertDataIntoDataBase(){
            //? MySQL Entities ------------------------------------------------------------------
            //* insert USERS
            $usersHELP = array_unique($this->users);
            foreach ($usersHELP as $user) {
                $this->sqlInserter->insertUserExample($this->creatorSQLConnection, $user);
            }
            //* insert RECIPES
            $ratingArray = $this->calculateAverageRating($this->userRatesRecipe);
            foreach ($this->recipes as $recipe) {
                $result = null;
                //? if postID is is found, rating is collected
                foreach ($ratingArray as $rating) {
                    if ($rating[0] === $recipe->getPostID()) {
                        $result = $rating[1];
                        break;
                    }
                }
                if ($result !== null) {
                    $recipe->setRating($result);
                } 
                $this->sqlInserter->insertRecipeExample($this->creatorSQLConnection, $recipe);
            }

            //* insert COMMENTS
            foreach ($this->comments as $comment) {
                $this->sqlInserter->insertCommentExample($this->creatorSQLConnection, $comment);
            }
            //* insert LISTRECIPE
            foreach ($this->listRecipes as $listRecipe) {
                $this->sqlInserter->insertListRecipeExample($this->creatorSQLConnection, $listRecipe);
            }
            //? Relationships ------------------------------------------------------------------
            //* insert USERRATESRECIPE
            foreach ($this->userRatesRecipe as $userRatesRecipeELEM) {
                $this->sqlInserter->insertUserRatesRecipeExample($this->creatorSQLConnection, $userRatesRecipeELEM->getUserID(), $userRatesRecipeELEM->getPostID(), $userRatesRecipeELEM->getRating());
            }
            //* insert USERLIKESRLISTECIPE
            foreach ($this->userLikesListRecipe as $userLikesListRecipeELEM) {
                $this->sqlInserter->insertUserLikesRecipeExample($this->creatorSQLConnection, $userLikesListRecipeELEM->getUserID(), $userLikesListRecipeELEM->getListRecipeID());
            }
            //* insert USERFOLLOW
            foreach ($this->userFollow as $userFollowELEM) {
                $this->sqlInserter->insertUserFollowExample($this->creatorSQLConnection, $userFollowELEM->getUser1ID(), $userFollowELEM->getUser2ID());
            }
            //* insert LISTCONTAINSOFRECIPE
            foreach ($this->listContainsOfRecipe as $listContainsOfRecipeELEM) {
                $this->sqlInserter->insertListContainsOfRecipeExample($this->creatorSQLConnection, $listContainsOfRecipeELEM->getlistRecipeID(), $listContainsOfRecipeELEM->getRecipeID());
            }

            $this->sqlCreator->closeThisDataBaseConnection();
        }
      
        
        /**
         * initialize all database schemas such as (user, listrecipe, recipe, comment)
         * @param mixed $amountOfUser (not null)
         * @return void
         */
        private function initData($amountOfUser){
            //* (1) create 10 USER
            for ($i = 0; $i < $amountOfUser; $i++) {
                array_push($this->users, $this->initUserData());
                $this->users = array_unique($this->users);
            }
            $this->removeDuplicateUsers();
            $amountOfUser = sizeof($this->users);

            //* (2) create ListRecipe
            for ($i = 0; $i < $amountOfUser; $i++) {
                array_push($this->listRecipes, $this->initListRecipeData());
                
            }
            //* (3) create RECIPE
            for ($i = 0; $i < $amountOfUser*5; $i++) {
                array_push($this->recipes, $this->initRecipeData());
            }
            //* (4) create Comment
            for ($i = 0; $i < $amountOfUser*10; $i++) {
                array_push($this->comments, $this->initCommentData());
            }
        }


        private function removeDuplicateUsers() {
            $uniqueUsers = [];
        
            foreach ($this->users as $user) {
                $username = $user->getUsername();
        
                $isDuplicate = false;
                foreach ($uniqueUsers as $uniqueUser) {
                    if ($uniqueUser->getUsername() === $username) {
                        $isDuplicate = true;
                        break;
                    }
                }
        
                if (!$isDuplicate) {
                    $uniqueUsers[] = $user;
                }
            }
            $this->users = $uniqueUsers;
        }


        public function showAllCreatedData(){
            $resultALL = "";
            $resultALL .= $this->sqlInserter->showUserTableData($this->creatorSQLConnection);
            $resultALL .= $this->sqlInserter->showRecipeTableData($this->creatorSQLConnection);
            $resultALL .= $this->sqlInserter->showCommentTableData($this->creatorSQLConnection);
            $resultALL .= $this->sqlInserter->showListRecipeTableData($this->creatorSQLConnection);
            //return $resultALL;
            return "";
        }

        /**
         * creates and insert a random build user
         * @return UserSQL (not null)
         */
        private function initUserData(){
            $randomBuildUser = $this->sqlInserter->createUserExample();
            return $randomBuildUser;
        }

        /**
         * creates and insert a random build recipe
         * @return RecipeSQL (not null)
         */
        private function initRecipeData(){
            $randomBuildRecipe = $this->sqlInserter->createRecipeExample();
            return $randomBuildRecipe;
        }

        /**
         * creates and insert a random build comment
         * @return CommentSQL (not null)
         */
        private function initCommentData(){
            $randomBuildComment = $this->sqlInserter->createCommentExample();
            return $randomBuildComment;
        }

        
        /**
         * creates and insert a random build listRecipe
         * @return ListRecipeSQL (not null)
         */
        private function initListRecipeData(){
            $randomBuildListRecipe = $this->sqlInserter->createListRecipeExample();
            return $randomBuildListRecipe;
        }

        

        /**
         * erase all data and database schema
         * @return void
         */
        public function eraseAllData(){
            $this->sqlCreator->eraseUserSchema();
            $this->sqlCreator->eraseRecipeSchema();
            $this->sqlCreator->eraseCommentSchema();
            $this->sqlCreator->eraseListRecipeSchema();
            $this->sqlCreator->eraseListContainsOfRecipeSchema();
            $this->sqlCreator->eraseUserFollowSchema();
            $this->sqlCreator->eraseUserLikesListRecipeSchema();
            $this->sqlCreator->eraseUserRatesRecipeSchema();
            $this->isRandomDataAvailable = false;
        }

        

        //* retrieving data from database
        public function retrieveDataFromSQLDataBase(){
            if($this->isRandomDataAvailable === true){
            $userIDsArray = $this->sqlDataBaseQuery->retrieveUserIDsFromTableData($this->creatorSQLConnection);
            return $userIDsArray;
            }
        }
        public function retrieveUserNameFromSQLDataBase(){
            if($this->isRandomDataAvailable === true){
            $userNamesArray = $this->sqlDataBaseQuery->retrieveUserUserNamesFromTableData($this->creatorSQLConnection);
            return $userNamesArray;
            }
        }

        public function retrieveUserRelatedData($userID){
            $userDataArray = $this->sqlDataBaseQuery->retrieveUserDataByUserID($this->creatorSQLConnection, $userID);
            return $userDataArray;
        }

        

        /**
         * @return string
         */
        public function __toString() {
        	return "SqlCreator: {$this->sqlCreator}";
        }
    }

?>
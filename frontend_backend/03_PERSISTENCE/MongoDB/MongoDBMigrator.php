<?php

require_once('MongoDBConverter/MongoUserObjectConverter.php');
require_once('MongoDBConverter/MongoCommentObjectConverter.php');
require_once('MongoDBConverter/MongoRecipeObjectConverter.php');
require_once('MongoDBConverter/MongoListRecipeObjectConverter.php');
require_once('MongoDBInserter.php');






    class MongoDBMigrator{

        private $dbnameMongo = "RecipeWebsiteMongoDB";

        private $userArray;
        private $commentArray;
        private $recipeArray;
        private $listRecipeArray;
        private $actualUser;
        private $mongoDBInserter;

        function __construct(){
        }

        /**
         * orchestrate the whole procedure
         * @param mixed $userID (not null, to get the actual status/actual userID)
         * @return void
         */
        public function startMigration($username){
            //* [1] clear all MongoDB
            $this->clearMongoDB();
            //* [2] migrate all MySQL to php-objects
            new MongoUserObjectConverter();
            new MongoCommentObjectConverter();
            new MongoRecipeObjectConverter();
            new MongoListRecipeObjectConverter(); 
            //* [3] migrate all php-objects to MongoDB-documents in a collection
            $this->mongoDBInserter = new MongoDBInserter();
            //* [3a] USER migration (have to update this one later) 
            $this->userArray = $this->mongoDBInserter->getAllUserFromMongoDB();
            //* [3b] COMMENT migration
            $this->commentArray = $this->mongoDBInserter->getAllCommentsFromMongoDB();
            //* [3c] RECIPE migration
            $this->recipeArray = $this->mongoDBInserter->getAllRecipesFromMongoDB();
            //* [3d] LISTRECIPE migration
            $this->listRecipeArray = $this->mongoDBInserter->getAllListRecipesFromMongoDB();
            //* [4] safe actual status, this means, who is the actual user (with all its data to shown in website-process)
            $this->actualUser = $this->getActualStatus($username);

            //* [5] erase all MySQL data
            $this->eraseAllMySQLData();
        }

    
        

        /**
         * clear the whole MongoDB
         * @return void
         */
        private function clearMongoDB() {
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");

            //* [1] Get list of all collections
            $collections = $manager->executeCommand($this->dbnameMongo, new MongoDB\Driver\Command(['listCollections' => 1]));

            //* [2] Delete the documents for each collection
            foreach ($collections as $collection) {
                $collectionName = $collection->name;

                $bulk = new MongoDB\Driver\BulkWrite();
                $bulk->delete([], ['limit' => 0]);

                $manager->executeBulkWrite($this->dbnameMongo . '.' . $collectionName, $bulk);
            }
        }


        /**
         * just toString for testing purpose
         * @param mixed $displayArray
         * @return void
         */
        public function toString($displayArray){
            foreach ($displayArray as $element) {
                echo "<pre>";
                    print_r($element);
                echo "</pre>";
            }
        }

        /**
         * retrieves all data from MongoDB to the actual status (menas for the actual userID)
         * @param mixed $userID
         * @return mixed (BSON user-document)
         */
        private function getActualStatus($username){
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
            $databaseName = $this->dbnameMongo;
            $collectionName = 'user_collection';
            
            
            $targetUsername = $username;
            
            //* [1] Create a query to find the document
            $query = new MongoDB\Driver\Query(['username' => $targetUsername]);
            
            //* [2] Run the query in user_collection targeting the field "userID"
            $result = $manager->executeQuery("$databaseName.$collectionName", $query);
            
            
            //* [3] Check if a document was found
            if ($result->isDead()) {
                echo "Kein Dokument mit der userID $targetUsername gefunden.";
            } else {
                //*[4] Read and return document 
                foreach ($result as $document) {
                    /* var_dump($document); */
                    return $document;
                }   
            }
        }

        public function getActualUser() {
            return $this->actualUser;
        }

         //* USER get -------------------------------------------------------------------------------------
      
        public function getUserArray() {
        	return $this->userArray;
        }

        //* COMMENT get -------------------------------------------------------------------------------------
      

        public function getCommentArray() {
        	return $this->commentArray;
        }

         //* RECIPE get -------------------------------------------------------------------------------------
         
         public function getRecipeArray() {
             return $this->recipeArray;
            }
        
        //* LISTRECIPE get -------------------------------------------------------------------------------------
            
        public function getListRecipeArray() {
        	return $this->listRecipeArray;
        }

        public function eraseAllMySQLData(){
            require_once(__DIR__.'/../MySQL/MySQLDataBaseCreator.php');
            $sqlCreator = new MySQLDataBaseCreator();
            $sqlCreator->eraseUserSchema();
            $sqlCreator->eraseRecipeSchema();
            $sqlCreator->eraseCommentSchema();
            $sqlCreator->eraseListRecipeSchema();
            $sqlCreator->eraseListContainsOfRecipeSchema();
            $sqlCreator->eraseUserFollowSchema();
            $sqlCreator->eraseUserLikesListRecipeSchema();
            $sqlCreator->eraseUserRatesRecipeSchema();
        }
    }


?>
<?php

    require_once('MongoDBConverter/MongoUserObjectConverter.php');
    require_once('MongoDBConverter/MongoCommentObjectConverter.php');
    require_once('MongoDBConverter/MongoRecipeObjectConverter.php');
    require_once('MongoDBConverter/MongoListRecipeObjectConverter.php');
    use MongoDB\Driver\Manager;
    use MongoDB\Driver\BulkWrite;
    use MongoDB\Driver\Query;



    class MongoDBInserter {

        private $dbnameMongo = "RecipeWebsiteMongoDB";
        private $usersArray;
        private $commentsArray;
        private $recipesArray;
        private $listRecipesArray;
        private $mongoUserObjectConverter;
        private $mongoCommentObjectConverter;
        private $mongoRecipeObjectConverter;
        private $mongoListRecipeObjectConverter;

        function __construct() {
            $this->mongoUserObjectConverter = new MongoUserObjectConverter();
            $this->mongoCommentObjectConverter = new MongoCommentObjectConverter();
            $this->mongoRecipeObjectConverter = new MongoRecipeObjectConverter();
            $this->mongoListRecipeObjectConverter = new MongoListRecipeObjectConverter(); 
            $this->usersArray = array();
            $this->commentsArray = array();
            $this->recipesArray = array();
            //* USER migration
            $this->usersArray = $this->mongoUserObjectConverter->getUsersArray();
            $this->writeAllUsersToUserCollection();
            //* COMMENT migration
            $this->commentsArray = $this->mongoCommentObjectConverter->getCommentsArray();
            $this->writeAllCommentsToCommentCollection();
            //* RECIPE migration
            $this->recipesArray = $this->mongoRecipeObjectConverter->getRecipesArray();
            $this->writeAllRecipesToRecipeCollection();
            //* LISTRECIPE migration
            $this->listRecipesArray = $this->mongoListRecipeObjectConverter->getListRecipeArray();
            $this->writeAllListRecipesToRecipeCollection();
        }


        //* USER conversion to mongo -------------------------------------------------------------------------------------
        private function writeAllUsersToUserCollection(){
            //* [1] create manager
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");

            //* [2] initialize the userDoc array
            $userDocs = [];

            //* [3] for every user, create a new document and add it to the array
            foreach ($this->usersArray as $user) {
                
                $userDoc = [
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'pwd' => $user->getPassword(),
                    'registrationDate' => $user->getRegistrationDate(),
                    'comments' => $user->getComments(),
                    'recipes' => $user->getRecipes(),
                    'listRecipes' => $user->getListRecipesManaged(),
                    'listRecipesLiked' => $user->getListRecipesLiked(),
                    'ratedRecipes' => $user->getRates(),
                    'following' => $user->getFollowing()
                ];
                $userDocs[] = $userDoc;
            }

            //* [4] insert all user documents into MongoDB
            $bulkWrite = new MongoDB\Driver\BulkWrite();
            foreach ($userDocs as $userDoc) {
                $bulkWrite->insert($userDoc);
            }

            //* [5] write all data to the database and the specific collection
            $manager->executeBulkWrite($this->dbnameMongo . '.user_collection', $bulkWrite);
        }

        public function getAllUserFromMongoDB(){
            //* [1] create manager
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");

            //* [2] specify the query to retrieve all documents
            $query = new MongoDB\Driver\Query([]);

            //* [3] execute the query and retrieve the result set
            $result = $manager->executeQuery($this->dbnameMongo.'.user_collection', $query);

            //* [4] iterate over the result set and store the documents in an array
            $userDocuments = [];
            foreach ($result as $document) {
                $userDocuments[] = $document;
            }
            //* [5] return the array of user documents
            return $userDocuments;

        }

        //* COMMENT conversion to mongo -------------------------------------------------------------------------------------
        private function writeAllCommentsToCommentCollection(){
            //* [1] create manager
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");

            //* [2] initialize the commentDocs array
            $commentDocs = [];

            //* [3] for every comment, create a new document and add it to the array
            foreach ($this->commentsArray as $comment) {
                $commentDoc = [
                    'commentContent' => $comment->getCommentContent(),
                    'ownerName' => $comment->getOwnerName(),
                    'creationDate' => $comment->getCreationDate(),
                    'editedDate' => $comment->getEditedDate(),
                ];
                $commentDocs[] = $commentDoc;
            }

            //* [4] insert all comment documents into MongoDB
            $bulkWrite = new MongoDB\Driver\BulkWrite();
            foreach ($commentDocs as $commentDoc) {
                $bulkWrite->insert($commentDoc);
            }

            //* [5] write all data to the database and the specific collection
            $manager->executeBulkWrite($this->dbnameMongo . '.comment_collection', $bulkWrite);
        }

        public function getAllCommentsFromMongoDB(){
            //* [1] create manager
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");

            //* [2] specify the query to retrieve all documents
            $query = new MongoDB\Driver\Query([]);

            //* [3] execute the query and retrieve the result set
            $result = $manager->executeQuery($this->dbnameMongo.'.comment_collection', $query);

            //* [4] iterate over the result set and store the documents in an array
            $commentDocuments = [];
            foreach ($result as $document) {
                $commentDocuments[] = $document;
            }
            //* [5] return the array of comment documents
            return $commentDocuments;

        }

        //* RECIPE conversion to mongo -------------------------------------------------------------------------------------
        private function writeAllRecipesToRecipeCollection(){
            //* [1] create manager
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");

            //* [2] initialize the recipeDocs array
            $recipeDocs = [];

            //* [3] for every recipe, create a new document and add it to the array
            foreach ($this->recipesArray as $recipe) {
                $recipeDoc = [
                    'recipeName' => $recipe->getRecipeName(),
                    'recipeDescription' => $recipe->getRecipeDescription(),
                    'category' => $recipe->getCategory(),
                    'rating' => $recipe->getRating(),
                    'comments' => $recipe->getComments(),
                    'userRated' => $recipe->getUserRated(),
                    'listRecipe' => (($recipe->getListRecipes() !== null) ? $recipe->getListRecipes() : array()),
                    'creationDate' => $recipe->getCreationDate(),
                    'editedDate' => $recipe->getEditedDate(),
                ];
                $recipeDocs[] = $recipeDoc;
            }

            //* [4] insert all recipe documents into MongoDB
            $bulkWrite = new MongoDB\Driver\BulkWrite();
            foreach ($recipeDocs as $recipeDoc) {
                $bulkWrite->insert($recipeDoc);
            }

            //* [5] write all data to the database and the specific collection
            $manager->executeBulkWrite($this->dbnameMongo . '.recipe_collection', $bulkWrite);
        }

        public function getAllRecipesFromMongoDB(){
            //* [1] create manager
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");

            //* [2] specify the query to retrieve all documents
            $query = new MongoDB\Driver\Query([]);

            //* [3] execute the query and retrieve the result set
            $result = $manager->executeQuery($this->dbnameMongo.'.recipe_collection', $query);

            //* [4] iterate over the result set and store the documents in an array
            $recipeDocuments = [];
            foreach ($result as $document) {
                $recipeDocuments[] = $document;
            }
            //* [5] return the array of comment documents
            return $recipeDocuments;

        }



        //* LISTRECIPE conversion to mongo -------------------------------------------------------------------------------------
        private function writeAllListRecipesToRecipeCollection(){
            //* [1] create manager
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");

            //* [2] initialize the recipeDocs array
            $listRecipeDocs = [];

            //* [3] for every recipe, create a new document and add it to the array
            foreach ($this->listRecipesArray as $listRecipe) {
                $listRecipeDoc = [
                    'listName' => $listRecipe->getListName(),
                    'listDescription' => $listRecipe->getListDescription(),
                    'privateStatus' => $listRecipe->getPrivateStatus(),
                    'recipes' => $listRecipe->getRecipesOfListRecipe(),
                    'likes' => $listRecipe->getLikesAmount(),
                    'creationDate' => $listRecipe->getCreationDate(),
                    'editedDate' => $listRecipe->getEditedDate(),
                ];
                $listRecipeDocs[] = $listRecipeDoc;
            }

            //* [4] insert all recipe documents into MongoDB
            $bulkWrite = new MongoDB\Driver\BulkWrite();
            foreach ($listRecipeDocs as $listRecipeDoc) {
                $bulkWrite->insert($listRecipeDoc);
            }

            //* [5] write all data to the database and the specific collection
            $manager->executeBulkWrite($this->dbnameMongo . '.listrecipe_collection', $bulkWrite);
        }

        public function getAllListRecipesFromMongoDB(){
            //* [1] create manager
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");

            //* [2] specify the query to retrieve all documents
            $query = new MongoDB\Driver\Query([]);

            //* [3] execute the query and retrieve the result set
            $result = $manager->executeQuery($this->dbnameMongo.'.listrecipe_collection', $query);

            //* [4] iterate over the result set and store the documents in an array
            $recipeDocuments = [];
            foreach ($result as $document) {
                $recipeDocuments[] = $document;
            }
            //* [5] return the array of comment documents
            return $recipeDocuments;

        }


    }

?>
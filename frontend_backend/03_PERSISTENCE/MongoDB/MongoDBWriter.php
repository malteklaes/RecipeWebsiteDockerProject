<?php 

    session_start();

    require_once('MongoDBConnector.php');
    require_once('MongoDBMigrator.php');
    require_once('MongoDBEntity/CommentMongo.php');
    require_once('MongoDBEntity/RecipeMongo.php');
    require_once('MongoDBEntity/ListRecipeMongo.php');
    use MongoDB\Driver\Manager;
    use MongoDB\Driver\BulkWrite;
    use MongoDB\Driver\Query;
    use MongoDB\BSON\ObjectId;

    class MongoDBWriter {
        private $dbnameMongo = "RecipeWebsiteMongoDB";

        function __construct(){
        }

        public function queryUser($username){
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
            $query = new MongoDB\Driver\Query(['username' => $username]);
            $resultQuery = $manager->executeQuery($this->dbnameMongo . '.user_collection', $query);
            $result = "";
            foreach ($resultQuery as $document) {
                $result .= "<br>1.Title: " . $document->username . "<br>";
            }
        }

  
        //* RECIPE ----------------------------------------------------------------------------------------------------------------
        
                public function insertRecipe($recipeObject){
                    //* [1] create manager
                    $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
                    
                    //* [2] initialize the recipeDocs array
                    $recipeDocs = [];
                    
                    //* [3] for every recipe, create a new document and add it to the array
                    $recipeDoc = [
                        'recipeName' => $recipeObject->getRecipeName(),
                        'recipeDescription' => $recipeObject->getRecipeDescription(),
                        'category' => $recipeObject->getCategory(),
                        'rating' => $recipeObject->getRating(),
                        'comments' => $recipeObject->getRawCommentsArray(),
                        'userRated' => array(),
                        'listRecipe' => array(),
                        'creationDate' => $recipeObject->getCreationDate(),
                        'editedDate' => $recipeObject->getEditedDate(),
                    ];
                    $recipeDocs[] = $recipeDoc;
                    
                    
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

                public function getAllCategoriesFromMongoDB(){
                    $categoriesArray = [];
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
                        if ($document->category !== null && !in_array($document->category, $categoriesArray)) {
                            $categoriesArray[] = $document->category;
                        }
                    }
                    
                    //* [5] return the array of comment documents
                    return $categoriesArray;
                    
                }

                //* COMMENT ----------------------------------------------------------------------------------------------------------------
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
                
                //* USER ----------------------------------------------------------------------------------------------------------------

                public function insertUserToMongoDB($recipeObject, $actualUsername){
                    //* create manager
                    $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
                   

                    //* find object by oid: "$actualUsername->_id->__toString()"
                    $filter = ['_id' => new ObjectId( $actualUsername->_id->__toString() )];
                    $update = [
                        '$push' => [
                            'recipes' => [
                                'recipeName' => $recipeObject->getRecipeName(),
                                'recipeDescription' => $recipeObject->getRecipeDescription(),
                                'category' => $recipeObject->getCategory(),
                                'rating' => $recipeObject->getRating(),
                                'comment' => $recipeObject->getComments(),
                                'creationDate' => $recipeObject->getCreationDate(),
                                'editedDate' => $recipeObject->getEditedDate()
                                
                            ]
                        ]
                    ];


                    //* insert all user documents into MongoDB
                    $bulkWrite = new BulkWrite();
                    $bulkWrite->update($filter, $update);

                    //* write all data to the database and the specific collection
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

                public function getActualStatus($username){
                    $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
                    $databaseName = "RecipeWebsiteMongoDB";
                    $collectionName = 'user_collection';
                    
                    
                    $targetUsername = $username;
                    
                    //* [1] Create a query to find the document
                    $query = new MongoDB\Driver\Query(['username' => $targetUsername]);
                    
                    //* [2] Run the query in user_collection targeting the field "userID"
                    $result = $manager->executeQuery("$databaseName.$collectionName", $query);
                    
                    
                    //* [3] Check if a document was found
                    if ($result->isDead()) {
                        echo "No document with userID $targetUsername were found.";
                    } else {
                        //*[4] Read and return document 
                        foreach ($result as $document) {
                            return $document;
                        }   
                    }
                }


                //* WRITE RECIPE TO LISTRECIPE ----------------------------------------------------------------------------------------------------------------

                public function insertRecipeToList($recipe, $listRecipe){
                    $listRecipeId = $listRecipe[1];
                    $listRecipeName = $listRecipe[0];
                    $recipesCommentArray = $listRecipe[8];
                    
                    $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
                    $filter = ['_id' => new MongoDB\BSON\ObjectId($listRecipeId)];
                    $query = new MongoDB\Driver\Query($filter); 
                    $cursor = $manager->executeQuery('RecipeWebsiteMongoDB.listrecipe_collection', $query);
                    
                    
                    if ($cursor->isDead()) {
                        echo 'The listRecipe-document with the specified ObjectId was not found!';
                    } else {
                        $listRecipe = $cursor->toArray()[0];
                        
                        if ($listRecipe->recipes === null) {
                            $listRecipe->recipes = [];
                        }
                        
                        
                        $commentArray = $this->arrayToCommentArray($recipesCommentArray);
                        
                        $newRecipe = [
                            'recipeName' => $recipe[0],
                            'recipeDescription' => $recipe[1],
                            'category' => $recipe[2],
                            'rating' => $recipe[3],
                            'comment' => $commentArray,
                            'creationDate' => $recipe[4],
                            'editedDate' => $recipe[5],
                        ];
                        
                        $listRecipe->recipes[] = $newRecipe;
                        
                        //* update recipes listrecipe
                        $recipeSearchObject = new RecipeMongo($recipe[0], $recipe[1], $recipe[2], $recipe[3], $commentArray, $recipe[4], $recipe[5]);
                        $recipeOID = $this->queryRecipeOID($recipeSearchObject);
                        $this->updateListRecipeInRecipe($recipeOID, $listRecipeName);
                        
                        $filter = ['_id' => new MongoDB\BSON\ObjectId($listRecipeId)];
                        $update = ['$set' => ['recipes' => $listRecipe->recipes]];
                        $bulk = new MongoDB\Driver\BulkWrite();
                        $bulk->update($filter, $update);
                        $manager->executeBulkWrite('RecipeWebsiteMongoDB.listrecipe_collection', $bulk);
                    }
                }

                /**
                 * convert comments bundle as strings into proper comment-document
                 *
                 * @param [type] $commentArray
                 * @return array<comment-document>
                 */
                private function arrayToCommentArray($commentStringArray){
                    $commentArrayMongo = array();
                    foreach($commentStringArray as $comment){
                        $help = $this->getCommentFromComments($comment);
                        $commentArrayMongo[] = [
                            "commentContent" => $help->commentContent,
                            "ownerName" => $help->ownerName,
                            "creationDate" => $help->creationDate,
                            "editedDate" => $help->editedDate
                        ]; 
                    
                    }
                    return $commentArrayMongo;
                }

                
                /**
                 * help function to retrieve the entire comment
                 * @param array<string> $searchComment
                 * @return null|comment-document
                 */
                public function getCommentFromComments($searchComment){
        
                    //* [1] create manager
                    $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
        
                    //* [2] specify the query to retrieve all documents
                    $query = new MongoDB\Driver\Query([]);
        
                    //* [3] execute the query and retrieve the result set
                    $result = $manager->executeQuery($this->dbnameMongo.'.comment_collection', $query);
                    
                    //* [4] search for specific comment
                    foreach ($result as $document) {
                        if($searchComment[0] == $document->commentContent&&
                        $searchComment[1] == $document->ownerName&&
                        $searchComment[2] == $document->editedDate){
                            return $document;
                        }
                    }
                    return null;
        
                }


                

                public function getAllListRecipesFromMongoDBUpdate(){
        
                    //* [1] create manager
                    $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
        
                    //* [2] specify the query to retrieve all documents
                    $query = new MongoDB\Driver\Query([]);
        
                    //* [3] execute the query and retrieve the result set
                    $result = $manager->executeQuery($this->dbnameMongo.'.listrecipe_collection', $query);
        
                    //* [4] iterate over the result set and store the documents in an array
                    $userDocuments = [];
                    foreach ($result as $document) {
                        $userDocuments[] = $document;
                    }
                    //* [5] return the array of user documents
                    return $userDocuments;
        
                }


                 //* INSERT NEW LISTRECIPE ----------------------------------------------------------------------------------------------------------------
        
                 
            /* public function insertListRecipeToMongo2($listRecipeName, $listRecipeDescription,$privateStatus, $selectedListRecipeMongo, $actualMongoDBUser){

                $recipeArrayWithoutComment = $selectedListRecipeMongo[1];
                $recipeArrayComments = $selectedListRecipeMongo[2];
                $recipesCommentArray = $selectedListRecipeMongo[8];
                $commentArray = $this->arrayToCommentMongoClassArray($selectedListRecipeMongo[2]);

                $recipe = new RecipeMongo(
                    $recipeArrayWithoutComment[0],
                    $recipeArrayWithoutComment[1],
                    $recipeArrayWithoutComment[2],
                    $recipeArrayWithoutComment[3],
                    $commentArray,
                    $recipeArrayWithoutComment[4],
                    $recipeArrayWithoutComment[5]
                );

                $recipeOID = $this->queryRecipeOID($recipe);
                $this->updateListRecipeInRecipe($recipeOID, $listRecipeName);

                $recipes[] = $recipe;

                $newListRecipe = new ListRecipeMongo(
                    $listRecipeName,
                    $listRecipeDescription,
                    $privateStatus,
                    $recipes,
                    0,
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s"),
                );

                //* [1] create manager
                $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");


                //* [2] for every recipe, create a new document and add it to the array
                $listRecipeDoc = [
                    'listName' => $newListRecipe->getListName(),
                    'listDescription' => $newListRecipe->getListDescription(),
                    'privateStatus' => $newListRecipe->getPrivateStatus(),
                    'recipes' => $newListRecipe->getRecipesOfListRecipe(),
                    'likes' => $newListRecipe->getLikesAmount(),
                    'creationDate' => $newListRecipe->getCreationDate(),
                    'editedDate' => $newListRecipe->getEditedDate(),
                ];
                $listRecipeDocs[] = $listRecipeDoc;


                //* [3] insert all recipe documents into MongoDB
                $bulkWrite = new MongoDB\Driver\BulkWrite();
                foreach ($listRecipeDocs as $listRecipeDoc) {
                    $bulkWrite->insert($listRecipeDoc);
                }

                //* [4] write all data to the database and the specific collection
                $manager->executeBulkWrite($this->dbnameMongo . '.listrecipe_collection', $bulkWrite);
            } */



            public function insertListRecipeToMongo($listRecipeName, $listRecipeDescription, $privateStatus, $selectedListRecipeMongo, $actualMongoDBUser) {
                $recipeArrayWithoutComment = $selectedListRecipeMongo[1];
                $commentArray = $this->arrayToCommentMongoClassArray($selectedListRecipeMongo[2]);

                $recipe = new RecipeMongo(
                    $recipeArrayWithoutComment[0],
                    $recipeArrayWithoutComment[1],
                    $recipeArrayWithoutComment[2],
                    $recipeArrayWithoutComment[3],
                    $commentArray,
                    $recipeArrayWithoutComment[4],
                    $recipeArrayWithoutComment[5]
                );

                //* update recipes in MongoDB
                $recipeOID = $this->queryRecipeOID($recipe);
                $this->updateListRecipeInRecipe($recipeOID, $listRecipeName);
                
                
       
                $recipes[] = $recipe;

                $newListRecipe = new ListRecipeMongo(
                    $listRecipeName,
                    $listRecipeDescription,
                    $privateStatus,
                    $recipes,
                    0,
                    date("Y-m-d H:i:s"),
                    date("Y-m-d H:i:s"),
                );

                //* update users in MongoDB
                $this->addListRecipeToUser($actualMongoDBUser->_id->__toString(), $newListRecipe);

                //* [1] create manager
                $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");


                //* [3] for every recipe, create a new document and add it to the array
                $listRecipeDoc = [
                    'listName' => $newListRecipe->getListName(),
                    'listDescription' => $newListRecipe->getListDescription(),
                    'privateStatus' => (($newListRecipe->getPrivateStatus() !== null) ? $newListRecipe->getPrivateStatus() : 0),
                    'recipes' => $newListRecipe->getRecipesOfListRecipe(),
                    'likes' => $newListRecipe->getLikesAmount(),
                    'creationDate' => $newListRecipe->getCreationDate(),
                    'editedDate' => $newListRecipe->getEditedDate(),
                ];
                $listRecipeDocs = $listRecipeDoc;

                //* [4] insert all recipe documents into MongoDB
                $bulkWrite = new MongoDB\Driver\BulkWrite();
                $bulkWrite->insert($listRecipeDocs);


                //* [5] write all data to the database and the specific collection
                $manager->executeBulkWrite($this->dbnameMongo . '.listrecipe_collection', $bulkWrite);
            }

           

                /**
                 * convert comments bundle as strings into proper comment-document
                 *
                 * @param [type] $commentArray
                 * @return array<CommentMongo>
                 */
                private function arrayToCommentMongoClassArray($commentStringArray){
                    $commentArrayMongo = array();
                    foreach($commentStringArray as $comment){
                        $help = $this->getCommentFromComments($comment);
                        $commentArrayMongo[] = new CommentMongo($help->commentContent, $help->ownerName, $help->creationDate, $help->editedDate);
                    }
                    return $commentArrayMongo;
                }


                /**
                 * updates recipe with the listname in which this recipe was inserted
                 * @param mixed $recipe
                 * @param mixed $listRecipe
                 * @return void
                 */
                public function updateListRecipeInRecipe($recipeOID, $listRecipeName){
                    $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
                    $filter = ['_id' => new MongoDB\BSON\ObjectId($recipeOID)];
                    $query = new MongoDB\Driver\Query($filter); 
                    $cursor = $manager->executeQuery($this->dbnameMongo . '.recipe_collection', $query);
                    
                    if ($cursor->isDead()) {
                        echo 'The listRecipe-document with the specified ObjectId was not found!';
                    } else {
                        $recipe = $cursor->toArray()[0];
                        
                        if ($recipe->listRecipe  === null) {
                            $recipe->listRecipe = [];
                        }
                        
                        $recipeUpdatedWithListName[] = [
                            'listName' => $listRecipeName
                        ];

                        $recipe->listRecipe[] = $recipeUpdatedWithListName;
                        
                    
                        $filter = ['_id' => new MongoDB\BSON\ObjectId($recipeOID)];
                        $update = ['$push' => ['listRecipe' => $recipeUpdatedWithListName]]; 
                        $bulk = new MongoDB\Driver\BulkWrite();
                        $bulk->update($filter, $update);

                        try {
                            $manager->executeBulkWrite($this->dbnameMongo . '.recipe_collection', $bulk);
                        } catch (MongoDB\Driver\Exception\Exception $e) {
                            echo "Error executing bulk write: " . $e->getMessage() . "<br>";
                        }
                    }
                }

                public function queryRecipeOID($recipe){
                    $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
                    
                    $query = new MongoDB\Driver\Query([
                        'recipeName' => $recipe->getRecipeName(),
                        'creationDate' => $recipe->getCreationDate(),
                        'editedDate' => $recipe->getEditedDate()
                    ]);
                    
                    $resultQuery = $manager->executeQuery($this->dbnameMongo . '.recipe_collection', $query);

                    
                    foreach (($resultQuery) as $document) {
                        return $document->_id->__toString();
                    }
                    return "";
                }


                public function addListRecipeToUser($userOID, $listRecipe){
                    
                    $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
                    $filter = ['_id' => new MongoDB\BSON\ObjectId($userOID)];
                    $query = new MongoDB\Driver\Query($filter); 
                    $cursor = $manager->executeQuery('RecipeWebsiteMongoDB.user_collection', $query);
                    
                    
                    if ($cursor->isDead()) {
                        echo 'The listRecipe-document with the specified ObjectId was not found!';
                    } else {
                        $user = $cursor->toArray()[0];
                        
                        if ($user->listRecipes === null) {
                            $user->listRecipes = [];
                        }

                        $listRecipeDoc = [
                            'listName' => $listRecipe->getListName(),
                            'listDescription' => $listRecipe->getListDescription(),
                            'privateStatus' => $listRecipe->getPrivateStatus(),
                            'recipes' => $listRecipe->getRecipesOfListRecipe(),
                            'likes' => $listRecipe->getLikesAmount(),
                            'creationDate' => $listRecipe->getCreationDate(),
                            'editedDate' => $listRecipe->getEditedDate(),
                        ];
                        $listRecipeDocs = $listRecipeDoc;
                        
                        $filter = ['_id' => new MongoDB\BSON\ObjectId($userOID)];
                        $update = ['$push' => ['listRecipes' => $listRecipeDocs]];
                        $bulk = new MongoDB\Driver\BulkWrite();
                        $bulk->update($filter, $update);
                        $manager->executeBulkWrite('RecipeWebsiteMongoDB.user_collection', $bulk);
                    }
                }








          

    }
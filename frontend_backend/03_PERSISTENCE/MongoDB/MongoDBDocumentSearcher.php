<?php

use MongoDB\Driver\Manager;
    use MongoDB\Driver\BulkWrite;
    use MongoDB\Driver\Query;
    use MongoDB\BSON\ObjectId;


    class MongoDBDocumentSearcher {

        private $dbnameMongo = "RecipeWebsiteMongoDB";



        public function searchRecipeDocument($document, $criteriaRecipe) {
            $resultRecipe = array();
            foreach ($document->recipes as $recipe) {
                if ($this->matchesCriteria($recipe, $criteriaRecipe)) {
                    $resultRecipe[] = $recipe->recipeName;
                    $resultRecipe[] = $recipe->rating;
                    $resultRecipe[] = $recipe->recipeDescription;
                    $resultRecipe[] = $recipe->category;
                    $resultRecipe[] = $recipe->creationDate;
                    $resultRecipe[] = $recipe->editedDate;
                }
            }
            return $resultRecipe;   
        }
        
        /**
         * Help function to check if a recipe meets the criteria
         * @param mixed $recipe
         * @param mixed $criteria
         * @return bool
         */
        private function matchesCriteria($recipe, $criteria) {
            foreach ($criteria as $key => $value) {
                if (!isset($recipe->$key) || $recipe->$key != $value) {
                    return false;
                }
            }
            return true;
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
                if($document->privateStatus == 0){
                    $listRecipeInfoBundle = array();
                if (
                    $document->listName !== null &&
                    $document->_id->__toString() !== null &&
                    $document->listDescription !== null &&
                    $document->privateStatus !== null &&
                    $document->likes !== null &&
                    $document->creationDate !== null &&
                    $document->editedDate !== null
                ) {
                    array_push($listRecipeInfoBundle, $document->listName);
                    array_push($listRecipeInfoBundle, $document->_id->__toString());
                    array_push($listRecipeInfoBundle, $document->listDescription);
                    array_push($listRecipeInfoBundle, $document->privateStatus);
                    array_push($listRecipeInfoBundle, $document->likes);
                    array_push($listRecipeInfoBundle, $document->creationDate);
                    array_push($listRecipeInfoBundle, $document->editedDate);
                    $recipeDocuments[] = $listRecipeInfoBundle;
                }
                }
            }
            //* [5] return the array of comment documents
            $newArray[] = "new ListRecipe";
            array_push($recipeDocuments, $newArray);
            return $recipeDocuments;
            
        }
        
    }



?>
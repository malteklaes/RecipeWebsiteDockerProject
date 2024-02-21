<?php

    class Report1MongoDB {
        private $dbnameMongo = "RecipeWebsiteMongoDB";

        function __construct(){
        }

        public function calulateTopRecipe_Report1($actualUser, $category){
    
            $resultArray = $this->retrieveAllRecipes_Report1($actualUser, $category);

            usort($resultArray, function($a, $b) {
                $firstComparison = $b[0] <=> $a[0];
                
                if ($firstComparison === 0) {
                    return $b[1] <=> $a[1];
                }
            
                return $firstComparison;
            });
            
            return $resultArray;
        }


        private function retrieveAllRecipes_Report1($actualUser, $category) {
            $resultObjects = array();
            $allRecipes = $this->retrieveAllRecipesFromMongoDB();
            if($category != null) {
                $allRecipesByCategory = array();
                foreach($allRecipes as $recipe) {
                    if($recipe->category == $category) {
                        array_push($allRecipesByCategory, $recipe);
                    }
                }
                $allRecipes = $allRecipesByCategory;
            }
            foreach($allRecipes as $recipe){
                $newRating = 0;
                $recipeToResult = array();
                $rating = $recipe->rating;
                if($rating == 0){
                    $ratesNumber = 0;
                }
                else {
                    $ratesNumber = count($recipe->userRated);
                }
                $recipeName = $recipe->recipeName;
                
                $newRating = ($rating * $ratesNumber + 5 + 1) / (5 * $ratesNumber + 5 + 5);
                $listRecipeNumber = 0;
                $listRecipeArray = $recipe->listRecipe;
                if (!empty($listRecipeArray)) {
                    $listRecipeNumber = count($listRecipeArray);
                }
                array_push($recipeToResult, round($newRating, 2));
                array_push($recipeToResult, $listRecipeNumber);
                array_push($recipeToResult, $this->defineRecipeName($actualUser, $recipe));
                $resultObjects[] = $recipeToResult;
            }
            return $resultObjects;
        }

        private function defineRecipeName($actualUser, $comparedRecipe){
            $definedRecipeName = $comparedRecipe->recipeName;
            foreach($actualUser->recipes as $recipe){
                if($recipe->recipeName == $comparedRecipe->recipeName &&
                $recipe->recipeDescription == $comparedRecipe->recipeDescription &&
                $recipe->category == $comparedRecipe->category &&
                $recipe->rating == $comparedRecipe->rating &&
                $recipe->creationDate == $comparedRecipe->creationDate &&
                $recipe->editedDate == $comparedRecipe->editedDate ){
                    $definedRecipeName .=  " <font color=#f56342><b> (myRecipe) </b></font>";
                }
            }
            return $definedRecipeName;
        }


        public function retrieveAllCategories() {
            $result = array();
            $allRecipes = $this->retrieveAllRecipesFromMongoDB();
            foreach($allRecipes as $recipe) {
                $category = $recipe->category;
                array_push($result, $category);
            }
            return array_unique($result);
        }

        private function retrieveAllRecipesFromMongoDB() {
            $manager = new MongoDB\Driver\Manager("mongodb://user:pwd@recipeWebsite_MongoDB:27017");
            
            //* [2] specify the query to retrieve all documents
            $query = new MongoDB\Driver\Query([]);
            
            //* [3] execute the query and retrieve the result set
            $result = $manager->executeQuery($this->dbnameMongo.'.recipe_collection', $query);
            $recipes = [];
            $recipeDocuments = [];
            foreach ($result as $document) {
                $recipeDocuments[] = $document;
            }
            //* [5] return the array of comment documents
            return $recipeDocuments;
                
        }

    }

?>
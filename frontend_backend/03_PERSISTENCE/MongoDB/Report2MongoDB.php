<?php

 


    class Report2MongoDB{
        private $dbnameMongo = "RecipeWebsiteMongoDB";

        function __construct(){

        }


        public function calulateTopListRecipe_Report2($actualUser, $selectedDate){
            //* [A] retrieve all calculations for all listRecipes
            $resultArray = $this->calculateSuccessNumberN($actualUser, $selectedDate);

            //* [B] sort them descending order
            usort($resultArray, function($a, $b) {
                return $b[0] - $a[0];
            });
            
            return $resultArray;
        }

        /**
         * caclulates successNumberN per listRecipe
         * @param mixed $selectedDate
         * @return array<string, int> (array<successNumberN, listName>)
         */
        private function calculateSuccessNumberN($actualUser, $selectedDate){
            $resultObjects = array();
            $allListRecipes = $this->getAllListRecipesFromMongoDB();

            foreach($allListRecipes as $listRecipe){
                if($selectedDate >= $listRecipe->creationDate){
                    //* one (valid) ListRecipe
                    $successNumberN = 0;
                    $listRecipeBundle = array(); //? (successNumberN, listName)

                    $likesPerListRecipe = $listRecipe->likes;
                    //* every recipe in a listRecipe
                    $recipeCommentRating = 0;
                    foreach($listRecipe->recipes as $recipe){
                        $recipeCommentRating += $this->calculateRecipeCommentRating($recipe);
                    }
                    
                    $recipeCommentRating = (($recipeCommentRating * 1) / 4);
                    $likesPerListRecipe = (($likesPerListRecipe * 3));
                    $successNumberN = $likesPerListRecipe + $recipeCommentRating;
                    
                    array_push($listRecipeBundle, round($successNumberN,2));
                    array_push($listRecipeBundle, $this->defineListName($actualUser, $listRecipe));
                    $resultObjects[] = $listRecipeBundle;
                }
            }
            return $resultObjects;
        }

        /**
         * calculates for each recipe its recipeCommentRating:
         * (1) amount of comments
         * (2) each comment text-length
         * (3) rating of a recipe (recipeRating)
         * (4) recipeCommentRating = [(1) * (2)] + (3)
         * @param mixed $recipe
         * @return int
         */
        private function calculateRecipeCommentRating($recipe){
            $recipeCommentRating = 0;
            $recipesRating = $recipe->rating;
            $commentCounter = 0;
            foreach($recipe->comment as $comment){
                $commentCounter++;
            }
            $finalRecipeCommentsNumber = 0;
            foreach($recipe->comment as $oneComment){
                $commentStringLength = strlen($oneComment->commentContent);
                $finalRecipeCommentsNumber += ($commentStringLength * $commentCounter);
            }
            
            $recipeCommentRating = $finalRecipeCommentsNumber + $recipesRating;
            return $recipeCommentRating;
        }

        private function defineListName($actualUser, $comparedListRecipe){
            $definedListRecipeName = $comparedListRecipe->listName;
            foreach($actualUser->listRecipes as $listRecipe){
                if($listRecipe->listName == $comparedListRecipe->listName &&
                $listRecipe->listDescription == $comparedListRecipe->listDescription &&
                $listRecipe->privateStatus == $comparedListRecipe->privateStatus &&
                $listRecipe->likes == $comparedListRecipe->likes &&
                $listRecipe->creationDate == $comparedListRecipe->creationDate &&
                $listRecipe->editedDate == $comparedListRecipe->editedDate ){
                    $definedListRecipeName .=  " <font color=#f56342><b> (myList) </b></font>";
                }
            }
            return $definedListRecipeName;
        }



        private function getAllListRecipesFromMongoDB(){
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
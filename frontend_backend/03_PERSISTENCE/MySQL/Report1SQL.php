<?php
    session_start();

    class Report1SQL {

        function __construct() {
        }

        public function calculateTopRecipes_Report1($myUserID, $category) {
            $resultArray = $this->retrieveAllRecipes_Report1($myUserID, $category);

            usort($resultArray, function($a, $b) {
                // Compare the first field of the inner array
                $firstComparison = $b[0] <=> $a[0];
            
                // If the first fields are equal, compare the second field
                if ($firstComparison === 0) {
                    return $b[1] <=> $a[1];
                }
            
                return $firstComparison;
            });
            

            return $resultArray;
        }

        private function retrieveAllRecipes_Report1($myUserID, $category) {
            $result = array();
            if ($category == null) {
                $sql = "SELECT * FROM recipe";
                $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
                $stmt = $dataBaseConnection->prepare($sql);
            }
            else {
                $sql = "SELECT * FROM recipe WHERE category = :category";
                $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
                $stmt = $dataBaseConnection->prepare($sql);
                $stmt->bindParam(':category', $category);
            }
            $stmt->execute();


            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $newRating = 0;
                $recipeToResult = array();
                $recipe = $userData['postID'];
                $rating = $userData['rating'];
                $ratesNumber = $this->retrieveNumUsersRatedRecipe($recipe);
                $newRating = ($rating * $ratesNumber + 5 + 1) / (5 * $ratesNumber + 5 + 5);
                $listRecipeNumber = $this->retrieveNumListRecipesWithRecipe($recipe);

                array_push($recipeToResult, round($newRating, 2));
                array_push($recipeToResult, $listRecipeNumber);

                if($userData['ownerUserID'] === $myUserID){
                    array_push($recipeToResult, $userData['recipeName'] . " <font color=#f56342><b> (myRecipe) </b></font>");
                }
                else {
                    array_push($recipeToResult, $userData['recipeName']);
                }

                $result[] = $recipeToResult;

            }

            return $result;

        }

        private function retrieveNumListRecipesWithRecipe($recipeID) {
            $sql = "SELECT count(*) as cnt FROM listContainsOfRecipe WHERE recipeID = :recipeID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':recipeID', $recipeID);
            $stmt->execute();

            return ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) ? $userData['cnt'] : 0;
        }

        private function retrieveNumUsersRatedRecipe($postID) {
            $sql = "SELECT count(*) as cnt FROM userRatesRecipe WHERE postID = :postID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();

            return ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) ? $userData['cnt'] : 0;
        }

        public function retrieveAllCategories() {
            $result = array();
            $sql = "SELECT Distinct(category) as cat FROM recipe";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->execute();

            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cat = $userData['cat'];
                array_push($result, $cat);
            }

            return $result;
        }


    }

?>
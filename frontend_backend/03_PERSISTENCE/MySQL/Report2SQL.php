<?php
    session_start();


    class Report2SQL{

        function __construct() {
        }

        public function calulateTopListRecipe_Report2($myUserID, $selectedDate){
            //* [A] retrieve all calculations for all listRecipes
            $resultArray = $this->retrieveAllLists_Report2($myUserID, $selectedDate );
            //* [B] sort them descending order
            usort($resultArray, function($a, $b) {
                return $b[0] - $a[0];
            });

            return $resultArray;
        }


        /**
         * Summary of retrieveAllLists_Report2
         * @param mixed $myUserID
         * @param mixed $selectedDate
         * @return array<string, int> (array<listName, successNumberN>)
         */
        public function retrieveAllLists_Report2($myUserID, $selectedDate ){
            $resultObjects = array();
            $dateFrom = $selectedDate;
            $sql = "SELECT * FROM listRecipe WHERE creationDate <= '$dateFrom'";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->execute();

            //* hier AusgangsPunkt, weil man durch alle Listen geht
            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $successNumberN = 0;
                $listRecipeBundle = array(); //? (listName, successNumberN)
                //* [a] collect and calculate "comments" and "recipes rating"
                $recipeCommentRating = $this->retrieveAndProcessRecipesByListID($userData['listID']);
                //* [b] collect and calculate all "likes" for a listRecipe
                $likesPerListRecipe =  $this->retrieveAndProcessAllLikesForOneListRecipe($userData['listID']);
                $recipeCommentRating = (($recipeCommentRating * 1) / 4);
                $likesPerListRecipe = (($likesPerListRecipe * 3));
                
                $successNumberN = $likesPerListRecipe + $recipeCommentRating;

                array_push($listRecipeBundle, $successNumberN);
                if($userData['ownerID'] === $myUserID){
                    array_push($listRecipeBundle, $userData['listName'] . " <font color=#f56342><b> (myList) </b></font>");
                }
                else {
                    array_push($listRecipeBundle, $userData['listName']);
                }
                $resultObjects[] = $listRecipeBundle;
            }
            return $resultObjects;
        }



        /**
         * retrieve all recipes for one listRecipe (listID)
         * is the connector between listRecipe and recipe
         * 1. fetch recipes RATING (+ go on with recipe's ID)
         * 2. fetch all comments for recipe's ID (commentAmount and commentLength)
         * 
         * Calculation:
         * (a) retrieve each recipe's rating
         * (b) retrieve each recipe's commentCaluclation
         * (c) (a) + (b)
         * (d) recipeCommentRating = sum((c)) * weightingOfRecipeCommentRating
         * 
         * @param mixed $listID (not null)
         * @return int recipeCommentRating ()
         */
        public function retrieveAndProcessRecipesByListID($listID){
            //* [A] setup all variables
            $recipeCommentRating = 0;

            //* collect all recipes which belongs to given list (listID)
            $sql = "SELECT * FROM listContainsOfRecipe WHERE listID = :listID";
            $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':listID', $listID);
            $stmt->execute();
            
            //* [C] process for all recipes
            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //* go for all comments belong to that recipe => calculate "finalRecipeCommentsNumber"
                $specificRecipe = $this->retrieveRatingForGivenRecipe($userData['recipeID']);
                $finalRecipeCommentsNumber = $this->retrieveAndProcessAllCommentNumberForARecipe($userData['recipeID']);
                $recipeCommentRating += intval($finalRecipeCommentsNumber) + intval($specificRecipe);
            }
            
            //* [D] put it the weithing
            return $recipeCommentRating;
            
        }
        
        /**
         * search for a recipe by recipeID and retrieves its rating
         * @param mixed $recipeID (not null)
         * @return mixed (rating for a recipe)
         */
        public function retrieveRatingForGivenRecipe($recipeID)  {
                $sql = "SELECT * FROM recipe WHERE postID = :recipeID";
                $dataBaseConnection= new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
                $stmt = $dataBaseConnection->prepare($sql);
                $stmt->bindParam(':recipeID', $recipeID);
                $stmt->execute();

                return ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) ? $userData['rating'] : 0;
        }




        /**
         * search and retrieves all comments for a given recipe
         * Calculation
         * (a) amount of comments (in this/one recipe)
         * (b) each length of a comment
         * (c) finalRecipeCommentsNumber = sum((a)*(b)) [in REPORT stated as: "#comments ∗ commentSize"]
         * @return int finalRecipeCommentsNumber (see calulation as in (c))
         */
        public function retrieveAndProcessAllCommentNumberForARecipe($postID) {
            //* [A] collect all necessary data
            $finalRecipeCommentsNumber = 0;
            $sql = "SELECT * FROM comment WHERE recipeIDReference = :postID";
            $dataBaseConnection = new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
            $stmt = $dataBaseConnection->prepare($sql);
            $stmt->bindParam(':postID', $postID);
            $stmt->execute();

            
            //* [B] AMOUNT OF COMMENTS in ONE recipe
            $commentCounter = 0;
            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $commentCounter++;
            }
            //* [C] length of each comment multiplied by the AMOUNT OF COMMENTS
            $stmt->execute();
            while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $commentStringLength = strlen($userData['commentContent']);
                $finalRecipeCommentsNumber += ($commentStringLength * $commentCounter);
            }
            return $finalRecipeCommentsNumber;
        }
        

        /**
         * calculates all likes a listRecipe recieves in total
         *
         * @param [type] $listID
         * @return int likesPerListRecipe
         */
        public function retrieveAndProcessAllLikesForOneListRecipe($listID) {
                //* [A] collect all necessary data
                $likesPerListRecipe = 0;
                $sql = "SELECT * FROM userLikesListRecipe WHERE listID = :listID";
                $dataBaseConnection = new PDO("mysql:host=dbSQL;dbname=RecipeWebsiteDB", "user", "pwd");
                $stmt = $dataBaseConnection->prepare($sql);
                $stmt->bindParam(':listID', $listID);
                $stmt->execute();
    
                
                //* [B] AMOUNT OF COMMENTS in ONE recipe
                while ($userData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $likesPerListRecipe++;
                }

                return $likesPerListRecipe;
            }

    }

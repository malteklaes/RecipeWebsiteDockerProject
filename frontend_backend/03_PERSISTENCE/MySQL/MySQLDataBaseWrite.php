<?php

session_start();

require_once('MySQLDataBaseConnector.php');
require_once('MySQLDataBaseInserter.php');
require_once('MySQLEntity/MySQLEntityHelper/PostIDSQL.php');
require_once('MySQLEntity/RecipeSQL.php');
require_once('MySQLEntity/ListRecipeSQL.php');
require_once('MySQLEntity/CommentSQL.php');



class MySQLDataBaseWrite {

    private $dbConnector;

    function __construct() {
        $this->dbConnector = new MySQLDataBaseConnector();

    }

    /**
     * main use case "Add a new Recipe": adds a new recipe with comment
     * @param mixed $listID
     * @param mixed $ownerUserID
     * @param mixed $recipeName
     * @param mixed $recipeDescription
     * @param mixed $recipeCategory
     * @param mixed $recipeRating
     * @param mixed $recipeComment
     * @return void
     */
    public function writeRecipeDataWithListID($listID, $ownerUserID, $recipeName, $recipeDescription, $recipeCategory, $recipeRating, $recipeComment, $creationDate, $editedDate) {
        $newRecipePostID = (new PostIDSQL())->getPostID();
        $newCommentPostID = (new PostIDSQL())->getPostID();
        $newRecipe = new RecipeSQL($newRecipePostID, $recipeName, $recipeDescription, $recipeCategory, $recipeRating, $ownerUserID);
        $newRecipe->setCreationDate($creationDate);
        $newRecipe->setEditedDate($editedDate);
        $newComment = new CommentSQL($newCommentPostID, $recipeComment, $ownerUserID, $newRecipePostID);
        $inserter = new MySQLDataBaseInserter();
        $inserter->insertRecipeExample((new MySQLDataBaseConnector())->getConnection(), $newRecipe);
        $inserter->insertCommentExample((new MySQLDataBaseConnector())->getConnection(), $newComment);
    }

    /**
     * adds a recipe to a ListRecipe
     * @param mixed $listID
     * @param mixed $recipeID
     * @return void
     */
    public function writeNewRecipeEntryToList($listID, $recipeID){
        $inserter = new MySQLDataBaseInserter();
        $inserter->insertListContainsOfRecipeExample((new MySQLDataBaseConnector())->getConnection(), $listID, $recipeID);
    }

    /**
     * main use case "Add recipe to ListRecipe": creates and adds a new ListRecipe with given owner but no extra recipes and likeAmount == 0
     * @param mixed $listID
     * @param mixed $ownerID
     * @param mixed $listName
     * @param mixed $likeAmount (will == 0)
     * @param mixed $listDescription
     * @param mixed $privateStatus
     * @return void
     */
    public function writeNewListRecipe($listID, $ownerID, $listName, $likeAmount, $listDescription, $privateStatus, $creationDate, $editedDate){
        $newListRecipe = new ListRecipeSQL($listID, $ownerID, $listName, $likeAmount, $listDescription, $privateStatus);
        $newListRecipe->setCreationDate($creationDate);
        $newListRecipe->setEditedDate($editedDate);
        $inserter = new MySQLDataBaseInserter();
        $inserter->insertListRecipeExample((new MySQLDataBaseConnector())->getConnection(), $newListRecipe);
    }

    


    
}

?>
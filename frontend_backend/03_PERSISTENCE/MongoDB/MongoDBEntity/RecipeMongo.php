<?php
require_once('PostMongo.php');

class RecipeMongo extends PostMongo {
    private $recipeName;
    private $recipeDescription;
    private $category;
    private $rating;
    private $comments;
    private $userRated;
    private $listRecipes;
    
    
    /**
     * Summary of __construct
     * @param mixed $recipeName
     * @param mixed $recipeDescription
     * @param mixed $category
     * @param mixed $rating
     * @param mixed $comments
     * @param mixed $creationDate
     * @param mixed $editedDate
     */
    public function __construct($recipeName, $recipeDescription, $category, $rating, $comments, $creationDate, $editedDate) {
        parent::__construct($creationDate, $editedDate);
        $this->recipeName = $recipeName;
        $this->recipeDescription = $recipeDescription;
        $this->category = $category;
        $this->rating = $rating;
        $this->comments = $comments; 
        $this->userRated = array(); //? of type: array<string, int> (array<username, rating>) 
        $this->listRecipes = array(); //? of type: array<string> (array<listname>) 
    }
    
    public function getRecipeName() {
        return $this->recipeName;
    }
    
    public function getRecipeDescription() {
        return $this->recipeDescription;
    }
     
    public function getCategory() {
        return $this->category;
    }
    
    public function getRating() {
        return $this->rating;
    }

    public function addComment(CommentMongo $comment){
        $this->comments[] = $comment;
    }

    public function getComments() {
        foreach($this->comments as $comment){
            $comments[] = [
                "commentContent" => $comment->getCommentContent(),
                "ownerName" => $comment->getOwnerName(),
                "creationDate" => $comment->getCreationDate(),
                "editedDate" => $comment->getEditedDate()
            ]; 
        }
    	return $comments;
    }

    public function getRawCommentsArray(){
        return $this->comments;
    }

    /**
     * Summary of addUserRated
     * @param array<string, int>  $userWithRating
     * @return void
     */
    public function addUserRated($userWithRating){
        $this->userRated = $userWithRating;
    }
    
    public function getUserRated() {
        foreach($this->userRated as $userRatedObject){
            $userRatedSummarize[] = [
                "username" => $userRatedObject[0],
                "rating" => $userRatedObject[1]
            ]; 
        }
    	return $userRatedSummarize;
    }

    /**
     * Summary of addListRecipes
     * @param array(string) $listRecipe
     * @return void
     */
    public function addListRecipes($listRecipe){
        $this->listRecipes = $listRecipe;
    }
    
    public function getListRecipes() {
        foreach($this->listRecipes as $listRecipe){
            $userRatedSummarize[] = [
                "listName" => $listRecipe,
            ]; 
        }
    	return $userRatedSummarize;
    }


    public function create(){
        parent::$creationDate = date("d-m-Y");
    }

    public function edit($newEditDate){
        parent::$editedDate = $newEditDate;
    }

    public function __toString() {
        return "<br>" . "<b>" . "RECIPE" . "</b>"  . "<br>".
            "Recipe Name: " . $this->recipeName . "<br>" .
            "Recipe Description: " . $this->recipeDescription . "<br>" .
            "Category: " . $this->category . "<br>" . 
            "Rating: " . $this->rating . "<br>" .
            "Comments: [ " . implode($this->comments) . "<br>]" . "<br>" .
            "UserRated: [ " . implode($this->userRated) . "<br>]" . "<br>" .
            "ListRecipes: [ " . implode($this->listRecipes) . "<br>]" . "<br>" .
            parent::toString();
    }

    
}

?>
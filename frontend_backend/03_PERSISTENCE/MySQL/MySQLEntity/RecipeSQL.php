<?php
require_once('PostSQL.php');

class RecipeSQL extends PostSQL {
    private $recipeName;
    private $recipeDescription;
    private $category;
    private $rating;
    
    
    /**
     * Summary of __construct
     * @param string $postID
     * @param string $recipeName
     * @param string $recipeDescription
     * @param string $category
     * @param int $rating (between 1-5 (stars))
     */
    public function __construct($postID, $recipeName, $recipeDescription, $category, $rating, $ownerUserID) {
        parent::__construct($postID, date("d-m-Y H:i:s"), date("d-m-Y H:i:s"), $ownerUserID);
        $this->recipeName = $recipeName;
        $this->recipeDescription = $recipeDescription;
        $this->category = $category;
        $this->rating = $rating;
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

    /**
    * @param $rating
    */
    public function setRating($rating) {
    	$this->rating = $rating;
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
            parent::toString();
    }

    
}

?>
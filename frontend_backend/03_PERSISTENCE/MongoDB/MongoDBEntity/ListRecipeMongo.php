<?php

class ListRecipeMongo {
    private $listName;
    private $likesAmount;
    private $listDescription;
    private $privateStatus;
    private $creationDate;
    private $editedDate;
    private $recipes;
    private $userLiked;
    

    
    /**
     * Summary of __construct
     * @param string $listName
     * @param string $listDescription
     * @param boolean $privateStatus
     * @param array<RecipeMongo> $recipes
     * @param int $likesAmount
     * @param mixed $creationDate
     * @param mixed $editedDate
     */
    public function __construct($listName, $listDescription, $privateStatus, $recipes, $likesAmount, $creationDate, $editedDate) {
        $this->listName = $listName;
        $this->listDescription = $listDescription;
        $this->privateStatus = $privateStatus;
        $this->creationDate = $creationDate;
        $this->editedDate = $editedDate;
        $this->recipes = $recipes;
        $this->userLiked = array();
        $this->likesAmount = $likesAmount;
    }
    
    
    public function getListName() {
        return $this->listName;
    }
    
    
    public function getLikesAmount() {
        return $this->likesAmount;
    }
    
    
    public function getListDescription() {
        return $this->listDescription;
    }
    
    
    public function getPrivateStatus() {
        return $this->privateStatus;
    }
    
    
    public function getCreationDate() {
        return $this->creationDate;
    }
    
 
    public function setEditedDate($editedDate) {
    	$this->editedDate = $editedDate;
    }


    public function getEditedDate() {
        return $this->editedDate;
    }


    public function setRecipes(RecipeMongo $recipe) {
        $this->recipes[] = $recipe;
    }
    

    public function getRecipesOfListRecipe() {
        foreach($this->recipes as $recipe){
            $recipes[] = [
                "recipeName" => $recipe->getRecipeName(),
                "recipeDescription" => $recipe->getRecipeDescription(),
                "category" => $recipe->getCategory(),
                "rating" => $recipe->getRating(), 
                "comment" => $recipe->getComments(),
                "creationDate" => $recipe->getCreationDate(),
                "editedDate" => $recipe->getEditedDate()
            ]; 
        }
    	return $recipes;
    }

    public function setUserLiked(UserMongo $user) {
        $this->userLiked[] = $user;
    }

    public function getUserLiked() {
        return $this->userLiked;
    }
   
    
   
    public function __toString() {
        return "<br>" . "<b>" . "LISTRECIPE" . "</b>" . "<br>" .
            "List Name: " . $this->listName . "<br>"
            . "Likes Amount: " . $this->likesAmount . "<br>"
            . "List Description: " . $this->listDescription . "<br>"
            . "Private Status: " . ($this->privateStatus ? "Private" : "Public") . "<br>"
            . "Creation Date: " . $this->creationDate . "<br>"
            . "Edited Date: " . $this->editedDate . "<br>"
            . "Recipes: [ " . implode(", ", $this->recipes) . "<br>]" . "<br>"
            . "Likes from Users: [ " . implode(", ", $this->userLiked) . "<br>]" . "<br>";
    }

    

    
}

?>

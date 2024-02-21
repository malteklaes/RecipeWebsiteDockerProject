<?php

    
    require_once(__DIR__.'/../ListRecipeSQL.php');
    require_once(__DIR__.'/../RecipeSQL.php');

class ListContainsOfRecipeSQL {
    private $listRecipeID;
    private $recipeID;

    public function __construct(String $listRecipeID, String $recipeID) {
        $this->listRecipeID = $listRecipeID;
        $this->recipeID = $recipeID;
    }


    public function getRecipeCommentRating_REPORT() {
        return 4;
    }


    public function getlistRecipeID() {
        return $this->listRecipeID;
    }

    public function getRecipeID() {
        return $this->recipeID;
    }

    public function __toString() {
        return "List ID: " . $this->listRecipeID . " contains " . "Recipe ID: " . $this->recipeID;
    }
}

?>

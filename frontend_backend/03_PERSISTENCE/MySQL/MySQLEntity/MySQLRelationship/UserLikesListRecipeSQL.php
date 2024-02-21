<?php

require_once(__DIR__.'/../UserSQL.php');
require_once(__DIR__.'/../ListRecipeSQL.php');

class UserLikesListRecipe {
    private $userID;
    private $listRecipeID;

    public function __construct(String $userID, String $listRecipeID) {
        $this->userID = $userID;
        $this->listRecipeID = $listRecipeID;
    }

    public function getLikesAmount_REPORT() {
        return 10;
    }

    public function getUserID() {
        return $this->userID;
    }

    public function getListRecipeID() {
        return $this->listRecipeID;
    }

    public function __toString() {
        return "User ID: " . $this->userID . " likes " . "List ID: " . $this->listRecipeID;
    }
}

?>

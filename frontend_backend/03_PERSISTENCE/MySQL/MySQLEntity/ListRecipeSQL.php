<?php

class ListRecipeSQL {
    private $listID;
    protected $ownerUserID;
    private $listName;
    private $likesAmount;
    private $listDescription;
    private $privateStatus;
    private $creationDate;
    private $editedDate;
    
    
    
    /**
     * 
     * @param string $listID
     * @param string $listName
     * @param int $likesAmount
     * @param string $listDescription
     * @param boolean $privateStatus
     */
    public function __construct($listID, $ownerUserID, $listName, $likesAmount, $listDescription, $privateStatus) {
        $this->listID = $listID;
        $this->ownerUserID = $ownerUserID;
        $this->listName = $listName;
        $this->likesAmount = $likesAmount;
        $this->listDescription = $listDescription;
        $this->privateStatus = $privateStatus;
        $randomTimestamp = mt_rand(strtotime('2010-01-01'), time());
        $secondsOf24Hours = 86400;
        $secondsToAdd = mt_rand(1, $secondsOf24Hours);
        $laterTimestamp = $randomTimestamp + $secondsToAdd;
        $this->creationDate = date('d-m-Y H:i:s', $randomTimestamp);
        $this->editedDate = date('d-m-Y H:i:s', $laterTimestamp);
    }
    
    
    public function getListID() {
        return $this->listID;
    }

    public function getOwnerUserID() {
    	return $this->ownerUserID;
    }

    /**
     * adds a an owner if there hasn't been one before
    * @param $ownerUserID
    */
    public function addOwnerUserID($ownerUserID) {
        if ($this->ownerUserID == null) {
            $this->ownerUserID = $ownerUserID;
        }
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
    
    
    public function getEditedDate() {
        return $this->editedDate;
    }

    /**
    * @param $creationDate
    */
    public function setCreationDate($creationDate) {
    	$this->creationDate = $creationDate;
    }

    
    public function setEditedDate($editedDate) {
    	$this->editedDate = $editedDate;
    }
    
    
    public function __toString() {
        return "<br>" . "<b>" . "LISTRECIPE" . "</b>" . "<br>" .
            "List ID: " . $this->listID . "<br>"
            . "List Owner: " . $this->ownerUserID . "<br>"
            . "List Name: " . $this->listName . "<br>"
            . "Likes Amount: " . $this->likesAmount . "<br>"
            . "List Description: " . $this->listDescription . "<br>"
            . "Private Status: " . ($this->privateStatus ? "Private" : "Public") . "<br>"
            . "Creation Date: " . $this->creationDate . "<br>"
            . "Edited Date: " . $this->editedDate . "<br>";
    }


    
}

?>

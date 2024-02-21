<?php

abstract class PostSQL {
    protected $postID;
    protected $creationDate;
    protected $editedDate;
    protected $ownerUserID;
    
    
    /**
     * 
     * @param string $postID
     * @param mixed $creationDate
     * @param mixed $editedDate
     * @param string $ownerUserID
     */
    public function __construct($postID, $creationDate, $editedDate, $ownerUserID) {
        $this->postID = $postID;
        //* $creationDate, $editedDate
        $randomTimestamp = mt_rand(strtotime('2010-01-01'), time());
        $secondsOf24Hours = 86400;
        $secondsToAdd = mt_rand(1, $secondsOf24Hours);
        $laterTimestamp = $randomTimestamp + $secondsToAdd;
        $this->creationDate = date('d-m-Y H:i:s', $randomTimestamp);
        $this->editedDate = date('d-m-Y H:i:s', $laterTimestamp);
        $this->ownerUserID = $ownerUserID;
    }
    
    
    public function getPostID() {
        return $this->postID;
    }
    
    
    public function getCreationDate() {
        return $this->creationDate;
    }
    
    
    public function getEditedDate() {
        return $this->editedDate;
    }

    public function getOwnerUserID() {
    	return $this->ownerUserID;
    }

    /**
     * @param $creationDate
     */
    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    /**
     * @param $editedDate
     */
    public function setEditedDate($editedDate) {
        $this->editedDate = $editedDate;
    }

    /**
     * adds a an owner if there hasn't been one before
    * @param $ownerUserID (not null)
    */
    public function addOwnerUserID($ownerUserID) {
        if($this->ownerUserID == null){
            $this->ownerUserID = $ownerUserID;
        }
    }
    
    
    public function toString() {
        return "<br>" ."<b>" . "POST" . "</b>" . "<br>" .
             "Post ID: " . $this->postID . "<br>"
            . "Creation Date: " . $this->creationDate . "<br>"
            . "Edited Date: " . $this->editedDate . "<br>"
            . "Owner: " . $this->ownerUserID . "<br>";
    }

    
}

?>

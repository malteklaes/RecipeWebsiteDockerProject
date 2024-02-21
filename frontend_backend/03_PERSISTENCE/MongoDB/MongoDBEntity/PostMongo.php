<?php

abstract class PostMongo {
    protected $creationDate;
    protected $editedDate;
    

    public function __construct($creationDate, $editedDate) {
        $this->creationDate = $creationDate;
        $this->editedDate = $editedDate;
    }
    
    public function getCreationDate() {
        return $this->creationDate;
    }
    
    public function getEditedDate() {
        return $this->editedDate;
    }

    public function setEditedDate($editedDate) {
        $this->editedDate = $editedDate;
    }
    
    abstract public function create();
    
    abstract public function edit($newEditDate);
    
    public function toString() {
        return "<br>" ."<b>" . "POST" . "</b>" . "<br>" .
            "Creation Date: " . $this->creationDate . "<br>"
            . "Edited Date: " . $this->editedDate . "<br>";
    }

    

    
}

?>

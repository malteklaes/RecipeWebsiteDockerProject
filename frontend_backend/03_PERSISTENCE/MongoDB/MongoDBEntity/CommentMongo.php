<?php
    require_once('PostMongo.php');

    class CommentMongo extends PostMongo {
        private $commentContent;
        private $commentSize;
        private $ownerName;
        
        /**
         * 
         * @param string $commentContent
         * @param int $commentSize
         */
        public function __construct($commentContent, $ownerName, $creationDate, $editedDate) {
            parent::__construct($creationDate, $editedDate);
            $this->commentContent = $commentContent;
            $this->commentSize = strlen($this->commentContent);
            $this->ownerName = $ownerName;
        }
        

        public function getCommentContent() {
            return $this->commentContent;
        }

        public function getCommentSize() {
            return $this->commentSize;
        }

        public function getOwnerName() {
        	return $this->ownerName;
        }
        
        public function countCommentLength() {
            return strlen($this->commentContent);
        }

        public function create(){
            parent::$creationDate = date("d-m-Y H:i:s");
        }

        public function edit($newEditDate){
            parent::$editedDate = $newEditDate;
        }
        
        public function __toString() {
        return "<br>" . "<b>" . "COMMENT" . "</b>" . "<br>" .
            "Comment Content: " . $this->commentContent . "<br>" .
            "Comment Size: " . $this->commentSize . "<br>" .
            parent::toString();
        }

        

        

        
    }

?>
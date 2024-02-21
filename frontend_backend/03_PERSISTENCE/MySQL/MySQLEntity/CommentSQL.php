<?php
    require_once('PostSQL.php');

    class CommentSQL extends PostSQL {
        private $commentContent;
        private $commentSize;
        private $recipeIDReference;
        
        
        /**
         * @param string $postID
         * @param string $commentContent
         * @param int $commentSize
         */
        public function __construct($postID, $commentContent, $ownerUserID, $recipeIDReference) {
            parent::__construct($postID, date("d-m-Y H:i:s"), date("d-m-Y H:i:s"), $ownerUserID);
            $this->commentContent = $commentContent;
            $this->commentSize = strlen($this->commentContent);
            $this->recipeIDReference = $recipeIDReference;
        }
        
        
        public function getCommentContent() {
            return $this->commentContent;
        }
        
        
        public function getCommentSize() {
            return $this->commentSize;
        }
        
        
        public function countCommentLength() {
            return strlen($this->commentContent);
        }

        public function getRecipeIDReference() {
        	return $this->recipeIDReference;
        }

        /**
         * adds a an recipeIDReference if there hasn't been one before
        * @param $recipeIDReference (not null)
        */
        public function addRecipeIDReference($recipeIDReference) {
            if ($this->recipeIDReference == null) {
                $this->recipeIDReference = $recipeIDReference;
            }
        }

        public function create(){
            parent::$creationDate = date("d-m-Y");
        }

        public function edit($newEditDate){
            parent::$editedDate = $newEditDate;
        }
        
        
        public function __toString() {
        return "<br>" . "<b>" . "COMMENT" . "</b>" . "<br>" .
            "Comment Content: " . $this->commentContent . "<br>" .
            "Comment Size: " . $this->commentSize . "<br>" .
            "RecipeReference: " . $this->recipeIDReference . "<br>" .
            parent::toString();
        }

        
    }

?>
<?php


    require_once(__DIR__.'/../UserSQL.php');
    require_once(__DIR__.'/../PostSQL.php');

    class UserRatesRecipeSQL {
        private $userID;
        private $postID;
        private $rating;

        public function __construct(string $userID, string $postID, int $rating) {
            $this->userID = $userID;
            $this->postID = $postID;
            $this->rating = $rating;
        }

        
        public function getRecipeCommentRating_REPORT() {
            return 5; 
        }

        public function getUserID() {
            return $this->userID;
        }

        public function getPostID() {
            return $this->postID;
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
        

        /**
         * @return string
         */
        public function __toString() {
        	return "UserID: {$this->userID}, PostID: {$this->postID}, Rating: {$this->rating}";
        }
    }

?>

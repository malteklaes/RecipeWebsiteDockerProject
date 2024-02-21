<?php
    
    class PostIDSQL{

        private $postID;
        public function __construct() {
            $this->postID = $this->createPostID();
        }

        private function createPostID(){
            $date = new DateTime();
            $day = $date->format('d');
            $month = $date->format('m');
            $year = $date->format('Y');
            $hour = $date->format('H');
            $second = $date->format('s');
            $millisecond = $date->format('v');
        
            $randomChars = '';
            $characters = 'abcdefghijklmnopqrstuvwxyz';

            for ($i = 0; $i < 4; $i++) {
                $randomChars .= $characters[rand(0, strlen($characters) - 1)];
            }
        
            $newUserID = "PID". $day . $month . $year . $hour . $second . $millisecond . $randomChars;
            return $newUserID;
        }

        public function getPostID() {
            return $this->postID;
        }

        public function equals($postIDother) {
            if (strlen($postIDother) !== strlen($this->postID)) {
                return false;
            }
            for ($i = 0; $i < strlen($postIDother); $i++) {
                if ($postIDother[$i] !== $this->postID[$i]) {
                    return false;
                }
            }
            return true;
        }

    }



?>
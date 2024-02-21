<?php
    
    class UserIDSQL{

        private $userID;
        public function __construct() {
            $this->userID = $this->createUserID();
        }

        private function createUserID(){
            $date = new DateTime();
            $day = $date->format('d');
            $month = $date->format('m');
            $year = $date->format('Y');
            $hour = $date->format('H');
            $second = $date->format('s');
            $millisecond = $date->format('v');
        
            $randomNumbers = '';
            for ($i = 0; $i < 5; $i++) {
                $randomNumbers .= mt_rand(0, 9);
            }
        
            $newUserID = "UID".  $day . $month . $year . $hour . $second . $millisecond . $randomNumbers;
            return $newUserID;
        }

        public function getUserID() {
            return $this->userID;
        }

        public function equals($userIDother) {
            if (strlen($userIDother) !== strlen($this->userID)) {
                return false;
            }
            for ($i = 0; $i < strlen($userIDother); $i++) {
                if ($userIDother[$i] !== $this->userID[$i]) {
                    return false;
                }
            }
            return true;
        }

    }



?>
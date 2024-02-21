<?php
    
    class ListRecipeIDSQL{

        private $listRecipeID;
        public function __construct() {
            $this->listRecipeID = $this->createListRecipeID();
        }

        private function createListRecipeID(){
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
        
            $newUserID = "RID". $day . $month . $year . $hour . $second . $millisecond . $randomChars;
            return $newUserID;
        }

        public function getListRecipeID() {
            return $this->listRecipeID;
        }

        public function equals($listRecipeIDother) {
            if (strlen($listRecipeIDother) !== strlen($this->listRecipeID)) {
                return false;
            }
            for ($i = 0; $i < strlen($listRecipeIDother); $i++) {
                if ($listRecipeIDother[$i] !== $this->listRecipeID[$i]) {
                    return false;
                }
            }
            return true;
        }

    }



?>
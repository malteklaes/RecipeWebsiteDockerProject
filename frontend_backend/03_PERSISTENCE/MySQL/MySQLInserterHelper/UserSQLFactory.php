<?php


    class UserSQLFactory{
        
        private $usernames = array( "johndoe","janedoe", "mike87","sarahsmith", "alex45","emilyjones","samwilson","laurawalker","maxpower","annamiller","davidbrown","oliviawood","peterparker","lisasmith","matthewwhite","amandajohnson","robertgreen","hannahdavis","chrisharris","victorialee",
        "markthompson",
        "sophiewilson",
        "danieljackson",
        "emmaroberts",
        "andrewmiller",
        "lucybrown",
        "williamsmith",
        "graceharris",
        "thomasdavis",
        "olivialopez",
        "jameswilson",
        "sophiethomas",
        "michaelsmith",
        "elizabethjones",
        "ryanjackson",
        "nataliewilliams",
        "josephmartin",
        "emilyrodriguez",
        "davidtaylor",
        "madisonlee",
        "ethanroberts",
        "chloejohnson",
        "christophersmith",
        "laurenscott",
        "jacksonmiller",
        "isabellawalker",
        "noahrodriguez",
        "averymitchell",
        "aidenwilson",
        "sophialee",
        
        "funnyfoodlover",
        "spiceguru",
        "yummytummy",
        "foodieking",
        "tastylicious",
        "sillysnacker",
        "cheesecakefan",
        "pizzaaddict",
        "sugarholic",
        "cookiecraver",
        "snackattack",
        "chocolatelover",
        "sushisamurai",
        "noodleninja",
        "burgerboss",
        "popcornprince",
        "cupcakequeen",
        "icecreamwizard",
        "donutdiva",
        "veggiechamp",
        "baconlover",
        "cheesemaster",
        "spicysalsa",
        "foodexplorer",
        "yogurtlover",
        "pancakepro",
        "sodapop",
        "foodfusion",
        "chefchampion",
        "flavorfanatic");
        private $emails = array();
        private $generalPassword;

        public function __construct (){ 
            //* USER example data
            foreach ($this->usernames as $username) {$this->emails[] = $username . "@example.com";}
            $this->generalPassword = "pwd";
        }

        /**
         * generates a random user
         * @return UserSQL (not null)
         */
        public function generateRandomUser(){
            $randomIndex = array_rand($this->usernames);
            $randomUsername = $this->usernames[$randomIndex];
            $randomEmails = $this->emails[$randomIndex];
            $exampleUSER = new UserSQL(((new UserIDSQL())->getUserID()), $randomUsername, $randomEmails, $this->generalPassword);
            return $exampleUSER;
        }
    }


?>
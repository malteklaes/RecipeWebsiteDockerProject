<?php


    class ListRecipeSQLFactory {

        private $listName = array(
            "my favorite Recipes",
            "morning dishes",
            "sweet dishes",
            "party food",
            "comfort meals",
            "quick and easy recipes",
            "vegetarian delights",
            "international cuisine",
            "homemade classics",
            "summer delights",
            "weeknight dinners",
            "hearty soups",
            "grilling specialties",
            "indulgent desserts",
            "family friendly meals",
            "one pot wonders",
            "healthy salads",
            "baking adventures",
            "festive treats",
            "delicious appetizers",
        );
        
        private $listDescription = array(
            "all what mum always cooked",
            "healthy food for the morning",
            "delicious treats for the sweet tooth",
            "recipes for your next party",
            "warm and comforting dishes",
            "recipes that are quick to prepare",
            "delicious vegetarian options",
            "explore flavors from around the world",
            "traditional recipes made from scratch",
            "light and refreshing dishes for summer",
            "easy and delicious dinners",
            "hearty soups for cold days",
            "grilling recipes for outdoor cooking",
            "indulge in mouthwatering desserts",
            "meals the whole family will love",
            "easy one-pot meals for busy days",
            "fresh and nutritious salad recipes",
            "bake your way to happiness",
            "festive recipes for special occasions",
            "appetizers to impress your guests",
        );
        

        /**
         * generates a random ListRecipe (with no user in it (user == null) and no array of recipes)
         * @return ListRecipeSQL (not null)
         */
        public function generateRandomListRecipe(){
            $randomIndex = array_rand($this->listName);
            $randomListName= $this->listName[$randomIndex];
            $randomListDescription = $this->listDescription[$randomIndex];

            $exampleListRecipe = new ListRecipeSQL(((new ListRecipeIDSQL())->getListRecipeID()), null, $randomListName, 0, $randomListDescription, boolval(rand(0, 1)));
        
            return $exampleListRecipe;
        }  

    }


?>
        
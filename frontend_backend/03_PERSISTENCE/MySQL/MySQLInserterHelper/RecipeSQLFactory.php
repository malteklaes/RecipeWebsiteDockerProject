<?php


    class RecipeSQLFactory{
        


        private $recipeNames = array("pizza", "pasta", "lasagne", "burger", "sushi", "salad", "soup", "stir-fry", "cake", "cookies", "pancakes",
            "smoothie", "curry", "tacos", "sandwich", "omelette", "grilled chicken", "steak", "fish", "fried rice",
            "risotto",
    "chili",
    "meatloaf",
    "potato salad",
    "gnocchi",
    "quiche",
    "enchiladas",
    "hummus",
    "chow mein",
    "beef stew",
    "lobster bisque",
    "fried chicken",
    "beef stir-fry",
    "guacamole",
    "pork chops",
    "chicken curry",
    "vegetable stir-fry",
    "caesar salad",
    "shrimp pasta",
    "spinach lasagna",
    "fajitas",
    "miso soup",
    "falafel",
    "beef burrito",
    "paella",
    "spring rolls",
    "fried calamari",
    "vegetable curry",
    "creamy mushroom pasta",
    "chicken teriyaki",
    "beef and broccoli",
    "soba noodles",
    "caprese salad",
    "honey glazed salmon",
    "vegetable paella",
    "chicken quesadilla",
    "ratatouille",
    "pulled pork",
    "sweet and sour chicken",
    "shrimp stir-fry",
    "mushroom risotto",
    "miso glazed cod",
    "chicken fajitas",
    "vegetable biryani",
    "spaghetti carbonara",
    "lobster roll",
    "beef tacos",
    "cauliflower curry",
    "shrimp scampi",
    "chicken korma",
    "beef wellington"
        );

        private $recipeDescription = array(
            array("tomatoes", "cheese", "dough", "spinach"),
            array("noodles", "tomato sauce"),
            array("pasta sheets", "ground beef", "tomato sauce", "cheese"),
            array("ground beef", "buns", "cheese", "lettuce", "tomatoes"),
            array("sushi rice", "nori seaweed", "raw fish"),
            array("lettuce", "cucumber", "tomatoes", "dressing"),
            array("broth", "vegetables", "noodles"),
            array("vegetables", "meat", "sauce"),
            array("flour", "sugar", "butter", "eggs"),
            array("flour", "sugar", "butter", "chocolate chips"),
            array("flour", "milk", "eggs", "sugar"),
            array("fruits", "yogurt", "milk"),
            array("rice", "curry paste", "coconut milk", "vegetables", "meat"),
            array("tortillas", "ground beef", "cheese", "salsa"),
            array("bread", "meat", "cheese", "lettuce", "tomatoes"),
            array("eggs", "milk", "cheese", "vegetables"),
            array("chicken breast", "marinade", "grill"),
            array("beef steak", "marinade", "grill"),
            array("fish fillet", "lemon", "butter"),
            array("rice", "vegetables", "eggs", "soy sauce"),

            array("rice", "mushrooms", "parmesan cheese", "white wine", "chicken broth"),
            array("ground beef", "beans", "tomatoes", "onions", "chili powder"),
            array("ground beef", "breadcrumbs", "onions", "ketchup", "egg"),
            array("potatoes", "mayonnaise", "mustard", "pickles", "onions"),
            array("potatoes", "flour", "egg", "salt", "butter"),
            array("pie crust", "eggs", "milk", "cheese", "vegetables"),
            array("tortillas", "chicken", "cheese", "salsa", "sour cream"),
            array("chickpeas", "lemon juice", "garlic", "tahini", "olive oil"),
            array("noodles", "vegetables", "soy sauce", "garlic", "ginger"),
            array("beef", "carrots", "potatoes", "onions", "beef broth"),
            array("lobster", "cream", "butter", "vegetables", "paprika"),
            array("chicken", "flour", "egg", "breadcrumbs", "oil"),
            array("beef", "broccoli", "soy sauce", "garlic", "ginger"),
            array("avocado", "lime juice", "tomatoes", "onions", "cilantro"),
            array("pork chops", "flour", "garlic powder", "paprika", "oil"),
            array("chicken", "curry powder", "coconut milk", "tomatoes", "onions"),
            array("vegetables", "soy sauce", "garlic", "ginger", "sesame oil"),
            array("romaine lettuce", "croutons", "parmesan cheese", "caesar dressing", "lemon"),
            array("shrimp", "pasta", "garlic", "lemon", "parsley"),
            array("spinach", "lasagna noodles", "ricotta cheese", "tomato sauce", "mozzarella cheese"),
            array("chicken", "bell peppers", "onions", "tortillas", "salsa"),
            array("miso paste", "tofu", "seaweed", "green onions", "soy sauce"),
            array("chickpeas", "parsley", "garlic", "cumin", "flour"),
            array("beef", "rice", "beans", "cheese", "salsa"),
            array("rice", "seafood", "saffron", "bell peppers", "peas"),
            array("rice paper", "shrimp", "lettuce", "mint", "dipping sauce"),
            array("calamari", "flour", "cornmeal", "salt", "pepper"),
            array("vegetables", "curry powder", "coconut milk", "tomatoes", "onions"),
            array("pasta", "mushrooms", "cream", "parmesan cheese", "garlic"),
            array("chicken", "soy sauce", "honey", "ginger", "garlic"),
            array("beef", "broccoli", "soy sauce", "garlic", "ginger"),
            array("buckwheat noodles", "soy sauce", "sesame oil", "green onions", "nori"),
            array("tomatoes", "mozzarella cheese", "basil", "balsamic glaze", "olive oil"),
            array("salmon", "honey", "soy sauce", "ginger", "garlic"),
            array("rice", "vegetables", "saffron", "bell peppers", "peas"),
            array("chicken", "cheese", "tortillas", "salsa", "sour cream"),
            array("eggplant", "zucchini", "tomatoes", "garlic", "olive oil"),
            array("pork", "bbq sauce", "buns", "coleslaw", "pickles"),
            array("chicken", "pineapple", "bell peppers", "onions", "sweet and sour sauce"),
            array("shrimp", "vegetables", "soy sauce", "garlic", "ginger"),
            array("rice", "mushrooms", "onions", "parmesan cheese", "vegetable broth"),
            array("cod", "miso paste", "soy sauce", "mirin", "sake"),
            array("chicken", "bell peppers", "onions", "tortillas", "fajita seasoning"),
            array("rice", "vegetables", "spices", "cashews", "raisins"),
            array("spaghetti", "bacon", "eggs", "parmesan cheese", "black pepper"),
            array("lobster", "buns", "mayonnaise", "lemon juice", "lettuce"),
            array("beef", "tortillas", "cheese", "salsa", "guacamole"),
            array("cauliflower", "potatoes", "tomatoes", "curry powder", "coconut milk"),
            array("shrimp", "garlic", "butter", "white wine", "parsley"),
            array("chicken", "yogurt", "spices", "tomato paste", "cream"),
            array("beef", "puff pastry", "mushrooms", "onions", "dijon mustard")
        );

        private $categories = array("Italian","Pasta","Mexican","Burgers","Sushi","Salads","Soups","Stir-Fry","Desserts","Cookies",
            "Breakfast","Smoothies","Indian","Tacos","Sandwiches","Omelettes","Grilled Chicken","Steak","Seafood","Asian Fusion");


        
        /**
         * generates a random recipe (with no user in it, user == null)
         * @return RecipeSQL (not null)
         */
        public function generateRandomRecipe(){
            $randomIndex = array_rand($this->categories);
            $randomRecipeName = $this->recipeNames[$randomIndex];
            $randomRecipeDescription = $this->recipeDescription[$randomIndex];
            $randomCategory = $this->categories[$randomIndex];

            $exampleRecipe = new RecipeSQL(((new PostIDSQL())->getPostID()), $randomRecipeName, implode(", ", $randomRecipeDescription), $randomCategory, 0, null);
            return $exampleRecipe;
        }   
    }


?>
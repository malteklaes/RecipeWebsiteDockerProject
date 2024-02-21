<?php


    class CommentSQLFactory{
        

        private $commentContent = array(
            "great food",
            "delicious",
            "recipe could be improved",
            "eat it today - fantastic!!",
            "I will cook it the next time!",
            "not bad - but not my favorite",
            "needs more seasoning",
            "could use more flavor",
            "tastes okay - but nothing special",
            "not impressed",
            "amazing dish",
            "absolutely delicious",
            "loved it",
            "will definitely make it again",
            "just average",
            "nothing to write home about",
            "did not meet my expectations",
           
            "disappointing",
            "would not recommend",
            "not worth the effort",
            "best recipe ever",
            "mouthwatering",
            "perfectly cooked",
            "flavorful and satisfying",
            "just okay",

            "nothing noteworthy",
            "lacks depth of flavor",
            "did not enjoy it",
            "could be better",
            "excellent",
            "yummy",
            "worth trying",
            "average at best",
            "bland and uninspiring",
            "did not enjoy the taste",
            "needs more balance",
            "mediocre",
            "lacked seasoning",
            "below average",
            "delightful",
            "tasty",
            "satisfying",
            "meh",
            "not my cup of tea",
            "expected more",
            "tasted strange",
            "not memorable",
            "could not finish it",
            "nothing special",

            "very tasty",
            "surprisingly good",
            "disgusting",
            "worth the hype",
            "simple yet delicious",
            "too salty",
            "the presentation was amazing",
            "needs more spice",
            "mouthwatering aroma",
            "made me want seconds",
            "unforgettable",
            "a bit too sweet",
            "healthy and flavorful",
            "so-so",
            "the texture was off",
            "highly recommended",
            "a family favorite",
            "unique combination of flavors",
            "not for everyone",
            "quick and easy to make",
            "pleased with the result",
            "hit the spot",
            "exquisite",
            "nothing to complain about",
            "better than expected",
            "refreshing",
            "needs more cooking time",
            "a bit too greasy",
            "bursting with flavor",
            "not my taste",
            "worth the calories",
            "nice presentation",
            "did not live up to the hype",
            "full of surprises",
            "not for the faint-hearted",
            "could not get enough",
            "a classic recipe",
            "not recommended",
            "a delightful surprise",
            "did not meet my expectations",
            "unique and delicious",
            "more than satisfied",
            "a bit too spicy",
            "could not finish it all",
            "good - but not great"
        );
        


        
        /**
         * generates a random comment (with no user in it (user == null) and belongs to no recipe (recipeIDReference == null))
         * @return CommentSQL (not null)
         */
        public function generateRandomComment(){
            $randomIndex = rand(1,sizeof($this->commentContent)-1);
            $randomCommentContent = $this->commentContent[$randomIndex];

            try{
                $exampleComment = new CommentSQL(((new PostIDSQL())->getPostID()), $randomCommentContent, null, null);
            } catch (Exception $e){
                echo "EXCPETION: " . $e;
            }
            return $exampleComment;
        }   
    }


?>
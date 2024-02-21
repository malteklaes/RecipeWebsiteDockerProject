<?php

class UserMongo {
    private $username;
    private $email;
    private $password;
    private $registrationDate;
    private $comments;
    private $recipes;
    private $rates;
    private $listRecipeLiked;
    private $listRecipeManaged;
    private $following;
    
    
    /**
     * Summary of __construct
     * @param string $username
     * @param string $email
     * @param string $password

     */
    public function __construct ($username, $email, $password, $registrationDate, $comments, $recipes, 
    $listRecipeManaged, $listRecipeLiked, $rates, $following) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->registrationDate = $registrationDate;
        $this->following = $following;
        $this->comments = $comments;
        $this->recipes = $recipes;
        $this->rates = $rates;
        $this->listRecipeManaged = $listRecipeManaged;
        $this->listRecipeLiked = $listRecipeLiked;
    }
    

    public function getUsername() {
        return $this->username;
    }
    

    public function getEmail () {
        return $this->email;
    }


    public function getPassword() {
        return $this->password;
    }

    public function setRegistrationDate($registrationDate) {
        $this->registrationDate = $registrationDate;
    }

    public function getRegistrationDate() {
        return $this->registrationDate;
    }

    public function setFollowing(UserMongo $user) {
        $this->following[] = $user;
    }

    public function getFollowing() {
        foreach($this->following as $user){
            $following[] = [
                "username" => $user->getUsername(),
                "email" => $user->getEmail(),
                "password" => $user->getPassword(),
                "registrationDate" => $user->getRegistrationDate(),
                "comments" => $user->getComments(),
                "recipes" => $user->getRecipes(),
                "listRecipeManaged" => $user->getListRecipesManaged(),
                "listRecipeLiked" => $user->getListRecipesLiked(),
                "rates" => $user->getRates(),
                "following" => $user->getFollowing(),
            ]; 
        }
        return $following;
    }

    

    public function getComments() {
        foreach($this->comments as $comment){
            $comments[] = [
                "commentContent" => $comment->getCommentContent(),
                "ownerName" => $comment->getOwnerName(),
                "creationDate" => $comment->getCreationDate(),
                "editedDate" => $comment->getEditedDate()
            ]; 
        }
    	return $comments;
    }
  
    public function getRecipes() {
        foreach($this->recipes as $recipe){
            $recipes[] = [
                "recipeName" => $recipe->getRecipeName(),
                "recipeDescription" => $recipe->getRecipeDescription(),
                "category" => $recipe->getCategory(),
                "rating" => $recipe->getRating(), 
                "comment" => $recipe->getComments(),
                "creationDate" => $recipe->getCreationDate(),
                "editedDate" => $recipe->getEditedDate()
            ]; 
        }
    	return $recipes;
    }

    public function setRates(RecipeMongo $recipe) {
        $this->rates[] = $recipe;
    }

    public function getRates() {
        foreach($this->rates as $rate){
            $rates[] = [
                "recipeName" => $rate->getRecipeName(),
                "recipeDescription" => $rate->getRecipeDescription(),
                "category" => $rate->getCategory(),
                "creationDate" => $rate->getCreationDate(),
                "editedDate" => $rate->getEditedDate()
            ]; 
        }
    	return $rates;
    }

    public function setListRecipesManaged(ListRecipeMongo $listRecipe) {
        $this->listRecipeManaged[] = $listRecipe;
    }

    public function getListRecipesManaged() {
        foreach($this->listRecipeManaged as $listRecipe){
            $listRecipeManaged[] = [
                'listName' => $listRecipe->getListName(),
                'listDescription' => $listRecipe->getListDescription(),
                'privateStatus' => $listRecipe->getPrivateStatus(),
                'recipes' => $listRecipe->getRecipesOfListRecipe(),
                'likes' => $listRecipe->getLikesAmount(),
                'creationDate' => $listRecipe->getCreationDate(),
                'editedDate' => $listRecipe->getEditedDate()
            ]; 
        }
    	return $listRecipeManaged;
    }

    public function setListRecipesLiked(ListRecipeMongo $listRecipe) {
        $this->listRecipeLiked[] = $listRecipe;
    }

    public function getListRecipesLiked() {
        foreach($this->listRecipeLiked as $likedListRecipe){
            $listRecipeLiked[] = [
                "listName" => $likedListRecipe->getListName(),
                "listDescription" => $likedListRecipe->getListDescription(),
                "privateStatus" => $likedListRecipe->getPrivateStatus(),
                "creationDate" => $likedListRecipe->getCreationDate(),
                "editedDate" => $likedListRecipe->getEditedDate()
            ]; 
        }
    	return $listRecipeLiked;
    }
    

    public function equals(UserMongo $other) {

        if ($this->username !== $other->username) {
            return false;
        }

        if ($this->email !== $other->email) {
            return false;
        }

        if ($this->password !== $other->password) {
            return false;
        }

        if ($this->registrationDate !== $other->registrationDate) {
            return false;
        }

        return true;
    }

    public function __toString() {
        return "<br>". "<b>" . "USER" . "</b>" . "<br>" .
            "Username: " . $this->username . "<br>"
            . "Email: " . $this->email . "<br>"
            . "Password: " . $this->password . "<br>"
            . "Registration Date: " . $this->registrationDate . "<br>"
            . "Following: [ " . implode($this->following) . "<br>]" . "<br>"
            . "Comments: [ " . implode($this->comments) . "<br>]" . "<br>"
            . "Recipes: [ " . implode($this->recipes) . "<br>]" . "<br>"
            . "Rates: [ " . implode($this->rates) . "<br>]" . "<br>"
            . "Managed ListRecipes: [ " . implode($this->listRecipeManaged) . "<br>]" . "<br>"
            . "Liked ListRecipes: [ " . implode($this->listRecipeLiked) . "<br>]" . "<br>";
    }

    
}

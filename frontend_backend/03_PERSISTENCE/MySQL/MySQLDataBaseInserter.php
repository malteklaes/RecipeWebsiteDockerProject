<?php
//* MySQL Entity
require_once('MySQLEntity/UserSQL.php');
require_once('MySQLEntity/ListRecipeSQL.php');
require_once('MySQLEntity/CommentSQL.php');
require_once('MySQLEntity/PostSQL.php');
require_once('MySQLEntity/RecipeSQL.php');
//* MySQL EntityHelper
require_once('MySQLEntity/MySQLEntityHelper/UserIDSQL.php');
require_once('MySQLEntity/MySQLEntityHelper/PostIDSQL.php');
require_once('MySQLEntity/MySQLEntityHelper/ListRecipeIDSQL.php');
//* MySQL Entity Relationship
require_once('MySQLEntity/MySQLRelationship/UserRatesRecipeSQL.php');
require_once('MySQLEntity/MySQLRelationship/ListContainsOfRecipeSQL.php');
require_once('MySQLEntity/MySQLRelationship/UserLikesListRecipeSQL.php');
require_once('MySQLEntity/MySQLRelationship/UserFollow.php');
//* MySQL factories to create random data == SQL_TABLES
require_once('MySQLInserterHelper/UserSQLFactory.php');
require_once('MySQLInserterHelper/RecipeSQLFactory.php');
require_once('MySQLInserterHelper/CommentSQLFactory.php');
require_once('MySQLInserterHelper/ListRecipeSQLFactory.php');

class MySQLDataBaseInserter {

    //* USER example data
    private $userSQLFactory;
    private $recipeSQLFactory;
    private $commentSQLFactory;
    private $listRecipeSQLFactory;
    

    public function __construct (){ 
        //* USER example data
        $this->userSQLFactory = new UserSQLFactory();
        $this->recipeSQLFactory = new RecipeSQLFactory();
        $this->commentSQLFactory = new CommentSQLFactory();
        $this->listRecipeSQLFactory = new ListRecipeSQLFactory();
    }



    //* USER  DATA -------------------------------------------------------------
    /**
     * creates a random user
     * @return UserSQL
     */
    public function createUserExample(){
        //* create random user
        return $this->userSQLFactory->generateRandomUser();
    }

    /**
     * creates, inserts and returns a random build user
     * @param mixed $dataBaseConnection (not null and open connection)
     * @param mixed $exampleUSER (not null)
     * @return void
     */
    public function insertUserExample($dataBaseConnection, $exampleUSER){
        //* insert data 
        $insertSql = "INSERT INTO user(userID, username, email, pwd, registrationDate)
             VALUES ('". $exampleUSER->getUserID()."','".
             $exampleUSER->getUsername()."','".
             $exampleUSER->getEmail()."','".
             $exampleUSER->getPassword()."', '" . date('YmdHis', strtotime($exampleUSER->getRegistrationDate())) . "');";
        $dataBaseConnection->exec($insertSql);
    }

    public function showUserTableData($dataBaseConnection){
        $table_name = "user";
        $query = "SELECT * FROM user";
        $stmt = $dataBaseConnection->query($query);
        $output_database_pdo = "\n<strong>$table_name - pdo</strong>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output_database_pdo .= "<p>".$row['userID']."</p>";
            $output_database_pdo .= "<p>".$row['username']."</p>";
            $output_database_pdo .= "<p>".$row['email']."</p>";
            $output_database_pdo .= "<p>".$row['pwd']."</p>";
            $output_database_pdo .= "<p>".$row['registrationDate']."</p>";
            $output_database_pdo .= "<hr>";
        }
        return $output_database_pdo;
    }

    //* RECIPE  DATA -------------------------------------------------------------
    /**
     * creates a random recipe
     * @return RecipeSQL
     */
    public function createRecipeExample(){
        //* create random recipe
        return $this->recipeSQLFactory->generateRandomRecipe();
    }

    /**
     * creates, inserts and returns a random build recipe
     * @param mixed $dataBaseConnection (not null and open connection)
     * @param mixed $exampleRecipe (not null)
     * @return void 
     */
    public function insertRecipeExample($dataBaseConnection, $exampleRecipe){
        //* insert data  
        $insertSql = "INSERT INTO recipe(postID, recipeName, recipeDescription, category, rating, ownerUserID, creationDate, editedDate)
        VALUES ('". $exampleRecipe->getPostID()."','".
        $exampleRecipe->getRecipeName()."','".
        $exampleRecipe->getRecipeDescription()."','".
        $exampleRecipe->getCategory()."','".
        $exampleRecipe->getRating()."','".
        $exampleRecipe->getOwnerUserID()."','".
        date('YmdHis', strtotime($exampleRecipe->getCreationDate()))."', '" . date('YmdHis', strtotime($exampleRecipe->getEditedDate())) . "');";
        $dataBaseConnection->exec($insertSql);
    }

    public function showRecipeTableData($dataBaseConnection){
        $table_name = "recipe";
        $query = "SELECT * FROM recipe";
        $stmt = $dataBaseConnection->query($query);
        $output_database_pdo = "\n<strong>$table_name - pdo</strong>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output_database_pdo .= "<p>".$row['postID']."</p>";
            $output_database_pdo .= "<p>".$row['recipeName']."</p>";
            $output_database_pdo .= "<p>".$row['recipeDescription']."</p>";
            $output_database_pdo .= "<p>".$row['category']."</p>";
            $output_database_pdo .= "<p>".$row['rating']."</p>";
            $output_database_pdo .= "<p>".$row['ownerUserID']."</p>";
            $output_database_pdo .= "<p>".$row['creationDate']."</p>";
            $output_database_pdo .= "<p>".$row['editedDate']."</p>";
            $output_database_pdo .= "<hr>";
        }
        return $output_database_pdo;
    }

    //* COMMENT  DATA -------------------------------------------------------------
    /**
     * creates a random comment
     * @return CommentSQL
     */
    public function createCommentExample(){
        //* create random comment
        return $this->commentSQLFactory->generateRandomComment();
    }

    /**
     * creates, inserts and returns a random build comment
     * @param mixed $dataBaseConnection (not null and open connection)
     * @param mixed $exampleComment (not null)
     * @return void
     */
    public function insertCommentExample($dataBaseConnection, $exampleComment){
        //* insert data  
        $insertSql = "INSERT INTO comment(postID, commentContent, ownerUserID, recipeIDReference, creationDate, editedDate)
        VALUES ('". $exampleComment->getPostID()."','".
        $exampleComment->getCommentContent()."','".
        $exampleComment->getOwnerUserID()."','".
        $exampleComment->getRecipeIDReference()."','".
        date('YmdHis', strtotime($exampleComment->getCreationDate()))."', '" . date('YmdHis', strtotime($exampleComment->getEditedDate())) . "');";
        $dataBaseConnection->exec($insertSql);
    }

    public function showCommentTableData($dataBaseConnection){
        $table_name = "comment";
        $query = "SELECT * FROM comment";
        $stmt = $dataBaseConnection->query($query);
        $output_database_pdo = "\n<strong>$table_name - pdo</strong>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output_database_pdo .= "<p>".$row['postID']."</p>";
            $output_database_pdo .= "<p>".$row['commentContent']."</p>";
            $output_database_pdo .= "<p>".$row['ownerUserID']."</p>";
            $output_database_pdo .= "<p>".$row['recipeIDReference']."</p>";
            $output_database_pdo .= "<p>".$row['creationDate']."</p>";
            $output_database_pdo .= "<p>".$row['editedDate']."</p>";
            $output_database_pdo .= "<hr>";
        }
        return $output_database_pdo;
    }

    //* LISTRECIPE  DATA -------------------------------------------------------------
    /**
     * creates a random listRecipe
     * @return ListRecipeSQL
     */
    public function createListRecipeExample(){
        //* create random listRecipe
        return $this->listRecipeSQLFactory->generateRandomListRecipe();
    }

    /**
     * creates, inserts and returns a random build listRecipe
     * @param mixed $dataBaseConnection (not null and open connection)
     * @param mixed $exampleListRecipe (not null)
     * @return void
     */
    public function insertListRecipeExample($dataBaseConnection, $exampleListRecipe){
        //* insert data  
        $insertSql = "INSERT INTO listRecipe(listID, ownerID, listName, likesAmount, listDescription, privateStatus, creationDate, editedDate)
        VALUES ('". $exampleListRecipe->getListID()."','".
        $exampleListRecipe->getOwnerUserID()."','".
        $exampleListRecipe->getListName()."','".
        $exampleListRecipe->getLikesAmount()."','".
        $exampleListRecipe->getListDescription()."','".
        (($exampleListRecipe->getPrivateStatus()) ? 1 : 0)."','".
        date('YmdHis', strtotime($exampleListRecipe->getCreationDate()))."', '" . date('YmdHis', strtotime($exampleListRecipe->getEditedDate())) . "');";
        $dataBaseConnection->exec($insertSql);
    }

    public function showListRecipeTableData($dataBaseConnection){
        $table_name = "listRecipe";
        $query = "SELECT * FROM listRecipe";
        $stmt = $dataBaseConnection->query($query);
        $output_database_pdo = "\n<strong>$table_name - pdo</strong>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output_database_pdo .= "<p>".$row['listID']."</p>";
            $output_database_pdo .= "<p>".$row['ownerID']."</p>";
            $output_database_pdo .= "<p>".$row['listName']."</p>";
            $output_database_pdo .= "<p>".$row['likesAmount']."</p>";
            $output_database_pdo .= "<p>".$row['listDescription']."</p>";
            $output_database_pdo .= "<p>".$row['privateStatus']."</p>";
            $output_database_pdo .= "<p>".$row['creationDate']."</p>";
            $output_database_pdo .= "<p>".$row['editedDate']."</p>";
            $output_database_pdo .= "<hr>";
        }
        return $output_database_pdo;
    }


    
    //* USERRATESRECIPE DATA -------------------------------------------------------------
    /**
     * inserts a random build user-recipe connection
     * @param mixed $dataBaseConnection (not null and open connection), 
     * @param mixed $userID (not null)
     * @param mixed $postID (not null)
     * @return void
     */
    public function insertUserRatesRecipeExample($dataBaseConnection, $userID, $postID, $rating){
       //* insert data 
        $insertSql = "INSERT INTO userRatesRecipe(userID, postID, rating)
             VALUES ('". $userID ."','". $postID . "','". $rating . "');";
        $dataBaseConnection->exec($insertSql);
    }

    public function showUserRatesRecipeTableData($dataBaseConnection){
        $table_name = "userRatesRecipe";
        $query = "SELECT * FROM userRatesRecipe";
        $stmt = $dataBaseConnection->query($query);
        $output_database_pdo = "\n<strong>$table_name - pdo</strong>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output_database_pdo .= "<p>".$row['userID']."</p>";
            $output_database_pdo .= "<p>".$row['postID']."</p>";
            $output_database_pdo .= "<p>".$row['rating']."</p>";
            $output_database_pdo .= "<hr>";
        }
        return $output_database_pdo;
    }

    //* USERLIKESRLISTECIPE DATA -------------------------------------------------------------
    
    /**
     * inserts a random build user-listRecipe connection
     * @param mixed $dataBaseConnection (not null and open connection), 
     * @param mixed $userID (not null)
     * @param mixed $listID (not null)
     * @return void
     */
    public function insertUserLikesRecipeExample($dataBaseConnection, $userID, $listID){
        //* insert data 
         $insertSql = "INSERT INTO userLikesListRecipe(userID, listID)
              VALUES ('". $userID ."','". $listID . "');";
         $dataBaseConnection->exec($insertSql);
     }
 
    public function showUserLikesRecipeTableData($dataBaseConnection){
        $table_name = "userLikesListRecipe";
        $query = "SELECT * FROM userLikesListRecipe";
        $stmt = $dataBaseConnection->query($query);
        $output_database_pdo = "\n<strong>$table_name - pdo</strong>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output_database_pdo .= "<p>".$row['userID']."</p>";
            $output_database_pdo .= "<p>".$row['listID']."</p>";
            $output_database_pdo .= "<hr>";
        }
        return $output_database_pdo;
    }


     //* USERFOLLOW DATA -------------------------------------------------------------
    
    /**
     * inserts a random build user-user connection
     * @param mixed $dataBaseConnection (not null and open connection), 
     * @param mixed $userID (not null)
     * @param mixed $userID (not null)
     * @return void
     */
    public function insertUserFollowExample($dataBaseConnection, $user1ID, $user2ID){
        //* insert data 
         $insertSql = "INSERT INTO userFollow(user1ID, user2ID)
              VALUES ('". $user1ID ."','". $user2ID . "');";
         $dataBaseConnection->exec($insertSql);
     }
 
     public function showUserFollowData($dataBaseConnection){
         $table_name = "userFollow";
         $query = "SELECT * FROM userFollow";
         $stmt = $dataBaseConnection->query($query);
         $output_database_pdo = "\n<strong>$table_name - pdo</strong>";
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             $output_database_pdo .= "<p>".$row['user1ID']."</p>";
             $output_database_pdo .= "<p>".$row['user2ID']."</p>";
             $output_database_pdo .= "<hr>";
         }
         return $output_database_pdo;
     }


     //* LISTCONTAINSOFRECIPE DATA -------------------------------------------------------------
    
    /**
     * inserts a random build listRecipe-recipe connection
     * @param mixed $dataBaseConnection (not null and open connection), 
     * @param mixed $userID (not null)
     * @param mixed $recipeID (not null)
     * @return void
     */
    public function insertListContainsOfRecipeExample($dataBaseConnection, $listID, $recipeID){
        //* insert data 
         $insertSql = "INSERT INTO listContainsOfRecipe(listID, recipeID)
              VALUES ('". $listID ."','". $recipeID . "');";
         $dataBaseConnection->exec($insertSql);
     }
 
     public function showListContainsOfRecipeData($dataBaseConnection){
         $table_name = "listContainsOfRecipe";
         $query = "SELECT * FROM listContainsOfRecipe";
         $stmt = $dataBaseConnection->query($query);
         $output_database_pdo = "\n<strong>$table_name - pdo</strong>";
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
             $output_database_pdo .= "<p>".$row['listID']."</p>";
             $output_database_pdo .= "<p>".$row['recipeID']."</p>";
             $output_database_pdo .= "<hr>";
         }
         return $output_database_pdo;
     }

     
















    public function createData(){
        $outputString = "";
    
        // *Test userID
        $userID = new UserIDSQL();
        $userID2 = new UserIDSQL();
        $outputString .= $userID->getUserID() . "<br>";
        $outputString .= $userID2->getUserID() . "<br>";
        $outputString .= strlen($userID->getUserID()). "<br>";
        $outputString .= ($userID->equals((new UserIDSQL())->getUserID())) ? "true" : "false";
        $outputString .= "<br>";
        $outputString .= ($userID->equals($userID->getUserID())) ? "true" : "false";

        
        // *Test postID
        $outputString .= "<br>";
        $postID = new PostIDSQL();
        $outputString .= $postID->getPostID();
        $outputString .= "<br>";
        $outputString .= ($userID->equals((new PostIDSQL())->getPostID())) ? "true" : "false";
        $outputString .= "<br>";
        $outputString .= ($postID->equals($postID->getPostID())) ? "true" : "false";

        
        // *Test listRecipeID
        $outputString .= "<br>";
        $listRecipeID = new ListRecipeIDSQL();
        $outputString .= $listRecipeID->getListRecipeID();
        $outputString .= "<br>";
        $outputString .= ($listRecipeID->equals((new ListRecipeIDSQL())->getListRecipeID())) ? "true" : "false";
        $outputString .= "<br>";
        $outputString .= ($listRecipeID->equals($listRecipeID->getListRecipeID())) ? "true" : "false";
      

        // *Test MySQL entities (user, listRecipe, post, recipe, comment)
        $user1 = new UserSQL((new UserIDSQL())->getUserID(), "user1", "user@hotmail.com", "pwd");
        $outputString .= $user1;
        $recipePizza = new RecipeSQL((new PostIDSQL())->getPostID(), "Pizza", "italien food", "italien", 4, null);
        $recipePizza->addOwnerUserID($user1->getUserID());
        $recipePasta = new RecipeSQL((new PostIDSQL())->getPostID(), "Paste", "italien food", "italien", 3, $user1->getUserID());
        $comment1 = new CommentSQL((new PostIDSQL())->getPostID(), "super food!!", $user1->getUserID(), $recipePizza->getPostID());
        $comment2 = new CommentSQL((new PostIDSQL())->getPostID(), "declicious", $user1->getUserID(), $recipePasta->getPostID());
        $comment3 = new CommentSQL((new PostIDSQL())->getPostID(), "great", $user1->getUserID(), $recipePizza->getPostID());
        // FIXME there are no array in recipe anymore, so no more adding to an array
        /*  $recipePizza->addComment($comment1);
        $recipePizza->addComment($comment3);
        $recipePasta->addComment($comment2); */
        $outputString .= $recipePizza;
        $listRecipe = new ListRecipeSQL((new ListRecipeIDSQL())->getListRecipeID(), null, "BasicRecipes", "all about italian food", "basics recipe collection", false);
        $listRecipe->addOwnerUserID($user1->getUserID());
        //echo "TEST: " . $listRecipe->getOwnerUserID();
        //$outputString .= $listRecipe;

        // *User rates Recipe
        /* $user1_rates_RecipePizza = new UserRatesRecipeSQL($user1->getUserID(), $recipePizza->getPostID());
        $outputString .= $user1_rates_RecipePizza;
        $outputString .= "<br>";
        $list_contains_recipe = new ListContainsOfRecipeSQL($listRecipeID->getListRecipeID(), $comment1->getPostID());
        $outputString .= $list_contains_recipe;
        $outputString .= "<br>";
        $user_likes_listRecipe = new UserLikesListRecipe($user1->getUserID(), $listRecipeID->getListRecipeID());
        $outputString .= $user_likes_listRecipe;
        $outputString .= "<br>";
 */
        return $outputString;
    }


    

    




}



?>
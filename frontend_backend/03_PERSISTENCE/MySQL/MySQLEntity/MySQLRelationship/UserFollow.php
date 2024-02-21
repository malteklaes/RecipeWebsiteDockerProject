<?php


require_once(__DIR__.'/../UserSQL.php');

class UserFollow {
    private $user1ID;
    private $user2ID;

    public function __construct(String $user1ID, String $user2ID) {
        $this->user1ID = $user1ID;
        $this->user2ID = $user2ID;
    }


    public function getUser1ID() {
    	return $this->user1ID;
    }

    public function getUser2ID() {
    	return $this->user2ID;
    }

    

    /**
     * @return string
     */
    public function __toString() {
    	return "User1ID: {$this->user1ID} follows User2ID: {$this->user2ID}";
    }
}

?>

<?php

class UserSQL {
    private $userID;
    private $username;
    private $email;
    private $password;
    private $registrationDate;
    
    
    
    /**
     * Summary of __construct
     * @param string $userID
     * @param string $username
     * @param string $email
     * @param string $password
     */
    public function __construct ( $userID, $username, $email, $password) {
        $this->userID = $userID;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $randomTimestamp = mt_rand(strtotime('2010-01-01'), time());

        $this->registrationDate = date('d-m-Y H:i:s', $randomTimestamp);
    }

    
    public function setRegistrationDate($registrationDate) {
        $this->registrationDate = $registrationDate;
    }


  
    public function getUserID() {
        return $this->userID;
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
    
   
    public function getRegistrationDate() {
        return $this->registrationDate;
    }
    
    


    public function equals(UserSQL $other) {
        if ($this->userID !== $other->userID) {
            return false;
        }

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
            "User ID: " . $this->userID . "<br>"
            . "Username: " . $this->username . "<br>"
            . "Email: " . $this->email . "<br>"
            . "Password: " . $this->password . "<br>"
            . "Registration Date: " . $this->registrationDate . "<br>";
    }

    
}

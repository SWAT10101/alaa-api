<?php

 class DbOperations{

    private $con;

    function __construct()
    {
       require_once dirname(__FILE__) . '/DbConnect.php';
       $db = new DbConnect;
       $this->con = $db->connect();

    }

    //This function to create users
    public function createUser($firstanem, $lastName, $email, $password, $phone, $block, $street, $building, $Floor, $Flat)
    {
        if(!$this->isEmailExist($email))
        {
            $stmt = $this->con->prepare("INSERT INTO users (FirstName, LasttName, Email, Password, Phone, Block, Street, Building, Floor, Flat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssisssss", $firstanem, $lastName, $email, $password, $phone, $block, $street, $building, $Floor, $Flat);

           if($stmt->execute())
           {
               return USER_CREATED;

           }
           else
           {
                 return USER_FAILURE;
           }

       }
       else
       {
           return USER_EXISTS;
       }

        
    }

    //This function for user login
    public function userLogin($email, $password)
    {
        if($this->isEmailExist($email))
        {
            $hashed_password = $this->getUserPasswordByEmail($email);

            if(password_verify($password, $hashed_password))
            {
              return USER_AUTHENTICATED;  
            }
            else
            {
              return USER_PASSWORD_DO_NOT_MATCH;
            }


        }
        else
        {
          return USER_NOT_FOUND;
        }
    }

    //This function to get user password by email
    private function getUserPasswordByEmail($email)
    {
        $stmt = $this->con->prepare("SELECT Password FROM users WHERE Email = ?"); 
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($password);
        $stmt->fetch();
        return $password;
     
    }

    //This function to get user by his email
    public function getUserByEmail($email)
    {
        $stmt = $this->con->prepare("SELECT FirstName, LasttName, Email, Phone, Block, Street, Building, Floor, Flat FROM users WHERE Email = ?"); 
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($firstname, $lastName, $email, $phone, $block, $street, $building, $floor, $flar);
        $stmt->fetch();
        $user = array();
        $user['FirstName'] = $firstname;
        $user['LasttName'] = $lastName;
        $user['Email'] = $email;
        $user['Phone'] = $phone;
        $user['Block'] = $block;
        $user['Street'] = $street;
        $user['Building'] = $building;
        $user['Floor'] = $floor;
        $user['Flat'] = $flar;
        return $user;
    }

    //This function to get  all users 
    public function getAllUsers()
    {
        $stmt = $this->con->prepare("SELECT FirstName, LasttName, Email, Phone, Block, Street, Building, Floor, Flat FROM users;"); 
        $stmt->execute();
        $stmt->bind_result($firstname, $lastName, $email, $phone, $block, $street, $building, $floor, $flar);
        $users = array();
        while($stmt->fetch())
        {
            $user = array();
            $user['FirstName'] = $firstname;
            $user['LasttName'] = $lastName;
            $user['Email'] = $email;
            $user['Phone'] = $phone;
            $user['Block'] = $block;
            $user['Street'] = $street;
            $user['Building'] = $building;
            $user['Floor'] = $floor;
            $user['Flat'] = $flar;
            array_push($users, $user);
        }
        return $users;
        
    }

    // This function to check if user exist
    private function isEmailExist($email){
        $stmt = $this->con->prepare("SELECT PersonID FROM users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

 }
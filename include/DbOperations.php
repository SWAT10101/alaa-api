<?php

 class DbOperations{

    private $con;

    function __construct()
    {
       require_once dirname(__FILE__) . '/DbConnect.php';
       $db = new DbConnect;
       $this->con = $db->connect();

    }

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

    private function isEmailExist($email){
        $stmt = $this->$con->perpare("SELECT PersonID FROM users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

 }
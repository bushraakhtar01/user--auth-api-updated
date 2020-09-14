<?php
// 'user' object
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $firstName;
    public $lastName;
    public $email;
    public $password;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
// create() method will be here
// create new user record
function create(){
 
    // insert query
    $query = "INSERT INTO " . $this->table_name . "
            SET
               firstName=:firstName,
               lastName=:lastName,

                email = :email,
                password = :password";
 
    // prepare the query
    $stmt = $this->conn->prepare($query);
 
    // sanitize
    
    $this->firstName=htmlspecialchars(strip_tags($this->firstName));
    $this->lastName=htmlspecialchars(strip_tags($this->lastName));
    $this->email=htmlspecialchars(strip_tags($this->email));
    $this->password=htmlspecialchars(strip_tags($this->password));
 
    // bind the values
    $stmt->bindParam(':firstName', $this->firstName);
    $stmt->bindParam(':lastName', $this->lastName);
    $stmt->bindParam(':email', $this->email);
 
    // hash the password before saving to database
    $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
    $stmt->bindParam(':password', $password_hash);

    $users_arr = array();
    $users_arr['userCredential'] = array();
    $user_item = array(
        'id'=>$id,
        'firstName'=>$firstName,
        'lastName'=>$lastName,
        'email' => $email,
        'password' => $password,
        
        );
        
    array_push($users_arr['userCredential'],$user_item);
 
    // execute the query, also check if query was successful
    if($stmt->execute()){

        return true;
    }
 
    return false;
}
public function read(){

    $query="SELECT *
    FROM
      $this->table_name " ;

    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;   
}


// check if given email exist in the database
function emailExists(){
 
    // query to check if email exists
    $query = "SELECT `id`,`firstName`,`lastName`, `password`
            FROM " . $this->table_name . "
            WHERE email = ?
            LIMIT 0,1";
 
    // prepare the query
    $stmt = $this->conn->prepare( $query );
 
    // sanitize
    $this->email=htmlspecialchars(strip_tags($this->email));
 
    // bind given email value
    $stmt->bindParam(1, $this->email);
 
    // execute the query
    $stmt->execute();
 
    // get number of rows
    $num = $stmt->rowCount();
 
    // if email exists, assign values to object properties for easy access and use for php sessions
    if($num>0){
 
        // get record details / values
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
        // assign values to object properties
        $this->id = $row['id'];
        $this->firstName = $row['firstName'];
        $this->lastName = $row['lastName'];
        

    
        $this->password = $row['password'];
 
        // return true because email exists in the database
        return true;
    }
 
    // return false if email does not exist in the database
    return false;
}
 

}
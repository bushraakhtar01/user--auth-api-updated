<?php

if (isset($_SERVER["HTTP_ORIGIN"]) === true) {
	$origin = $_SERVER["HTTP_ORIGIN"];
	$allowed_origins = array(
    
		"https://localhost:3000",
	"http://localhost:3000",
        "http://localhost:3001",
        "https://192.168.2.106:3000"
	);
	if (in_array($origin, $allowed_origins, true) === true) {
		header('Access-Control-Allow-Origin: ' . $origin);
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type');
	
	}
	if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
		exit; // OPTIONS request wants only the policy, we can stop here
	}
}

include_once 'config/database.php';
include_once 'objects/user.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();


$user = new User($db);

$result = $user->read();
$num=$result->rowCount();

if($num>0){
$users_arr = array();
$users_arr['data'] = array();

while($row = $result->fetch(PDO::FETCH_ASSOC)){
extract($row);

$user_item = array(
'id'=>$id,
'firstName'=>$firstName,
'lastName'=>$lastName,

'email' => $email,
'password' => $password,

);

array_push($users_arr['data'],$user_item);
}
echo json_encode($users_arr);
}else{
echo json_encode(
array('message' => 'No post found') 
);

}
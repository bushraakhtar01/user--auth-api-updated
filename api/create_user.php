<?php
// required headers
// header("Access-Control-Allow-Origin: http://localhost/api_authentication/");

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
 
// instantiate product object
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$user->firstName = $_POST["firstName"];
$user->lastName = $_POST["lastName"];
$user->email = $_POST["email"];
$user->password = $_POST["password"];
 
// use the create() method here

// create the user
if(
    !empty($user->firstName) &&
    !empty($user->lastName) &&
    !empty($user->email) &&
    !empty($user->password) &&
    $user->create()
){
 
    // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array("message" => "User was created."));
}
 
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}
?>
 
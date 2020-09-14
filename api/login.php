<?php
// required headers

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
 
// database connection will be here
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/user.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate user object
$user = new User($db);
 
// check email existence here
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
// $user->firstName = $data->firstName;
// $user->lastName = $data->lastName;
$user->email = $data->email;

$email_exists = $user->emailExists();
 
// files for jwt will be here
// generate json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// generate jwt will be here
// check if email exists and if password is correct
if($email_exists && password_verify($data->password, $user->password)){
 
    $token = array(
       "iss" => $iss,
       "aud" => $aud,
       "iat" => $iat,
       "nbf" => $nbf,
       "data" => array(
           "id" => $user->id,
           "email" => $user->email,
           "firstName"=>$user->firstName,
           "lastName"=>$user->lastName

       )
    );
 
    // set response code
    http_response_code(200);
 
    // generate jwt
    $jwt = JWT::encode($token, $key);
    echo json_encode(
            array(
                "id"=>$user->id,
                "firstName"=>$user->firstName,
                "lastName"=>$user->lastName,
                "email"=> $user->email,

              
                "message" => "Successful login.",
                "jwt" => $jwt
            )
        );
 
}
 
// login failed will be here
// login failed
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user login failed
    echo json_encode(array("message" => "Login failed."));
}
?>
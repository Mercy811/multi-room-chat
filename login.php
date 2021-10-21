<?php
// Since we are sending a JSON response here (not an HTML document), 
// set the MIME Type to application/json
header("Content-Type: application/json");

// Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
// This will store the data into an associative array
$json_obj = json_decode($json_str, true);

// Access variable passed from js
// Equiavlent to $_POST[]
$user_input = $json_obj['username'];
$pwd_input = $json_obj['password'];


// Retrieve data from mysql 
require 'database.php';
$stmt = $mysqli->prepare("select user_id, password from users where username=?");

// if (!$stmt) {
//     $msg = "Query Prep Failed: %{$mysqli->error}\n";
//     exit;
// }

$stmt->bind_param('s', $user_input);
$stmt->execute();

$stmt->bind_result($count, $user_id, $password);
$stmt->fetch();
$stmt->close();


if (password_verify($pwd_input, $password)) {
    session_start();
    $_SESSION['username'] = $user_input;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));

    echo json_encode(array(
        "success" => true,
        "user_id" => $user_id,
    ));
    exit;
} else {
    echo json_encode(array(
        "success" => false
    ));
    exit;
}

?>

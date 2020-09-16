<?php

header ("Access-Control-Allow-Origin: *");
header ("Access-Control-Expose-Headers: Content-Length, X-JSON");
header ("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header ("Access-Control-Allow-Headers: *");
header ('Content-type: text/html; charset=utf-8');

$host="localhost";
$db="nosbir";
$username="nosbirEE";
$pass="*#3156/*EE";
$data = json_decode(file_get_contents("php://input"),true);

try{
    $db=new PDO("mysql:host=$host;dbname=$db;charset=utf8",$username,$pass);
}catch(PDOException $e){
    http_response_code(500);
    echo json_encode(array(
        "status"=>0,
        "error" => $e->getMessage()
    ));

    die();

}

?>
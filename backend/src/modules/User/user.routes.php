<?php
function handleUserRequest()
{
    global $requestMethod;
    $requestUri = $_SERVER["REQUEST_URI"];
    if (strpos($requestUri, '/register') !== false) {
        if ($requestMethod == "POST") {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (!empty($inputData)) {
                $storeUser = register($inputData);
            } else {
                $storeUser = register($_POST);
            }
            echo $storeUser;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/login') !== false) {
        if ($requestMethod == "POST") {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (!empty($inputData)) {
                $storeUser = login($inputData);
            } else {
                $storeUser = login($_POST);
            }
            echo $storeUser;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/getSingleUser') !== false) {
        if ($requestMethod == "GET") {
            if (isset($_GET['id'])) {
                $user  = getSingleUser($_GET);
                echo $user;
            }
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/getAllUsers') !== false) {
        if ($requestMethod == "GET") {
            $user  = getAllUsers($_GET);
            echo $user;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/updateUser') !== false) {
        if ($requestMethod == "PUT") {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (!empty($inputData)) {
                $updatedUser = updateUser($inputData);
            } else {
                $updatedUser = updateUser($_POST);
            }
            echo $updatedUser;
        } else WrongMethodResponse($requestMethod);
    } else {
        $data = [
            "status" => 404,
            'message' => $requestMethod . ' Not Found',
            "success" => false
        ];
        header("HTTP/1.0 404 Not Found");
        echo json_encode($data);
    }
}

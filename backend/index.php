<?php

    require './DB/connection.php';
    include './src/utils/Responses.php';
    include './src/utils/ApiFeatures.php';
    include './src/utils/zipFunctions.php';
    include './src/modules/Task/task.model.php';
    include './src/modules/Task/task.controller.php';
    include './src/modules/Task/task.routes.php';
    include './src/modules/User/user.model.php';
    include './src/modules/User/user.controller.php';
    include './src/modules/User/user.routes.php';
    include './src/modules/Image/image.model.php';
    include './src/modules/Image/Image.controller.php';
    include './src/modules/Image/Image.routes.php';
    include './src/modules/Label/label.model.php';
    include './src/modules/Label/label.controller.php';
    include './src/modules/Label/label.routes.php';
    include './src/modules/Type/type.model.php';
    include './src/modules/Type/type.controller.php';
    include './src/modules/Type/type.routes.php';

    header('Access-Control-Allow-Origin: *'); 
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-HTTP-Method-Override');
    header('Access-Control-Allow-Credentials: true');
    header('Content-Type: application/json');
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    // ini_set('display_errors', 0);
    // ini_set('log_errors', 1);     
    error_reporting(E_ALL);   

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    $requestMethod = $_SERVER["REQUEST_METHOD"];
    $requestUri = $_SERVER["REQUEST_URI"];

    if (strpos($requestUri, '/task') !== false) {
        handleTaskRequest();
    } elseif (strpos($requestUri, '/user') !== false) {
        handleUserRequest();
    } elseif (strpos($requestUri, '/image') !== false){
        handelImageRequest();
    } elseif (strpos($requestUri, '/label') !== false){
        handelLabelRequest();
    } elseif (strpos($requestUri, '/type') !== false) {
        handleTypeRequest();
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'Not Found']);
    }
?>
<?php
function handleTypeRequest()
{
    global $requestMethod;
    global $requestUri;

    if (strpos($requestUri, '/AddType') !== false) {
        if ($requestMethod == "POST") {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (!empty($inputData)) {
                $storeType = addType($inputData);
            } else {
                $storeType = addType($_POST);
            }
            echo $storeType;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/getAllTypesWithLabels') !== false) {
        if ($requestMethod == "GET") {
            $typesListWithLabels = getAllTypesWithLabels($_GET);
            echo $typesListWithLabels;
        } else {
            WrongMethodResponse($requestMethod);
        }
    } elseif (strpos($requestUri, '/getAllTypes') !== false) {
        if ($requestMethod == "GET") {
            $typesList = getAllTypes($_GET);
            echo $typesList;
        } else {
            WrongMethodResponse($requestMethod);
        }
    } elseif (strpos($requestUri, '/updateTypeWithLabels') !== false) {
        if ($requestMethod == "PUT") {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (!empty($inputData)) {
                $storeType = updateTypeWithLabels($inputData);
            } else {
                $storeType = updateTypeWithLabels($_POST);
            }
            echo $storeType;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/updateType') !== false) {
        if ($requestMethod == "PUT") {
            $updatedType = updateType($_GET);
            echo $updatedType;
        } else {
            WrongMethodResponse($requestMethod);
        }
    } elseif (strpos($requestUri, '/deleteType') !== false) {
        if ($requestMethod == "DELETE") {
            $deletedType = deleteType($_GET);
            echo $deletedType;
        } else {
            WrongMethodResponse($requestMethod);
        }
    } elseif (strpos($requestUri, '/addTypeWithLabels') !== false) {
        if ($requestMethod == "POST") {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (!empty($inputData)) {
                $storeType = addTypeWithLabels($inputData);
            } else {
                $storeType = addTypeWithLabels($_POST);
            }
            echo $storeType;
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

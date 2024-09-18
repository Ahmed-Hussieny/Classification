<?php
function handelImageRequest()
{
    global $requestMethod;
    global $requestUri;

    if (strpos($requestUri, '/AddImage') !== false) {
        if ($requestMethod == "POST") {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (!empty($inputData)) {
                $storeImage = addImage($inputData);
            } else {
                $storeImage = addImage($_POST);
            }
            echo $storeImage;
        } else WrongMethodResponse($requestMethod);
    }
    if (strpos($requestUri, '/AssignLabelToImage') !== false) {
        if ($requestMethod == "PUT") {
            $updatedImage = updateImage($_GET);
            echo $updatedImage;
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

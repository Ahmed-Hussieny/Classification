<?php
function handelLabelRequest()
{
    global $requestMethod;
    global $requestUri;

    if (strpos($requestUri, '/AddLabel') !== false) {
        if ($requestMethod == "POST") {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (!empty($inputData)) {
                $storeLabel = addLabel($inputData);
            } else {
                $storeLabel = addLabel($_POST);
            }
            echo $storeLabel;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/getAllLabelsForSpecificType') !== false) {
        if ($requestMethod == "GET") {
            $allLabelsForSpecificType = getAllLabelsForSpecificType($_GET);
            echo $allLabelsForSpecificType;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/deleteLabel') !== false) {
        if ($requestMethod == "DELETE") {
            $deletedLabel = deleteLabel($_GET);
            echo $deletedLabel;
        } else WrongMethodResponse($requestMethod);
    }
}

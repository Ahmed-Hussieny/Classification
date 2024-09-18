<?php
function addLabel($inputData)
{
    global $conn;

    $label = new Label([
        'title' => mysqli_real_escape_string($conn, $inputData['title']),
        'typeId' => mysqli_real_escape_string($conn, $inputData['typeId'])
    ]);
    if (!$label->title) {
        return ErrorResponse(200, "Bad Request", "title is required");
    }

    $result = $label->saveLabel($conn);
    if ($result) {
        $data = [
            "status" => 200,
            "message" => "Label added successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 Created");
        return json_encode($data);
    } else if ($result === false) {
        return ErrorResponse(200, "Bad Request", "Label already exists in the same type");
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting data");
    }
}

function getAllLabelsForSpecificType($userParams)
{
    global $conn;
    $typeId = mysqli_real_escape_string($conn, $userParams['typeId']);
    if (!$typeId) {
        return ErrorResponse(200, "Internal Server Error", "typeId is required");
    }

    $result = Label::fetchLabels($conn, $typeId);
    if ($result) {
        $data = [
            "status" => 200,
            "message" => "labels fetched Successfully",
            "data" => $result,
            "success" => true
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    }
    return ErrorResponse(200, "Internal Server Error", "Internal Server Error");
}

function deleteLabel($userParams)
{
    global $conn;
    $labelId = $userParams['id'];
    if (!$labelId) {
        return ErrorResponse(200, "Bad Request", "id is required");
    }
    $result = label::deleteLabel($conn, $labelId);
    if ($result) {
        $data = [
            "status" => 200,
            "message" => "Label deleted successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while deleting data");
    }
}

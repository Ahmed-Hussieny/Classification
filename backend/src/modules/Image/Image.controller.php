<?php
function addImage($inputData)
{
    global $conn;
    $image = new Image([
        'name' => mysqli_real_escape_string($conn, $inputData['name']),
        'taskId' => mysqli_real_escape_string($conn, $inputData['taskId'])
    ]);

    if (!$image->name) {
        return ErrorResponse(200, "Bad Request", "Name is required");
    }

    $result = $image->saveImage($conn);
    if ($result) {
        $data = [
            "status" => 200,
            "message" => "Image added successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 Created");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting data");
    }
}

function updateImage($userParams)
{
    global $conn;
    $image = new Image([
        'id' => mysqli_real_escape_string($conn, $userParams['imageId']),
        'labelId' => mysqli_real_escape_string($conn, $userParams['labelId'])
    ]);

    if (!$image->id || !$image->labelId) {
        return ErrorResponse(200, "Bad Request", "Image Id and Label Id is required");
    }
    $result = $image->updateImage($conn);
    if ($result) {
        $data = [
            "status" => 200,
            "message" => "Image updated successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while updating data");
    }
}

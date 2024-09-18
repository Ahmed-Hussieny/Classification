<?php
function WrongMethodResponse($requestMethod)
{
    $data = [
        "status" => 405,
        'message' => $requestMethod . ' Method Not Allowed',
        "success" => false
    ];
    header("HTTP/1.0 405 Method Not Allowed");
    echo json_encode($data);
}


function ErrorResponse($statusNumber, $statusMessage, $Message)
{
    $data = [
        "status" => $statusNumber,
        'message' => $Message,
        "success" => false
    ];
    header("HTTP/1.0 $statusNumber $statusMessage");
    echo json_encode($data);
}

function getPostParameter($key)
{
    return isset($_POST[$key]) ? $_POST[$key] : null;
}

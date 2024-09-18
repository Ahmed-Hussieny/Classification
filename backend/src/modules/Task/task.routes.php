<?php
function handleTaskRequest()
{
    global $requestMethod;
    global $requestUri;

    if (strpos($requestUri, '/AddTask') !== false) {
        if ($requestMethod == "POST") {
            $inputData = json_decode(file_get_contents("php://input"), true);
            if (!empty($inputData)) {
                $storeTask = addTask($inputData);
            } else {
                $storeTask = addTask($_POST);
            }
            echo $storeTask;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/getAllTasks') !== false) {
        if ($requestMethod == "GET") {
            $tasksList = getTasksList($_GET);
            echo $tasksList;
        } else {
            WrongMethodResponse($requestMethod);
        }
    } elseif (strpos($requestUri, '/getTasksListForSpecificUser') !== false) {
        if ($requestMethod == "GET") {
            $tasksList = getTasksListForSpecificUser($_GET);
            echo $tasksList;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/getSingleTask') !== false) {
        if ($requestMethod == "GET") {
            $task  = getSingleTask($_GET);
            echo $task;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/assignTaskToUser') !== false) {
        if ($requestMethod == "PUT") {
            $updateTask = assignTaskToUser($_GET);
            echo $updateTask;
        } else WrongMethodResponse($requestMethod);
    } elseif (strpos($requestUri, '/downloadTask') !== false) {
        if ($requestMethod == "GET") {
            $updateTask = downloadTaskFile($_GET);
            echo $updateTask;
        } else WrongMethodResponse($requestMethod);
    } else {
        return ErrorResponse(404, "Not Found", $requestMethod . ' Not Found');
    }
}

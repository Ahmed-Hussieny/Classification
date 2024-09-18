<?php

function addTask($taskInputData)
{
    global $conn;

    $task = new Task([
        'name' => getPostParameter('name'),
        'typeId' => getPostParameter('typeId'),
        'userId' => getPostParameter('userId'),
        'zip' => getPostParameter('zip')
    ]);

    if (!$task->name || !$task->typeId || !$task->userId || !isset($_FILES['zip'])) {
        return ErrorResponse(200, "Bad Request", "All data are required including the ZIP file");
    }

    $zipFile = $_FILES['zip'];
    $targetDir = "uploads/";

    if (!$task->handleZipUpload($zipFile, $targetDir)) {
        return ErrorResponse(200, "Internal Server Error", "Error moving the uploaded ZIP file");
    }

    $zipPath = $targetDir . basename($zipFile['name']);
    $imageNames = [];

    if (file_exists($zipPath) && $task->extractZip($zipPath, $targetDir, $imageNames)) {
        if (!$task->saveTask($conn)) {
            return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting data");
        }
        if (!$task->id) {
            return ErrorResponse(200, "Internal Server Error", "Task ID is not set after saving the task");
        }
        foreach ($imageNames as $imageName) {
            $image = new Image([
                'name' => $imageName,
                'taskId' => $task->id
            ]);
            if (!$image->saveImage($conn)) {
                return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting image data");
            }
        }

        $data = [
            "status" => 200,
            "message" => "Data inserted and file uploaded successfully",
            "file" => $task->zip,
            "images" => $imageNames,
            "success" => true
        ];
        header("HTTP/1.0 200 Created");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Error extracting the ZIP file or no images found");
    }
}

function getTasksListForSpecificUser($userParams)
{
    global $conn;
    $userId = $userParams['userId'];
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $searchName = $userParams['searchName'];

    if (!$userId) {
        return ErrorResponse(200, "Bad Request", "User Id is required");
    }
    $offset = ($page - 1) * $limit;

    $query = "SELECT * FROM tasks WHERE userId = '$userId' AND name LIKE '%" . $searchName . "%'";
    // $query .= " AND (name LIKE '%" .$searchName . "%'";
    $query .= " LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $tasks = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $task = new Task($row);
                $task->userId = user::fetchUser($conn, $userId);
                $task->typeId = type::fetchType($conn, $task->typeId);
                $task->images = Image::fetchImages($conn, $task->id);
                $tasks[] = $task;
            }
            $totalTasksQuery = "SELECT COUNT(*) as total FROM tasks WHERE userId = '$userId'";
            $totalResult = mysqli_query($conn, $totalTasksQuery);
            $totalRow = mysqli_fetch_assoc($totalResult);
            $totalTasks = $totalRow['total'];
            $data = [
                "status" => 200,
                "message" => "Data found",
                "success" => true,
                "data" => $tasks,
                "pagination" => [
                    "current_page" => $page,
                    "limit" => $limit,
                    "total_tasks" => $totalTasks,
                    "total_pages" => ceil($totalTasks / $limit),
                    "search" => $searchName
                ]
            ];
            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            return ErrorResponse(200, "OK", "No data found");
        }
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error: " . mysqli_error($conn));
    }
}

function getTasksList($taskParams)
{
    global $conn;
    $page = isset($taskParams['page']) ? intval($taskParams['page']) : 1;
    $limit = isset($taskParams['limit']) ? intval($taskParams['limit']) : 10;
    $searchName = isset($taskParams['searchName']) ? $taskParams['searchName'] : "";
    $searchUser = isset($taskParams['searchUser']) ? intval($taskParams['searchUser']) : 0;
    $searchDate = isset($taskParams['searchDate']) ? $taskParams['searchDate'] : "";

    $offset = ($page - 1) * $limit;
    $searchQuery = buildSearchQuery($conn, 'tasks', $searchName, $searchUser, $searchDate);
    $searchQuery .= " LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $searchQuery);

    if ($result) {
        $tasks = [];
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $task = new Task($row);
                $task->userId = user::fetchUser($conn, $task->userId);
                $task->typeId = type::fetchType($conn, $task->typeId);
                $task->images = Image::fetchImages($conn, $task->id);
                $tasks[] = $task;
            }

            $pagination = getPaginationData($conn, 'tasks', $searchName, $searchUser, $searchDate, $limit);

            $data = [
                "status" => 200,
                "message" => "Data found",
                "success" => true,
                "data" => $tasks,
                "pagination" => [
                    "current_page" => $page,
                    "limit" => $limit,
                    "total_tasks" => $pagination['total_data'],
                    "total_pages" => $pagination['total_pages'],
                    "searchDate" => $searchDate
                ]
            ];

            header("HTTP/1.0 200 OK");
            echo json_encode($data);
        } else {
            return ErrorResponse(200, "OK", "No data found");
        }
    } else {
        return ErrorResponse(500, "Internal Server Error", "Internal Server Error: " . mysqli_error($conn));
    }
}


function getSingleTask($taskParams)
{
    global $conn;
    $id = $taskParams['id'];

    if ($id == null) {
        return ErrorResponse(200, "Internal Server Error", "Id is required");
    }

    $taskId = mysqli_real_escape_string($conn, $id);
    $query = "SELECT * FROM tasks WHERE id = '$taskId' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $res = mysqli_fetch_assoc($result);
            $task = new Task($res);
            $task->images = Image::fetchImages($conn, $id);
            $task->labels = Label::fetchLabels($conn, $task->typeId);
            $data = [
                "status" => 200,
                "message" => "Task fetched Successfully",
                "success" => true,
                "data" => $task
            ];
            header("HTTP/1.0 200 OK");
            return json_encode($data);
        } else {
            return ErrorResponse(200, "Not Found", "No data found for this id");
        }
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error");
    }
}

function downloadTaskFile($userParams)
{
    global $conn;
    $taskId = $userParams['taskId'];
    if ($userParams['taskId'] == null) {
        return ErrorResponse(200, "Internal Server Error", "task id and user id are required");
    }
    $taskId = intval($taskId);
    if ($taskId <= 0) {
        return ErrorResponse(200, "Bad Request", "Invalid Task ID");
    }

    $query = "SELECT zip FROM tasks WHERE id = $taskId";
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $task = mysqli_fetch_assoc($result);
            $zipFileName = $task['zip'];
            $filePath = "/opt/lampp/htdocs/PHP/koraStatsProject/uploads/" . $zipFileName;

            if (file_exists($filePath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filePath));
                flush();
                readfile($filePath);
                exit;
            } else {
                return ErrorResponse(200, "Not Found", "File not found");
            }
        } else {
            return ErrorResponse(200, "Not Found", "Task not found");
        }
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error");
    }
}

function assignTaskToUser($userParams)
{
    global $conn;

    if ($userParams['taskId'] == null || $userParams['userId'] == null) {
        return ErrorResponse(200, "Internal Server Error", "task id and user id are required");
    }

    $taskId = mysqli_real_escape_string($conn, $userParams['taskId']);
    $userId = mysqli_real_escape_string($conn, $userParams['userId']);

    $query = "UPDATE tasks SET userId = '$userId' WHERE id = '$taskId'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = [
            "status" => 200,
            "message" => "Task assigned successfully",
            "success" => true
        ];
        header('HTTP/1.0 200 OK');
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Failed to assign task: " . mysqli_error($conn));
    }
}

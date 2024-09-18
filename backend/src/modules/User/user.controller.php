<?php
function register($taskInputData)
{
    global $conn;

    $user = new User([
        'name' => mysqli_real_escape_string($conn, $taskInputData['name']),
        'password' => mysqli_real_escape_string($conn, $taskInputData['password']),
        'userType' => mysqli_real_escape_string($conn, $taskInputData['userType']),
    ]);

    if (!$user->name || !$user->password) {
        return ErrorResponse(200, "Bad Request", "Name and password are required");
    }

    $result = $user->saveUser($conn);

    if (!$result) {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting data");
    }

    $data = [
        "status" => 200,
        "message" => "User registered successfully",
        "success" => true
    ];
    header("HTTP/1.0 200 Created");
    return json_encode($data);
}

function login($taskInputData)
{
    global $conn;
    $user = new User([
        'name' => mysqli_real_escape_string($conn, $taskInputData['name']),
        'password' => mysqli_real_escape_string($conn, $taskInputData['password'])
    ]);
    if (!$user->name || !$user->password) {
        return ErrorResponse(200, "Bad Request", "Name and password are required");
    }

    $result = $user->login($conn);

    if (!$result) {
        return ErrorResponse(200, "Unauthorized", "Invalid username or password");
    }

    $data = [
        "status" => 200,
        "message" => "Login successful",
        "success" => true,
        "Data" => $result
    ];
    header("HTTP/1.0 200 OK");
    return json_encode($data);
}

function getSingleUser($userParams)
{
    global $conn;
    $userId = mysqli_real_escape_string($conn, $userParams['id']);
    if (!$userId) {
        return ErrorResponse(200, "Internal Server Error", "Id is required");
    }

    $result = user::fetchUser($conn, $userId);
    if ($result) {
        $data = [
            "status" => 200,
            "message" => "User fetched Successfully",
            "data" => $result,
            "success" => true
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    }
    return ErrorResponse(200, "Internal Server Error", "Internal Server Error");
}

function getAllUsers($taskParams)
{
    global $conn;
    $page = isset($taskParams['page']) ? intval($taskParams['page']) : 1;
    $limit = isset($taskParams['limit']) ? intval($taskParams['limit']) : 10;
    $searchName = isset($taskParams['searchName']) ? $taskParams['searchName'] : "";

    $offset = ($page - 1) * $limit;
    $searchQuery = buildSearchQuery($conn, 'users', $searchName, null, null);
    $searchQuery .= " LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $searchQuery);
    if ($result) {
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $pagination = getPaginationData($conn, 'users', $searchName, null, null, $limit);

        $data = [
            "status" => 200,
            "message" => "Users fetched Successfully",
            "success" => true,
            "data" => $users,
            "pagination" => [
                "current_page" => $page,
                "limit" => $limit,
                "total_users" => $pagination['total_data'],
                "total_pages" => $pagination['total_pages'],
            ]
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    }
    return ErrorResponse(200, "Internal Server Error", "Internal Server Error");
}

function updateUser($taskInputData)
{
    global $conn;
    $user = new User([
        'id' => mysqli_real_escape_string($conn, $taskInputData['id']),
        'name' => mysqli_real_escape_string($conn, $taskInputData['name']),
        'userType' => mysqli_real_escape_string($conn, $taskInputData['userType']),
    ]);

    if (!$user->id) {
        return ErrorResponse(200, "Bad Request", "Id is required");
    }

    $result = $user->updateUser($conn);
    if ($result) {
        $data = [
            "status" => 200,
            "message" => "User updated successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    }
    return ErrorResponse(200, "Internal Server Error", "Internal Server Error");
}

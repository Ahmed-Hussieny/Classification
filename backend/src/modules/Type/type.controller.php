<?php
function addType($taskInputData)
{
    global $conn;
    $type = new Type([
        'name' => mysqli_real_escape_string($conn, $taskInputData['name'])
    ]);

    if (!$type->name) {
        return ErrorResponse(200, "Bad Request", "Name is required");
    }

    $result = $type->saveType($conn);

    if ($result) {
        $data = [
            "status" => 200,
            "message" => "Type added successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 Created");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting data");
    }
}

function addTypeWithLabels($taskInputData)
{
    global $conn;
    $type = new Type([
        'name' => mysqli_real_escape_string($conn, $taskInputData['name'])
    ]);
    $labels = $taskInputData['labels'];
    if (!$type->name) {
        return ErrorResponse(200, "Bad Request", "Name is required");
    }
    $result = $type->saveType($conn);
    if ($result) {
        $typeId = $type->id;
        foreach ($labels as $labelTitle) {
            $label = new Label([
                'typeId' => $typeId,
                'title' => $labelTitle["title"]
            ]);
            if (!$label->saveLabel($conn)) {
                return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting data");
            }
        }
        $data = [
            "status" => 200,
            "message" => "Type added successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 Created");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting data");
    }
}

function getAllTypes($taskParams)
{
    global $conn;
    $page = isset($taskParams['page']) ? intval($taskParams['page']) : 1;
    $limit = isset($taskParams['limit']) ? intval($taskParams['limit']) : 10;
    $searchName = isset($taskParams['searchName']) ? $taskParams['searchName'] : "";

    $offset = ($page - 1) * $limit;
    $searchQuery = buildSearchQuery($conn, 'types', $searchName, null, null);
    $searchQuery .= " LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $searchQuery);
    if ($result) {
        $types = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $pagination = getPaginationData($conn, 'types', $searchName, null, null, $limit);

        $data = [
            "status" => 200,
            "message" => "Types fetched successfully",
            "data" => $types,
            "success" => true,
            "pagination" => [
                "current_page" => $page,
                "limit" => $limit,
                "total_types" => $pagination['total_data'],
                "total_pages" => $pagination['total_pages'],
            ]
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while fetching data");
    }
}

function getAllTypesWithLabels($taskParams)
{
    global $conn;
    $page = isset($taskParams['page']) ? intval($taskParams['page']) : 1;
    $limit = isset($taskParams['limit']) ? intval($taskParams['limit']) : 10;
    $searchName = isset($taskParams['searchName']) ? $taskParams['searchName'] : "";

    $offset = ($page - 1) * $limit;
    $searchQuery = buildSearchQuery($conn, 'types', $searchName, null, null);
    $searchQuery .= " LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $searchQuery);

    if ($result) {
        $types = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($types as &$type) {
            $type = (object) $type;
            $type->labels = Label::fetchLabels($conn, $type->id);
        }
        $pagination = getPaginationData($conn, 'types', $searchName, null, null, $limit);

        $data = [
            "status" => 200,
            "message" => "Types fetched successfully",
            "success" => true,
            "data" => $types,
            "pagination" => [
                "current_page" => $page,
                "limit" => $limit,
                "total_types" => $pagination['total_data'],
                "total_pages" => $pagination['total_pages'],
            ],
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while fetching data");
    }
}


function updateType($userParams)
{
    global $conn;
    $type = new Type([
        'id' => mysqli_real_escape_string($conn, $userParams['id']),
        'name' => mysqli_real_escape_string($conn, $userParams['name'])
    ]);

    if (!$type->id || !$type->name) {
        return ErrorResponse(200, "Bad Request", "Type Id and Name is required");
    }

    $result = $type->updateType($conn);

    if ($result) {
        $data = [
            "status" => 200,
            "message" => "Type updated successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while updating data");
    }
}

function updateTypeWithLabels($taskInputData)
{
    global $conn;
    $type = new Type([
        'id' =>  mysqli_real_escape_string($conn, $taskInputData['id']),
        'name' => mysqli_real_escape_string($conn, $taskInputData['name'])
    ]);
    $labels = $taskInputData['labels'];
    if (!$type->name) {
        return ErrorResponse(200, "Bad Request", "Name is required");
    }
    $result = $type->updateType($conn);
    if ($result) {
        $typeId = $type->id;
        foreach ($labels as $labelTitle) {
            $label = new Label([
                'typeId' => $typeId,
                'title' => $labelTitle["title"]
            ]);
            if (!$label->saveLabel($conn)) {
                return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting data");
            }
        }
        $data = [
            "status" => 200,
            "message" => "Type added successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 Created");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while inserting data");
    }
}

function deleteType($userParams)
{
    global $conn;
    $type = new Type([
        'id' => mysqli_real_escape_string($conn, $userParams['typeId']),
        "name" => "delete"
    ]);

    if (!$type->id) {
        return ErrorResponse(200, "Bad Request", "Type Id is required");
    }

    $result = $type->deleteType($conn);

    if ($result) {
        $data = [
            "status" => 200,
            "message" => "Type deleted successfully",
            "success" => true
        ];
        header("HTTP/1.0 200 OK");
        return json_encode($data);
    } else {
        return ErrorResponse(200, "Internal Server Error", "Internal Server Error while deleting data");
    }
}

<?php

function getPaginationData($conn, $tabel, $searchName, $searchUser, $searchDate, $limit)
{
    $totalDataQuery = buildSearchQuery($conn, $tabel, $searchName, $searchUser, $searchDate);
    $totalDataQuery = str_replace("SELECT *", "SELECT COUNT(*) as total", $totalDataQuery);
    $totalResult = mysqli_query($conn, $totalDataQuery);
    $totalRow = mysqli_fetch_assoc($totalResult);
    $totalData = $totalRow['total'];

    return [
        'total_data' => $totalData,
        'total_pages' => ceil($totalData / $limit),
    ];
}

function buildSearchQuery($conn, $tabel, $searchName, $searchUser, $searchDate)
{
    $query = "SELECT * FROM $tabel WHERE 1=1";

    if ($searchName) {
        $query .= " AND name LIKE '%" . mysqli_real_escape_string($conn, $searchName) . "%'";
    }

    if ($searchUser) {
        $query .= " AND userId = " . intval($searchUser);
    }

    if ($searchDate) {
        $query .= " AND DATE(createdAt) = '" . mysqli_real_escape_string($conn, $searchDate) . "'";
    }

    return $query;
}

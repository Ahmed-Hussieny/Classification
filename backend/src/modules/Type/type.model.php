<?php
class Type
{
    public $id;
    public $name;

    public function __construct($typeData)
    {
        $this->id = $typeData['id'] ?? null;
        $this->name = $typeData['name'];
    }

    public function saveType($conn)
    {
        $query = "INSERT INTO types (name) VALUES ('$this->name')";
        if (mysqli_query($conn, $query)) {
            $this->id = mysqli_insert_id($conn);
            return true;
        } else {
            return false;
        }
    }

    public function updateType($conn)
    {
        $query = "UPDATE `types` SET `name` = '$this->name' WHERE id = $this->id";
        return mysqli_query($conn, $query);
    }
    public function deleteType($conn)
    {
        $query = "DELETE FROM `types` WHERE id = $this->id";
        return mysqli_query($conn, $query);
    }

    public static function fetchType($conn, $id)
    {
        $query = "SELECT * FROM types WHERE id='$id'";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_assoc($result);
    }

    public static function fetchTypes($conn)
    {
        $query = "SELECT * FROM types";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

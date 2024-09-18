<?php
class Image
{
    public $id;
    public $name;
    public $taskId;
    public $labelId;

    public function __construct($imageData)
    {
        $this->id = $imageData['id'] ?? null;
        $this->name = $imageData['name'];
        $this->taskId = $imageData['taskId'];
        $this->labelId = $imageData['labelId'] ?? null;
    }

    public function saveImage($conn)
    {
        $query = "INSERT INTO images (name, taskId) VALUES ('$this->name', '$this->taskId')";
        return mysqli_query($conn, $query);
    }

    public function updateImage($conn)
    {
        $query = "UPDATE `images` SET `labelId` = $this->labelId WHERE id = $this->id";
        return mysqli_query($conn, $query);
    }

    public static function fetchImages($conn, $id)
    {
        $query = "SELECT * FROM images WHERE taskId = '$id'";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

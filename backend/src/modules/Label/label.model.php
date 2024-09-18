<?php
class Label
{
    public $id;
    public $title;
    public $typeId;

    public function __construct($labelData)
    {
        $this->id = $labelData['id'] ?? null;
        $this->title = $labelData['title'];
        $this->typeId = $labelData['typeId'];
    }

    public function saveLabel($conn)
    {
        $query = "SELECT * FROM labels WHERE title = '$this->title' AND typeId = '$this->typeId'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            return false;
        }
        $query = "INSERT INTO labels (title, typeId) VALUES ('$this->title', '$this->typeId')";
        return mysqli_query($conn, $query);
    }

    public static function deleteLabel($conn, $id)
    {
        $query = "DELETE FROM labels WHERE id = '$id'";
        return mysqli_query($conn, $query);
    }

    public static function fetchLabels($conn, $id)
    {
        $query = "SELECT * FROM labels WHERE typeId = '$id'";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

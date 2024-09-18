<?php
class Task
{
    public $id;
    public $name;
    public $typeId;
    public $userId;
    public $zip;
    public $createdAt;
    public $updatedAt;
    public $images;
    public $labels;


    public function __construct($taskData)
    {
        $this->id = $taskData['id'] ?? null;
        $this->name = $taskData['name'];
        $this->typeId = $taskData['typeId'];
        $this->userId = $taskData['userId'];
        $this->zip = $taskData['zip'];
        $this->createdAt = $taskData['createdAt'] ?? null;
        $this->updatedAt = $taskData['updatedAt'] ?? null;
    }

    public function saveTask($conn)
    {
        $query = "INSERT INTO tasks (name, typeId, userId, zip) VALUES ('$this->name', '$this->typeId', '$this->userId', '$this->zip')";
        if (mysqli_query($conn, $query)) {
            $this->id = mysqli_insert_id($conn);
            return true;
        } else {
            return false;
        }
    }

    public function handleZipUpload($zipFile, $targetDir)
    {
        if ($zipFile['error'] == 0) {
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $zipFileName = basename($zipFile['name']);
            $targetFilePath = $targetDir . $zipFileName;

            if (move_uploaded_file($zipFile['tmp_name'], $targetFilePath)) {
                $this->zip = $zipFileName;
                return true;
            }
        }
        return false;
    }

    function extractZip($zipPath, $targetDir, &$imageNames)
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileName = $zip->getNameIndex($i);
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                if (in_array(strtolower($fileExt), ['jpg', 'PNG', 'svg', 'jpeg', 'png', 'gif'])) {
                    $zip->extractTo($targetDir, $fileName);
                    $imageNames[] = $fileName;
                }
            }
            $zip->close();
            return true;
        } else {
            return false;
        }
    }
}

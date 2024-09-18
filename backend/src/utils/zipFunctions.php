<?php
    function handleZipUpload($zipFile, $targetDir)
    {
        if ($zipFile['error'] == 0) {
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $zipFileName = basename($zipFile['name']);
            $targetFilePath = $targetDir . $zipFileName;

            if (move_uploaded_file($zipFile['tmp_name'], $targetFilePath)) {
                return $zipFileName;
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
                if (in_array(strtolower($fileExt), ['jpg', 'PNG','svg', 'jpeg', 'png', 'gif'])) {
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
?>
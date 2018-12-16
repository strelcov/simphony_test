<?php
namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file, $id, $dateFolder = null)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $resultFileDir = ($dateFolder === null ? date('Y-m') : $dateFolder) . DIRECTORY_SEPARATOR . $id;
        $resultFileName = $resultFileDir . DIRECTORY_SEPARATOR . $fileName;
        $dir = $this->getTargetDir() . DIRECTORY_SEPARATOR . $resultFileDir;
        $file->move($dir, $fileName);
        return $resultFileName;
    }

    public function deleteFile($name)
    {
        $fullPath = $this->getTargetDir() . DIRECTORY_SEPARATOR . $name;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return true;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }

    public function emptyDirectory($dirname, $selfDelete = false)
    {
        if (is_dir($dirname))
            $dirHandle = opendir($dirname);
        if (!$dirHandle)
            return false;
        while ($file = readdir($dirHandle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file))
                    @unlink($dirname . "/" . $file);
                else
                    $this->emptyDirectory($dirname . '/' . $file, true);
            }
        }
        closedir($dirHandle);
        if ($selfDelete) {
            @rmdir($dirname);
        }
        return true;
    }
}
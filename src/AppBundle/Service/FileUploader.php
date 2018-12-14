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

    public function upload(UploadedFile $file, $id)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $resultFileDir = date('Y-m') . DIRECTORY_SEPARATOR . $id;
        $resultFileName = $resultFileDir . DIRECTORY_SEPARATOR . $fileName;
        $dir = $this->getTargetDir() . DIRECTORY_SEPARATOR . $resultFileDir;
        $file->move($dir, $fileName);
        return $resultFileName;
    }

    public function deleteFile($name)
    {
        $fullPath = $this->getTargetDir() . DIRECTORY_SEPARATOR . $name;
    }

    public function deleteFolder($id)
    {
        //$fullPath = $this->getTargetDir() . DIRECTORY_SEPARATOR . $name;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }
}
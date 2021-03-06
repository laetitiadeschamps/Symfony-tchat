<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FileUpload
{
    private $targetDirectory;
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /** Method to upload a file
    * @param UploadedFile
    * @param string
    * @return string or null
    */
    public function upload(UploadedFile $file, $targetDirectory =null)
    {
        $this->targetDirectory=$targetDirectory ?? $_ENV['UPLOAD_FOLDER'];
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $fileName);
            return $fileName;
        } catch (FileException $e) {
            return $e;
        }
        return null;
        
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
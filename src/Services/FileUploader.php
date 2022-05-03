<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader {

    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger){
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file){
        $origineFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFileName = $this->slugger->slug($origineFileName);
        $fileName = $safeFileName. '-'.uniqid().'.'. $file->guessExtension();

        try{
            $file->move($this->getTargetDirectory(), $fileName);
        }catch (FileException $e){

        }

        return $fileName;

    }

    public function getTargetDirectory(){
        return $this->targetDirectory;
    }

}
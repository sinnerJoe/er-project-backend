<?php
require_once(__DIR__."/../Model.php");

class Image extends Model {

    public static $IMAGE_FOLDER = "previews";

    public function __construct(){
        parent::__construct(__DIR__);
    }
    
    public function saveImage($content, $userId) {
       $filename = uniqid($userId.'_', true).'.png';
       $this->saveFile($filename, $content);

       return $this->insert('createImage.sql', ['filepath' => $filename])->lastInsertId();
    }

    public function deleteImage($imageId, $filename) {
        $this->deleteImageFile($filename);
        return $this->execute('deleteImage.sql', ['image_id' => $imageId]);
    }

    public function deleteMultipleImages($ids) {
        if(count($ids) > 0) {
            return $this->delete('images', [inOp('image_id', $ids)], $ids);
        }
    }

    private function getImagesOfUser($userId) {
        return $this->fetchAll('getImagesOfUser.sql', ['user_id' => $userId]);
    }

    public function deleteImagesOfUser($userId) {
        $data = $this->getImagesOfUser($userId);
        $ids = array_map(function($image) {
            return $image['image_id'];
        }, $data);
        
        $this->deleteMultipleImages($ids);
        foreach($data as $image) {
            $this->deleteImageFile($image['filepath']);
        }
    }

    public function deleteImageFile($filename) {
        $fullPath = $this->getFullPath($filename);
        if(file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function getImagePath($imageId) {
        return $this->fetchOne('getFilePath.sql', ['image_id' => $imageId]);
    }

    public static function getImageUrl($imageName) {
        return self::$IMAGE_FOLDER.'/'.$imageName;
    }
    
    public function getFullPath($filename) {
        return __DIR__.'/../../'.(self::$IMAGE_FOLDER).'/'.$filename;
    }

    private function saveFile($filename, $content) {
        $imageContent = explode(',', $content)[1];
        $file = fopen($this->getFullPath($filename), 'wb');
        $this->decodeWrite($file, $imageContent);
        fclose($file);
    }

    private function decodeWrite($file, $encoded) {
        for ($i=0; $i < ceil(strlen($encoded)/256); $i++) {
            $chunk = substr($encoded,$i*256,256);
            $sanitizedChunk = str_replace(' ', '+', $chunk);
            fwrite($file, base64_decode($sanitizedChunk));
        }
    }
}
<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileProcess
{
    public $diskPublic = "public";

    public function getFullLink(mixed $path){
        if (!is_null($path)){
            if (is_array($path)){
                $temp = [];
                foreach ($path as $item){
                    $temp[] = url($item);
                }
                return $temp;
            }
            return url($path);
        }
        return null;
    }

    public function uploadFile($file,$folder = '',$disk = null){
        $ext = $file->getClientOriginalExtension();
        $file_base_name = str_replace('.' . $ext, '', $file->getClientOriginalName());
        $fileNameFinal = strtolower(time() . Str::random(5) . '-' . Str::slug($file_base_name)) . '.' . $ext;
        $path = Storage::disk($disk ?? $this->diskPublic)->putFileAs($folder , $file, $fileNameFinal);
        return "storage/{$path}";
    }

    public function deleteFile($path, $disk = null){
        $path = ltrim($path, 'storage/');
        $disk ??= $this->diskStorage;
        return Storage::disk($disk)->delete($path);
    }
}

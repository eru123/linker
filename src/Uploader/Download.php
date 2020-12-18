<?php

namespace Linker\Uploader;

class Download {
    public static function file(string $path,array $config = []){
        if(file_exists($path)){
            $description = $config["description"] ?? "File Download";
            $mime = $config["mime"] ?? "application/octet-stream";
            $ext = pathinfo(basename($path),PATHINFO_EXTENSION);
            $filename = isset($config["filename"]) ? rtrim($config["filename"],$ext).".".$ext : basename($path);
            $encoding = $config["encoding"] ??"binary";
            $expires = $config["expires"] ?? 0;
            $cache_control = $config["cache"] ??"must-revalidate, post-check=0, pre-check=0";
            $pragma = $config["pragma"] ?? "public";
            $filesize = filesize($path);
            header("Content-Description: $description");
            header("Content-Type: $mime");
            header("Content-Disposition: attachment; filename=$filename");
            header("Content-Transfer-Encoding: $encoding");
            header("Expires: $expires");
            header("Cache-Control: $cache_control");
            header("Pragma: $pragma");
            header("Content-Length: $filesize");
            ob_clean();
            flush();
            readfile($path);
            exit;
        } else throw new \Exception("File does not exists");
    }
}
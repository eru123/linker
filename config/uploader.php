<?php

// WARNING: IF YOU INSTALL LINKER  FROM LINKER INSTALLER
// DO NOT CHANGE ARRAY KEYS IN THIS FILE OTHERWISE YOU
// KNOW WHAT YOU ARE DOING.

return [

    // Set 'upload' to TRUE if you want to use file upload
    // Type: Boolean
    "upload" => FALSE,

    // Set 'download' to TRUE if you want to use file download
    // Type: Boolean
    "download" => FALSE,

    
    // ******************************
    // CONFIGURATIONS FOR FILE UPLOAD
    // ******************************

    // Uploaded files directory
    // Type: String
    "dir" => __DIR__."/../uploads",

    // Allowed file extensions
    // Separated by single spaces
    // Type: String
    // Example value: "txt mp4 mp3 jpg jpeg txt"
    "exts" => "txt mp4 mp3 jpg jpeg txt",

    // Allowed file upload size in Mega Bytes (MB)
    // This is a separate setting from php.ini
    // php.ini max_file_upload should be equal or larger
    // Type: Number
    "max_size" => 2

];


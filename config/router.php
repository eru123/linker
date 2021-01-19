<?php

// WARNING: IF YOU INSTALL LINKER  FROM LINKER INSTALLER
// DO NOT CHANGE ARRAY KEYS IN THIS FILE OTHERWISE YOU
// KNOW WHAT YOU ARE DOING.

return [

    // Set 'use' to TRUE if you want to use auto routing
    // Type: Boolean
    "use" => TRUE,

    // Automatic rendering
    // If set to TRUE, files will be rendered automatically
    // else it will only return a path of the requested file
    // Type: Boolean
    "render" => TRUE,

    // Auto routing directory
    // If using auto route, no file can be accessed outside
    // Type: String
    "dir" => __DIR__."/../public",
    
    // Auto index file
    // if the file or page is not specified but the directory is defined
    // the index file is searched in different extensions , if not found 
    // in the directory 
    // indexes separated by single spaces
    // Type: String
    "index" => "index",

    // Used extension for file search
    // This extensions is use from left to right
    // Type: String 
    "exts" => "php html",

    // Directory level
    // Root document directory is always 0
    // Change it to 1 or above if you install
    // this project on a sub directory
    // Type: Integer
    "level" => 0,

    // 404 Page path
    // 404 page must be inside the auto routing directory
    // 404 page path must be set as if you were inside the auto routing directory
    // Example original path: public/notfound.php 
    // Example 404 page path: notfound.php
    // Type: String || NULL
    "error" => "error.php",

    // File exclusion
    // Array values must be a string 
    // Array values is REGEX format
    // Type: Array
    "exclude" => []
    
];


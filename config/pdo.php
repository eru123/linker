<?php

// WARNING:IF YOU INSTALL LINKER FROM LINKER INSTALLER
// DO NOT CHANGE ARRAY KEYS IN THIS FILE OTHERWISE YOU
// KNOW WHAT YOU ARE DOING.

return [
    
    // Set 'use' to TRUE if you want to use PDO
    "use" => FALSE, // Type: Boolean

    // Set 'model' to TRUE if you want to use PDOModel
    // Type: Boolean
    "model" => FALSE, 

    // pre made models
    // 'model values' must be an existing table name
    // Type: Array
    "models" => [],

    // PDO username
    // Type: String
    "user" => "root", 

    // PDO password 
    // Type: String
    "pass" => "", 

    // PDO hostname
    // Type: String
    "host" => "localhost", 
    
    // PDO database name
    // Type: String
    "db" => "mysql", 

    // Database schema
    // format [tb => [col,col,...],tb => [col,col,...], ....]
    // Type: Array
    "schema" => [],

    // Database Schema method
    // Choices (normal|force|dynamic) default - dynamic
    // Executed when schema is not an emtpy array
    // Be careful using dynamic method, it can alter your selected tables
    // that is specified on your schema
    // Type: String
    "schema_method" => "dynamic"
];
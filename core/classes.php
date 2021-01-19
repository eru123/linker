<?php 

// WARNING:IF YOU INSTALL LINKER FROM LINKER INSTALLER
// DO NOT CHANGE ARRAY KEYS IN THIS FILE OTHERWISE YOU
// KNOW WHAT YOU ARE DOING.

return [
    "ROUTER"            => \Linker\Router\Auto::class,
    "ROUTER_REQ"        => \Linker\Router\Request::class,
    "UPLOAD"            => \Linker\Uploader\Upload::class,
    "DOWNLOAD"          => \Linker\Uploader\Download::class,
    "FS"                => \Linker\FileSystem\FileSystem::class,
    "KV"                => \Linker\Frecbase\Keyval::class,
    "QUERY"             => \Linker\Request\Query::class,
    "URI"               => \Linker\Request\URI::class,
    "PDO"               => \Linker\Database\PDO::class,
    "PDOMODEL"          => \Linker\Database\PDO\Model::class
];
<?php

// WARNING:IF YOU INSTALL LINKER FROM LINKER INSTALLER
// DO  NOT  CHANGE THIS FILE UNLESS YOU  KNOW WHAT YOU
// YOU ARE DOING.

$paths = include_once(__DIR__."/paths.php");

require_once $paths["autoload"];

return new \Linker\Application($paths);
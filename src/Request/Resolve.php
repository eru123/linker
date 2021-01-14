<?php

namespace Linker\Request;

class Resolve
{
    public static function json($a): void
    {
        header('Content-Type: application/json');
        echo json_encode((array) $a);
        exit;
    }
}
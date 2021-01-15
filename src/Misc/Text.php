<?php

namespace Linker\Misc;

class Text {
    public static function randChars($l = NULL, $str = NULL): string
    {
        $str_set = $str ?? "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
        $str_obj = str_split($str_set);
        $str_cnt = count($str_obj) - 1;

        $x = "";

        $l = $l ?? (rand(0, $str_cnt) / 4);
        if ($l < 1) $l = 1;
        for ($i = 0; $i < $l; $i++) {
            $x .= (string)$str_obj[rand(0, $str_cnt)];
        }
        return $x;
    }
}
<?php

namespace Linker\Misc;

class Seconds {
    public static function min(int $min) {
        return 60 * $min;
    }
    public static function hour(int $hr){
        return self::min($hr * 60);
    }
    public static function day(int $day){
        return self::hour($day * 24);
    }
    public static function week(int $week){
        return self::day($week * 7);
    }
    public static function month(int $month) {
        return self::day($month * 30);
    }
    public static function year(int $year){
        return self::day($year * 365);
    }
}
<?php

namespace Linker\Crypt;

class Core {
    public static function lzw_decompress($Ta){

        $ec = 256;
        $Ua = 8;
        $nb = [];
        $Ug = 0;
        $Vg = 0;

        for($s = 0; $s < strlen($Ta); $s++){
            $Ug=($Ug<<8)+ord($Ta[$s]);
            $Vg+=8;
            if($Vg >= $Ua){
                $Vg -= $Ua;
                $nb[] = $Ug >> $Vg;
                $Ug&=(1 << $Vg) -1;
                $ec++;
                if($ec >> $Ua) $Ua++;
            }
        }

        $dc=range("\0","\xFF");

        $H="";

        foreach($nb as $s => $mb){
            $tc = $dc[$mb];
            if (!isset($tc)) $tc = $tj.$tj[0];
            $H .= $tc;
            if($s) $dc[] = $tj.$tc[0];
            $tj = $tc;
        } 

        return$H;
    }
    public static function lzw_compress($string) {
        
        // compression
        $dictionary = array_flip(range("\0", "\xFF"));
        $word = "";
        $codes = array();
        for ($i=0; $i <= strlen($string); $i++) {
            $x = @$string[$i];
            if (strlen($x) && isset($dictionary[$word . $x])) {
                $word .= $x;
            } elseif ($i) {
                $codes[] = $dictionary[$word];
                $dictionary[$word . $x] = count($dictionary);
                $word = $x;
            }
        }
        // convert codes to binary string
        $dictionary_count = 256;
        $bits = 8; 
        $return = "";
        $rest = 0;
        $rest_length = 0;
        foreach ($codes as $code) {
            $rest = ($rest << $bits) + $code;
            $rest_length += $bits;
            $dictionary_count++;
            if ($dictionary_count >> $bits) {
                $bits++;
            }
            while ($rest_length > 7) {
                $rest_length -= 8;
                $return .= chr($rest >> $rest_length);
                $rest &= (1 << $rest_length) - 1;
            }
        }
        return $return . ($rest_length ? chr($rest << (8 - $rest_length)) : "");
    }
    public static function blow(string $str, string $key): string
    {
        for ($i = 0; $i < strlen($str); $i++) {
            $str[$i] = $str[$i] ^ $key[$i % strlen($key)];
        }
        return $str;
    }
    public static function encode(string $str, string $key): string
    {
        $hash = self::blow($str, $key);
        return base64_encode($hash);
    }
    public static function decode(string $encoded, string $key): string
    {
        $hash = base64_decode($encoded);
        return self::blow($hash, $key);
    }
}
<?php

function randChars($l = NULL, $str = NULL): string
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


include_once __DIR__."/vendor/autoload.php";

use Linker\PDO\Core as PDO;
use Linker\PDO\Model;
use Linker\Request\Query;
use Linker\Request\URI;
use Linker\Frecbase\Keyval;
use Linker\Frecbase\Emitation as FBE;
use Linker\Cache\Memory as Cache;
use Linker\Array\SearchRow as Search;
use Linker\Misc\Text;
$pdo = new PDO([
	"host" => "localhost",
	"user" => "admin",
	"pass" => "admin",
	"db" => "test"
]);

$model = new Model('aaaa',$pdo);

$query = new Query();
$uri = new URI();

$cache = new Cache();

$fb = new FBE("public/db","test");

$fb->create_database("skidd")->select_database("skidd")->create_table("users")->select_table("users");
$fb->create_data("test_id",["name"=>"jericho","age"=>14]);
$fb->select_id("test_id")->select_field("name");
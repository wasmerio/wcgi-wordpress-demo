<?php

/// Use this if you want to use with local php:
/// cd new && php -S localhost:8000
// define("DB_PATH", dirname(__DIR__)."/db/.ht.sqlite");

/// Use this if you want to use with Wasmer:
/// cd new && wasmer-dev run-unstable .. --mapdir=/db:../db
define("DB_PATH", "/db/.ht.sqlite");

echo "DB at: ". DB_PATH;

class MyDB extends SQLite3 {
    function __construct() {
       $this->open(DB_PATH);
    }
 }
 
 $db = new MyDB();
 if(!$db) {
    echo $db->lastErrorMsg();
 } else {
    echo "Opened database successfully\n";
 }

 $sql =<<<EOF
    SELECT * from wp_posts;
EOF;

 $ret = $db->query($sql);
 if (!$ret) {
    echo $db->lastErrorMsg();
 }
 else {
 while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
    var_dump($row);
 }
 echo "Operation done successfully\n";
}
 $db->close();
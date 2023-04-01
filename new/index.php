<?php

// define("DB_PATH", dirname(__DIR__)."/db/.ht.sqlite");
// echo DB_PATH;
define("DB_PATH", "/db/.ht.sqlite");

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
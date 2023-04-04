<?php


// register_shutdown_function('shutdown');
// function shutdown(){
//     echo " ";
// }

header("Location: https://google.com/", true, 302);
// echo "Hello World!";
// echo " ";
// error_log("debug: before exit");
exit(0);
// throw new Exception('Division by zero.');
error_log("debug: after exit");
echo "Goodbye World!";

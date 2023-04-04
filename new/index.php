<?php


register_shutdown_function('shutdown');
function shutdown(){
    error_log("debug: shutting down");
}

echo "Hello World!";
error_log("debug: before exit");
exit(0);
// throw new Exception('Division by zero.');
error_log("debug: after exit");
echo "Goodbye World!";

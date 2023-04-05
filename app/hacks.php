<?

function var_error_log(...$vars)
{
    ob_start();                    // start buffer capture
    echo "WCGI Debug: ";
    var_dump(...$vars);           // dump the values
    $contents = ob_get_contents(); // put the buffer into a variable
    ob_end_clean();                // end capture
    error_log($contents, 0);        // log contents of the result of var_dump( $object )
}

// A function that replaces exit; fixing it by adding a status always
// and by forcing the headers to be sent by forcing a write to STDOUT
if (!function_exists('do_exit')) {
    function do_exit()
    {
        fwrite(STDOUT, '');
        exit(0);
    }
}

// PHP-CGI doesn't have realpath defined, so we hack it a bit
if (!function_exists('realpath')) {
    function realpath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('/\/+/', '/', $path);
        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return str_replace('//', '/', '/' . implode('/', $absolutes));
    }
}

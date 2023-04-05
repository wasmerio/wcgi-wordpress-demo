<?php

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
        echo " ";
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

// Serves a static file from the filesystem
function serve_static_file($requestedAbsoluteFile) {
    $mimeTypes = [
        'txt'  => 'text/plain',
        'csv'  => 'text/csv',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'php'  => 'text/html',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'xml'  => 'application/xml',
        'swf'  => 'application/x-shockwave-flash',
        'flv'  => 'video/x-flv',
        // Images
        'png'  => 'image/png',
        'jpe'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'ico'  => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif'  => 'image/tiff',
        'svg'  => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // Archives
        'zip'  => 'application/zip',
        'rar'  => 'application/x-rar-compressed',
        'exe'  => 'application/x-msdownload',
        'msi'  => 'application/x-msdownload',
        'cab'  => 'application/vnd.ms-cab-compressed',
        // Audio/video
        'mpg'  => 'audio/mpeg',
        'mp2'  => 'audio/mpeg',
        'mp3'  => 'audio/mpeg',
        'mp4'  => 'audio/mp4',
        'qt'   => 'video/quicktime',
        'mov'  => 'video/quicktime',
        'ogg'  => 'audio/ogg',
        'oga'  => 'audio/ogg',
        'wav'  => 'audio/wav',
        'webm' => 'audio/webm',
        'aac'  => 'audio/aac',
        // Adobe
        'pdf'  => 'application/pdf',
        'psd'  => 'image/vnd.adobe.photoshop',
        'ai'   => 'application/postscript',
        'eps'  => 'application/postscript',
        'ps'   => 'application/postscript',
        // MS Office
        'doc'  => 'application/msword',
        'dot'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
        'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
        'odt'  => 'application/vnd.oasis.opendocument.text',
        'rtf'  => 'application/rtf',
        'xls'  => 'application/vnd.ms-excel',
        'xlt'  => 'application/vnd.ms-excel',
        'xla'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
        'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pot'  => 'application/vnd.ms-powerpoint',
        'pps'  => 'application/vnd.ms-powerpoint',
        'ppa'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        'mdb'  => 'application/vnd.ms-access',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
    ];

    header("Content-Length: " . filesize($requestedAbsoluteFile));
    $path_parts = pathinfo($requestedAbsoluteFile);
    $mime_content_type = $mimeTypes[$path_parts['extension']];

    // $mime_content_type = mime_content_type($requestedAbsoluteFile);
    header('Content-Type: ' . $mime_content_type);
    $last_modified_time = filemtime($requestedAbsoluteFile);
    $etag = md5_file($requestedAbsoluteFile);

    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $last_modified_time) . " GMT");
    header("Etag: $etag");

    // if (
    //     @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
    //     trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag
    // ) {
    //     header("HTTP/1.1 304 Not Modified");
    //     echo " ";
    //     echo " "; exit(0);
    // }

    $fh = fopen($requestedAbsoluteFile, 'r');
    fpassthru($fh);
    fclose($fh);
}
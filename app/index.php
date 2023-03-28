<?php

/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);
define('WP_DEFAULT_THEME', 'mesmerize');
define('USE_CACHE', true);
define('SERVE_STATIC', true);

// var_dump($_SERVER);
$PATH_INFO = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "/";
$requestedAbsoluteFile = dirname(__FILE__) . $PATH_INFO;
if (SERVE_STATIC && is_file($requestedAbsoluteFile)) {
    // if requested file is'nt a php file
    if (!preg_match('/\.php$/', $requestedAbsoluteFile)) {
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

        if (
            @strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time ||
            trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag
        ) {
            header("HTTP/1.1 304 Not Modified");
            exit;
        }

        $fh = fopen($requestedAbsoluteFile, 'r');
        fpassthru($fh);
        fclose($fh);
    } else {
        // if requested file is php, include it
        include_once $requestedAbsoluteFile;
    }
} else {
    if (USE_CACHE) {
        $cacheFilename = dirname(__FILE__) . "/wp-content/cache/manual/" . md5($PATH_INFO);
        if (file_exists(($cacheFilename))) {
            $fh = fopen($cacheFilename, 'r');
            fpassthru($fh);
        } else {
            // We generate the cache
            ob_start();
            /** Loads the WordPress Environment and Template */
            require __DIR__ . '/wp-blog-header.php';
            $output = ob_get_contents();
            file_put_contents($cacheFilename, $output);
            ob_end_flush();
        }
    } else {
        require __DIR__ . '/wp-blog-header.php';
    }
}

<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Never serve legacy user-media URLs as static files; authorized replacements use /media/* routes.
if (preg_match('#^/(?:images/users|video/users|comment-media|stories|chat_attachments)(?:/|$)#', $uri)) {
    http_response_code(404);
    exit;
}

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri) && !is_dir(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';

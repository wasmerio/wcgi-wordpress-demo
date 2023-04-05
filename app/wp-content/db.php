<?php

if (true) {
    require_once __DIR__ . '/sqlite/db.php';
}
else {
    require_once __DIR__ . '/db.old.php';
}
// namespace {

    /**
     * Installs the site.
     *
     * Runs the required functions to set up and populate the database,
     * including primary admin user and initial options.
     *
     * @since 2.1.0
     *
     * @param string $blog_title Site title.
     * @param string $user_name User's username.
     * @param string $user_email User's email.
     * @param bool $public Whether site is public.
     * @param string $deprecated Optional. Not used.
     * @param string $user_password Optional. User's chosen password. Default empty (random password).
     * @param string $language Optional. Language chosen. Default empty.
     *
     * @return array Array keys 'url', 'user_id', 'password', and 'password_message'.
     */
    // function wp_install($blog_title, $user_name, $user_email, $public, $deprecated = '', $user_password = '', $language = '')
    // {
    //     echo "DSFDF";
    // }

    // $GLOBALS['wpdb'] = new WP_SQLite_DB();
    // $GLOBALS['wpdb'] = new WP_SQLite_DB\wpsqlitedb();
// }
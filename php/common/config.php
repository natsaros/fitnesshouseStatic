<?php
$config = parse_ini_file(getcwd() . DS . 'conf/config.ini');
if($config) {
    // ** MySQL settings - You can get this info from your web host ** //
    /** The name of the database for WordPress */
    defined('DB_NAME') or define('DB_NAME', $config['dbname']);

    /** MySQL database username */
    defined('DB_USER') or define('DB_USER', $config['username']);

    /** MySQL database password */
    defined('DB_PASSWORD') or define('DB_PASSWORD', $config['password']);

    /** MySQL hostname */
    defined('DB_HOST') or define('DB_HOST', $config['host']);

    /** Database Charset to use in creating database tables. */
    defined('DB_CHARSET') or define('DB_CHARSET', 'utf8');

    /** The Database Collate type. Don't change this if in doubt. */
    defined('DB_COLLATE') or define('DB_COLLATE', '');

    defined('TABLE_PREFIX') or define('TABLE_PREFIX', $config['table_prefix']);

    defined('ALLOWED_TYPES') or define('ALLOWED_TYPES', $config['allowed_types']);

    $log_file = $config['log_file'];
    if(isEmpty($log_file)) {
        $log_file = 'error_log';
    }
    defined('CONF_LOG_FILE') or define('CONF_LOG_FILE', $log_file . '.txt');
    defined('DEV_MODE') or define('DEV_MODE', $config['dev_mode'] === 'true');

} else {
    $die = sprintf(
            "There doesn't seem to be a %s file. I need this before we can get started.",
            '<code>config.ini</code>'
        ) . '</p>';

    echo $die;
    die();
}

?>
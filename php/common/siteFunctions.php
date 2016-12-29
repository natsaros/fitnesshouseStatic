<?php
//Set custom Error Handler
function exception_error_handler($severity, $message, $file, $line) {
    //TODO : check this is not throwing correct error in DB->connect()
    if(mysqli_connect_errno()) {
        echo sprintf("Connect failed: %s\n", mysqli_connect_error());
    }
    throw new SystemException($message, 0, $severity, $file, $line);
}

set_error_handler("exception_error_handler", E_ALL & ~E_NOTICE);

define('PHP_POSTFIX', '.php');
define('ADMIN_STR', 'admin');
define('NAV_STR', 'navigation');
define('ACTION_STR', 'action');
define('COMMON_STR', 'common');
define('CLASSES_STR', 'classes');

define('REQUEST_URI', getRootUri());

define('DS', DIRECTORY_SEPARATOR);

if(!defined('ROOT_PATH'))
    define('ROOT_PATH', dirname(__DIR__) . DS);

define('ADMIN_ROOT_PATH', ROOT_PATH . ADMIN_STR . DS);
define('ADMIN_NAV_PATH', ROOT_PATH . ADMIN_STR . DS . NAV_STR . DS);
define('ADMIN_ACTION_PATH', ROOT_PATH . ADMIN_STR . DS . ACTION_STR . DS);
define('COMMON_ROOT_PATH', ROOT_PATH . COMMON_STR . DS);
define('CLASSES_ROOT_PATH', COMMON_ROOT_PATH . CLASSES_STR . DS);

define('ASSETS_URI', REQUEST_URI . 'assets' . DS);
define('CSS_URI', ASSETS_URI . 'css' . DS);
define('JS_URI', ASSETS_URI . 'js' . DS);

require_once(CLASSES_ROOT_PATH . 'SystemException.php');
require_once(CLASSES_ROOT_PATH . 'DB.php');
require_once(CLASSES_ROOT_PATH . 'Globals.php');
require_once(CLASSES_ROOT_PATH . 'UserFetcher.php');

function isAdmin() {
    return strpos(getRequestUri(), ADMIN_STR) !== false;
}

function isAdminAction() {
    return strpos(getRequestUri(), ADMIN_STR . '/' . ACTION_STR) !== false;
}

function getRootUri() {
    $uri = $_SERVER['REQUEST_URI'];
    $uri = preg_replace("/[^\/]+$/", "", $uri);
    if(isAdminAction()) {
        $uri = preg_replace("/admin[\/]action[\/]*$/", "", $uri);
    } else if(isAdmin()) {
        $uri = preg_replace("/admin[\/]*$/", "", $uri);
    }
    return $uri;
}

function getRequestUri() {
    $uri = $_SERVER['REQUEST_URI'];
    $uri = preg_replace("/\?[^\?]+$/", "", $uri);
    return $uri . "/";
}

function getAdminRequestUriNoDelim() {
    return getRootUri() . ADMIN_STR;
}

function getAdminRequestUri() {
    return getAdminRequestUriNoDelim() . DS;
}

function getAdminActionRequestUri() {
    return getAdminRequestUriNoDelim() . DS . ACTION_STR . DS;
}

function getActiveAdminPage() {
    $uri = $_SERVER['REQUEST_URI'];
    $page_id = preg_replace("/[^\/][\w]+(?=\?)/", "", $uri);
    return $page_id;
}

function initLoad() {

    $db = getDb();

    if(!$db->isInitialized()) {
        $init_queries = $db->db_schema();
        $result = $db->query($init_queries);
        if($result === false) {
            throw new SystemException('Database has not been initialized');
        }
    }
}

function isLoggedIn() {
    return !is_null(getUserFromSession());
}

function Redirect($url, $refreshRate = null, $permanent = false) {
    if(!headers_sent()) {
        if(is_null($refreshRate)) {
            header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
        } else {
            header('Refresh : ' . $refreshRate . 'url: ' . $url, true, ($permanent === true) ? 301 : 302);
        }
    } else {
        echo '<script type="text/javascript">';
        echo 'window.location.href="' . $url . '";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
        echo '</noscript>';
    }

    exit();
}

function getDb() {
    if(is_null(Globals::get('DB'))) {
        Globals::set('DB', DB::getInstance());
    }
    return Globals::get('DB');
}

function getUserFromSession() {
    return $_SESSION['USER'];
}

function setUserToSession($user) {
    $_SESSION['USER'] = $user;
    $_SESSION['timeout'] = time();
    $_SESSION['valid'] = true;
}

function require_safe($path) {
    if(file_exists(($path))) {
        require($path);
    } else {
        throw new SystemException($path . " doesn't exist");
    }
}

function exists_safe($path) {
    if(file_exists(($path))) {
        return true;
    } else {
        throw new SystemException($path . " doesn't exist");
    }
}

?>
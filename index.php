<?php
//echo "Site is updated now. Please wait. Thanks."; exit;
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Cache-Control: post-check=0,pre-check=0", false);
header("Cache-Control: max-age=0", false);
header("Pragma: no-cache");


//$domain_path_s = 'igrushki-detkam.ru';
$domain_path_s = 'id1122.com';

if (substr($_SERVER['HTTP_HOST'],0,4) == 'www.')
	define('DOMAIN_PATH','http://www.'.$domain_path_s);
else
	define('DOMAIN_PATH','http://'.$domain_path_s);

define('FTP_PATH',dirname(__FILE__));

define('META_DESCRIPTION','Èíòåğíåò-ìàãàçèí äåòñêèõ èãğóøåê. Îãğîìíûé âûáîğ èãğ è èãğóøåê.');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
    
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
/*
set_include_path(implode(PATH_SEPARATOR, array(
    dirname(dirname(dirname(__FILE__))),
    get_include_path(),
)));
*/
set_include_path(implode(PATH_SEPARATOR, array(
    'C:\E_500\xampp\htdocs\zend\ZendFramework-1.10.8\library',
    get_include_path(),
)));


// Set unique id for user
if (!isset($_COOKIE['user_uid'])) {
	define('USER_UID',uniqid());
	setcookie('user_uid',USER_UID,time()+366*24*60*60,'/');
} else {
	define('USER_UID',$_COOKIE['user_uid']);
}
// ----------------------

include APPLICATION_PATH.'/utils/getwebpage.php';

include 'Zend/Loader.php';

spl_autoload_register('OldAutoload');
//function __autoload($class)
function OldAutoload($class)
{
	//print_r($class);exit;
    $parts = explode('_', $class);
    if (in_array($parts[0], array('Zend', 'Es'))) {
        Zend_Loader::loadClass($class);
    } elseif ($parts[count($parts)-1] == 'Plugin') {
    	$class = str_replace('_', "", $class);
    	Zend_Loader::loadFile($class . '.php', APPLICATION_PATH . '/plugins/', true);
    } else {
        $class = str_replace('_', "/", $class);
		//print_r(APPLICATION_PATH . '/models/');exit;
        Zend_Loader::loadFile($class . '.php', APPLICATION_PATH . '/models/', true);
    }
}

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap('frontcontroller');
//$front = $application->getBootstrap()->getResource('frontcontroller');

/**
 * DB
 */
 /*
$db = Zend_Db::factory($config->database);
Zend_Db_Table_Abstract::setDefaultAdapter($db);
$db->getConnection()->query('SET NAMES "' . $config->database->params->names . '"');
Zend_Registry::set('db', $db);*/

/**
 * Front Controller
 */
$front  = Zend_Controller_Front::getInstance();
$front->setDefaultModule('def');
$front->addModuleDirectory(APPLICATION_PATH . '/modules');
//$front->dispatch();

$front->registerPlugin(new FirstOfAll_Plugin());

$application->bootstrap()->run();
#!/usr/bin/php
<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */
/**
 * this script is for assigning users to a dispute
 */
// {{{ bootstrap
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH',
               realpath(dirname(__FILE__) . '/../application'));
 
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    get_include_path(),
)));
require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance();

// Define some CLI options
$getopt = new Zend_Console_Getopt(array(
    'env|e-s'       => 'Application environment for which to create database (defaults to development)',
    'help|h'        => 'Help -- usage message',
//    'verbose|v'     => 'Verbose out put',
    'loadSQL|l'     => 'Load the sql',
    'dummyEmail|d-s' => 'send mail to a dummy address',
));
try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    // Bad options passed: report usage
    echo $e->getUsageMessage();
    return false;
}
 
// If help requested, report usage message
if ($getopt->getOption('h')) {
    echo $getopt->getUsageMessage();
    return true;
}
 
// Initialize values based on presence or absence of CLI options
$env       = $getopt->getOption('e');
//$verbose   = $getopt->getOption('v');
$loadSQL   = $getopt->getOption('l');
$dummy     = $getopt->getOption('d');

defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (null === $env) ? 'development' : $env);
 
// Initialize Zend_Application
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
 
// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');


// Initialize contact config file
$config = new Zend_Config_Ini( APPLICATION_PATH . "/configs/contacts.ini" );
Zend_Registry::set( 'contacts', $config );

date_default_timezone_set( 'Australia/Melbourne' );
// }}}
// {{{ db ckeck
#
$dbAdapter = $bootstrap->getResource('db');

// Check to see if we have a database file already
$options = $bootstrap->getOption('resources');
$dbFile  = $options['db']['params']['dbname'];
if (file_exists($dbFile)) {
    unlink($dbFile);
}
// }}}
// {{{ include files
require_once( APPLICATION_PATH . "/modules/default/models/DbTable/Auth.php");
require_once( APPLICATION_PATH . "/modules/default/models/Auth.php");
require_once( APPLICATION_PATH . "/modules/default/models/AuthMapper.php");
require_once( APPLICATION_PATH . "/modules/default/models/DbTable/UserToDispute.php");
require_once( APPLICATION_PATH . "/modules/default/models/UserToDispute.php");
require_once( APPLICATION_PATH . "/modules/default/models/UserToDisputeMapper.php");
require_once( APPLICATION_PATH . "/modules/default/models/DbTable/Disputes.php");
require_once( APPLICATION_PATH . "/modules/default/models/Disputes.php");
require_once( APPLICATION_PATH . "/modules/default/models/DisputesMapper.php");
// }}}
/////////////////////////////////////
$options =  $bootstrap->getOptions();
Zend_Debug::Dump( $options['app']['baseUrl'] );

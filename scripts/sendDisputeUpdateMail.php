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
    'verbose|v'     => 'Verbose out put',
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
$verbose   = $getopt->getOption('v');

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


// {{{ Initialies config files
  // session
  $path = APPLICATION_PATH . '/configs/session.ini';

  $config = new Zend_Config_Ini($path);
  $sessionConfig = $config->get(APPLICATION_ENV);

  //contacts
  $path = APPLICATION_PATH . '/configs/contacts.ini';

  $config = new Zend_Config_Ini($path);
  $config = $config->get(APPLICATION_ENV);

  Zend_Registry::set( 'contacts', $config );

  // application ini
  $path = APPLICATION_PATH . '/configs/application.ini';
  
  $config = new Zend_Config_Ini($path);
  $config = $config->get(APPLICATION_ENV);

  Zend_Registry::set('config', $config);
// }}}

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
/////////////////////////////////////
// {{{ include files
require_once( APPLICATION_PATH . "/modules/engine/controllers/DisputeController.php");

require_once( APPLICATION_PATH . "/modules/default/models/DbTable/Auth.php");
require_once( APPLICATION_PATH . "/modules/default/models/Auth.php");
require_once( APPLICATION_PATH . "/modules/default/models/AuthMapper.php");

require_once( APPLICATION_PATH . "/../library/Extend/Queue/Model/DbTable/Mail.php");
require_once( APPLICATION_PATH . "/../library/Extend/Queue/Model/Mail.php");
require_once( APPLICATION_PATH . "/../library/Extend/Queue/Model/MailMapper.php");

require_once( APPLICATION_PATH . "/modules/engine/models/DbTable/ClaimStateLog.php");
require_once( APPLICATION_PATH . "/../library/Engine/Models/ClaimStateLog.php");
require_once( APPLICATION_PATH . "/../library/Engine/Models/ClaimStateLogMapper.php");

// }}}
// {{{ main menu
$queueAdapter    = new Extend_Queue_Model_MailMapper();
$claimLogAdapter = new Engine_Models_ClaimStateLogMapper();
$authAdapter     = new Default_Model_AuthMapper();

//$userDisputeAdapter = new Default_Model_UserToDisputeMapper();

$queuedMail = $queueAdapter->findAll();
$sentMailList = array();
$baseUrl = $bootstrap->getOptions();
$baseUrl = $baseUrl['app']['baseUrl'];


$message = new Zend_View();
$message->setScriptPath( APPLICATION_PATH . '/../scripts/emails/' );
$message->assign( 'messages_sent', 0  );

try{
   // proccess any mail in the queue
   foreach( $queuedMail as $mail )
      {
      // find if the user has made a claim recently
      $lastChange =  $claimLogAdapter->findLastDisputeBy( $mail->getUserID(), $mail->getDisputeID() );
 
      // if they are yet to make a change then send a message
      if( $lastChange == null )
         {
         $sentMailList[] = prossess( $authAdapter, $queueAdapter, $mail, $baseUrl );
         } 
      // if they have made a chage, check if a messages should be sent
      if( $lastChange != null )
         {
         $mailDate       = new Zend_Date( $mail->getAddedAt(), Zend_Date::ISO_8601 );
         $lastChangeDate = new Zend_Date( $lastChange->getTimestamp(), Zend_Date::ISO_8601 );
         $currentDate    = Zend_Date::now();

         // send the mail if there diff between the last log in is more than the session time else if 6h have elapsed since the mail was added.
         if( $lastChangeDate->isEarlier( $mailDate->add( 1, Zend_Date::HOUR )) == true
          || $mailDate->isEarlier( $currentDate->sub( 6, Zend_Date::HOUR) ) == true )
            {
            $sentMailList[] = prossess( $authAdapter, $queueAdapter, $mail, $baseUrl );
            }  
         }
      }
   // message setup
   $message->assign( 'messages_sent', count($sentMailList)  );
   $message->assign( 'list', $sentMailList );
   }
catch( Exception $e )
   {
   // message setup
   $message->assign( 'errors', $e );

   echo $e;
   }

   sendNotifacation( 'reconsider.odr@gmail.com', $message->render( 'mailUpdate.phtml'), "User Mail Sent" );

// }}}
// {{{ function prossess( $authAdapter, $queueAdapter, $mail, $baseUrl )
function prossess( $authAdapter, $queueAdapter, $mail, $baseUrl )
   {
   echo "Sending mail: \n";
   $user = $authAdapter->find( $mail->getUserID(), new Default_Model_Auth() );
   if( $user != null )
      {
      $sendRes = constructMessage( $user->getEmail(),  $mail->getMailType(), $baseUrl );
    
      echo "---> Removeing queued mail ... ";
      $removeRes = $queueAdapter->remove( $mail );
      echo "Done.\n";
                 
      echo "---> Updating log ... ";
      // log actions
      $log = array( "UID"        => $user->getID(),
                    "SendMail"   => isset($sendRes),
                    "RemoveMail" => $removeRes );
      echo "Done.\n";
      }                         
   else                         
      {                         
      echo "--> Faild!\n";
      echo "--> Updating log ... ";
      $log = array( "UID"       => "UID( ".$mail->getUserID().") not found!",
                    "SendMail"  => false, 
                    "RemoveMail" => false );
      echo "Done.\n";
      }
   return $log;
   }

// }}}
// {{{ function constructMessage( $address, $messageType )
function constructMessage( $address, $messageType, $baseUrl )
   { 

   // message setup
   $message = new Zend_View();
   $message->setScriptPath( APPLICATION_PATH . '/../scripts/emails/' );
   $message->assign( 'href_base', $baseUrl  );

   if( $messageType == MAIL_DISPUTE_CHANGE )
      {
      $subject = "Notification: potentual resolution of the dispute";
      $message = $message->render( 'changeInDisputeState.phtml' );
      }
   else
      {
      $subject = "Notification: changes have been made to the dispute";
      $message = $message->render( 'changeToDisputeClaim.phtml' );
      }
   return sendNotifacation( $address, $message, $subject );
   }
// }}}
// {{{ function sendNotifacation( $addresses, $message )
function sendNotifacation( $address, $message, $subject )
   {
   try{
       // mail trancport setup
       if( !isset( Zend_Registry::get( 'contacts' )->smptServer ) )
          {
          $tr = new Zend_Mail_Transport_Sendmail( Zend_Registry::get( 'contacts' )->sys_sendmail );
          }
       else
          {
          $tr = new Zend_Mail_Transport_Smtp( Zend_Registry::get( 'contacts' )->smptServer,  Zend_Registry::get( 'contacts' )->smptConfig->toArray() );
          }
       Zend_Mail::setDefaultTransport($tr);
       Zend_Mail::setDefaultFrom( Zend_Registry::get( 'contacts' )->sys_sendmail, Zend_Registry::get( 'contacts' )->sys_sendmailName );
       Zend_Mail::setDefaultReplyTo( Zend_Registry::get( 'contacts' )->no_reply, Zend_Registry::get( 'contacts' )->no_replyName );

       // construct the message 
       $mail = new Zend_Mail();
       $mail->addTo( $address, "ReConsider study participant" );
       $mail->setSubject( $subject );
       $mail->setBodyHtml( $message );

       $mail->send();
       }
    catch( Exception $e )
       {
       return false;
       }
   return true;
   }


// }}}

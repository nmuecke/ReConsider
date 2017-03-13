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
// {{{ main menu
$authAdapter  = new Default_Model_AuthMapper();
$options = $bootstrap->getOptions();


$users = $authAdapter->fetchAll();
if( $users == null || count( $users ) <= 0 )
   {
   throw new exception( "No users found!" );
   }

$doctors  = array();
$patients = array();
$others   = array();
$unvalidated   = array();

foreach( $users as $user )
   {
   if( $user->getEmailIsValid() == true )
      {
      switch( $user->getRealname() )
         {
         case 'ballarat':
            $doctors[] = $user;
            break;
         case 'deakin':
            $patients[] = $user;
            break;
        default:
            $others[] = $user;
         }
      } 
   else
      {
      $unvalidated[] = $user;
      }
   }


   $exit = false;
   while( $exit != true )
      {
      echo "----------------------- Menu -----------------------------\n";
      echo " 1)   View numbers \n";
      echo " 2)   View distribution of univercities \n";
      echo " 3)   Balamce disputants (manual) \n";
      echo " 4)   Balance disputants (auto) \n";
      echo " 5)   Assign disputants \n";
      echo " 6)   Send Notifacations \n";
      echo " e)   Exit \n";
  
      $handle = fopen ("php://stdin","r");
      $line = fgets($handle);
      switch( trim( $line ) )
         {
         case '1':
            viewNumbers( $doctors, $patients, $others, $unvalidated, $users );
            break;
         case '2':
            viewDistribution( $users );
            break;
         case '3':
            //$spareUser = assigneOtherManual(  $doctors, $patients, $others, $users );
            echo "\n\nSorry. Not avalible\n";
            break;
         case '4': 
            $spareUser = assigneOtherAuto(  $doctors, $patients );
            break;
         case '5':
            assigneDisputatns( $doctors, $patients, $loadSQL );
            break;
         case '6':
            $message = new Zend_View();
            $message->setScriptPath( APPLICATION_PATH . '/../scripts/emails/' );
            $message->assign( 'href_base', $options['app']['baseUrl']  );

            if( count( $doctors ) != count( $patients ) )
               {
               echo "\n   The number of doctors and patients do not match.\n";
               if( confirm() != true )
                  break;
               }

            if( isset( $dummy )) 
               {
               $dummyUser = new Default_Model_Auth();
               $dummyUser->setEmail( $dummy );
               $spareUser = $dummyUser ;
               $doctors = $patients = array( $dummyUser );
               }
            if( isset( $spareUser ) && $spareUser != null )
               {
               sendNotifacation( array( $spareUser ), "ReConsider: participation not required", $message->render( "unrequiredNotifacation.phtml" ) );
               }

            sendNotifacation( $doctors,   "ReConsider: dispute now activated", $message->render( "doctorNotifacation.phtml" ) );
            sendNotifacation( $patients,  "ReConsider: dispute now activated", $message->render( "patientNotifacation.phtml" ) );
            break;
         case 'e':
         case 'E':
            $exit = true;
            break;
         default;
         }
      }

// }}}
// {{{ function viewDistribution( $users )
function viewDistribution( $users )
   {
   $res = array();
   foreach( $users as $user )
      {
         if( !isset( $res[$user->getRealName()] ) )
            {
            $res[$user->getRealName()] = 0;
            }
      $res[$user->getRealName()]++; 
      }

   echo "Numbers by univercity:\n";
   echo "```````````````````````````````````````````````\n";
   foreach( $res as $uni=>$num )
      {
      printf( "   %-20s %s\n", $uni,  $num );
      }
   echo "```````````````````````````````````````````````\n";
   printf( "   %-20s %s\n", 'Total',  count( $users) );
   echo "```````````````````````````````````````````````\n";
   echo "\n";

   pause();
   }
// }}}
// {{{ function viewNumbers( $doctors, $patients, $others, $unvalidated, $all )
function viewNumbers( $doctors, $patients, $others, $unvalidated, $all )
   {
   echo "User talliy\n";
   echo "``````````````````````````````````````````````````\n";
   echo "  num of Doctors: \t " . count( $doctors     ) . "\n";
   echo "  num of Patients:\t " . count( $patients    ) . "\n";
   echo "  num of others:  \t " . count( $others      ) . "\n";
   echo "  num of unvalid: \t " . count( $unvalidated ) . "\n";
   echo "``````````````````````````````````````````````````\n";
   echo "  Total:          \t " . count( $all         ) . "\n";
   echo "``````````````````````````````````````````````````\n";
   echo "\n";

   pause();
   }

// }}}
// {{{ function assigneOtherManual(  & $doctors, & $patients, & $others, $users )
function assigneOtherManual(  & $doctors, & $patients, & $others, $users )
   {
   $exit = false;
   while( $exit != true )
      {
      echo "User talliy\n";
      echo "``````````````````````````````````````````````\n";
      echo "  num of Doctors: \t " . count( $doctors  ) . "\n";
      echo "  num of Patients:\t " . count( $patients ) . "\n";
      echo "  num of others:  \t " . count( $others   ) . "\n";
      echo "```````````````````````````````````````````````\n";
      echo "\n"; 
      echo "Assigne others to doctors(d), patients(p) or exit (e): ";
      }
   }


// }}}
// {{{ function assigneOtherAuto(  & $doctors, & $patients )
function assigneOtherAuto(  & $doctors, & $patients )
   {
   if( count( $doctors ) == count( $patients ) )
      {
      echo "\nNumbers are even, nothin to do.\n\n";
      return true;
      }
   else if( count( $doctors ) > count( $patients ) )
      {
      echo"\nThere are more doctors than patients.\n Asssigning surpluss doctors to patients.\n\n";
      return ballanceArrays( $doctors, $patients );
      }
   else if( count( $doctors ) < count( $patients ) )
      {
      echo"\nThere are less doctors than patients.\n Asssigning surpluss patients to doctors.\n\n";
      return ballanceArrays( $patients, $doctors );
      }
   return null;
   }


// }}}
// {{{ function ballanceArrays( & $bigger, & $smaller )
function ballanceArrays( & $bigger, & $smaller )
   {
   $dif = count( $bigger ) - count( $smaller );
   if( $dif == 1 )
      {
      echo "One element was unassigned.\n";
      return array_pop( $bigger );
      }

   $dif = $dif / 2;

   for( $xx = 0; $xx < $dif; $xx++ )
      {
      $spare = array_pop( $bigger );
      if( $spare == null )
         {
         echo "Error! element was not found";
         return null;
         }
      $smaller[] = $spare;
      }

   if( ($dif % 2) == 1 )
      {
      echo "One element was unassigned.\n";
      return array_pop( $bigger );
      }

   return true;
   }


// }}}
// {{{ function assigneDisputatns( $doctors, $patients )
function assigneDisputatns( $doctors, $patients, $loadSQL )
   {
   echo "Assign disputatns to disputes?\n";
   if( confirm() == false )
      {
      return null;;
      }
   if( count( $doctors ) != count( $patients ) )
      {
      echo "The ratio of doctors to students is uneven.\n";
      if( confirm() != true )
         {
         return null;
         }
      }

   $u2dAdapter     = new Default_Model_UserToDisputeMapper();
   $disputeAdapter = new Default_Model_DisputesMapper();
   $xx = 0;
   while( $xx < count( $doctors ) && $xx < count( $patients ) )
      {
      $dispute = new Default_Model_Disputes();
      $dispute->setDisputeType( 'EHR' )
              ->setStatus( 'ACTIVE' ); 

      if( isset( $loadSQL ) )
         {
         echo '-->Updating dispute table ... ';
         $id = $disputeAdapter->initiate( $dispute );
         echo 'done.' . "\n";
         echo '-----> New dispute created with ID: ' . $id . "\n";
         }
      else
         {
         $id = '-99';
         echo '-->Not Updating dispute table' . "\n";
         }  
      $doc = new Default_Model_UserToDispute();      
      $doc->setDisputeId( $id )
          ->setUserID( $doctors[$xx]->getID() );

      $pat = new Default_Model_UserToDispute();      
      $pat->setDisputeId( $id )
          ->setUserID( $patients[$xx]->getID() );
      echo '-->Updating mapping between user and disputes' . "\n";
      if( isset( $loadSQL ) )
         {
         echo '------>starting on doctor ... ';
         $u2dAdapter->save( $doc );
         echo 'Done.' . "\n";
         echo '------>starting on patient ... ';
         $u2dAdapter->save( $pat );
         }
      else
         {
         echo '------>saveing skipped ... ';
         }
      echo 'Done.' . "\n";
      $xx++;
      }
   echo "-->" . $xx . " new disputes created\n\n";
   pause();
   }   


// }}}
// {{{ function sendNotifacation( $addresses, $subject, $message )
function sendNotifacation( $addresses, $subject, $message )
   {
   echo '-->Sending mail subject:' . $subject . "\n";
   if( !isset( Zend_Registry::get( 'contacts' )->smptServer ) )
      {
      $tr = new Zend_Mail_Transport_Sendmail( Zend_Registry::get( 'contacts' )->sys_sendmail );
      }
   else
      {
      $tr = new Zend_Mail_Transport_Smtp( Zend_Registry::get( 'contacts' )->smptServer,  Zend_Registry::get( 'contacts' )->smptConfig->toArray() );
      }
   Zend_Mail::setDefaultTransport($tr);

   Zend_Mail::setDefaultTransport($tr);
   Zend_Mail::setDefaultFrom( Zend_Registry::get( 'contacts' )->sys_sendmail, Zend_Registry::get( 'contacts' )->sys_sendmailName );
   Zend_Mail::setDefaultReplyTo( Zend_Registry::get( 'contacts' )->no_reply, Zend_Registry::get( 'contacts' )->no_replyName );
 

   foreach( $addresses as $address )
      {
      $mail = new Zend_Mail();
      $mail->addTo( $address->getEmail(), "The participant" );
      $mail->setSubject( $subject);
      $mail->setBodyHTML( $message );

      $mail->send();
      echo "----> Mail sent \n";
      }
   echo "--> Done\n";
   }

// }}}
// {{{ function pause()
function pause()
   {
   echo "Hit enter to continue";
   $handle = fopen ("php://stdin","r");
   $line = fgets($handle);

   return true;
   }


// }}}
// {{{ function confirm()
function confirm()
   {
   echo "Do you wish to proceed? (type yes)";
   $handle = fopen ("php://stdin","r");
   $line = fgets($handle);
   if( trim( $line ) != 'yes')
      {
      return null;
      }
   return true;
   }
// }}}

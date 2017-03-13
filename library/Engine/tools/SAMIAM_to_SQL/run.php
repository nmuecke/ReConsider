#!/usr/bin/php
<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */
// {{{ bootstrap
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH',
               realpath(dirname(__FILE__) . '/../../../../application'));
 
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    get_include_path(),
)));
require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance();

// Define some CLI options
$getopt = new Zend_Console_Getopt(array(
    'withdata|w=s'  => 'Load database with sample data (dont forget to update the severs claims & nodes tables)',
    'env|e-s'       => 'Application environment for which to create database (defaults to development)',
    'help|h'        => 'Help -- usage message',
    'verbose|v'     => 'Verbose out put',
    'loadSQL|l'     => 'Load the sql',
    'outputSQL|o=s' => 'Output the sql to a file',
    
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
$withData  = $getopt->getOption('w');
$env       = $getopt->getOption('e');
$verbose   = $getopt->getOption('v');
$loadSQL   = $getopt->getOption('l');
$outputSQL = $getopt->getOption('o');

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

require_once( "class.SAMIAM_Factory.php" );
/////////////////////////////////////


if( !isset( $withData ) )
   {
   echo $getopt->getUsageMessage();
   die( "\nNo SAMIAM data file provided!\n\n" );
   }


$data = SAMIAM_Factory::Factory( $withData );

$select = array();
require_once( APPLICATION_PATH . "/modules/engine/models/DbTable/Nodes.php");
require_once( APPLICATION_PATH . "/modules/engine/models/DbTable/Claims.php");
require_once( APPLICATION_PATH . "/modules/engine/models/DbTable/BayesProb.php");
require_once( APPLICATION_PATH . "/../library/Engine/Inference/Model/BayesProb.php");
require_once( APPLICATION_PATH . "/../library/Engine/Inference/Model/BayesProbMapper.php");

$nodeAdapter  = new Engine_Node_Model_NodeMapper();
$claimAdapter = new Engine_Node_Model_ClaimMapper();
$probAdapter  = new Engine_Inference_Model_BayesProbMapper();


// store the row data for creating the sql
$rows = array();

foreach( $data->getNodes() as $key=>$samNode )
   {
   $node = $nodeAdapter->findNode( $samNode->getID() );
   if( $node == null )
      {
      Zend_Debug::Dump( $samNode );
      DIE( "\nnode not found\n\n" );
      }

   $claims = $claimAdapter->findNodesClaims( $node->getNodeID() );
   $node->addClaims( $claims );

   Zend_Debug::Dump( $node->getNodeID(), "Working ON -- Node ID" );

   foreach( $claims as $claim )
      {
      $row = new Engine_Inference_Model_BayesProb();
      $row->setNodeID( $node->getID()   )
          ->setClaimID( $claim->getClaimID() )  
          ->setParentNodeID( "NULL" )
          ->setParentClaimID( "NULL" )
          ->setProb( $samNode->getPriorAt( $claim->getValue() ) );
      $rows[] = $row;
      if( $verbose == true )
         echo $row->toString() . "\n";
      }

   $xx = 0;
   while( ($samParentNode = $samNode->getDependentAt( $xx ) ) )
      {
      $parentNode = $nodeAdapter->findNode( $samParentNode->getID() );
      $parentClaims = $claimAdapter->findNodesClaims( $parentNode->getNodeID() );

      $conditionalProbs = $samNode->getProbs( );
           
      Zend_Debug::Dump( $samParentNode->getID(), "Adding Probs for Parent Node ID" );
  
      foreach( $parentClaims as $parentClaim ) //$conditionalProbs as $claimKey=>$claimProbs )
         {

         if( ($claimProbs = $conditionalProbs[$parentClaim->getValue()] ) === null )
            {     
            Zend_Debug::Dump( $samParentNode->getID(), "Parent Node ID" );
            Zend_Debug::Dump( $conditionalProbs );
            Zend_Debug::Dump( $parentClaims, "Parent Node Claims" );

            Die(); 
            }

         foreach( $claims as $claim ) //$claimProbs as $key=>$prob )
            {
            if( ($prob = $claimProbs[$claim->getValue()] ) === null )
               { 
               Zend_Debug::Dump( $samNode->getID(), "Node ID" );
               Zend_Debug::Dump( $conditionalProbs );
               Zend_Debug::Dump( $claims, "Parent Node Claims" );

               Die();
               }
               
            $row = new Engine_Inference_Model_BayesProb();
            $row->setNodeID( $node->getID()   )
                ->setClaimID( $claim->getClaimID() ) 
                ->setParentNodeID( $parentNode->getID()   )
                ->setParentClaimID( $parentClaim->getClaimID() )
                ->setProb( $prob  );
            $rows[] = $row;

            if( $verbose == true )
               echo $row->toString() . "\n";
            }
         }
    
      $xx++;
      }   

   }

   if( $loadSQL == true || isset( $outputSQL ) )
      $sql = "INSERT INTO `bayesProb` ( `nodeID`, `claimID`, `parentNodeID`, `parentClaimID`, `prob` )\n VALUES";
 
      foreach( $rows as $row )
         {
         $sql .= "( " . $row->getNodeID() 
               . ", " . $row->getClaimID()
               . ", " . (string)$row->getParentNodeID()
               . ", " . (string)$row->getParentClaimID()
               . ", " . $row->getProb()
               . " ),\n";
         }
      $sql = trim( $sql, ",\n" ) . ";";


   if( $loadSQL == true )
      {
      //$probAdapter::query( $sql );
      }

   if( isset( $outputSQL ) )
      {
      $handle = fopen( $outputSQL, "w" );
      
      fputs( $handle, $sql );
      fclose( $handle );
      }
   //Zend_Debug::Dump( $node );
   



//////////////////////////////////////////


<?php

define( "MAIL_DISPUTE_CHANGE", "DISPUTE_UPDATE" );
define( "MAIL_CLAIM_CHANGE",   "CLAIM_UPDATE"   );

class Engine_DisputeSummaryController extends Zend_Controller_Action
{
    private $_user;
    private $_oponent;
    private $_disputeID;
    private $_disputeType;


    public function init()
       {
       //$auth = Extend_User::getInstance();
       $auth = Zend_Auth::getInstance();

       if( !$auth->hasIdentity() )
          {
          $this->_helper->redirector('request-denied', 'auth', 'default' );
          }

       // get the user's info
       $dbAdapter = new Default_Model_AuthMapper();
       $user = new Default_Model_Auth();
       $dbAdapter->find( $auth->getIdentity()->username, $user );

       if( null === ( $user = $dbAdapter->findByUsername( $auth->getIdentity()->username, $user ) ) )
          {
          throw new Exception( "ERROR! Unable to validate your user credentuals ".
                               "whilest initiating a new dispute." );
          }
       $session = new Zend_Session_Namespace('Dispute');
       if( isset( $session->ID ) )
          {
          $disputeID = $session->ID;
          }
       else if( $this->getRequest()->isPost() )
          {
          $disputeID = $this->getRequest()->getPost( 'disputeID' );
          }
       else
          {
          throw new Exception("Session was not valid");
          }

       if( !$this->_isValideDispute( $disputeID, $user->getID() ) )
          {
          throw new Exception("Dispute ID was not valid");
          }
          
      

       $this->_user    = $user;
       $this->_oponent = null;

       $urlHistory = new Extend_Helpers_RefPage();

       $previousPage = $urlHistory->getLastVisited();

       $urlHistory->save( $this->_request->getRequestUri() );
       $currentPage = strtolower( $this->_request->getRequestUri());

       // make sure that the dispute was enter via the propper chanles
       switch( strtolower( $previousPage ) )
          {
          // re-entering the /engine/dispute controller from the same action
          case $currentPage:
             break; 
         
          // directed to /engine/dispute controller from a diferent action
          case "/engine/dispute-summary":
          case "/engine/dispute-summary/":
          case "/engine/dispute-summary/index":
             break;

          // entering from outside the /engine/dispute controller
          case "/disputes":
          case "/disputes/":
          case "/disputes/index":
             break;

          // entering from anywhere else         
          default:
             $this->_helper->redirector( 'request-denied', 'dispute', 'engine' );
          }
/*
       $mailQueue = New Extend_Queue_Model_MailMapper();

       // if the user has logged in before their mail was sent, don't send it
       if( ($mail =  $mailQueue->findDisputeMailFor( $this->_disputeID, $this->_user->getId() ) ) != null )
          {
          $mailQueue->remove( $mail );
          }
*/
       }

    public function indexAction()
       {
       $request = $this->getRequest();
       $exitForm = new Engine_Form_ExitDispute();

       $request = $this->getRequest();

       if( $request->isPost() )
          {
          if( $exitForm->isValid( $request->getPost() ) )
             {
             $values = $request->getPost();
             if( isset( $values['exit'] ) )
                {
                // take the to the dispute status page to informe then what
                $this->_helper->redirector( 'index', 'disputes', 'default' );
                }
             }
          }

       $this->view->exitForm = $exitForm;
       // create the node assembely for constructing the dispute and pass it to the dispute 
       // actions controller
       $nodeAssembly = new Engine_Node_Assembly( $this->_disputeID, $this->_user->getID() );
       $dispute = new Engine_Dispute_Actions( $this->_disputeType, $nodeAssembly );

       // bulid the dispute
       $this->view->rootnode = $dispute->getDispute();
       }


    private function _isValideDispute( $disputeID, $userID )
       {

       $db = new Default_Model_UserToDisputeMapper();
       if( $db->exists( $disputeID, $userID ) )
          {
          $dispute = new Zend_Session_Namespace('Dispute');
          $dispute->ID = $disputeID;
          $dispute->lock;

          $this->_disputeID = $dispute->ID; 

          $disputeDB = new Default_Model_DisputesMapper();
          $dispute = new Default_Model_Disputes();
          $dispute = $disputeDB->find( $this->_disputeID, $dispute );

          if( $dispute == null || $dispute->getDisputeType() == null )
             {
             return false;
             }

          $this->_disputeType = $dispute->getDisputeType();

          return true;
          }
       return false;
       }
    
}


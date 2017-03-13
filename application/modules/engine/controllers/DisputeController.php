<?php

define( "MAIL_DISPUTE_CHANGE", "DISPUTE_UPDATE" );
define( "MAIL_CLAIM_CHANGE",   "CLAIM_UPDATE"   );
define( "MIN_ATTEMPTS",        5   );

class Engine_DisputeController extends Zend_Controller_Action
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
          case "/engine/dispute":
          case "/engine/dispute/":
          case "/engine/dispute/index":
             break;

          // directed to /engine/dispute controller from a diferent action
          case "/engine/dispute/status":
          case "/engine/dispute/potentual-agreemnt":
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

       $mailQueue = New Extend_Queue_Model_MailMapper();

       // if the user has logged in before their mail was sent, don't send it
       if( ($mail =  $mailQueue->findDisputeMailFor( $this->_disputeID, $this->_user->getId() ) ) != null )
          {
          $mailQueue->remove( $mail );
          }

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
                $this->_helper->redirector( 'exit-dispute', 'dispute', 'engine' );
                }
             }
          }

       $this->view->exitForm = $exitForm;
 
       self::_protocol( $request );
       }

    public function statusAction()
       {
       $request = $this->getRequest();

       $this->view->request = //Zend_Debug::Dump( $request->getParams(), "Request", false );

       $userID = $this->_user->getID();

       // create the node assembely for constructing the dispute and pass it to the dispute 
       // actions controller
       $nodeAssembly = new Engine_Node_Assembly( $this->_disputeID, $userID );
       $dispute = new Engine_Dispute_Actions( $this->_disputeType, $nodeAssembly );

       $this->view->outcomeState = $dispute->getRootNode()->getStatus();

       if( $dispute->agreemenReached(  $this->_disputeID, $userID ) == true )
          {
          $this->view->outcomeState = "RESOLVED";
          }
       
       }

    public function exitDisputeAction()
       {
       // nothing to do here
       }

    public function potentualAgreemntAction()
       {
       $request = $this->getRequest();

       $this->view->request = //Zend_Debug::Dump( $request->getParams(), "Request", false );

       $userID = $this->_user->getID();

       // create the node assembely for constructing the dispute and pass it to the dispute 
       // actions controller
       $nodeAssembly = new Engine_Node_Assembly( $this->_disputeID, $userID );
       $dispute = new Engine_Dispute_Actions( $this->_disputeType, $nodeAssembly );

       // double check that the disput can be resolved
       if( $dispute->porentualResolution(  ) != true)
          {
          $this->_helper->redirector( 'status', 'dispute', 'engine' );
          }

       // get the number of resolution attempts
       $disputesAdapter = new Default_Model_DisputesMapper();
       $disputeStatus   = new Default_Model_Disputes();
       $disputeStatus   = $disputesAdapter->find( $this->_disputeID, $disputeStatus );

       $this->view->numberOfRejections = $disputeStatus->getNumRejections(); 

       //Zend_Debug::Dump( "Potentual Agreement found" );
       // update dispute status
       // refer user to potentual agreement Action
       $this->view->rootnode = $dispute->getRootNode();

       if( $dispute->agreemenReached( $this->_disputeID, $userID ) == true )
          {
          // take the user back to the dispute status page
          $this->_helper->redirector( 'status', 'dispute', 'engine' );

          }

       if( $this->view->numberOfRejections > MIN_ATTEMPTS )
          {
          $form = new Engine_Form_SettleOrEndDispute();
          }
       else
          {
          $form = new Engine_Form_SettleDispute();
          }

       $request = $this->getRequest();

       if( $request->isPost() )
          {
          if( $form->isValid( $request->getPost() ) )
             {
             $values = $request->getPost();
             $userClaim = $dispute->getClaimStateAdapter()->findClaimFor( $this->_disputeID,
                                                                 $userID,
                                                                 $dispute->getRootNode()->getID(),
                                                                 $dispute->getRootNode()->getRecomendedOutcome()->getClaimID()
                                                                 );
             if( isset( $values['decline'] ) )
                {
                //Zend_Debug::Dump( $values );
                $dispute->agreementRejected( $this->_disputeID, $userID );
                $userClaim->setStatus( 'REJECTED' );
                $dispute->logClaim( $userClaim );
                
                // take the to the dispute status page to informe then what
                $this->_helper->redirector( 'status', 'dispute', 'engine' );
                }
             else if( isset( $values['accept']) )
                {
                //Zend_Debug::Dump( $values );
                $dispute->agreementAccepted( $this->_disputeID, $userID );
                $userClaim->setStatus( 'ACCEPTED' );
                $dispute->logClaim( $userClaim );
                
                // take the to the dispute status page to informe then what
                $this->_helper->redirector( 'status', 'dispute', 'engine' );
                
                }
             else if( isset( $values['end_dispute']) && $this->view->numberOfRejections >  MIN_ATTEMPTS )
                {
                $userClaim->setStatus( 'UNRESOLVABLE' );
                $dispute->logClaim( $userClaim );
                $this->_helper->redirector( 'unresolvable', 'dispute', 'engine' );
                }
             }
          }

       $this->view->form = $form; 
       }

    public function unresolvableAction()
       {
       $disputesAdapter = new Default_Model_DisputesMapper();
       $disputeStatus   = new Default_Model_Disputes();
       $disputeStatus   = $disputesAdapter->find( $this->_disputeID, $disputeStatus );

       if( $disputeStatus->getNumRejections() <  MIN_ATTEMPTS )
          {
	  $this->_helper->redirector( 'request-denied', 'dispute', 'engine' );
          }
       
       $form = new Engine_Form_EndDispute();
       
       $request = $this->getRequest();
       if( $request->isPost() )
          {
          if( $form->isValid( $request->getPost() ) )
             {
             $values = $request->getPost();
             if( isset( $values['decline'] ) )
                {
                
                $disputeStatus->setStatus( 'UNRESOLVABLE' )
                              ->setOutcomeID( 2 );
                $disputesAdapter->update( $disputeStatus );
                // exit the dispuet
                $this->_helper->redirector( 'index', 'disputes', 'default' );
                }
             else if( isset( $values['accept']) )
                {

                // take them back to the agreement page
                $this->_helper->redirector( 'potentual-agreemnt', 'dispute', 'engine' );
                }
             }
          }

       $this->view->form = $form; 
       }
 

    public function requestDeniedAction()
       {
       // nothing to do here
       }

   private function _protocol( $request )
       {
       //$this->view->request = Zend_Debug::Dump( $request->getParams(), "Request", false );


       // create the node assembely for constructing the dispute and pass it to the dispute 
       // actions controller
       $nodeAssembly = new Engine_Node_Assembly( $this->_disputeID, $this->_user->getID() );
       $dispute = new Engine_Dispute_Actions( $this->_disputeType, $nodeAssembly );

       if( $request->isPost() )
          {
          // exand the node if requested bu user // not actualy used
          if( $request->getPost( "expandeNode" ) != null &&  $request->getPost( "nodeID" ) )
             {
             $dispute->initSubNodesClaims( (int)$request->getPost( "nodeID" ) );
             }
          // update the node
          else if( $request->getPost( "nodeID" ) != null && $request->getPost( "claimID" ) != null )
             {
             // get node information from post
             $claimUpdate = new Engine_Models_ClaimState();
             $claimUpdate->setDisputeID( $this->_disputeID )
                         ->setUserID( $this->_user->getID() )
                         ->setNodeID( (int)$request->getPost( "nodeID" ) )
                         ->setClaimID( (int)$request->getPost( "claimID" ) )
                         ->setSysInference( null )
                         ->setUserInference( null )
                         ->setStatus( null );

             // update node
             //$dispute->updateClaim( $claimUpdate );
             $dispute->updateStatus( $claimUpdate );
             $dispute->logClaim( $claimUpdate );

             // update the status of the parent node if it's childrent arn't is dissagrement then check it's parent
             $parentNode = $dispute->getParentNode( $claimUpdate->getNodeID() );
             $dispute->inferUserNode( Engine_Inference_BayesianSQL::getInstance(), $parentNode, $this->_user->getID() );
             $dispute->inferSysNode( Engine_Inference_BayesianSQL::getInstance(),  $parentNode, $this->_user->getID() );
                
             $parentClaimUpdate = new Engine_Models_ClaimState();
             $parentClaimUpdate->setDisputeID( $this->_disputeID )
                               ->setUserID( $this->_user->getID() )
                               ->setNodeID( $parentNode->getID() );
             $dispute->updateParentClaimStatus( $parentClaimUpdate );
             //$dispute->logClaim( $parentClaimUpdate, "system" );
             }// end node update

          }// end dealing with post data


       // test if a potentual agreement can be reached
       if( $dispute->porentualResolution(  ) == true )
          {
          $dispute->inferSysNode( Engine_Inference_BayesianSQL::getInstance(), $dispute->getRootNode(), $this->_user->getID() );

          // update the mail queue with a potentual res message
          $this->_queueMail( $this->_user->getID(), $this->_disputeID, MAIL_DISPUTE_CHANGE );

          // refer user to potentual agreement Action
          $this->_helper->redirector( 'potentual-agreemnt', 'dispute', 'engine' );
          }
       // if a chnage may have occureed update the mail queue
       else if ( isset( $claimUpdate ) )
          {
          $this->_queueMail( $this->_user->getID(), $this->_disputeID, MAIL_CLAIM_CHANGE );
          }

       // get the disputed nodes and make sure they exist in the dispute
       // before the dispute is built
       $disputed_claims = $dispute->getClaimsInDispute( $this->_user->getID() );
       foreach( $disputed_claims as $claim )
          {
          // make sure the nodes claims are initated
          //Zend_Debug::Dump( "Adding claim (" . $claim->getID() . ") to Node (" . $claim->getNodeID() . ")" );
          $dispute->initSubNodesClaims( $claim->getNodeID() ); 
          }

       $unclaimed_claims = $dispute->getUnclaimedClaims( $this->_user->getID() );
       $agreed_claims = $dispute->getClaimsInAgreement( $this->_user->getID() );
       $disagreed_claims = $dispute->getClaimsInDisagreemnt( $this->_user->getID() );
       $asserted_claims = $dispute->getClaimsAsserted( $this->_user->getID() );
       $pendingReview_claims = $dispute->getClaimsInStateOf( $this->_user->getID(), 'PENDING_REVIEW' );

       $this->view->subMenus = array();
       $this->view->subMenus[] = $this->_buildSubMenu( "pending_review-nodes", "Review Issues:", $pendingReview_claims ); 
       $this->view->subMenus[] = $this->_buildSubMenu( "disputed-nodes", "Issues Disputed:", $disputed_claims ); 
       $this->view->subMenus[] = $this->_buildSubMenu( "disagreed-nodes", "Issues Disagreed:", $disagreed_claims ); 
       $this->view->subMenus[] = $this->_buildSubMenu( "agreed-nodes", "Issues Agreed:", $agreed_claims ); 
       $this->view->subMenus[] = $this->_buildSubMenu( "unclaimed-nodes", "Issues Unclaimed:", $unclaimed_claims ); 
//       $this->view->subMenus[] = $this->_buildSubMenu( "asserted-nodes", "Issues waiting on a responce:", $asserted_claims ); 

       // bulid the dispute
       $this->view->rootnode = $dispute->getDispute();
       }


    private function _queueMail( $userId, $disputeId, $mailType )
       {
       $userDisputeAdapter  = new Default_Model_DisputesAndUsersMapper();
       
       $res = $userDisputeAdapter->getOtherUsersID( $userId, $disputeId );
       if( $res == null )
          {
          return null;
          }

       $queueAdapter = new Extend_Queue_Model_MailMapper();
       
       $queuedMail = $queueAdapter->findDisputeMailFor( $disputeId, $res->userID);

       if( $queuedMail == null )
          {
          $mail = new Extend_Queue_Model_Mail();
          $mail->setUserId( $res->userID )
               ->setMailType( $mailType )
               ->setDisputeId( $disputeId );

          $queueAdapter->save( $mail );
          }

        // check if the message in the queue is a change message or
        // a dispute update
        else if( $queuedMail->getMailType() ==  MAIL_CLAIM_CHANGE )
          {
          // if the current message is a dispute change, update the 
          // message type
          if( $mailType == MAIL_DISPUTE_CHANGE )
             {
             $queuedMail->setMailType( $mailType );
             $queueAdapter->save( $queuedMail );
             }
          }

       return true;
       }

    private function _buildSubMenu( $id, $title, array $claims )
       {
       $claimsAdapter = new Engine_Node_Model_NodeMapper();


       $listItems = array();
       foreach( $claims as $claim )
          {
          $claim = $claimsAdapter->find( $claim->getNodeID() );
          $listItems[$claim->getTitle()]['url'] = array( 'action'     => 'index', 
                                                         'controller' => 'dispute',
                                                         'module'     => 'engine'
                                                         );
          $listItems[$claim->getTitle()]['anchor'] = 'anchor_' . $claim->getID();

          }

       $menu = array( "id"    => "submenu-" . $id, 
                      "title" => count($claims) . " " .$title, 
                      "items" => $listItems 
                      );

       return $menu;
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


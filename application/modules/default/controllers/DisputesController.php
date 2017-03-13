<?php

class DisputesController extends Zend_Controller_Action
{

    private $_user;

    public function init()
    {
       //$this->_user = Extend_User::getInstance();
       $this->_user = Zend_Auth::getInstance();

       if( !$this->_user->hasIdentity() )
          {
	  $this->_helper->redirector('Request-Denied', 'Auth' );
          }

       // make sure the session namespace is cleard befor entering into a dispute;
       $sessionDispute = new Zend_Session_Namespace('Dispute');
       Zend_Session::namespaceUnset( 'Dispute' );

    // track last page visited
    $urlHistory = new Extend_Helpers_RefPage();
    $previousPage = $urlHistory->getLastVisited();
    $urlHistory->save( $this->_request->getRequestUri() );
    }

    public function indexAction()
    {
        // action body
       $this->view->currentDisputes = array();
       $this->view->resolvedDisputes = array();

       $userId = $this->_user->getIdentity()->id;

       $dbAdapter = new Default_Model_DisputesAndUsersMapper();

       $mailQueue = New Extend_Queue_Model_MailMapper();
  
/*   
       // if the user has logged in before their mail was sent, don't send it
       if( ($mail =  $mailQueue->findExistingMailFor( $userId ) ) != null )
          {
          foreach( $mail as $item )
             {
             $mailQueue->remove( $item );
             }
          }
*/


       $disputes = $dbAdapter->getCurrentDisputes( $userId );

       $this->view->currentDisputes = $disputes;

       $disputes = $dbAdapter->getResolvedDisputes( $userId );
       $survey = false;
       // check if the survey has been taken
       $surveyResDB = new Default_Model_SurveyResultsMapper();
       
       $this->view->takenSurvey = $surveyResDB->hasTakenSurvey( $userId );
       $this->view->resolvedDisputes = $disputes;

       $dbUser = new Default_Model_AuthUsersMapper();
       $user   = new Default_Model_AuthUsers();
 
       $user = $dbUser->find( $userId, $user );
       if( $user == null )
          {
          throw new exception( "Faital Error! User data not found. " );
          }
//       $this->view->userID   = $userId;
       $this->view->user = $user;

       $dbEIData = new Administration_Model_EIDataMapper();

       $eiData = $dbEIData->findActive();
 
       $this->view->eiData = $eiData;

/*
*/
    }
    
    public function outcomeAction()
    { 
    $this->_helper->redirector( 'Index', 'Dispute-Summary', 'engine' );
    // display the dispute summary 
    }


    public function newAction()
    {

       $form = new Default_Form_InitiateDispute();
       $request = $this->getRequest();

       if( $request->isPost() )
          {
          if( $form->isValid( $request->getPost() ) )
             {
             if( $this->_initiateDispute( $form->getValues( ) ) == true )
                {
                $this->_helper->redirector('index', 'Disputes' );
                }
             // TODO handel failed dispute initiatipn properly
             }
          }

      $this->view->form = $form;

        
    }
    
    // inform user of the new dispute
    protected function _initiateDispute( $values )
    {

    if( $this->_user->getEmail() == $values['email'] )
       {
       throw new Exception( "ERROR! you cannot create a dispute with yourself." );
       }

    // test for exixting users
    if( null !== ( $oponentUser = $this->_emailExists( $values['email'] ) ) )
       {
       // test for exixting dispute of the same type between existing user
       $dbAdapter = new Default_Model_DisputesAndUsersMapper();

       if( null !== ( $existing = $dbAdapter->existingDispute( $this->_user->getId(), 
                                                               $oponentUser->getId()
                                                               ) ) )
          {
          // do something better here
          throw new Exception( "Unable to create the requested dispute, ".
                               "a dispute of the same nature currently ".
                               "exists, between the you and your nominated ".
                               "disputante (REF: dID-".$existing ." )."
                              );
          }
       }
    else 
       {
       // TODO send an email to user
       // TODO have a resend option somewhere
       // TODO create a dumbie user to hold the position
       $this->_contactUser();
       }

    // create dispute
    $dbDispute    = new Default_Model_DisputesMapper();
    $disputeModel = new Default_Model_Disputes();

    $disputeModel->setDisputeType( $values['DisputeType'] )
                 ->setStatus( CREATEING );
 
    // create the dispute and update the id for the model
    $disputeModel->setId( $dbDispute->initiate( $disputeModel ) );  

    // add user(s) to dispute
    $dbUser2Dispute  = new Default_Model_UserToDisputeMapper();
    $utd1Mapper = new Default_Model_UserToDispute();
    $utd2Mapper = new Default_Model_UserToDispute();

    $utd1Mapper->setDisputeID( $disputeModel->getId()  )
               ->setUserID( $this->_user->getId() );

    $utd2Mapper->setDisputeID( $disputeModel->getId()  )
               ->setUserID( $oponentUser->getId() );
    
    $dbUser2Dispute->save( $utd1Mapper );
    $dbUser2Dispute->save( $utd2Mapper );
    // validate the dispute has be constructed correctly


    // update the state of the dispute to indicate that its ready
    // to start
    $disputeModel->setStatus( INITIATED );
    $dbDispute->update( $disputeModel );

    return true;
    }  
    
    // inform user of the new dispute
    protected function _contactUser ()
    {
       throw new Exception( "TODO: write code to handle new users ".
                            "when forming a new dispute" ); 

    }  

    protected function _emailExists ( $email )
    {
       $dbAdapter = new Default_Model_AuthMapper();
       $authModel = new Default_Model_Auth();
       if( null !== ($authModel = $dbAdapter->findByEmail( $email, $authModel ) ) )
          {
          return $authModel;
          } 

       return null;
    }  

}




<?php


class Administration_DisputeController extends Zend_Controller_Action
   {
   public function init()
      {
      /* Initialize action controller here */

      $urlHistory = new Extend_Helpers_RefPage();
      $urlHistory->save( $this->_request->getRequestUri() );
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

      $this->validationID = $auth->getIdentity()->id;

      }

   public function indexAction()
      {
      $this->view->request = $this->getRequest()->getPost();
      //$this->view->request = $_POST;
      $dbAuthUser = new Default_Model_AuthUsersMapper();
      $request = $this->getRequest();

      if( $request->isPost() )
         {
         $updates = $request->getPost( 'userToUpdate' );
         if( is_array( $updates ) && count( $updates ) > 0 )
            {
            if( $request->getPost( 'updateEI' ) )
               {
               foreach( $updates as $userID )
                  {
                  $user = $dbAuthUser->find( $userID, new Default_Model_AuthUsers() );
                  $user->setEITest( $request->getPost( 'eiTest_' . $user->getID() ) );
                  $dbAuthUser->save( $user );
                  }
               }
            else if( $request->getPost( 'updateDispute' ) )
               {
               $u2dAdapter     = new Default_Model_UserToDisputeMapper();
               $disputeAdapter = new Default_Model_DisputesMapper();
               $authAdapter    = new Default_Model_AuthMapper();

               $docs = array();
               $pats = array();
               for( $xx = 0; $xx < count( $updates ); $xx++ )
                  {
                  $user = $dbAuthUser->find( $updates[$xx], new Default_Model_AuthUsers() );
                  if( $user->getRole() == null )
                     {
                     if( $user->getGender() == 'M' )
                        {
                        $docs[] = $user;
                        }
                     else
                        {
                        $pats[] = $user;
                        }
                     }
                  }
               if( count( $docs ) > 0 && count( $docs ) > count( $pats ) )
                  {
                  while( count( $docs ) > count( $pats ) )
                     {
                     $user = array_pop( $docs );
                     array_push( $pats, $user );
                     }
                  }
               else if( count( $pats ) > 0 && count( $docs ) < count( $pats ) )
                  {
                  while( count( $docs ) < count( $pats ) )
                     {
                     $user = array_pop( $pats );
                     array_push( $docs, $user );
                     }
                  } 

               shuffle( $docs );
               shuffle( $pats );

               for( $xx = 0; $xx < count( $docs ) && $xx < count( $pats ); $xx++ )               
                  {
                  $docUser = $docs[$xx];
                  $patUser = $pats[$xx];

                  $docUser->setRole( DOCTOR );
                  $patUser->setRole( PATIENT );

                  $dispute = new Default_Model_Disputes();
                  $dispute->setDisputeType( 'EHR' )
                          ->setStatus( 'ACTIVE' );

                  $disputeId = $disputeAdapter->initiate( $dispute );
 
                  $doc = new Default_Model_UserToDispute();
                  $doc->setDisputeId( $disputeId )
                      ->setUserID( $docUser->getID() );

                  $pat = new Default_Model_UserToDispute();
                  $pat->setDisputeId( $disputeId )
                      ->setUserID( $patUser->getID() );

                  $u2dAdapter->save( $doc );
                  $u2dAdapter->save( $pat );

                  $dbAuthUser->save( $docUser );
                  $dbAuthUser->save( $patUser );

                  $docMsg = $this->_createMessage( $docUser->getRole() );
                  $patMsg = $this->_createMessage( $patUser->getRole() );
                       
                  $docAuth = $authAdapter->find( $docUser->getID(), new Default_Model_Auth() );
                  $patAuth = $authAdapter->find( $patUser->getID(), new Default_Model_Auth() );
 
                  $this->_sendNotifacation( $docAuth->getEmail(), "ReConsider: dispute now activated", $docMsg );
                  $this->_sendNotifacation( $patAuth->getEmail(), "ReConsider: dispute now activated", $patMsg );
                  }
               }
            }
         }

      $users = $dbAuthUser->findActiveUserData();

      $this->view->users = $users;

      }
   private function _createMessage( $role )
      {
      $message = new Zend_View();
      $message->setScriptPath( APPLICATION_PATH . '/../scripts/emails/' );
      $message->assign( 'href_base', $this->view->baseUrl()  );

      switch( $role )
         {
         case DOCTOR:
            $renderedMessage = $message->render( "doctorNotifacation.phtml" );
            break;
         case PATIENT:
            $renderedMessage = $message->render( "patientNotifacation.phtml" );
            break;
         default:
            $renderedMessage = $message->render( "unrequiredNotifacation.phtml" );
         }

      return $renderedMessage;
      }

   private function _sendNotifacation( $address, $subject, $message )
      {
      if( !isset( Zend_Registry::get( 'contacts' )->smptServer ) )
         {
         $tr = new Zend_Mail_Transport_Sendmail( Zend_Registry::get( 'contacts' )->sys_sendmail );
         }
      else
         {
         $tr = new Zend_Mail_Transport_Smtp( Zend_Registry::get( 'contacts' )->smptServer,  
                                          Zend_Registry::get( 'contacts' )->smptConfig->toArray() );
         }

      Zend_Mail::setDefaultTransport($tr);
      Zend_Mail::setDefaultFrom( Zend_Registry::get( 'contacts' )->sys_sendmail, 
                                 Zend_Registry::get( 'contacts' )->sys_sendmailName );
      Zend_Mail::setDefaultReplyTo( Zend_Registry::get( 'contacts' )->no_reply, 
                                    Zend_Registry::get( 'contacts' )->no_replyName );
/*
      // attachement setup
      $path            = APPLICATION_PATH . "/../public/Download/";
      $file            = "plsc1-EB.pdf";

      $at = new Zend_Mime_Part( file_get_contents( $path.$file ) );
      $at->type        = 'application/pdf';
      $at->disposition = Zend_Mime::DISPOSITION_INLINE;
      $at->encoding    = Zend_Mime::ENCODING_BASE64;
      $at->filename    = $file;
*/

      $mail = new Zend_Mail();
      $mail->addTo( $address, "The participant" );
      $mail->setSubject( $subject);
      $mail->setBodyHTML( $message );

      $mail->send();
      }


   public function eiAction()
      {
      $dbEI = new Administration_Model_EIDataMapper();

      $form = new Administration_Form_AddEI();
      $request = $this->getRequest();

      if( $request->isPost() )
         {
         if( $request->getPost('updateEI') != NULL )
            {
            if( $request->getPost('Deactivate' ) != null )
               {
               $dbEI->deactivate( );
               }
            else if(  $request->getPost( 'Activate' ) != null )
               {
               $dbEI->activate( $request->getPost('Activate' ) );
               }
            else if(  $request->getPost( 'remove' ) != null )
               {
               $dbEI->remove( $request->getPost('remove' ) );
               }
            }
         else
            {
            if( $form->isValid( $request->getPost() ) )
               {
               $ei = new Administration_Model_EIData();
               $ei->setEIID( $request->getPost('eiid' ) )
                  ->setPassword( $request->getPost('password' ) )
                  ->setActive( false );

               $dbEI->save( $ei );
               }
            }
          }
      $this->view->form   = $form;
      $this->view->eidata = $dbEI->fetchAll();
      }

}

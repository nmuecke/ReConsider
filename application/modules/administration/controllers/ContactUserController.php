<?php
class Administration_ContactUserController extends Zend_Controller_Action
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
      $dbAuthUser = new Default_Model_AuthUsersMapper();
      $users = $dbAuthUser->findAllUserData();


      $form = new Administration_Form_MailUser();
      $to = array();
      foreach( $users as $user )
         { 
         $to[$user['id']] = $user['firstName'] . ' ' . $user['lastName'] . ' (' . $user['id'] . ')';
         }
      $dropBox = $form->getElement( 'to' );
      $dropBox->addMultiOptions( $to );
 
      $request = $this->getRequest();

      if( $request->isPost() )
         {

          if( $form->isValid( $request->getPost() ) )
             {
             $at = null;
             if( $form->getValue( 'doc_path' ) != null )
                {
                $path = Zend_Registry::get( 'config' )->app->uploadPath;

                /* Uploading Document File on Server */
                $upload = new Zend_File_Transfer(); //_Adapter_Http();
                $upload->setDestination( $path );
               
                $file = $upload->getFileInfo('doc_path');
                if( $upload->isUploaded($file) && $upload->isValid($file) ) 
                    {
                    $doc_path = $form->getElement( 'doc_path' );
                    $doc_path->addErrors( $upload->getMessages() );
                    $doc_path->addError( "Unable to upload file!" );
                    }
                else
                   {
                   $upload->receive();

                   //add an attachement
                   $at = new Zend_Mime_Part( file_get_contents( $upload->getFileName('doc_path') ) );
//                   $at->type        = $upload->getMimeType('doc_path');
                   $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                   $at->encoding    = Zend_Mime::ENCODING_BASE64;
                   $at->filename    = $upload->getFileName('doc_path', false );
                   }
                }
            if( $form->isValid( $request->getPost()) )
               { 
               $dbAuth = new Default_Model_AuthMapper();
               $auth = $dbAuth->find( $request->getPost('to'), new Default_Model_Auth() );

               $this->_sendNotifacation( $auth->getEmail(), 
                                         $request->getPost( 'subject' ), 
                                         $request->getPost( 'message' ), 
                                         $at 
                                        );
               if( $at != null )
                  {
                  // clean up
                  unlink( $upload->getFileName('doc_path') );
                  }
               }
            }
         }

      if( $request->getParam( 'template' ) != null && $request->getParam( 'to' ))
         {
         $template = $request->getParam( 'template' );
         $to       = $request->getParam( 'to' );
         $form = $this->_emailTemplate( $form, $template, $to );
         }
  
      $this->view->form = $form;
      }

   public function eiTestResultsAction()
      {
      }

   private function _emailTemplate( Zend_Form $form, $template, $userID )
      {
      $to      = $form->getElement( 'to' );
      $subject = $form->getElement( 'subject' );
      $message = $form->getElement( 'message' );

      $mailView = new Zend_View();
      $mailView->setScriptPath( APPLICATION_PATH . '/modules/administration/views/emails/' );

      switch( $template )
         {
         case 'eiTestResults':
            $op = $to->getMultiOptions();
            // remove all other options
            $to->setMultiOptions( array( $userID => $op[$userID] ) )
               ->setValue( $userID );
            $subject->setValue( $mailView->render('eiTestResults_Subject.phtml') );
            $message->setValue( $mailView->render('eiTestResults_Message.phtml') );     
            break;
 
         default:
            $subject->setValue( "" );
            $message->setValue( "" );     
         }



      return $form;
      }

   private function _sendNotifacation( $address, $subject, $message, $attachement )
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


      $mail = new Zend_Mail();
      $mail->addTo( $address, "The participant" );
      $mail->setSubject( $subject);
      $mail->setBodyText( $message );
      //$mail->setBodyHTML( $message );
      if( $attachement != null )
         {
         $mail->addAttachment( $attachement );   
         }
      $mail->send();
      }
   }


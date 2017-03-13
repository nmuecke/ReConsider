<?php

class Engine_UpdateUser_Email extends Engine_UpdateUser_Abstract
   {
   private $base_message_path;
   private $dinamicContent;
   private $setting_registory;

    protected function _resendEmailVerifacation( Default_Model_Auth $authModel )
       {
       // message setup
       $message = new Zend_View();
       $message->setScriptPath( APPLICATION_PATH . '/modules/default/views/emails/' );
       $message->assign( 'validationCode', $authModel->getEmailValidationCode() );
       $message->assign( 'href_base', $this->view->baseUrl() . "/Auth/Validate-Email" );
       $message->assign( 'href_params', "/vc/". $authModel->getEmailValidationCode() );

       // mail trancport setup
       if( !isset( Zend_Registry::get( 'contacts' )->smptServer )
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
       $mail->addTo( $authModel->getEmail(), "New User" );
       $mail->setSubject( "ReConsider registration validation" );
       $mail->setBodyHtml( $message->render('resendValidation.phtml') );

       $mail->send();
       }
   }

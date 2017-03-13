<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */
class AuthController extends Zend_Controller_Action
{
   protected $_Form_Login;
   protected $_Form_CreateAccount;
   protected $_Form_ValidateAccount;
   protected $_Form_UpdateAccount;
   protected $_Form_LostAccount;
   protected $_terms;

   protected $_flashMessenger = null;

    public function init()
    {
    // track last page
    $urlHistory = new Extend_Helpers_RefPage();
    $this->previousPage = $urlHistory->getLastVisited();
    $urlHistory->save( $this->_request->getRequestUri() );

    $this->currentPage = $urlHistory->getLastVisited();

        /* Initialize action controller here */
    // $this->_Form_Login         = "Default_Form_Auth_Login";
    // $this->_Form_CreateAccount = "Default_Form_Auth_CreateAccount";
    // $this->_Form_TermsOfUse    = "Default_Form_Auth_TermsOfUse";
    // $this->_Form_UpdateAccount = "Default_Form_Auth_UpdateAccount";

    $this->_Form_Login           = "Default_Form_Auth_LoginStudy";
    $this->_Form_CreateAccount   = "Default_Form_Auth_CreateStudyAccount";
    $this->_Form_ValidateEmail   = "Default_Form_Auth_ValidateEmail";

    $this->_Form_UpdateAccount   = "Default_Form_Auth_UpdateAccount";
    $this->_Form_LostAccount     = "Default_Form_Auth_LostAccount";

    $this->_terms                = array( "hasAcceptedPlainLanguageStatement", 
                                          "hasAcceptedInformedConsent", 
                                          "hasAcceptedTermsOfUse",
                                         );

    $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }

    protected function getForm( $type )
       {
       $form = new $type();

       return $form;
       }

    public function indexAction()
    {

       if( $this->_isLoggedIn() == true )
          {
//          $this->_helper->redirector('index', 'index');
          $this->_helper->redirector('index', 'disputes');
          }

       $form = $this->getForm( $this->_Form_Login );
       $request = $this->getRequest();

       if( $request->isPost() ) 
          {
          if( $form->isValid( $request->getPost() ) ) 
             {
             if( ($auth = $this->_process( $form->getValues(), $this->_getAuthAdapter() )) != null )
                {

                if( $auth->getIdentity()->role == 'baned' )
                   {
                   Zend_Auth::getInstance()->clearIdentity();
                   $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->bandedAccount );
                   $this->_helper->redirector( 'index', 'Auth' );
                   }
                else if( $auth->getIdentity()->emailIsValid )
                   {
                   // We're authenticated! Redirect to the home page
                   $this->_updateAccess( $auth->getIdentity()->id );
                   //$this->_helper->redirector('index', 'index');
                   $this->_helper->redirector('index', 'disputes');
                   }

                Zend_Auth::getInstance()->clearIdentity();
                $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->unvalidatedAccount );
                $this->_helper->redirector( 'Validate-Account', 'Auth' );
                }
             // recover a lost password
             else  if( ($auth = $this->_process( $form->getValues(), $this->_getAuthRecoveryAdapter() )) != null )
                {
                if( $auth->getIdentity()->emailIsValid )
                   {
                   // We're authenticated! Redirect to the home page
                   $this->_updateAccess( $auth->getIdentity()->id );
                   //$this->_helper->redirector('index', 'index');
                   $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->changePassword );
                   $this->_helper->redirector('User', 'Auth');
                   }
                   
                Zend_Auth::getInstance()->clearIdentity();
                $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->unvalidatedAccount );
                $this->_helper->redirector( 'Validate-Account', 'Auth' );
                }

             $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->failedUnamePasswd );
             }
          }
       $this->view->msgs = $this->_flashMessenger->getMessages();
       $this->view->form = $form;

    }

    public function validateAccountAction()
      {
      if( $this->_isLoggedIn() == true )
          {
//          $this->_helper->redirector('index', 'index');
          $this->_helper->redirector('index', 'disputes');
          }

       $form = $this->getForm( $this->_Form_ValidateEmail );
       $request = $this->getRequest();

       $value = "";
       if( isset( $request->vc ) )
          {
          $form->getElement("validationCode")->setValue( $request->vc );
          }

       if( $request->isPost() )
          {
          if( $form->isValid( $request->getPost() ) )
             {   
             if( $form->reSend->isChecked() )
                {
                $values = $request->getPost();
                $this->_resendEmail( $values['email'] );
                $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->validationCodeSent );
                }
             else
                {
                $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->emailValidated );
                $this->_helper->redirector('index', 'Auth');
                }
             }
          }

       $this->view->msgs = $this->_flashMessenger->getMessages();
       $this->view->form = $form;

      }


    public function userAction()
       {
       if( $this->_isLoggedIn() != true )
          {
          $this->_helper->redirector( 'auth', 'create-account');
          }

       $form = $this->getForm( $this->_Form_UpdateAccount );
       $request = $this->getRequest();

       if( $request->isPost() )
          {
          $values = $request->getPost();
          if( $form->isValid( $values ) )
             {
             $authAddapter = new Default_Model_AuthMapper();
             $user         = new Default_Model_Auth();

             $user = $authAddapter->find( Zend_Auth::getInstance()->getIdentity()->id, $user );
             $user->newPassword( $values['newPassword'] )
                  ->setRecoveryPassword( null )
                  ->setRecoveryPassword_salt( null );

             $authAddapter->update( $user );
             $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->accountUpdated ); 
             $this->_helper->redirector('index', 'disputes');
             }
          }

       $this->view->msgs = $this->_flashMessenger->getMessages();
       $this->view->form = $form;
       }

    public function lostAccountAction()
       {
       // user is logged in take them home
       if( $this->_isLoggedIn() == true )
          {
          $this->_helper->redirector('index', 'disputes');
          //$this->_helper->redirector('index', 'index');
          }

       $form = $this->getForm( $this->_Form_LostAccount );
       $request = $this->getRequest();

       if( $request->isPost() )
          {
          $values = $request->getPost();
          if( $form->isValid( $values ) )
             {
             $authAddapter = new Default_Model_AuthMapper();
             $user         = new Default_Model_Auth();


             $user = $authAddapter->findByEmail( $values['email'], $user );

             // create a new password
             $newPassword = substr( sha1( mt_rand() . $user->getUsername() ), 0 , 10  );
             $user->newRecoveryPassword( $newPassword );

             $authAddapter->update( $user );

             $this->_sendEmailLostAccount( $user, $newPassword );
             $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->lostAccountSent  );
             $this->_helper->redirector('index', 'Auth');
             }
          }

       $this->view->msgs = $this->_flashMessenger->getMessages();
       $this->view->form = $form;


       }

    public function createAccountAction()
    {
       // user is logged in take them home
       if( $this->_isLoggedIn() == true )
          {
          $this->_helper->redirector('index', 'disputes');
          //$this->_helper->redirector('index', 'index');
          }
       // user has enter the page from the wrong location, take them to the consent page
       else if( $this->_hasConcented() == false )
          {
          $this->_helper->redirector('index', 'Sign-Up');
          }

       $form = $this->getForm( $this->_Form_CreateAccount );
       $request = $this->getRequest();
       
       if( $request->isPost() )
          {
          if( $form->isValid( $request->getPost() ) ) 
             {
             if( $this->_newUser( $form->getValues() ) )
                {
                $this->_helper->redirector('account-created', 'auth');
                }
             }
          }

       $this->view->form = $form;

    }

    public function accountCreatedAction()
       {
       $this->view->msgs = $this->_flashMessenger->getMessages();

       }

    protected function _hasConcented()
       {

       $sessionSignUp = new Zend_Session_Namespace('SignUp');
 

       foreach( $this->_terms as $term )
          {
          if( $sessionSignUp->terms[$term] != true )
             {
             return false;
             }
          }

       // make sure that the dispute was enter via the propper chanles
       switch( strtolower( $this->previousPage ) )
          {
          // re-entering the /default/create account controller from the same action
          case strtolower( $this->currentPage ):
          

          // directed to /engine/dispute controller from a diferent action
          case "/sign-up/terms-of-use":
          case "/sign-up/terms-of-use/":
          case "/sign-up/terms-of-use/index":
             return true;
             break;

          // entering from anywhere else         
          default:
             return false;
          }
       }

    public function requestDeniedAction()
    {
        // action body
    }


    public function logoutAction()
    {
       if( $this->_isLoggedIn() != true )
          {
          $this->_helper->redirector('auth', 'index');
          }

       Zend_Auth::getInstance()->clearIdentity();
       $this->_helper->redirector( 'index' ); // back to login page
    }

    protected function _process( $values, $adapter )
    {

       $adapter->setIdentity( $values['username'] ); 
       $adapter->setCredential( $values['password'] );

       $auth = Zend_Auth::getInstance();
       $result = $auth->authenticate( $adapter );

       if( $result->isValid() )
          {
          $user = $adapter->getResultRowObject();
          $auth->getStorage()->write( $user );
          return $auth;
          }
      
      return null;
    }

    protected function _getAuthAdapter()
    {
        
      $dbAdapter = Zend_Db_Table::getDefaultAdapter();
      $authAdapter = new Zend_Auth_Adapter_DbTable( $dbAdapter );
        
      $authAdapter->setTableName( 'auth' )
                  ->setIdentityColumn( 'username' )
                  ->setCredentialColumn( 'password' )
                  ->setCredentialTreatment( 'SHA1(CONCAT( ?, password_salt) )' );
                  //->setCredentialTreatment( 'SHA1(CONCAT( ?, username, id) )' );
            

      return $authAdapter;
    }
    protected function _getAuthRecoveryAdapter()
    {

      $dbAdapter = Zend_Db_Table::getDefaultAdapter();
      $authAdapter = new Zend_Auth_Adapter_DbTable( $dbAdapter );

      $authAdapter->setTableName( 'auth' )
                  ->setIdentityColumn( 'username' )
                  ->setCredentialColumn( 'recoveryPassword' )
                  ->setCredentialTreatment( 'SHA1(CONCAT( ?, recoveryPassword_salt) )' );
                  //->setCredentialTreatment( 'SHA1(CONCAT( ?, username, id) )' );


      return $authAdapter;
    }


    protected function _newUser( $values )
       {
       $dbAdapter = new Default_Model_AuthMapper();
       $authModel = new Default_Model_Auth();

       $authModel->setUsername( $values['username'] )
                 ->setRealname( $values['real_name'] )
                 ->setEmail(    $values['email'] )
                 ->setRole( 'user' )
                 ->newEmailValidationCode()
                 ->newPassword( $values['password'] );

       if( ( $id = $dbAdapter->save( $authModel ) ) == null )
          {
          $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->error->emailExists );
          return false;
          }

       $dbNames    = new Default_Model_ListOfNamesMapper();
       $dbAuthUser = new Default_Model_AuthUsersMapper();
       $userModel  = new Default_Model_AuthUsers();

       do {
          $firstName = $dbNames->getRandomName( $values['gender'] );
          $lastName  = $dbNames->getRandomName( LASTNAME );


          } while( $dbAuthUser->nameExists( $firstName->getName(), $lastName->getName() ) == true );

       $userModel->setId( $id )
                 ->setFirstName( $firstName->getName() )
                 ->setLastName( $lastName->getName() )
                 ->setGender( $values['gender'] );


       if( $dbAuthUser->save( $userModel ) == null )
          {
          $dbAdapter->voidUser( $authModel );

          throw new exception( "Error creating pseudonym!" );
          }    
 
       $this->_sendEmailVerifacation( $authModel );   
       return true;
       }

    protected function _resendEmail( $email )
       {
       $dbAdapter = new Default_Model_AuthMapper();
       $auth = new Default_Model_Auth();
       $auth = $dbAdapter->findByEmail( $email, $auth );

       if( $auth != null )
          {
          $auth->newEmailValidationCode();
          $dbAdapter->update( $auth );
          $this->_resendEmailVerifacation( $auth );
          return true;
          }
       $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages')->auth->error->sendingMessage  );
       }

    protected function _sendEmailVerifacation( Default_Model_Auth $authModel )
       {
       // attachement setup
       $path            = APPLICATION_PATH . "/../public/Download/";
       $file            = "plsc1-EB.pdf"; 

       $at = new Zend_Mime_Part( file_get_contents( $path.$file ) );
       $at->type        = 'application/pdf';
       $at->disposition = Zend_Mime::DISPOSITION_INLINE;
       $at->encoding    = Zend_Mime::ENCODING_BASE64;
       $at->filename    = $file;

 
       // message setup
       $message = new Zend_View();
       $message->setScriptPath( APPLICATION_PATH . '/modules/default/views/emails/' );
       $message->assign( 'validationCode', $authModel->getEmailValidationCode() );
       $message->assign( 'href_base', $this->view->baseUrl() . "/Auth/Validate-Account" );
       $message->assign( 'href_params', "/vc/". $authModel->getEmailValidationCode() );

       // mail trancport setup
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
  
       // construct the message 
       $mail = new Zend_Mail();
       $mail->addTo( $authModel->getEmail(), "New User" );
       $mail->setSubject( "ReConsider registration" );
       $mail->setBodyHtml( $message->render('signup.phtml') );
       $mail->addAttachment($at); 
       $mail->send();
       }

    protected function _resendEmailVerifacation( Default_Model_Auth $authModel )
       {
       // message setup
       $message = new Zend_View();
       $message->setScriptPath( APPLICATION_PATH . '/modules/default/views/emails/' );
       $message->assign( 'validationCode', $authModel->getEmailValidationCode() );
       $message->assign( 'href_base', $this->view->baseUrl() . "/Auth/Validate-Account" );
       $message->assign( 'href_params', "/vc/". $authModel->getEmailValidationCode() );
       
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
       $mail->addTo( $authModel->getEmail(), "New User" );
       $mail->setSubject( "ReConsider registration validation" );
       $mail->setBodyHtml( $message->render('resendValidation.phtml') );
       
       $mail->send();
       }
    protected function _sendEmailLostAccount( Default_Model_Auth $authModel, $passwd )
       {
              // message setup
       $message = new Zend_View();
       $message->setScriptPath( APPLICATION_PATH . '/modules/default/views/emails/' );
       $message->assign( 'passwd', $passwd );
       $message->assign( 'href_base', $this->view->baseUrl() . "/Auth/Index" );

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
       $mail->addTo( $authModel->getEmail(), "User" );
       $mail->setSubject( "ReConsider account recovery" );
       $mail->setBodyHtml( $message->render('lostAccount.phtml') );

       $mail->send();

       }

    protected function _updateUser( $values )
    {
       $dbAdapter = Zend_Db_Table::getDefaultAdapter();
       $dbAdapter = new Default_Model_AuthMapper();
       $authModel = new Default_Model_Auth();


       $authModel->setID(       $values['id']        )
                 ->setUsername( $values['username']  )
                 ->setRealname( $values['real_name'] )
                 ->setEmail(    $values['email']     )
                 ->newPassword( $values['password']  );

       if( $dbAdapter->update( $authModel ) == null )
          {
          $this->view->usernameExists = true;
          return false;
          }
          
    return true;
    }

    protected function _isLoggedIn()
    {
       $auth = Zend_Auth::getInstance();

       if( $auth->hasIdentity() )
          {
          return true;
          } 
       return false;
    }

    protected function _updateAccess( $userID )
    {
    $accessLog = new Default_Model_AccessLogMapper();
    $log       = new Default_Model_AccessLog();
    $log->setUserID( $userID );
    
    return $accessLog->save( $log );
    }


}






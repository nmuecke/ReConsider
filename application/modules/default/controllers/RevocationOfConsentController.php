<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */
class RevocationOfConsentController extends Zend_Controller_Action
{
    public function init()
    {
       $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');       
       if( $this->_isLoggedIn() == false )
          {
          $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages' )->roc->notLoggedIn );
          $this->_helper->redirector( 'index', 'auth');
          }

       // track url
       $urlHistory = new Extend_Helpers_RefPage();
       $this->previousPage = $urlHistory->getLastVisited();
       $urlHistory->save( $this->_request->getRequestUri() );
       $this->currentPage  = $urlHistory->getLastVisited();

       $this->sessionSignUp = new Zend_Session_Namespace('SignUp');

    }

    public function indexAction()
    {
       $form = new Default_Form_RevocationOfConsent_LeaveStudy();
    
       $request = $this->getRequest();

       if( $request->isPost() ) 
          {
          if( $request->getPost( 'back' ) != null )
             {
             $this->_helper->redirector( 'index', 'disputes');
             }

          if( $form->isValid( $request->getPost() ) ) 
             {
             $authAdapter = new Default_Model_AuthMapper();
             $auth        = new Default_Model_Auth();
             $auth->setId( Zend_Auth::getInstance()->getIdentity()->id )
                  ->setUsername( NULL )
                  ->setEmail( Zend_Auth::getInstance()->getIdentity()->id . "-left_study" )
                  ->setPassword( "password voided" )
                  ->setPassword_salt( "voided password" );

             $authAdapter->voidUser( $auth );

             Zend_Auth::getInstance()->clearIdentity();
             $this->_flashMessenger->addMessage( Zend_Registry::get( 'messages' )->roc->accountRemoved );
             $this->_helper->redirector( 'index', 'index');
             }
          }

       $this->view->form = $form;
    }

    protected function _isLoggedIn()
    {
       //$auth = Extend_User::getInstance();
       $auth = Zend_Auth::getInstance();

       if( $auth->hasIdentity() )
          {
          return true;
          }
       return false;
    }

}






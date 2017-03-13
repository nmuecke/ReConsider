<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */
class SignUpController extends Zend_Controller_Action
{
    public function init()
    {
       if( $this->_isLoggedIn() == true )
          {
//          $this->_helper->redirector('index', 'index');
          $this->_helper->redirector('index', 'disputes');
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
       Zend_Session::namespaceUnset( 'SignUp' );
       $this->_helper->redirector( "Plain-Language-Statement", 'Sign-Up');
    }


    public function termsOfUseAction()
    {
    
    if( !$this->_validEnteryPage( array( '/sign-up/informed-consent' ) ) )
       {
       $this->_helper->redirector( "index", 'Sign-Up');
       }

    // Study override - prevents the terms of use page poping up
    if( !isset( $this->sessionSignUp->terms ) )
       {
       $this->sessionSignUp->terms = array( "empty" => false);
       }
    $this->sessionSignUp->terms[hasAcceptedTermsOfUse] = true;

    $this->_helper->redirector( 'create-account', 'Auth');
    // end mod
   
    if( $this->_form( "TermsOfUse", "hasAcceptedTermsOfUse" ) == true )
       {
       $this->_helper->redirector( 'create-account', 'Auth');
       }
    }

    public function plainLanguageStatementAction()
    {
/*
    if( !$this->_validEnteryPage( array( "/sign-up" ) ) )
       {
       $this->_helper->redirector( "index", 'Sign-Up');
       }
*/
    if( $this->_form( "PlainLanguageStatement", "hasAcceptedPlainLanguageStatement" ) == true )
       {
       $this->_helper->redirector( "Informed-Consent", 'Sign-Up');
       }

    }

    public function informedConsentAction()
    {

    if( !$this->_validEnteryPage( array( "/sign-up/plain-language-statement" ) ) )
       {
       $this->_helper->redirector( "index", 'Sign-Up');
       }

    if( $this->_form( "InformedConsent", "hasAcceptedInformedConsent" ) == true )
       {
       $this->_helper->redirector( "Terms-Of-Use", 'Sign-Up');
       }

    }
    protected function _validEnteryPage( array $validPages )
       {
       foreach( $validPages as $page )
          {
          $page = strtolower( $page );

          switch( strtolower( $this->previousPage ))
             {
             case strtolower( $this->currentPage ):
             case $page:
             case $page . '/':
             case $page . '/index':
             case $page . '/index.php':
             case $page . '/index.html':
                return true;
                break;
             }
          }
       return false;
       }

    protected function _form( $formName, $checkName, $labelMessage = null )
    {

       $form = new Default_Form_SignUp_AcceptCondition( );
       $form->setFormName( $formName );
       $form->setCheckName( $checkName );
       $form->setLabelMessage( $labelMessage );
    
       $request = $this->getRequest();

       if( $request->isPost() ) 
          {
          if( $request->getPost( 'back' ) != NULL )
             {
             $this->_helper->redirector( "index", 'index');
             }
          if( $form->isValid( $request->getPost() ) ) 
             {
             if( $request->getPost( $checkName ) == "I Agree" )
                {
                if( !isset( $this->sessionSignUp->terms ) )
                   {
                   $this->sessionSignUp->terms = array( "empty" => false);
                   }
                $this->sessionSignUp->terms[$checkName] = true;
                return true;
                }
             }
          }

       $this->view->form = $form;
    return false; 
    }

    protected function _isLoggedIn()
    {
       $auth = Extend_User::getInstance();

       if( $auth->hasIdentity() )
          {
          return true;
          }
       return false;
    }

}






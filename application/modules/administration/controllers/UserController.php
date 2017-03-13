<?php
class Administration_UserController extends Zend_Controller_Action
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
      $dbAdapter = new Default_Model_AuthMapper();
      $request = $this->getRequest();

      if( $request->isPost() )
         {
         $user = $request->getPost( 'edit' );
         if( $user != null )
            {
            $this->_forward( 'edit-user', 'user', 'administration', array( 'id' => $user) );
            }
         $updates = $request->getPost( 'userToUpdate' );
         if( is_array( $updates ) && count( $updates ) > 0 )
            {
            foreach( $updates as $userID )
               {
               $auth = $dbAdapter->find( $userID, new Default_Model_Auth() );
               if( $auth != null )
                  {
                  // make sure you cant lock your self out
                  if( $auth->getId() != $this->validationID )
                     {
                     $auth->setRole( $request->getPost( 'role_' . $auth->getId() ) );
                     $dbAdapter->update( $auth );
                     }
                  }
               }
            }
         }

      $users = $dbAdapter->fetchAll();

      $this->view->users = $users;
      }

   public function editUserAction( )
      {
      $form = new Administration_Form_EditUser();
      $request = $this->getRequest();

      if ( $request->getPost( 'hid' ) != null )
         {
         $id = $request->getPost( 'hid' );
         }
      else
         {
         $id = $request->getParam( 'id' );
         }
      $auAdappter = new Default_Model_AuthUsersMapper();
      $authAddapter = new Default_Model_AuthMapper();
     
      $user = $auAdappter->find( $id,   new Default_Model_AuthUsers() );
      $auth = $authAddapter->find( $id, new Default_Model_Auth()      );
      
      if( $user == null || $request->getParam( 'back' ) != null )
         {
         $this->_helper->redirector( 'index', 'user', 'administration' );
         }

      if ( $request->isPost() && $request->getParam( 'id' ) == null )
         {
         if( $form->isValid( $request->getPost() ) )
            {
            $user->setFirstName( $request->getParam( 'FirstName' ) );
            $user->setLastName( $request->getParam( 'LastName' ) );
            $auAdappter->save( $user );
          
            $auth->setRealname( $request->getParam( 'Uni' ) );
            $authAddapter->update( $auth );
            }
          }

      $form->getElement( 'hid' )->setValue( $id );
      $form->getElement( 'FirstName' )->setValue( $user->getFirstName() );
      $form->getElement( 'LastName' )->setValue( $user->getLastName() );
      $this->view->name = $user->getFirstName() . ' ' . $user->getLastName();
      $this->view->form = $form;
      }
   }

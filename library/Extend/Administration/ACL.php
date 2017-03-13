<?php

class Extend_Administration_ACL extends Zend_Controller_Plugin_Abstract
   {
   private $_acl = null;
 
   public function __construct( ) 
      {
      $this->_acl = $this->_loadAcl();
      }
 
   public function preDispatch( Zend_Controller_Request_Abstract $request ) 
      {
      //As in the earlier example, authed users will have the role user
      $role = 'guest';
  
      $auth = Zend_Auth::getInstance();
      if( $auth->hasIdentity() )
         {
         // add something to get the role  here
         $dbAdapter = new Default_Model_AuthMapper();
         $user = $dbAdapter->find( $auth->getIdentity()->id, new Default_Model_Auth() );
   
         if( $user != null )
            {
            $role = $user->getRole();
            }
        
         }
 
      //For this example, we will use the controller as the resource:
 
      if( !$this->_acl->isAllowed( $role, strtolower( $request->getModuleName() ), 'view' ) ) 
         {
         //If the user has no access we send him elsewhere by changing the request
         $request->setModuleName('default')
                 ->setControllerName('auth')
                 ->setActionName('request-denied');
         }
      }

   protected function _loadAcl()
      {
      $acl = new Zend_Acl();

      $acl->addRole( new Zend_Acl_Role('guest') )
          ->addRole( new Zend_Acl_Role('baned') )
          ->addRole( new Zend_Acl_Role('user'),  'guest' )
          ->addRole( new Zend_Acl_Role('admin'), 'user'  );
    
      $acl->addResource( new Zend_Acl_Resource("default") );
      $acl->addResource( new Zend_Acl_Resource("administration") );
      $acl->addResource( new Zend_Acl_Resource("engine") );
    
      $acl->deny();
      $acl->allow( 'baned', 'default', 'view' );
      $acl->allow( 'guest', 'default', 'view' );
     
      $acl->allow( 'user', 'default', 'view'  );
      $acl->allow( 'user', 'engine',  'view'  );

      $acl->allow( 'admin', 'administration', 'view'  );



      return $acl;
      }
   }

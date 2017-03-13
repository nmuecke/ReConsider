<?php
class Zend_View_Helper_Navigation extends Zend_View_Helper_Abstract 
   {
   public function Navigation()
      {
      $menuItem[00] = array( 'text' => 'Home',
                             'url'  => array( array( 'controller' => 'index' ),'default', true ),
                             'class' => '' );
      $menuItem[01] = array( 'text' => 'Login',
                             'url'  => array( array( 'controller' => 'Auth' ),'default', true ),
                             'class' => '' );
      $menuItem[98] = array( 'text' => 'About',
                             'url'  => array( array( 'controller' => 'About' ),'default', true ),
                             'class' => '' );
      $menuItem[99] = array( 'text' => 'Help',
                             'url'  => array( array( 'controller' => 'Help' ),'default', true ),
                             'class' => '' );

      // modify the array for when a user is logged in 
      $auth = Zend_Auth::getInstance();
      if ($auth->hasIdentity()) {
         $menuItem[01] = array( 'text' => 'ODR Study',
                                'url'  => array( array( 'controller' => 'Disputes' ),'default', true ),
                                'class' => '' );

         $menuItem[02] = array( 'text' => 'My Account',
                                'url'  => array( array( 'controller' => 'Auth', 'action'=>'User' ),'default', true ),
                                'class' => '' );
      
       // filter links based on role
       $dbAuth = new Default_Model_AuthMapper();
       $user = $dbAuth->find( $auth->getIdentity()->id, new Default_Model_Auth() );

       if( $user != null && $user->getRole() == 'admin' )
         $menuItem[10] = array( 'text' => 'Admin',
                                'url'  => array( array( 'controller' => 'index', 
                                                        'action'=>'index',
                                                        'module'=>'administration' ),
                                                 'default', 
                                                 true ),
                                'class' => '' );


         }

      ksort( $menuItem );
      return $menuItem;
      }
   }


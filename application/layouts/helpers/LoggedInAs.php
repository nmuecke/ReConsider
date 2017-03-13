<?php
class Layout_Helper_LoggedInAs extends Zend_View_Helper_Abstract 
   {
   public function loggedInAs ()
      {
      $auth = Zend_Auth::getInstance();
      //$auth = Extend_User::getInstance();

      if( $auth->hasIdentity() ) 
         {
         $username = $auth->getIdentity()->id; //username;
         $logoutUrl = $this->view->url( array( 'controller'=>'auth',
                                               'action'=>'logout'
                                             ), 
                                        null, 
                                        true
                                       );

         return 'Welcome participant ' . $username .  '. <a href="'.$logoutUrl.'">Logout</a>';
         //return 'Welcome ' . $username .  '. <a href="'.$logoutUrl.'">Logout</a>';
         } 
        // this prevents the login option from being displayed when at the auth controller
#       $request = Zend_Controller_Front::getInstance()->getRequest();
#       $controller = $request->getControllerName();
#       $action = $request->getActionName();
#
#       if( $controller == 'auth' && $action == 'index' ) 
#          {
#          return '';
#          }

       $loginUrl = $this->view->url( array( 'controller'=>'auth', 'action'=>'index' ) );

       return '<a href="'.$loginUrl.'">Login</a>';
       }
   }



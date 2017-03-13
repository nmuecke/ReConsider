<?php
class Zend_View_Helper_CurrentPage extends Zend_View_Helper_Abstract
   {
   public function CurrentPage( array $url )
      {
      if( !isset( $url[0]['action'] ) ){
         $url_action = 'index';
         }
      else {
         $url_action = strtolower( $url[0]['action'] );
         }

      $url_controller = strtolower( $url[0]['controller'] );

      $request =  Zend_Controller_Front::getInstance()->getRequest();
      $action     = strtolower( $request->getActionName() );
      $controller = strtolower( $request->getControllerName());

      if( $url_controller == $controller &&  $url_action == $action  )
         {
         return true;
         }

      return false;
      }

   }


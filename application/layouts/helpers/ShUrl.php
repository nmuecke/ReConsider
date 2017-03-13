<?php
class Zend_View_Helper_ShUrl extends Zend_View_Helper_Abstract
   {
   public function ShUrl( $controller, $action = 'index' )
      {
      // to handle the passing of action/controller arrays
      if( is_array( $controller ) )
         {
         if( !array_key_exists( "controller", $controller ) )
            {
            throw new Exception('Incorrect array supplyed for url creation.');
            }
         if( !array_key_exists( "action", $controller ) )
            {
            $controller['action'] = 'index'; 
            }
         
         }
      else
         {
         $controller = array( "controller" => $controller, "action" => $action );

         }
      
      return $this->view->url( $controller, 'default', true );
 
      }
  } 

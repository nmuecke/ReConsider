<?php
class Plugins_CheckHasAccess extends Zend_Controller_Plugin_Abstract
   {
   public function init(){
      echo "erwerwerwerw";
      }

   public function preDispatch( Zend_Controller_Request_Abstract $request ) 
      {

               //do thing before all controller loaded here :D
      print_r( $request );

      }
   }

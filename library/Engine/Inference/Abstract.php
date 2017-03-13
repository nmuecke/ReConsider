<?php

abstract class Engine_Inference_Abstract
   {
   private static $_instance;

   final private function __construct(){}
    
   static public function getInstance( $class = __CLASS__ )
      {   
      if( null === self::$_instance )
         {
         self::$_instance = new $class();
         }

      return self::$_instance;
      }
 
   abstract public function inferClaim( Engine_Models_ClaimState $claim );

   }
?>

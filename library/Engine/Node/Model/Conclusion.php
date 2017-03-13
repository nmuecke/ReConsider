<?php

class Engine_Node_Model_Conclusion extends Engine_Node_Model_Abstract
   {
   
   final public function addClaim( $claim )
       {
       //Zend_Debug::Dump( , 'Attempting to add a claim to a conclusion node. Claim not added' );
       return $this;
       }

   final public function addClaims( array $claim )
       {
       //Zend_Debug::Dump( , 'Attempting to add a claimis to a conclusion node. Claims not added' );
       return $this;
       }

   }

<?php

class Engine_Node_Model_Root extends Engine_Node_Model_Argument
   {

   public function getRecomendedOutcome()
      {
      $recomenedClaim = new Engine_Node_Model_Claim();
      $recomenedClaim->setSysInference( -1 );
      foreach( $this->getClaims() as $claim )
         {
         if( $claim->getSysInference() > $recomenedClaim->getSysInference() )
            {
            $recomenedClaim = clone $claim;
            }
         }
      return $recomenedClaim;
      }
   }

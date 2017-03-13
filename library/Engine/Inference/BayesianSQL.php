<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Inference_BayesianSQL extends Engine_Inference_BayesianAbstract
   {
   protected $_baysProbAdapter = null;

   static public function getInstance( $class = __CLASS__ )
      {
      return parent::getInstance( $class );
      }
   
   // {{{ protected getPriorP( Engine_Models_ClaimState $claim )
   protected function getPriorP( Engine_Models_ClaimState $claim )
      {
      $prob = self::_getAdapter()->findPriorP( $claim->getNodeID(), $claim->getClaimID() );
      if( $prob == null || $prob->getProb() == 0 )
         {
         // make sure that we dont try to multipy by 0 as throw exceptions
         $prob = new Engine_Inference_Model_BayesProb();
         $prob->setNodeID( $childClaim->getNodeID() )
              ->setClaimID( $childClaim->getClaimID() )
              ->setParentNodeID( $parentClaim->getNodeID() )
              ->setParentClaimID( $parentClaim->getClaimID() )
              ->setProb( 0.00000000001 );
         }
      return $prob;
      }

   // }}}
   // {{{ protected getAllPriorP( Engine_Models_ClaimState $claim )
   protected function getAllPriorP( Engine_Models_ClaimState $claim )
      {
      $prob = self::_getAdapter()->findAllPriorP( $claim->getNodeID() );
      return $prob;
      }

   // }}}
   // {{{ protected getConditionalP( Engine_Models_ClaimState $parentClaim, Engine_Models_ClaimState $childClaim )
   protected function getConditionalP( Engine_Models_ClaimState $parentClaim, Engine_Models_ClaimState $childClaim )
      {
      $prob = self::_getAdapter()->findConditionalP( 
                                                    $childClaim->getNodeID(),
                                                    $childClaim->getClaimID(), 
                                                    $parentClaim->getNodeID(),
                                                    $parentClaim->getClaimID()
                                                   );
      if( $prob == null || $prob->getProb() == 0 )
         {
         // make sure that we dont try to multipy by 0 as throw exceptions
         $prob = new Engine_Inference_Model_BayesProb();
         $prob->setNodeID( $childClaim->getNodeID() )
              ->setClaimID( $childClaim->getClaimID() )
              ->setParentNodeID( $parentClaim->getNodeID() )
              ->setParentClaimID( $parentClaim->getClaimID() )
              ->setProb( 0.00000000001 );
    
         }
      return $prob;

      }
   // }}}
   // {{{ protected getAllConditionalP( Engine_Models_ClaimState $parentClaim, Engine_Models_ClaimState $childClaim )
   protected function getAllConditionalP( Engine_Models_ClaimState $parentClaim, Engine_Models_ClaimState $childClaim )
      {
      $prob = self::_getAdapter()->findAllConditionalP( 
                                                    $childClaim->getNodeID(),
                                                    $parentClaim->getNodeID()
                                                   );

      return $prob;

      }
   // }}}
   // {{{ protected _getAdapter()
   protected function _getAdapter()
      {
      if( $this->_baysProbAdapter == null )
         {
         $this->_baysProbAdapter = new Engine_Inference_Model_BayesProbMapper();
         }

      return $this->_baysProbAdapter;  
    
      }
   // }}}
   }
?>

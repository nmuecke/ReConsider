<?php

class Engine_Models_ClaimStateLog extends Engine_Models_ClaimState
   {
   
   private $_timestamp;

   public function __construct( Engine_Models_ClaimState $obj = null )
      {
      if( $obj != null )
         {
         $this->setDisputeID(     $obj->getDisputeID()     );
         $this->setUserID(        $obj->getUserID()        );
         $this->setNodeID(        $obj->getNodeID()        );
         $this->setClaimID(       $obj->getClaimID()       );
         $this->setSysInference(  $obj->getSysInference()  );
         $this->setUserInference( $obj->getUserInference() );
         $this->setStatus(        $obj->getStatus()        );
         }
      }

   public function setTimestamp( $value )
      {
      $this->_timestamp = $value;
      return $this;
      }

   public function getTimestamp()
      {
      return $this->_timestamp;
      }

   }

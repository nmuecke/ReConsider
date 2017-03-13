<?php

class Engine_Models_ClaimState extends Extend_Db_AbstractTableModel
   {
   
   private $_disputeID;
   private $_userID;
   private $_nodeID;
   private $_claimID;
   private $_sysInference;
   private $_userInference;
   private $_status;


   public function setUserID( $value )
      {
      $this->_userID = $value;
      return $this;
      }

   public function getUserID()
      {
      return $this->_userID;
      }

   public function setDisputeID( $value )
      {
      $this->_disputeID = $value;
      return $this;
      }

   public function getDisputeID()
      {
      return $this->_disputeID;
      }

   public function setNodeID( $value )
      {
      $this->_nodeID = $value;
      return $this;
      }

   public function getNodeID()
      {
      return $this->_nodeID;
      }

   public function setClaimID( $value )
      {
      $this->_claimID = $value;
      return $this;
      }

   public function getClaimID()
      {
      return $this->_claimID;
      }

   public function setSysInference( $value )
      {
      $this->_sysInference = $value;
      return $this;
      }

   public function getSysInference()
      {
      return $this->_sysInference;
      }

   public function setUserInference( $value )
      {
      $this->_userInference = $value;
      return $this;
      }

   public function getUserInference()
      {
      return $this->_userInference;
      }

   public function setStatus( $value )
      {
      $this->_status = $value;
      return $this;
      }

   public function getStatus()
      {
      return $this->_status;
      }

   }

<?php

class Extend_Queue_Model_Mail extends Extend_Db_AbstractTableModel
   {
   protected $_mailType;
   protected $_disputeID;
   protected $_userID;
//   protected $_sentAt;
   protected $_addedAt;

   public function setMailType( $var )
      {
      $this->_mailType = $var;
      return $this;
      }

   public function getMailType()
      {
      return $this->_mailType;
      }

   public function setDisputeID( $var )
      { 
      $this->_disputeID = $var;
      return $this;
      }

   public function getDisputeID()
      {
      return $this->_disputeID;
      }

   public function setUserID( $var )
      { 
      $this->_userID = $var;
      return $this;
      }

   public function getUserID()
      {
      return $this->_userID;
      }
/*
   public function setSentAt( $var )
      { 
      $this->_sentAt = $var;
      return $this;
      }

   public function getSentAt()
      {
      return $this->_sentAt;
      }
*/
   public function setAddedAt( $var )
      { 
      $this->_addedAt = $var;
      return $this;
      }

   public function getAddedAt()
      {
      return $this->_addedAt;
      }

   }

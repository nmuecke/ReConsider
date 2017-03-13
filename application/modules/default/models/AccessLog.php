<?php

class Default_Model_AccessLog extends Extend_Db_AbstractTableModel
   {
   private $_userID;
   private $_timestamp;

   public function setUserID( $value )
      {
      $this->_userID = $value;
      return $this;
      }

   public function getUserID()
      {
      return $this->_userID;
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

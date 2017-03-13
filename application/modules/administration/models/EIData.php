<?php

class Administration_Model_EIData extends Extend_Db_AbstractTableModel
   {
     
   protected $_eiID;
   protected $_password;
   protected $_active;
      
   public function setEIID( $val  )
      {
      $this->_eiID = (string)$val;
      return $this;
      }
       
   public function getEIID()
      {
      return $this->_eiID;
      }
      
   public function setPassword( $val  )
      {
      $this->_password = (string)$val;
      return $this;
      }

   public function getPassword()
      {
      return $this->_password;
      }

   public function setActive( $val  )
      {
      $this->_active = (string)$val;
      return $this;
      }

   public function getActive()
      {
      return $this->_active;
      }

   }


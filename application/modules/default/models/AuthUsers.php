<?php
// application/models/Auth.php

class Default_Model_AuthUsers extends Extend_Db_AbstractTableModel
   {
     
   protected $_role;
   protected $_eiTest;
   protected $_firstName;
   protected $_lastName;
   protected $_gender;
   
   public function setRole( $var )
      {
      $this->_role = (string)$var;
      return $this;
      }

   public function getRole()
      {
      return $this->_role;
      }

   public function setEITest( $var )
      {
      $this->_eiTest = $var;
      return $this;
      }

   public function getEITest()
      {
      return $this->_eiTest;
      }
   
   public function setFirstName( $name )
      {
      $this->_firstName = (string)$name;
      return $this;
      }
       
   public function getFirstName()
      {
      return $this->_firstName;
      }
      
   public function setLastName( $name )
      {
      $this->_lastName = (string)$name;
      return $this;
      }

   public function getLastName()
      {
      return $this->_lastName;
      }
  
   public function setGender( $var )
      {
      $this->_gender = (string)$var;
      return $this;
      }

   public function getGender()
      {
      return $this->_gender;
      }
 
   }


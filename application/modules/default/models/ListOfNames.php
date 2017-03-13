<?php
// application/models/Auth.php

class Default_Model_ListOfNames extends Extend_Db_AbstractTableModel
   {
     
   protected $_name;
   protected $_type;
      
   public function setName( $name )
      {
      $this->_name = (string)$name;
      return $this;
      }
       
   public function getName()
      {
      return $this->_name;
      }
      
   public function setType( $var )
      {
      $this->_type = (string)$var;
      return $this;
      }

   public function getType()
      {
      return $this->_type;
      }
 
   }


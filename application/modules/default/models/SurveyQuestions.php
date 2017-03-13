<?php
// application/models/Auth.php

class Default_Model_SurveyQuestions extends Extend_Db_AbstractTableModel
   {
     
   protected $_name;
   protected $_type;
   protected $_label;
   protected $_highLabel;
   protected $_lowLabel;
   protected $_required;
   protected $_order;
      
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
 
   public function setLabel( $val )
      {
      $this->_label = $val;
      return $this;
      }

   public function getLabel()
      {
      return $this->_label;
      }

   public function setHighLabel( $val )
      {
      $this->_highLabel = $val;
      return $this;
      }

   public function getHighLabel()
      {
      return $this->_highLabel;
      }

   public function setLowLabel( $val )
      {
      $this->_lowLabel = $val;
      return $this;
      }

   public function getLowLabel()
      {
      return $this->_lowLabel;
      }

   public function setRequired( $val )
      {
      $val = strtolower( $val );
      switch( (string)$val )
         {
         case '0':
         case 'f':
         case 'false':
         case null:
            $val = false;
            break;

         case '1':
         case 't':
         case 'true':
            $val = true;
            break;

         default:
            $val = true;
         }
      $this->_required = $val;
      return $this;
      }

   public function getRequired()
      {
      return $this->_required;
      }

   public function setOrder( $val )
      {
      $this->_order = $val;
      return $this;
      }

   public function getOrder()
      {
      return $this->_order;
      }
   }


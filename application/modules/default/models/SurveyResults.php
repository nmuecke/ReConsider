<?php
// application/models/Auth.php

class Default_Model_SurveyResults extends Extend_Db_AbstractTableModel
   { 
     
   protected $_userID;
   protected $_questionID;
   protected $_result;
      
   public function setUserID( $val )
      {
      $this->_userID = $val;
      return $this;
      }
       
   public function getUserID()
      {
      return $this->_userID;
      }
      
  public function setQuestionID( $val )
      {
      $this->_questionID = $val;
      return $this;
      }

   public function getQuestionID()
      {
      return $this->_questionID;
      }
 
  public function setResult( $val )
      {
      $this->_result = $val;
      return $this;
      }

   public function getResult()
      {
      return $this->_result;
      }
   }


<?php

class Engine_Inference_Model_BayesProb extends Extend_Db_AbstractTableModel
   {
   private $_nodeID;
   private $_claimID;
   private $_parentNodeID;
   private $_parentClaimID;
   private $_prob;


   public function setNodeID( $value )
      {
      $this->_nodeID = (int)$value;
      return $this;
      }

   public function getNodeID()
      {
      return $this->_nodeID;
      }

   public function setClaimID( $value )
      {
      $this->_claimID = (int)$value;
      return $this;
      }

   public function getClaimID()
      {
      return $this->_claimID;
      }

   public function setParentNodeID( $value )
      {
      $this->_parentNodeID = (int)$value;
      return $this;
      }

   public function getParentNodeID()
      {
      return $this->_parentNodeID;
      }

   public function setParentClaimID( $value )
      {
      $this->_parentClaimID = (int)$value;
      return $this;
      }

   public function getParentClaimID()
      {
      return $this->_parentClaimID;
      }

   public function setProb( $value )
      {
      $this->_prob = (float)$value;
      return $this;
      }

   public function getProb()
      {
      return $this->_prob;
      }

   public function toString()
      {
      return "  _id: "            . self::getID()          
           . "\t _nodeID: "        . self::getNodeID()         
           . "\t _claimID: "       . self::getClaimID()       
           . "\t _parentNodeID: "  . self::getParentNodeID()  
           . "\t _parentClaimID: " . self::getParentClaimID() 
           . "\t _prob: "          . self::getProb()        
           . "\t";
      }
   }

<?php

class Engine_Models_RootNodes extends Extend_Db_AbstractTableModel
   {
   private $_nodeID;
   private $_disputeType;
   private $_description;
   private $_label;


   public function setNodeID( $value )
      {
      $this->_nodeID = $value;
      return $this;
      }

   public function getNodeID()
      {
      return $this->_nodeID;
      }

   public function setDisputeType( $value )
      {
      $this->_disputeType = $value;
      return $this;
      }

   public function getDisputeType()
      {
      return $this->_disputeType;
      }

   public function setDescription( $value )
      {
      $this->_description = $value;
      return $this;
      }

   public function getDescription()
      {
      return $this->_description;
      }

   public function setLabel( $value )
      {
      $this->_label = $value;
      return $this;
      }

   public function getLabel()
      {
      return $this->_label;
      }

   }

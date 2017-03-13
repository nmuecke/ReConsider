<?php
// application/models/Disputes.php
/*
          const CREATEING   = 'creating';
          const INITIATED   = 'initiated';
          const COMPLETED   = 'completed';
          const ACTIVE      = 'active';
*/
class Default_Model_Disputes extends Extend_Db_AbstractTableModel
{
          
         

 
          protected $_disputeType;
          protected $_status;
          protected $_outcomeID;
          protected $_numRejections; // how many time the users reject a settelment offer
       

          public function setDisputeType( $disputeType )
          {
              $this->_disputeType = (string)$disputeType;
              return $this;
          }
       
          public function getDisputeType()
          {
              return $this->_disputeType;
          }

          public function setStatus( $state )
          {
              $this->_status = (string)$state;
              return $this;
          }

          public function getStatus()
          {
              return $this->_status;
          }

          public function setOutcomeID( $state )
          {
              $this->_outcomeID = $state;
              return $this;
          }

          public function getOutcomeID()
          {
              return $this->_outcomeID;
          }

          public function setNumRejections( $val )
          {
              $this->_numRejections = (int)$val;
              return $this;
          }

          public function incromentNumRejections( $val = 1)
          {
              $this->_numRejections = $this->_numRejections + (int)$val;
              return $this;
          }
          public function getNumRejections( )
          {
              return $this->_numRejections;
          }          
      }


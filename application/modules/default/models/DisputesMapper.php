<?php

class Default_Model_DisputesMapper
   {
   protected $_dbTable;

   public function setDbTable($dbTable)
      {
      if( is_string($dbTable) )
         {
         $dbTable = new $dbTable();
         }

      if( !$dbTable instanceof Zend_Db_Table_Abstract )
         {
         throw new Exception('Invalid table data gateway provided');
         }

      $this->_dbTable = $dbTable;

      return $this;
      }

   public function getDbTable()
      {
      if( null === $this->_dbTable )
         {
         $this->setDbTable('Default_Model_DbTable_Disputes');
         }

      return $this->_dbTable;
      }

   public function initiate( Default_Model_Disputes $model )
      {
      $data = array(
                    'id'            => $model->getId(),
                    'disputeType'   => $model->getDisputeType(),
                    'status'        => $model->getStatus(),
                    'numRejections' => 0,
                    'outcomeID'     => NULL
                    );

      if( null === ( $id = $model->getId() ) )
         {
         $id = $this->getDbTable()->insert($data);
         return $id;
         }
      else
         {
         return "Error: Initiating dispute failed, dispute exists!";
         }
      }

   public function update( Default_Model_Disputes $model )
      {
      $data = array(
                    'id'            => $model->getId(),
                    'disputeType'   => $model->getDisputeType(),
                    'status'        => $model->getStatus(),
                    'numRejections' => $model->getNumRejections(),
                    'outcomeID'     => $model->getOutcomeID()
                    );

      if( null === ( $id = $model->getId() ) )
         {
         return "Error: dispute not found!";
         }
      else
         {
         $this->getDbTable()->update($data, array('id = ?' => $id));
         }

      }
   public function find( $id, Default_Model_Disputes $model )
      {

      $select= $this->getDbTable()->select()->where( 'id = ?', $id );
      $row = $this->getDbTable()->fetchRow($select);

      if( 0 == count($row) )
         {
         return null;
         }
      $model->setId( $row->id )
            ->setDisputeType( $row->disputeType )
            ->setStatus( $row->status )
            ->setNumRejections( $row->numRejections )
            ->setOutcomeID( $row->outcomeID );


      return $model;
      }

   }

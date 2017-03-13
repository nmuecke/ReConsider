<?php

class Default_Model_UserToDisputeMapper
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
         $this->setDbTable('Default_Model_DbTable_UserToDispute');
         }

      return $this->_dbTable;
      }

   public function save( Default_Model_UserToDispute $model )
      {
      $data = array(
                    'id'         => $model->getId(),
                    'disputeID'  => $model->getDisputeID(),
                    'userID'     => $model->getUserID()
                    );

      if( null === ( $id = $model->getId() ) )
         {
         // new dispute association
         $this->getDbTable()->insert($data);
         }
      else
         {
         // update
         $this->getDbTable()->update($data, array('id = ?' => $id));
         }

      }

   public function find( $userID, Default_Model_UserToDispute $model )
      {
      $select = $this->getDbTable()->select()->where( 'userID = ?', $userID );
      $row    = $this->getDbTable()->fetchRow($select);

      if( 0 == count($row) )
         {
         return null;
         }
      $model->setId( $row->id )
            ->setUserID( $row->userID )
            ->setDisputeID( $row->disputeID );


      return $model;
      }

   public function exists( $disputeID, $userID )
      {

      $select= $this->getDbTable()->select()
                                  ->where( 'disputeID = ?', $disputeID )
                                  ->where( 'userID = ?', $userID );

      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return false;
         }

      return true;
      }

   public function existingDisputes( $userID_1, $userID_2, Default_Model_UserToDispute $model )
      {
      $tbName = 'userToDispute';
      $select = $this->getDbTable()->select( )
                                   ->from( array( 't1' => $tbName ), array( 't1.disputeID' ))
                                   ->join( array( 't2' => $tbName ), '', array( )  )
                                   ->where( 't1.userID = ?', $userID_1 )
                                   ->where( 't2.userID = ?', $userID_2 )
                                   ->where( 't1.disputeID = t2.disputeID' );

//       //  Zend_Debug::Dump($select->assemble());

      $row = $this->getDbTable()->fetchRow($select);
      if( 0 == count( $row ) )
         {
         return null;
         }

      return $row->disputeID;
      }
   


   }

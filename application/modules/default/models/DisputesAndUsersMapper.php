<?php

class Default_Model_DisputesAndUsersMapper
   {
   protected $_dbTable;
   protected $_tbUsersTo = 'userToDispute';
   protected $_tbDisputes = 'disputes';
   protected $_tbAuth = 'auth';
   protected $_tbAuthUsers = 'authUsers';
   protected $_tbOutcomes = 'outcomes';

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
        // throw new Exception('Invalid table data gateway provided');
         }

      return $this->_dbTable;
      }

   public function existingDispute( $userID_1, $userID_2 )
      {
      $subselect = $this->getDbTable()->select( )
                                      ->from( array( 't1' => $this->_tbUsersTo ), array( 't1.disputeID' ))
                                      ->join( array( 't2' => $this->_tbUsersTo ), '', array( )  )
                                      ->where( 't1.userID = ?', $userID_1 )
                                      ->where( 't2.userID = ?', $userID_2 )
                                      ->where( 't1.disputeID = t2.disputeID' );

      $select = $this->getDbTable()->select( );
      $select->setIntegrityCheck( false );
      $select->from( array( 't2' => $this->_tbDisputes ), array( 't2.id' ) );
      $select->where( 't2.id = ( '.$subselect->__toString().')' );
      $select->where( 't2.outcomeID IS NULL' );



      // //  Zend_Debug::Dump($select->assemble());

      $row = $this->getDbTable()->fetchRow($select);
      if( 0 == count( $row ) )
         {
         return null;
         }

      return $row->disputeID;
      }


   public function getCurrentDisputes( $userID )
      {

      $select = $this->_selectDisputes( $userID );
      $select->where( 't2.outcomeID IS NULL' );

      ////  Zend_Debug::Dump($select->assemble());

      $res = $this->getDbTable()->fetchAll( $select );

      $output = array();

      foreach( $res as $row )
         {
         $output[] = array( 'id'      => $row->disputeID, 
                            'type'    => $row->disputeType, 
                            'with'    => $this->_getOtherUsername( $userID, $row->disputeID ), 
                            'status'  => $row->status 
                           );

         }

      return $output;

      }
   private function _selectDisputes( $userID )
      {
      $select = $this->getDbTable()->select( );
      $select->setIntegrityCheck( false );
      $select->from( array( 't1' => $this->_tbUsersTo  ), array( 't1.disputeID' ) );
      $select->join( array( 't2' => $this->_tbDisputes ), 't1.disputeID = t2.id', array( 't2.disputeType', 't2.status'  ) );
      $select->where( 't1.userID = ?', $userID );

      return $select;
      }
   private function _getOtherUsername( $userID, $disputeID )
      {
      $select = $this->getDbTable()->select( );
      $select->setIntegrityCheck( false );
      $select->from( array( 't1' => $this->_tbUsersTo ), array( ) );
      $select->join( array( 't2' => $this->_tbAuth ), 't1.userID = t2.id' ,array( 't2.username' ) );
      $select->where( 't1.disputeID = ?', $disputeID );
      $select->where( 't1.userID != ?', $userID );

      $sub_res = $this->getDbTable()->fetchRow( $select );

      return $sub_res->username; 
  
      }
 
   public function getOtherUsersID( $userID, $disputeID )
      {
      $select = $this->getDbTable()->select( )->where( 'userID != ?', $userID )
                                              ->where( 'disputeID = ?', $disputeID );

      $res = $this->getDbTable()->fetchRow( $select );

      if( count( $res ) != 1 )
         {
         return null;
         }
      
      return $res;

      }
 
   public function getResolvedDisputes( $userID )
      {

      $select = $this->_selectDisputes( $userID );
 //     $select->join( array( 't3' => $this->_tbOutcomes ), 't2.outcomeID = t3.id', array( 't3.summary' ) );
      $select->where( 't2.outcomeID IS NOT NULL' );

      ////  Zend_Debug::Dump($select->assemble());

      $res = $this->getDbTable()->fetchAll( $select );

      $output = array();

      foreach( $res as $row )
         {
         $output[] = array( 'id'      => $row->disputeID,
                            'type'    => $row->disputeType,
                            'with'    => $this->_getOtherUsername( $userID, $row->disputeID ), 
                            'status'  => $row->status,
   //                         'outcome' => $row->summary
                            'outcome' => "unavalible"
                           );

         }

      return $output;

      }

   public function getDisputes()
      {
      $select = $this->getDbTable()->select( );
      $select->setIntegrityCheck( false );
      $select->from( array( 't1' => $this->_tbDisputes ), array( 't1.disputeType', 
                                                                 't1.id',
                                                                 't1.status',
                                                                 't1.numRejections'  ) );
      ////  Zend_Debug::Dump($select->assemble());

      $res = $this->getDbTable()->fetchAll( $select );

      $output = array();

      foreach( $res as $row )
         {
         $subSelect = $this->getDbTable()->select( );
         $subSelect->setIntegrityCheck( false );
         $subSelect->from( array( 't2' => $this->_tbUsersTo ),   array( 't2.userID' ) );
         $subSelect->join( array( 't3' => $this->_tbAuth ),      't2.userID = t3.id',    array( 't3.realname' ) );
         $subSelect->join( array( 't4' => $this->_tbAuthUsers ), 't2.userID = t4.id',    array( 't4.role', 
                                                                                             't4.gender' ) );
         $subSelect->where( 't2.disputeID = ?', $row->id );

         $subRes = $this->getDbTable()->fetchAll( $subSelect );
         $users = array();
         foreach( $subRes as $subRow )
            {
            $users[] = array( 'userID' => $subRow->userID,
                              'role'   => $subRow->role,
                              'group'  => $subRow->realname,
                              'gender' => $subRow->gender,
                             );
            }

         $output[] = array( 'disputeID'      => $row->id,
                            'type'           => $row->disputeType,
                            'status'         => $row->status,
                            'numRejections'  => $row->numRejections,
                            'users'          => $users,
                           );

         }

      return $output;

      }
   }

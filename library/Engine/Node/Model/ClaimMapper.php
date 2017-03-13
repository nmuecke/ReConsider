<?php

class Engine_Node_Model_ClaimMapper
   {
   protected $_dbTable;

   public function setDbTable( $dbTable )
      {
      if( is_string( $dbTable ) )
         {
         $dbTable = new $dbTable();
         }

      if( !$dbTable instanceof Zend_Db_Table_Abstract )
         {
         throw new Exception( 'Invalid table data gateway provided' );
         }

      $this->_dbTable = $dbTable;

      return $this;
      }

   public function getDbTable()
      {
      if( null === $this->_dbTable )
         {
         $this->setDbTable('Engine_Model_DbTable_Claims');
         }

      return $this->_dbTable;
      }

   public function find( $id, Engine_Node_Model_Claim $claim )
      {
      $select = $this->getDbTable()->select()->where( 'id = ?', $id );
      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }

      
      return self::_loadClaim( $row, $claim );
      }

   public function findClaim( $nodeID, $claimID, Engine_Node_Model_Claim $claim )
      {
      $select = $this->getDbTable()->select()
                                   ->from( array( "t1"=>"claims") )
                                   ->join( array( "t2"=>"nodes"), 't1.nodeID = t2.nodeID', array() )
                                   ->where( 't2.ID = ?', $nodeID )
                                   ->where( 't1.claimID = ?', $claimID );

      $row = $this->getDbTable()->fetchRow( $select );

      //Zend_Debug::Dump( $select->assemble() );
      if( 0 == count($row) )
         {
         return null;
         }


      return self::_loadClaim( $row, $claim );
      }


   public function findNodesClaims( $nodeID )
      { 
      $claims = array();

      $select = $this->getDbTable()->select()->where( 'nodeID = ?', $nodeID );
       
      $res = $this->getDbTable()->fetchAll( $select );

      foreach( $res as $row )
         {
         $claim = new Engine_Node_Model_Claim();
         $claims[] = self::_loadClaim( $row, $claim );
         }

      return $claims;      
      }  

   protected function _loadClaim( $row, Engine_Node_Model_Claim $claim )
      {
      $claim->setId( $row->id )
            ->setNodeID ( $row->nodeID )
            ->setClaimID( $row->claimID )
            ->setCounterClaimID( $row->counterClaimID )
            ->setValue( $row->value )
            ->setWeight( $row->weight )
            ->setThreshold( $row->threshold )
            ->setAction( $row->action );

      return $claim;

      }
  
       
   }

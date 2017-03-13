<?php

class Engine_Models_ClaimStateLogMapper extends Engine_Models_ClaimStateMapper
   {
   protected $_dbClass = "Engine_Model_DbTable_ClaimStateLog";

   // {{{  protected _load( $row, Engine_Models_ClaimStateLog $obj )
   protected function _load( $row, Engine_Models_ClaimStateLog $obj )
      {
      $obj = parent::_load( $row, $obj );
      $obj->setTimestamp( $row->timestamp );
            
      return $obj;
      }     
            
   // }}}   
   // {{{ public _extractArray( Engine_Models_ClaimStateLog $obj )
   protected function _extractArray( Engine_Models_ClaimStateLog $obj )
      {
      $vars = parent::_extractArray( $obj );
      $vars['timestamp'] = $obj->getTimestamp();

      return $vars;
      }     
            
            
   // }}}

   /*
    * this is a log, so we never want to update the tabel
    * only add new rows
    */
   public function save( Engine_Models_ClaimStateLog $obj )
      {
      $data = $this->_extractArray( $obj );
 
      $this->getDbTable()->insert( $data );
      }

   /*
    * Find out who update their claim last for a given dispute
    */
   public function findLastChangeToDispute(  $disputeID )
      {
      $select= $this->getDbTable()->select()->where( 'disputeID = ?', $disputeID )
                                            ->order( array( 'timestamp DESC', 'id' ) )
                                            ->limit( 1 );

      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }

      return self::_load( $row, new Engine_Models_ClaimStateLog() );
      }

   /*
    * Find the lasto update made to a users claim for the given dispute
    */
   public function findLastDisputeBy( $userID, $disputeID )
      {
      $select= $this->getDbTable()->select()->where( 'userID = ?', $userID )
                                            ->where( 'disputeID = ?', $disputeID )
                                            ->order( array( 'timestamp DESC', 'id' ) )
                                            ->limit( 1 );
      
      $row = $this->getDbTable()->fetchRow( $select );
                                   
      if( 0 == count($row) )
         { 
         return null;
         }

      return self::_load( $row, new Engine_Models_ClaimStateLog() );
      }

   }

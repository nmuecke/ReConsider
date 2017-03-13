<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Models_ClaimStateMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Engine_Model_DbTable_ClaimState";

   // {{{  protected _load( $row, Engine_Models_ClaimState $obj )
   protected function _load( $row, Engine_Models_ClaimState $obj )
      {
      $obj->setId( $row->id )
          ->setDisputeID( $row->disputeID )
          ->setUserID( $row->userID )
          ->setNodeID( $row->nodeID )
          ->setClaimID( $row->claimID )
          ->setSysInference( $row->sysInference )
          ->setUserInference( $row->userInference )
          ->setStatus( $row->status );

      return $obj;
      }

   // }}}
   // {{{ public _extractArray( Engine_Models_ClaimState $obj )
   protected function _extractArray( Engine_Models_ClaimState $obj )
      {
      return array(
                    'id'                 => $obj->getId(),
                    'disputeID'          => $obj->getDisputeID(),
                    'nodeID'             => $obj->getNodeID(),
                    'userID'             => $obj->getUserID(),
                    'claimID'            => $obj->getClaimID(),
                    'sysInference'       => $obj->getSysInference(),
                    'userInference'      => $obj->getUserInference(),
                    'status'             => $obj->getStatus()
                    );
      }


   // }}}
   // {{{ public save( Engine_Models_ClaimState $obj )
   public function save( Engine_Models_ClaimState $obj )
      {
      $data = $this->_extractArray( $obj );
      
      if( null == $obj->getId() )
            {
            //Zend_Debug::Dump( "Insert data: node id " . $data['nodeID'] );
            $this->getDbTable()->insert( $data );
            }
         else
            {
            //Zend_Debug::Dump( "Updateing data: node id " . $data['nodeID'] );
            $this->getDbTable()->update( $data, array('id = ?' => $obj->getId() ));
            }
 
      }


   // }}}
   // {{{ public removeClaim( Engine_Models_ClaimState $obj )
   public function removeClaim( Engine_Models_ClaimState $obj )
      {
      $data = array( 
                    'disputeID = ?'      => $obj->getDisputeID(),
                    'nodeID = ?'         => $obj->getNodeID(),
                    'userID = ?'         => $obj->getUserID(),
                    'claimID = ?'        => $obj->getClaimID()
                    );

 
      if( null == $obj->getDisputeID() 
       || null == $obj->getNodeID() 
       || null == $obj->getUserID() 
       || null == $obj->getClaimID()    )
            {
            //Zend_Debug::Dump( "Unable to removeing claim: id " . $obj->getId() );
            //Zend_Debug::Dump( $obj );
            return null;
            }
         else
            {
            //Zend_Debug::Dump( "Removeing claim: id " . $obj->getId() );
            $this->getDbTable()->delete( $data );
            }
 
      }


   // }}}
   // {{{ public removeClaimStatus( Engine_Models_ClaimState $obj )
   public function removeClaimStatus( $disputeID, $nodeID, $userID = false )
      {
      $updateState = array( 'status' => NULL );
      $where = array( 
                    'disputeID = ?'      => $disputeID,
                    'nodeID = ?'         => $nodeID
                    );
      
      if( $userID != false )
         {
         $where['userID = ?'] = $userID;
         }

 
      if( null == $disputeID 
       || null == $nodeID )
            {
            //Zend_Debug::Dump( "Unable to removeing node claim status: id " . $nodeID );
            return null;
            }
         else
            {
            //Zend_Debug::Dump( $where, "Removeing claim status: for" . $nodeID );
            $this->getDbTable()->update( $updateState, $where );
            }
 
      }


   // }}}
   // {{{ public changeClaimStatus( Engine_Models_ClaimState $obj )
   public function changeClaimStatus( $disputeID, $nodeID, $newState, $userID = false )
      {
      $updateState = array( 'status' => $newState );
      $where = array( 
                    'disputeID = ?'      => $disputeID,
                    'nodeID = ?'         => $nodeID,
                    'status != ?'        => null
                    );
      
      if( $userID != false )
         {
         $where['userID = ?'] = $userID;
         }

 
      if( null == $disputeID 
       || null == $nodeID )
            {
            //Zend_Debug::Dump( "Unable to removeing node claim status: id " . $nodeID );
            return null;
            }
         else
            {
            //Zend_Debug::Dump( $where, "Removeing claim status: for" . $nodeID );
            $this->getDbTable()->update( $updateState, $where );
            }
 
      }


   // }}}
   // {{{ public findClaimFor( $disputeID, $userID, $nodeID, $claimID, $notUser = false )
   public function findClaimFor( $disputeID, $userID, $nodeID, $claimID, $notUser = false )
      {
      if( $notUser == true )
         {
         $select= $this->getDbTable()->select()->where( 'userID  != ?', $userID  )
                                               ->where( 'disputeID  = ? ', $disputeID  )
                                               ->where( 'nodeID  = ?', $nodeID  )
                                               ->where( 'claimID = ?', $claimID );
         }
      else
         {
         $select= $this->getDbTable()->select()->where( 'userID  = ?', $userID  )
                                               ->where( 'disputeID  = ? ', $disputeID  )
                                               ->where( 'nodeID  = ?', $nodeID  )
                                               ->where( 'claimID = ?', $claimID );

         }
      $row = $this->getDbTable()->fetchRow($select);

      if( 0 == count($row) )
         {
         return null;
         }

      return $this->_load( $row, new Engine_Models_ClaimState() );
      }


   // }}}
   // {{{ public findDisputeClaimsFor( $disputeID,  $nodeID, $claimID )
   public function findDisputeClaimsFor( $disputeID, $nodeID )
      {
      $select= $this->getDbTable()->select()
                                            ->where( 'disputeID  = ? ', $disputeID  )
                                            ->where( 'nodeID  = ?', $nodeID  );

      $rows = $this->getDbTable()->fetchAll($select);

      if( 0 == count($rows) )
         {
         return null;
         }
      $results = array();
      foreach( $row as $row )
         {
         $results[$row->userID]['claims'][$row->claimID] = $this->_load( $row, new Engine_Models_ClaimState() );
         $results[$row->userID]['claims'][$row->claimID] = $this->_load( $row, new Engine_Models_ClaimState() );
         if( $row->status != null )
            $results[$row->userID]['active'] = $row->claimID;
         }

      return $results;
      }


   // }}}
   // {{{ public findActiveClaimFor( $disputeID, $userID, $nodeID )
   public function findActiveClaimFor( $disputeID, $userID, $nodeID )
      {
      $select= $this->getDbTable()->select()->where( 'userID = ?', $userID )
                                            ->where( 'nodeID = ?', $nodeID )
                                            ->where( 'disputeID  = ? ', $disputeID  )
                                            ->where( 'status != ?', 'null' );

      $row = $this->getDbTable()->fetchRow($select);

      if( 0 == count($row) )
         {
         return null;
         }
      
      return $this->_load( $row, new Engine_Models_ClaimState() );
      }


   // }}}
   // {{{ public findActiveDisputeClaimsFor( $disputeID, $nodeID )
   public function findActiveDisputeClaimsFor( $disputeID, $nodeID )
      {
      $select= $this->getDbTable()->select()
                                            ->where( 'nodeID = ?', $nodeID )
                                            ->where( 'disputeID  = ? ', $disputeID  )
                                            ->where( 'status != ?', 'null' );

      $rows = $this->getDbTable()->fetchAll($select);

      if( 0 == count($rows) )
         {
         return null;
         }

      $results = array();
      foreach( $row as $row )
         {
         $results[$row->userID] = $this->_load( $row, new Engine_Models_ClaimState() );
         }

      return $results;
      }


   // }}}
   // {{{ public findActiveChildClaimsFor( $disputeID, $userID, $nodeID )
   public function findActiveChildClaimsFor( $disputeID, $userID, $nodeID )
      {
      $select= $this->getDbTable()->select()->where( 'userID = ?', $userID )
                                            ->where( 'nodeID = ?', $nodeID )
                                            ->where( 'status != ?', 'null' );

      $rows = $this->getDbTable()->fetchALL($select);

      $claims = array();
      foreach( $rows as $row )
         {
         $claims[$row->id] = $this->_load( $row, new Engine_Models_ClaimState() );
         }

      return $claims;
      }


   // }}}
   // {{{ public findNodesWithStatusOf( $disputeID, $userID, $status )
   public function findClaimsWithStatusOf( $disputeID, $userID, $status )
      {
      $nodeIDs = array();

      $select = $this->getDbTable()->select()
                                   ->where( 'disputeID  = ? ', $disputeID  )
                                   ->where( 'userID  = ? ', $userID  )
                                   ->where( 'status  = ? ', $status  );

      ////Zend_Debug::Dump( $select->assemble() );
      $rows = $this->getDbTable()->fetchAll( $select );
  
      $claims = array();
      foreach( $rows as $row )
         {
         $claims[] = $this->_load( $row, new Engine_Models_ClaimState() );
         }

      return $claims;
      }


   // }}}
   // {{{ public findUnclaimedNodes( $disputeID, $userID )
   public function findUnclaimedNodes( $disputeID, $userID )
      {
      $nodeIDs = array();
      $select = $this->getDbTable()->select()
                                   ->where( 'disputeID  = ? ', $disputeID  )
                                   ->where( 'userID  = ? ', $userID  )
                                   ->where( 'status != ? ', 'NULL' );
//Zend_Debug::Dump( $select->assemble(), "not claimed" );

      $rows = $this->getDbTable()->fetchAll( $select );
     
      $select = $this->getDbTable()->select();

      $select->where( 'disputeID  = ? ', $disputeID  )
             ->where( 'userID  = ? ', $userID  );
      foreach($rows as $row )
         {
         $select->where( "nodeID != ? ", $row->nodeID );
         }
      $select->group( "nodeID" );
 
      //Zend_Debug::Dump( $select->assemble(), "not claimed" );

      $rows = $this->getDbTable()->fetchAll( $select );


      $claims = array();
      foreach( $rows as $row )
         {
         $claims[] = $this->_load( $row, new Engine_Models_ClaimState() );
         }

      return $claims;
      }


   // }}}
   // {{{ public findClaimsFor( $disputeID, $userID, $nodeID )
   public function findClaimsFor( $disputeID, $userID, $nodeID )
      {
      $select= $this->getDbTable()->select()->where( 'userID = ?', $userID )
                                            ->where( 'disputeID  = ? ', $disputeID  )
                                            ->where( 'nodeID = ?', $nodeID );

      $rows = $this->getDbTable()->fetchALL($select);

      $claims = array();
      foreach( $rows as $row )
         {
         $claims[] = $this->_load( $row, new Engine_Models_ClaimState() );
         }

      return $claims;
      }


   // }}}
   // {{{ public findActiveClaim( Engine_Models_ClaimState $obj, $notUser = false )
   public function findActiveClaim( Engine_Models_ClaimState $obj, $notUser = false )
      {
      if( $notUser == false )
         {
         $select = $this->getDbTable()->select()->where( 'disputeID = ?', $obj->getDisputeID() )
                                                ->where( 'userID = ?', $obj->getUserID() )
                                                ->where( 'nodeID = ?', $obj->getNodeID() )
                                                ->where( 'status != ?', 'null' );
         }
      else 
         {
         $select = $this->getDbTable()->select()->where( 'disputeID = ?', $obj->getDisputeID() )
                                                ->where( 'userID != ?', $obj->getUserID() )
                                                ->where( 'userID != ?', 'null' )
                                                ->where( 'nodeID = ?', $obj->getNodeID() )
                                                ->where( 'status != ?', 'null' );
          }
      //Zend_Debug::Dump( $select->assemble() );

      $row = $this->getDbTable()->fetchRow( $select );


      if( 0 == count($row) )
         {
         //Zend_Debug::Dump( "no row found", "ROW" );
         return null;
         }

            

      return $this->_load( $row, new Engine_Models_ClaimState() );
     
      }


   // }}}
   // {{{ public findSystemInferedClaim( Engine_Models_ClaimState $obj )
   public function findSystemInferedClaim( Engine_Models_ClaimState $obj )
      {
      $select = $this->getDbTable()->select()->where( 'disputeID = ?', $obj->getDisputeID() )
                                             ->where( 'nodeID = ?', $obj->getNodeID() );
      //Zend_Debug::Dump( $select->assemble(), "System Infered Claim" );

      $row = $this->getDbTable()->fetchRow( $select );


      if( 0 == count($row) )
         {
         //Zend_Debug::Dump( "no row found", "ROW" );
         return null;
         }



      return $this->_load( $row, new Engine_Models_ClaimState() );

      }


   // }}}
   }

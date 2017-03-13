<?php

class Engine_Inference_Model_BayesProbMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Engine_Model_DbTable_BayesProb";

   protected function _load( $row, Engine_Inference_Model_BayesProb $obj )
      {
      $obj->setId( $row->id )
          ->setNodeID( $row->nodeID )
          ->setClaimID( $row->claimID )
          ->setParentNodeID( $row->parentNodeID )
          ->setParentClaimID( $row->parentClaimID )
          ->setProb( $row->prob );

      return $obj;
      }

   protected function _extractArray( Engine_Inference_Model_BayesProb $obj )
      {
      $data = array( "id"            => $obj->getID(),
                     "nodeID"        => $obj->getNodeID(),
                     "claimID"       => $obj->getClaimID(),
                     "parentNodeID"  => $obj->getParentNodeID(),
                     "parentClaimID" => $obj->getParentClaimID(),
                     "prob"          => $obj->getProb(),
                    );

      return $data;
      }

   public function findChildClaimsFor( $nodeID )
      {
      $select = $this->getDbTable()->select()->where( 'parentNodeID = ?', $nodeID );

      $row = $this->getDbTable()->fetchRow($select);

      if( 0 == count($row) )
         {
         return null;
         }

      return $this->_load( $row, new Engine_Inference_Model_BayesProb() );

      }

   public function findPriorP( $nodeID, $claimID )
      {
      $select = $this->getDbTable()->select()->where( 'nodeID = ?', $nodeID )
                                             ->where( 'claimID = ?', $claimID );

      $row = $this->getDbTable()->fetchRow($select);

      if( 0 == count($row) )
         {
         return null;
         }

      return $this->_load( $row, new Engine_Inference_Model_BayesProb() );
      }


   public function findConditionalP( $nodeID, $claimID, $parentNodeID, $parentClaimID )
      {
      $select = $this->getDbTable()->select()->where( 'nodeID = ?', $nodeID )
                                             ->where( 'claimID = ?', $claimID ) 
                                             ->where( 'parentNodeID = ?', $parentNodeID )
                                             ->where( 'parentClaimID = ?', $parentClaimID ); 

      $row = $this->getDbTable()->fetchRow($select);

      if( 0 == count($row) )
         {
         return null;
         }

      return $this->_load( $row, new Engine_Inference_Model_BayesProb() );
      }

   public function findNotConditionalP( $nodeID, $claimID, $parentNodeID, $parentClaimID )
      {
      $select = $this->getDbTable()->select()->where( 'nodeID = ?', $nodeID )
                                             ->where( 'claimID = ?', $claimID )
                                             ->where( 'parentNodeID = ?', $parentNodeID )
                                             ->where( 'parentClaimID != ?', $parentClaimID );

      $rows = $this->getDbTable()->fetchAll($select);

      if( 0 == count($rows) )
         {
         return null;
         }
      $results = array();
      foreach( $rows as $row )
         {
         $results[$row->parentClaimID][$row->claimID] = $this->_load( $row, new Engine_Inference_Model_BayesProb() );
         }
      return $results; 
      }

   public function findAllPriorP( $nodeID )
      {
      $select = $this->getDbTable()->select()->where( 'nodeID = ?', $nodeID );

      $rows = $this->getDbTable()->fetchAll($select);

      if( 0 == count($rows) )
         {
         return null;
         }

      $results = array();
      foreach( $rows as $row )
         {
         $results[$row->claimID] = $this->_load( $row, new Engine_Inference_Model_BayesProb() );
         }
      return $results;

      return $this->_load( $row, new Engine_Inference_Model_BayesProb() );

      }

   public function findAllConditionalP( $nodeID, $parentNodeID )
      {
      $select = $this->getDbTable()->select()->where( 'nodeID = ?', $nodeID )
                                             ->where( 'parentNodeID = ?', $parentNodeID );

      $rows = $this->getDbTable()->fetchAll($select);

      if( 0 == count($rows) )
         {
         return null;
         }
      $results = array();
      foreach( $rows as $row )
         {
         $results[$row->parentClaimID][$row->claimID] = $this->_load( $row, new Engine_Inference_Model_BayesProb() );
         }
      return $results;

      }

   public function save( Engine_Inference_Model_BayesProb $obj )
      {
      $data = self::_extractArray( $obj );

      if( null == $obj->getId() )
            {
            $this->getDbTable()->insert( $data );
            }
         else
            {
            $this->getDbTable()->update( $data, array('id = ?' => $obj->getId() ));
            }


      }
   
   public function findAll()
      {
      $elements = array();

      $resultSet = $this->getDbTable()->fetchAll();
      foreach( $resultSet as $row ) 
        {
        $node = new Engine_Models_RootNodes();
        $elements[] = self::_load( $row, $node );
        }

      return $elements;
      }

   }

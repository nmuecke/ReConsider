<?php

class Engine_Models_DisputesToNodesMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Engine_Model_RootNodes";

   protected function _load( $row, Engine_Models_RootNodes $rootnode )
      {
      $rootnode->setId( $row->id )
               ->setNodeID( $row->nodeID )
               ->setDisputeType( $row->disputeType )
               ->setDesctiption( $row->desctiption )
               ->setLabel( $row->label );

      return $rootnode;
      }

   public function findRootNodeID( $disputeType )
      {
      $select = $this->getDbTable()->select( )
                                   ->where( 'disputeType = ? ', $disputeType );

      $row = $this->getDbTable()->fetchRow( $select );
      if( 0 == count( $row ) )
         {
         return null;
         }
      return $row->nodeID;
      }

   public function findAll()
      {
      $rootnodes = array();

      $resultSet = $this->getDbTable()->fetchAll();
      foreach( $resultSet as $row ) 
        {
        $node = new Engine_Models_RootNodes();
        $rootnodes[] = self::_load( $row, $node );
        }

      return $rootnodes;
      }

   }

<?php

class Engine_Node_Model_RootMapper extends Engine_Node_Model_AbstractMapper
   {

   public function findRootNode( $disputeType, Engine_Node_Model_Root $node )
      { 
      $nodes = array();

      $select = $this->getDbTable()->select()
                                   ->from( array( 't1' => 'nodes' ) )
                                   ->join( array( 't2' => 'rootNodes'), 't1.nodeID = t2.nodeID', array() )
                                   ->where( 't2.disputeType = ?', $disputeType );

      //Zend_Debug::Dump( $select->assemble() );

      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }

      return self::_loadNode( $row, $node );
      }  
      
   public function findDisputeNodes( $disputeType )
      {
      return null;
      } 
   }

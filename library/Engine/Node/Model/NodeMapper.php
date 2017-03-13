<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Node_Model_NodeMapper
   {
   protected $_dbTable;
   protected $_factory;

   // {{{ public setDbTable( $dbTable )
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

   // }}}
   // {{{ public getDbTable()
   public function getDbTable()
      {
      if( null === $this->_dbTable )
         {
         $this->setDbTable('Engine_Model_DbTable_Nodes');
         }

      return $this->_dbTable;
      }
   // }}}
   // {{{ protected _getFactory()
   protected function _getFactory()
      {
      if( null === $this->_factory )
         {
         $this->_factory = Engine_Node_Model_Factory::getInstance();
         }
      return $this->_factory;
      }
   // }}}
   // {{{ public find( $id )
   public function find( $id )
      {
      $select = $this->getDbTable()->select()->where( 'id = ?', $id );
      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }
      $node = self::_getFactory()->createNode( $row->nodeType );

      return self::_loadNode( $row, $node );
      }


   // }}}
   // {{{ public findNode( $id )
   public function findNode( $id )
      {
      $select = $this->getDbTable()->select()->where( 'nodeID = ?', $id );
      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }
      $node = self::_getFactory()->createNode( $row->nodeType );

      return self::_loadNode( $row, $node );
      }


   // }}}
   // {{{ public findParentNode( $id )
   public function findParentNode( $id )
      {
      if( is_int( $id ) )
         {
         $childNode = $this->find( $id );
         if( $childNode == null )
            return null;

         $id = $childNode->getParentNodeID();
         $select = $this->getDbTable()->select()->where( 'nodeID = ?', $id );
         }
      else 
         {
         $select = $this->getDbTable()->select()->where( 'parentNodeID = ?', $id );
         }

      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }
      $node = self::_getFactory()->createNode( $row->nodeType );

      return self::_loadNode( $row, $node );
      }


   // }}}
   // {{{ public hasChild( $id )
   public function hasChild( $id )
      {
      $select = $this->getDbTable()->select()->where( 'parentNodeID = ?', $id );

      $row = $this->getDbTable()->fetchAll( $select );

      if( 0 == count($row) )
         {
         return false;
         }
      return true;
      }


   // }}}
   // {{{ public _loadNode( $row, Engine_Node_Model_Abstract $node )
   protected function _loadNode( $row, Engine_Node_Model_Abstract $node )
      {
      $node->setId( $row->id )
           //->sesDisputeType( $row->disputeType )
           ->setNodeID( $row->nodeID )
           ->setParentNodeID( $row->parentNodeID )
           ->setCounterNodeID( $row->counterNodeID )
           ->setTitle( $row->title )
           ->setPrefix( $row->prefix )
           ->setSuffix( $row->suffix )
           ->setRelevance( $row->relevance )
           ->setMoreInfo( $row->moreInfo )
           ->setAction( $row->action );

      return $node;

      }

   // }}}
   // {{{ public findRootNode( $disputeType, Engine_Node_Model_Root $node = null )
   public function findRootNode( $disputeType, Engine_Node_Model_Root $node = null )
      {
      $nodes = array();
/*
      $select = $this->getDbTable()->select()
                                   ->where( 'disputeType = ?', $disputeType )
                                   ->where( 'parentNodeID IS NULL'  );
*/
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

      if( $node == null )
         {  
         $node = self::_getFactory()->createNode( $row->nodeType );
         }

      return self::_loadNode( $row, $node );
      }

   // }}}
   // {{{ public findSubNodesOf( $nodeID )
   public function findSubNodesOf( $nodeID )
      {
      $nodes = array();

      $select = $this->getDbTable()->select()
                                   ->where( 'parentNodeID  = ? ', $nodeID  );

      ////Zend_Debug::Dump( $select->assemble() );
      $result = $this->getDbTable()->fetchAll( $select );
  

      foreach( $result as $row )
         {
         $node = self::_getFactory()->createNode( $row->nodeType );         
         $nodes[] = self::_loadNode( $row, $node );
         }

      return $nodes;
      }

   // }}}
   }

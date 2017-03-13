<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

abstract class Extend_Db_AbstractMapper
   {
   protected $_dbTable;
   protected $_dbClass;

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
         $this->setDbTable( $this->_dbClass );
         }

      return $this->_dbTable;
      }

   public function find( $id, Extend_Db_AbstractTableModel $obj )
      {
      $select = $this->getDbTable()->select()->where( 'id = ?', $id );
      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }

      return $this->_load( $row, $obj );
      }

   
   protected function _load( $row, Extend_Db_AbstractTableModel $obj )
      {
      $obj->setId( $row->id );

      return $obj;
      }

   }

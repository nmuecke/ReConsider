<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Default_Model_AccessLogMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Default_Model_DbTable_AccessLog";

   // {{{  protected _load( $row, Default_Model_AccessLog $obj )
   protected function _load( $row, Default_Model_AccessLog $obj )
      {
      $obj->setId( $row->id )
          ->setUserID( $row->userID )
          ->setTimestamp( $row->timestamp );

      return $obj;
      }

   // }}}
   // {{{ public _extractArray( Default_Model_AccessLog $obj )
   protected function _extractArray( Default_Model_AccessLog $obj )
      {
      return array(
                    'id'                 => $obj->getId(),
                    'userID'             => $obj->getUserID(),
                    'timestamp'          => $obj->getTimestamp()
                    );
      }
   

   // }}}

   public function save( Default_Model_AccessLog $obj )
      {
      $data = self::_extractArray( $obj );
      
      if( null != $obj->getId() )
         { 
         return null;
         }       
      else                               
         {                               
         $this->getDbTable()->insert( $data );
         }
      return true;
      }
   
   public function findLastAccessBy( $userID )
      {
      $select= $this->getDbTable()->select()->where( 'userID = ?', $userID )
                                            ->order( array( 'timestamp DESC', 'id' ) )
                                            ->limit( 1 );
      
      $row = $this->getDbTable()->fetchRow( $select );
                                   
      if( 0 == count($row) )       
         {                         
         return null;
         }
      
      return self::_load( $row, new Default_Model_AccessLog() ); 
      }

   }


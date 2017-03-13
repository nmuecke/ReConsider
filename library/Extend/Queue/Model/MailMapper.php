<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Extend_Queue_Model_MailMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Extend_Queue_Model_DbTable_Mail";

   protected function _load( $row, Extend_Queue_Model_Mail $obj )
      {
      $obj->setId( $row->id )
          ->setMailType( $row->mailType )
          ->setDisputeID( $row->disputeID )
          ->setUserId( $row->userID )
//          ->setSentAt( $row->sentAt )
          ->setAddedAt( $row->addedAt );

      return $obj;
      }

   protected function _toArray( Extend_Queue_Model_Mail $obj )
      {
      return array(
                   'id'        => $obj->getId(),
                   'mailType'      => $obj->getMailType(),
                   'disputeID' => $obj->getDisputeID(),
                   'userID'    => $obj->getUserID(),
                   //'sentAt'    => $obj->getSentAt(),
                   'addedAt'   => $obj->getAddedAt(),
                   );
      }

   public function findExistingMailFor( $userID )
      {
      $select = $this->getDbTable()->select()->where( 'userID = ?', $userID );

      $rows = $this->getDbTable()->fetchAll( $select );

      $res = array();
      foreach( $rows as $row )
         {
         $res[] = $this->_load( $row, new Extend_Queue_Model_Mail() );
         }

      return $res;
      }
   
   public function findDisputeMailFor( $disputeId, $userId )
      {
      $select = $this->getDbTable()->select()->where( 'userID = ?', $userId )
                                             ->where( 'disputeID = ?', $disputeId );

      $row = $this->getDbTable()->fetchRow( $select );

      if( count( $row ) == 0 )
         {
         return null;
         }

      return $this->_load( $row, new Extend_Queue_Model_Mail() );
      }


   public function save( Extend_Queue_Model_Mail $obj )
      {
      $data = $this->_toArray( $obj );
      
      if( null == $obj->getId() )
            {
            $this->getDbTable()->insert( $data );
            }
         else
            {
            $this->getDbTable()->update( $data, array('id = ?' => $obj->getId() ));
            }

      }

   public function remove( Extend_Queue_Model_Mail $obj )
      {
      $data = $this->_toArray( $obj );

      if( $obj->getId() == null )   
         {
         return null;
         }
      else
         {
         $this->getDbTable()->delete( array( 'id'=>$data['id'] ) );
         }
      return true;
      }


   public function findAll( )
      {
      $select = $this->getDbTable()->select();

      $rows = $this->getDbTable()->fetchAll( $select );

      $res = array();
      foreach( $rows as $row )
         {
         $res[] = $this->_load( $row, new Extend_Queue_Model_Mail() );
         }

      return $res;
      }

   }

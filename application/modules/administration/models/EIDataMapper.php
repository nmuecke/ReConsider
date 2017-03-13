<?php


class Administration_Model_EIDataMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Administration_Model_DbTable_EIData";

   protected function _load( $row, Administration_Model_EIData $obj )
      {
      $obj->setId( $row->id )
          ->setEIID( $row->eiid )
          ->setPassword( $row->password )
          ->setActive( $row->active );

      return $obj;
      }    

   protected function _toArray(  Administration_Model_EIData $obj )
      {
      $data = array(
                     'id'       => $obj->getId(),
                     'eiid'     => $obj->getEIID(),
                     'password' => $obj->getPassword(),
                     'active'   => $obj->getActive(),
                   );

      return $data;
      }

   public function save( Administration_Model_EIData $obj )
      {
      $data = $this->_toArray( $obj );
 
         if( ($res = $this->findByEIID( $data['eiid'] )) == null )
            {
            return $this->getDbTable()->insert($data);
            } 
         else 
            {
            return $this->getDbTable()->update($data, array('id = ?' => $res->getId() ));
            }
         }

   public function findByEIID( $id )
      {
      $select = $this->getDbTable()->select()->where( 'eiid = ?', $id );

      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }

      return $this->_load( $row, new Administration_Model_EIData() );
      }

   public function findActive()
      {
      $select = $this->getDbTable()->select()->where( 'active = ?', true );

      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }

      return $this->_load( $row, new Administration_Model_EIData() ); 
      }

   public function fetchAll( )
      {
      $res = array();
      $rows = $this->getDbTable()->fetchAll();
      
      foreach( $rows as $row )
         {
         $res[] = $this->_load( $row, new Administration_Model_EIData() );
         }
      return $res;
      }

   public function deactivate( )
      {
      $this->getDbTable()->update(array( 'active' => false ), array('active = ?' => true ));
      }

   public function activate( $id )
      {
      $this->deactivate( );
      $this->getDbTable()->update(array( 'active' => true ), array('id = ?' => $id ));
      }
 
   public function remove( $id )
      {
      $select = $this->getDbTable()->select()->where( 'id = ?', $id );

      $row = $this->getDbTable()->fetchRow( $select );
      if( count( $row ) == 1 )
         {
         $row->delete();
         }
      }

   }

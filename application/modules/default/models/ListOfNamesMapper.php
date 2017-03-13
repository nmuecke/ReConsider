<?php

define( "MALE",     "M" );
define( "FEMALE",   "F" );
define( "LASTNAME", "L" );

class Default_Model_ListOfNamesMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Default_Model_DbTable_ListOfNames";

   protected function _load( $row, Default_Model_ListOfNames $obj )
      {
      $obj->setId( $row->id )
          ->setName( $row->name )
          ->setType( $row->type );

      return $obj;
      }    

   protected function _toArray(  Default_Model_ListOfNames $obj )
      {
      $data = array(
                     'id'   => $obj->getId(),
                     'name' => $obj->getName(),
                     'type' => $obj->getType(),
                   );

      return $data;
      }

   public function save( Default_Model_ListOfNames $obj )
      {
      $data = $this->_toArray( $obj );
 
         if( $this->find( $data['id'], new Default_Model_AuthUser() ) == null )
            {
            $this->getDbTable()->insert($data);
            return true;
            } 
         else 
            {
            $this->getDbTable()->update($data, array('id = ?' => $data['id'] ));
            }
         }

   public function getRandomName( $type )
      {
      switch( $type )
         {
         case MALE:
         case FEMALE:
         case LASTNAME:
            break;
         default:
            throw new Exception( "Invalid name type supplyed! " . $type . " given" );
         }
      $select = $this->getDbTable()->select()->where( 'type = ?', $type )
                                             ->order( new Zend_Db_Expr('RAND()') )
                                             ->limit( 1 );
      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return null;
         }

      return $this->_load( $row, new Default_Model_ListOfNames() );
      } 
   }

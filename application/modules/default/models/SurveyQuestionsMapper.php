<?php


class Default_Model_SurveyQuestionsMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Default_Model_DbTable_SurveyQuestions";

   protected function _load( $row, Default_Model_SurveyQuestions $obj )
      {
      $obj->setId( $row->id )
          ->setLabel( $row->label )
          ->setHighLabel( $row->highLabel )
          ->setLowLabel( $row->lowLabel )
          ->setOrder( $row->order )
          ->setRequired( $row->required )
          ->setName( $row->name )
          ->setType( $row->type );

      return $obj;
      }    

   protected function _toArray(  Default_Model_SurveyQuestions $obj )
      {
      $data = array(
                     'id'        => $obj->getId(),
                     'name'      => $obj->getName(),
                     'type'      => $obj->getType(),
                     'label'     => $obj->getLabel(),
                     'highLabel' => $obj->getHighLabel(),
                     'lowLabel'  => $obj->getLowLabel(),
                     'order'     => $obj->getOrder(),
                     'required'  => $obj->getRequired(),
                   );

      return $data;
      }

   public function save( Default_Model_SurveyQuestions $obj )
      {
      $data = $this->_toArray( $obj );
 
         if(  $data['id'] == null )
            {
            $this->getDbTable()->insert($data);
            return true;
            } 
         else 
            {
            $this->getDbTable()->update($data, array('id = ?' => $data['id'] ));
            }
         }

   public function findAll()
      {
      $select = $this->getDbTable()->select( )
                                   ->order( array( 'order ASC' ) );
     
      $rows = $this->getDbTable()->fetchAll( $select );
      $res = array();
      foreach( $rows as $row )
         {
         $res[$row->order] = $this->_load( $row, new Default_Model_SurveyQuestions() );
         }
      return $res;
      }

   public function findByName( $name )
      {
      $select = $this->getDbTable()->select( )
                                   ->where( 'name = ?', $name );

      $row = $this->getDbTable()->fetchRow( $select );

      if( count( $row ) == 1 )
         {
         return $this->_load( $row, new Default_Model_SurveyQuestions() );
         }
      return null;
      }

     // formats the survey questions into a matrix
   public function formatSurveyQuestions( array $rawData, Extend_Struct_Model_Table $data )
      {
      $row = new Extend_Struct_Model_Row( "heading" );
      $row->setCol( 0, "" );
      foreach( $rawData as $dataItem )
         {
         $row->setCol( $dataItem->getID(), $dataItem->getLabel() );
         }

      $data->addRow( $row );

      return $data;
      }
 
   }

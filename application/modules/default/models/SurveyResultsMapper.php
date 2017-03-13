<?php


class Default_Model_SurveyResultsMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Default_Model_DbTable_SurveyResults";

   protected function _load( $row, Default_Model_SurveyResults $obj )
      {
      $obj->setId( $row->id )
          ->setQuestionID( $row->questionID )
          ->setResult( $row->result )
          ->setUserID( $row->userID );
           

      return $obj;
      }    

   protected function _toArray(  Default_Model_SurveyResults $obj )
      {
      $data = array(
                     'id'         => $obj->getId(),
                     'questionID' => $obj->getQuestionID(),
                     'result'     => $obj->getResult(),
                     'userID'     => $obj->getUserID(),
                   );

      return $data;
      }

   public function save( Default_Model_SurveyResults $obj )
      {
      $data = $this->_toArray( $obj );
 
         if( $data['id'] == null )
            {
            $this->getDbTable()->insert($data);
            return true;
            } 
         else 
            {
            $this->getDbTable()->update($data, array('id = ?' => $data['id'] ));
            }
         }

   public function hasTakenSurvey( $userID )
      {
      $select = $this->getDbTable()->select()
                                   ->where( 'userID = ?', $userID )
                                   ->limit( 1 );

      $res = $this->getDbTable()->fetchRow( $select );

      if( count( $res ) == 1 )
         {
         return true;
         } 

      return false;
      }

   public function getResults( array $userIDs = null )
      {
      $select = $this->getDbTable()->select();
      if( $userIDs == null )
         {
         }
      else
         {
         foreach( $userIDs as $userID )
            {
            $select->where( 'userID = ?', $userID );
            }
         }
      $rows = $this->getDbTable()->fetchAll( $select );

      $res = array();
      foreach( $rows as $row )
         {
         $res[] = $this->_load( $row, new Default_Model_SurveyResults() );
         }
       
      return $res;

      }

   // formats the survey results into a userid orientates matrix
   public function formatSurvey( array $rawData, Extend_Struct_Model_Table $data )
      {
      foreach( $rawData as $dataItem )
         {
         $rowID = $data->getRowID( $dataItem->getUserID() );
         if( $rowID == false )
            {
            $row = new Extend_Struct_Model_Row( $dataItem->getUserID(), 1, array( $dataItem->getUserID() ) );
            $row->setCol( $dataItem->getQuestionID(), $dataItem->getResult() );
            $data->addRow( $row );
            }
         else         
            { 
            $data->updateData( $dataItem->getResult(), $rowID, $dataItem->getQuestionID() );
            }

         }

      return $data;
      }

   }

<?php
define( "PATIENT", "P" );
define( "DOCTOR",  "D" );

class Default_Model_AuthUsersMapper extends Extend_Db_AbstractMapper
   {
   protected $_dbClass = "Default_Model_DbTable_AuthUsers";

   protected function _load( $row, Default_Model_AuthUsers $obj )
      {
      $obj->setId( $row->id )
          ->setRole( $row->role )
          ->setEITest( $row->eiTest )
          ->setFirstName( $row->firstName )
          ->setLastName( $row->lastName )
          ->setGender( $row->gender );

      return $obj;
      }    

   protected function _toArray(  Default_Model_AuthUsers $obj )
      {
      $data = array(
                     'id'        => $obj->getId(),
                     'role'      => $obj->getRole(),
                     'eiTest'    => $obj->getEITest(),
                     'firstName' => $obj->getFirstName(),
                     'lastName'  => $obj->getLastName(),
                     'gender'    => $obj->getGender(),
                   );

      return $data;
      }

   public function save( Default_Model_AuthUsers $obj )
      {
      $data = $this->_toArray( $obj );
 
         if( $this->find( $data['id'], new Default_Model_AuthUsers() ) == null )
            {
            $this->getDbTable()->insert($data);
            return true;
            } 
         else 
            {
            $this->getDbTable()->update($data, array('id = ?' => $data['id'] ));
            }
         }

   public function nameExists( $firstName, $lastName )
      {
/*
      if( is_null( $firstName ) || is_null( $lastName ) )
         {
         return true;
         }
*/
      $select = $this->getDbTable()->select()->where( 'firstName = ?', $firstName )
                                             ->where( 'lastName = ?',  $lastName )
                                             ->limit( 1 );
      $row = $this->getDbTable()->fetchRow( $select );

      if( 0 == count($row) )
         {
         return false;
         }

      return true;
      }

   public function findAllUserData( array $where = array() )
      {
      $tbName1 = 'auth';
      $tbName2 = 'authUsers';
      $tbName3 = 'userToDispute';
      $tbName4 = 'disputes';
      $select = $this->getDbTable()->select( )
                                   ->setIntegrityCheck( false )
                                   ->from( array( 't1' => $tbName1 ), 
                                           array( 't1.id', 
                                                  't1.emailIsValid' ,
                                                  't1.realname'
                                                                        ))
                                   ->join( array( 't2' => $tbName2 ), 't1.id = t2.id' )
                                   ->joinLeft( array( 't3' => $tbName3 ), 't1.id = t3.userID', 
                                               array( 't3.disputeID' ))
                                   ->joinLeft( array( 't4' => $tbName4 ), 't3.disputeID = t4.id',
                                               array( 't4.status' ));

      $rows = $this->getDbTable()->fetchAll( $select );
      $res = array();
      foreach( $rows as $row )
         {
         $res[] = array(
                        'id' => $row->id,
                        'uni' => $row->realname,
                        'emailIsValid' => $row->emailIsValid,
                        'role' => $row->role,
                        'eiTest' => $row->eiTest,
                        'firstName' => $row->firstName,
                        'lastName' => $row->lastName,
                        'gender' => $row->gender,
                        'disputeID' => $row->disputeID,
                        'status' => $row->status
                        );
         }

      return $res;
      }     

   public function findActiveUserData( $userID = null )
      {
      $tbName1 = 'auth';
      $tbName2 = 'authUsers';
      $tbName3 = 'userToDispute';
      $tbName4 = 'disputes';
      $select = $this->getDbTable()->select( )
                                   ->setIntegrityCheck( false )
                                   ->from( array( 't1' => $tbName1 ),
                                           array( 't1.id',
                                                  't1.emailIsValid' ,
                                                  't1.realname'
                                                                        ))
                                   ->join( array( 't2' => $tbName2 ), 't1.id = t2.id' )
                                   ->joinLeft( array( 't3' => $tbName3 ), 't1.id = t3.userID',
                                               array( 't3.disputeID' ))
                                   ->joinLeft( array( 't4' => $tbName4 ), 't3.disputeID = t4.id',
                                               array( 't4.status' ))
                                   ->where( 't1.role != ?', 'baned' );
      if( $userID != null )
         {
         $select->where( 't1.id = ?', $userID );
         }

      $rows = $this->getDbTable()->fetchAll( $select );
      $res = array();
      foreach( $rows as $row )
         {
         $srAddapter = new Default_Model_SurveyResultsMapper(); 
    
         $survey_res = $srAddapter->hasTakenSurvey( $row->id );
         
         $res[] = array(
                        'id' => $row->id,
                        'uni' => $row->realname,
                        'emailIsValid' => $row->emailIsValid,
                        'role' => $row->role,
                        'eiTest' => $row->eiTest,
                        'firstName' => $row->firstName,
                        'lastName' => $row->lastName,
                        'gender' => $row->gender,
                        'disputeID' => $row->disputeID,
                        'takenSurvey' => $survey_res,
                        'status' => $row->status
                        );
         }

      return $res;
      }

   }

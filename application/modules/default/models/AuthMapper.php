<?php

class Default_Model_AuthMapper
   {
   protected $_dbTable;
       
   public function setDbTable($dbTable)
      {
      if( is_string($dbTable) ) 
         {
         $dbTable = new $dbTable();
         }

      if( !$dbTable instanceof Zend_Db_Table_Abstract ) 
         {
         throw new Exception('Invalid table data gateway provided');
         }

      $this->_dbTable = $dbTable;

      return $this;
      }
       
      public function getDbTable()
         {
          if( null === $this->_dbTable ) 
             {
             $this->setDbTable('Default_Model_DbTable_Auth');
             }

         return $this->_dbTable;
         }
       
      public function save( Default_Model_Auth $auth )
         {
         $data = array(
                       'username'              => $auth->getUsername(),
                       'realname'              => $auth->getRealname(),
                       'email'                 => $auth->getEmail(),
                       'role'                  => $auth->getRole(),
                       'emailIsValid'          => null,
                       'emailValidationCode'   => $auth->getEmailValidationCode( ),
                       'password'              => $auth->getPassword(),
                       'password_salt'         => $auth->getPassword_salt(),
                       'recoveryPassword'      => NULL, //$auth->getPassword(),
                       'recoveryPassword_salt' => NULL, //$auth->getPassword_salt()
                      );
       
//         if( null === ( $username = $auth->getUsername() ) ) 
         if( $this->find( $auth->getUsername(), $auth ) == null )
            {
            $data['id'] = $this->_getCreatID(  $auth->getRealname(), $auth->getUsername() );
            $id = $this->getDbTable()->insert($data);
            $auth->setId( $id );
            return $id;
            } 
         else 
            {
            return null; //User exists;
            }
         }
      protected function _getCreatID( $realname, $username )
         {
         $id = '';
         $rows = $this->getDbTable()->fetchALL();
         
         $id = (int)decoct(ord($realname)) . (int)decoct(ord($username)) . (string)count( $rows ) + 1;
         

         return $id;
         }
 
      public function validEmail( Default_Model_Auth $auth ) 
         {
         $auth->emailIsValid = true;
         $this->update( $auth );

         return $auth;
         }

      public function updateAccess( Default_Model_Auth $auth )
         {
         $auth->lastAccessed = time();
         $this->update( $auth );

         return $auth;
         }

      public function voidUser( Default_Model_Auth $auth )
         {
         $data = array('id'                  => $auth->getID(),
                       'username'            => $auth->getUsername(),
                       'realname'            => $auth->getRealname(),
                       'email'               => $auth->getEmail(),
                       'role'                => $auth->getRole(),
                       'emailIsValid'        => $auth->getEmailIsValid( ),
                       'emailValidationCode' => $auth->getEmailValidationCode( )
                       );

         if( null === ( $id = $auth->getId() ) )
            {
            return "user not exits";
            } 
         else
            {
            $this->getDbTable()->update($data, array('id = ?' => $id));
            }

         }

      public function update( Default_Model_Auth $auth )
         {
         $data = array(
                       'username'              => $auth->getUsername(),
                       'realname'              => $auth->getRealname(),
                       'email'                 => $auth->getEmail(),
                       'role'                  => $auth->getRole(),
                       'emailIsValid'          => $auth->getEmailIsValid(),
                       'emailValidationCode'   => $auth->getEmailValidationCode(),
                       'recoveryPassword'      => $auth->getRecoveryPassword(),
                       'recoveryPassword_salt' => $auth->getRecoveryPassword_salt()
                       );
         // only update the password if it's set
         $passwd = $auth->getPassword();
         $passsa = $auth->getPassword_salt();
         if( !empty( $passwd ) && !empty( $passsa ) )
            {
            $data['password']          = $passwd;
            $data['password_salt']     = $passsa;
            }
/* may yet need to implement updates this way
         $recPasswd = $auth->getRecoveryPassword();
         $recPasssa = $auth->getRecoveryPassword_salt();
         if( !empty( $recPasswd ) && !empty( $recPasssa ) )
            {
            $data['recoveryPassword']          = $recPasswd;
            $data['recoveryPassword_salt']     = $recPasssa;
            }
*/
         if( null === ( $username = $auth->getUsername() ) )
            {
            return "user not exits";
            } 
         else
            {
            $this->getDbTable()->update($data, array('username = ?' => $username));
            }
        
         }

         public function find( $id, Default_Model_auth $auth, $password = false )
            {

            $select= $this->getDbTable()->select()->where( 'id = ?', $id );
            $row = $this->getDbTable()->fetchRow($select);

            if( 0 == count($row) )
               {
               return null;
               }
            $auth->setId( $row->id )
                 ->setUsername( $row->username )
                 ->setRealname( $row->realname )
                 ->setEmail( $row->email )
                 ->setRole( $row->role )
                 ->setEmailIsValid( $row->emailIsValid )
                 ->setEmailValidationCode( $row->emailValidationCode )
                 ->setRecoveryPassword( null  )
                 ->setRecoveryPassword_salt( null )
                 ->setPassword( null  )
                 ->setPassword_salt( null );

            // dont return the password unless it's needed
            if( $password == true )
               {
               $auth->setPassword( $row->password )
                    ->setRecoveryPassword( $row->recoveryPassword )
                    ->setRecoveryPassword_salt( $row->recoveryPassword_salt )
                    ->setPassword_salt( $row->password_salt );
               }

            return $auth;
          }
         public function findByUsername( $username, Default_Model_auth $auth, $password = false )
            {

            $select= $this->getDbTable()->select()->where( 'username = ?', $username );
            $row = $this->getDbTable()->fetchRow($select);

            if( 0 == count($row) ) 
               {
               return null;
               }
            $auth->setId( $row->id )
                 ->setUsername( $row->username )
                 ->setRealname( $row->realname )
                 ->setEmail( $row->email )
                 ->setRole( $row->role )
                 ->setEmailIsValid( $row->emailIsValid )
                 ->setEmailValidationCode( $row->emailValidationCode )      
                 ->setRecoveryPassword( null  )
                 ->setRecoveryPassword_salt( null )
                 ->setPassword( null  )
                 ->setPassword_salt( null );

            // dont return the password unless it's needed
            if( $password == true )
               {
               $auth->setPassword( $row->password )
                    ->setRecoveryPassword( $row->recoveryPassword )
                    ->setRecoveryPassword_salt( $row->recoveryPassword_salt )
                    ->setPassword_salt( $row->password_salt );
               }

            return $auth;
          }

         public function findByEmail( $email, Default_Model_auth $auth )
            {

            $select= $this->getDbTable()->select()->where( 'email = ?', $email );
            $row = $this->getDbTable()->fetchRow($select);

            if( 0 == count($row) )
               {
               return null;
               }
            $auth->setId( $row->id )
                 ->setUsername( $row->username )
                 ->setRealname( $row->realname )
                 ->setEmail( $row->email )
                 ->setEmailIsValid( $row->emailIsValid )
                 ->setEmailValidationCode( $row->emailValidationCode )      
                 ->setRole( $row->role )
                 // this function is for looking up other users so there sould be 
                 // no reason to look for the password;
                 ->setPassword( null  )
                 ->setPassword_salt( null )
                 ->setRecoveryPassword( null  )
                 ->setRecoveryPassword_salt( null );
//                 ->setPassword( $row->password )
//                 ->setPassword_salt( $row->password_salt );


            return $auth;
          }
      
 
          public function fetchAll( $oderby = null )
             {
             $entries   = array();

             $resultSet = $this->getDbTable()->fetchAll();
             foreach ($resultSet as $row) 
               {
               $entry = new Default_Model_Auth();
               $entry->setId( $row->id )
                     ->setUsername( $row->username )
                     ->setRealname( $row->realname )
                     ->setRole( $row->role )
                     ->setEmail( $row->email )
                     ->setEmailIsValid( $row->emailIsValid )      
                     ->setEmailValidationCode( $row->emailValidationCode )      
                     ->setPassword( "" );
               $entries[] = $entry;
               }

             return $entries;
             }

        public function emailExists( $email )
           {
           $select = $this->getDbTable()->select()
                                        ->where( 'email = ?', $email );

           $row = $this->getDbTable()->fetchRow($select);

           if( 1 != count($row) )
              {
              return null;
              }

           // return the id if valid
           return $row->id;
           }


        public function validateEmail( $email, $code )
           {
           $select = $this->getDbTable()->select()
                                        ->where( 'emailValidationCode = ?', $code )
                                        ->where( 'email = ?', $email );

           $row = $this->getDbTable()->fetchRow($select);

           if( 1 != count($row) )
              {
              return null;
              }

           // return the id if valid
           return $row->id;
           }
   }

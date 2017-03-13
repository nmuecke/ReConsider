<?php

class Default_Form_Auth_UpdateAccount extends Zend_Form
   {

   public function init()
      {
      $this->setName("Update Account");
      $this->setMethod('post');
/*
      $this->addElement( 'text',
                         'real_name',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(0, 150)
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Name:',
                                )
                         );

      $this->addElement( 'text',
                         'email',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(0, 60)
                                                              ),
                                                        array( 'EmailAddress',
                                                                true
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Email:',
                                )
                         );

      $this->addElement( 'text',
                         'username',
                          array( 'filters'    => array( 'StringTrim',
                                                        'StringToLower'
                                                       ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(0, 50)
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Username:',
                                )
                         );

*/
      $this->addElement( 'password',
                         'oldPassword',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array( 0, 50 )
                                                              ),
                                                       ),
                                  'required'   => true,
                                  'label'      => 'Old Password:',
                                 )
                        );
      $this->addElement( 'password',
                         'newPassword',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array( 0, 50 )
                                                              ),
                                                       ),
                                  'required'   => true,
                                  'label'      => 'New Password:',
                                 )
                        );
      $this->addElement( 'password',
                         'newPassword2',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'Identical',  
                                                                false, 
                                                                array( 'token' => 'newPassword' )
                                                              ),
                                                       ),
                                  'required'   => true,
                                  'label'      => 'Retype New Password:',
                                 )
                        );
      $this->addElement( 'submit',
                         'updateAccount',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Update',
                                )
                        );
      }

   public function isValid( $data )
      {
      $res = true;
      $res = parent::isValid( $data );

      $auth = Zend_Auth::getInstance();

       if( !$auth->hasIdentity() )
          {
          $this->oldPassword->addError( "You must be logged in to change your password" );
          $res = false;
          }

      $authAddapter = new Default_Model_AuthMapper();
      $user         = new Default_Model_Auth();

      $user = $authAddapter->find( Zend_Auth::getInstance()->getIdentity()->id, $user, true );

      switch( SHA1( $data['oldPassword'] ) )
         {
         case NULL:
            $this->oldPassword->addError( "Invalid password given." );
            $res = false;
            break;
         case $user->getPassword_salt():
         case $user->getRecoveryPassword_salt():
            // password is valid
            break;
         default:
            $this->oldPassword->addError( "The password you entred does not match the current password" );
            $res = false;
         }      
/* 
      if( $user->getRecoveryPassword() == NULL || $user->getPassword_salt() != SHA1( $data['oldPassword'] ) ) 
         {
         if( $user->getRecoveryPassword_salt() == NULL || $user->getRecoveryPassword_salt() != SHA1( $data['oldPassword'] ) ) 
            {
            $this->oldPassword->addError( "The password you entred does not match the current password" );
            $res = false;
            }
         }
*/
      return $res;
      }
   }


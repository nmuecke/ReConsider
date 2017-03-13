<?php
// application/models/Auth.php

class Default_Model_Auth extends Extend_Db_AbstractTableModel
{
       
          protected $_role;
          protected $_email;
          protected $_emailIsValid;
          protected $_emailValidationCode;
          protected $_username;
          protected $_realname;
          protected $_password;
          protected $_password_salt;
          protected $_recoveryPassword;
          protected $_recoveryPassword_salt;
      
          public function setUsername( $name )
          {
              $this->_username = (string)$name;
              return $this;
          }
       
          public function getUsername()
          {
              return $this->_username;
          }
       
          public function setRealname( $name )
          {
              $this->_realname = (string)$name;
              return $this;
          }
       
          public function getRealname()
          {
              return $this->_realname;
          }

          public function newPassword( $password )
          {
              $this->setPassword_salt( sha1( (string)$password ) );
              //$this->setPassword( sha1( (string)$this->_username . (string)$this->_id . sha1( (string)$password ) ) );
              $this->setPassword( sha1( (string)$password . sha1( (string)$password ) ) );
              return $this;
          }
       
          public function setPassword( $password )
          {
              $this->_password = (string)$password;
              return $this;
          }
       
          public function getPassword()
          {
              return $this->_password;
          }
       
          public function setPassword_salt( $salt )
          {
              $this->_password_salt = (string)$salt;
              return $this;
          }
       
          public function getPassword_salt()
          {
              return $this->_password_salt;
          }

          public function newRecoveryPassword( $recoveryPassword )
          {
              $this->setRecoveryPassword_salt( sha1( (string)$recoveryPassword ) );
              //$this->setRecoveryPassword( sha1( (string)$this->_username . (string)$this->_id . sha1( (string)$recoveryPassword ) ) );
              $this->setRecoveryPassword( sha1( (string)$recoveryPassword . sha1( (string)$recoveryPassword ) ) );
              return $this;
          }

          public function setRecoveryPassword( $recoveryPassword )
          {
              $this->_recoveryPassword = (string)$recoveryPassword;
              return $this;
          }

          public function getRecoveryPassword()
          {
              return $this->_recoveryPassword;
          }

          public function setRecoveryPassword_salt( $salt )
          {
              $this->_recoveryPassword_salt = (string)$salt;
              return $this;
          }

          public function getRecoveryPassword_salt()
          {
              return $this->_recoveryPassword_salt;
          }
 
          public function setEmailValidationCode( $val )
          {
              $this->_emailValidationCode = (string)$val;
              return $this;
          }
       
          public function getEmailValidationCode()
          {
              return $this->_emailValidationCode;
          }

          public function isValidEmail()
          {
              if( $this->_emailIsValid != true )
                 return null;

              return true;
          }

          public function newEmailValidationCode( )
          {
              $this->setEmailValidationCode( substr( sha1( mt_rand() . $this->getUsername() ), 0 , 20  ) );
              return $this;
          }

          public function setEmailIsValid( $val )
          {
              $this->_emailIsValid = (bool)$val;
              return $this;
          }

          public function getEmailIsValid()
          {
              return $this->_emailIsValid;
          }

          public function setEmail( $email )
          {
              $this->_email = (string)$email;
              return $this;
          }

          public function getEmail()
          {
              return $this->_email;
          }

          public function setRole( $name )
          {
              $this->_role = (string)$name;
              return $this;
          }

          public function getRole()
          {
              return $this->_role;
          }
 
          public function setTermsOfUse( $accepted )
          {
              $this->_termsOfUse = (bool) $accepted;
              return $this;
          }

          public function getTermsOfUse()
          {
              return $this->_termsOfUse;
          }

      }


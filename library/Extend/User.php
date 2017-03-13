<?php
/**
 *
 * LICENSE
 *
 * @category   
 * @package    
 * @copyright  
 * @license    
 * @version    
 */


/**
 * @category   
 * @package    
 * @copyright  
 * @license    
 */
class Extend_User extends Zend_Auth
    {
    protected $_id;
    protected $_username;
    protected $_realname;
    protected $_role;
    protected $_email;
    protected $_emailIsValid;
    protected $_emailValidationCode;
    protected $_password;
    protected $_password_salt;

    /**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
    protected function __construct()
    {}


    /**
     * Returns an instance of Extention_User
     *
     * Singleton pattern implementation
     *
     * @return Extention_User  Provides a fluent interface
     */
    public static function getInstance()
       {
       if( null === self::$_instance ) 
          {
          // create instance
       //   self::$_instance = new self();
          $c = __CLASS__;
          self::$_instance = new $c;
          }

       return self::$_instance;
       }

    // makesuer that when the storage is updated so is the rest
    public function writeStorage( $data )
       {
       $this->getStorage()->write( $data );

       if( $this->getIdentity() != null && !isset( $this->_id ) )
          {
          $this->setId(                  $this->getIdentity()->id                  );
          $this->setUsername(            $this->getIdentity()->username            );
          $this->setRealname(            $this->getIdentity()->realname            );
          $this->setRole(                $this->getIdentity()->role                );
          $this->setEmail(               $this->getIdentity()->email               );
          $this->setEmailIsValid(        $this->getIdentity()->emailIsValid        );
          $this->setEmailValidationCode( $this->getIdentity()->emailValidationCode );
          $this->setPassword(            $this->getIdentity()->password            );
          $this->setPassword_salt(       $this->getIdentity()->password_salt       );


          }

       }
    
    public function toArray()
       {
       return array( 'id'                  => $this->getId(),
                     'username'            => $this->getUsername(),
                     'realname'            => $this->getRealname(),
                     'email'               => $this->getEmail(),
                     'emailIsValid'        => $this->getEmailIsValid(),
                     'emailValidationCode' => $this->getEmailValidationCode(),
                     'lastAccessed'        => $this->getLastAccessed(),
                     'password'            => $this->getPassword(),
                     'password_salt'       => $this->getPassword_salt()
                     );
       }
 

   private function _valuesNotSet( )
       {
       // if there is no set value then set one
       if( $this->_id == null && $this->getIdentity() != null )
          {
          $this->setId(                  $this->getIdentity()->id                  );
          $this->setUsername(            $this->getIdentity()->username            );
          $this->setRealname(            $this->getIdentity()->realname            );
          $this->setRole(                $this->getIdentity()->role                );
          $this->setEmail(               $this->getIdentity()->email               );
          $this->setEmailIsValid(        $this->getIdentity()->emailIsValid        );
          $this->setEmailValidationCode( $this->getIdentity()->emailValidationCode );
          $this->setPassword(            $this->getIdentity()->password            );
          $this->setPassword_salt(       $this->getIdentity()->password_salt       );

          }


       }
    public function setOptions( array $options )
       {
        $methods = get_class_methods( $this );
        foreach ( $options as $key => $value )
           {
           $method = 'set' . ucfirst( $key );
           if( in_array( $method, $methods ) ) 
              {
              $this->$method($value);
              }
           }
       return $this;
       }

    public function logAccess( $action = null )
       {
     
       }

    public function hasAccess( $action = null )
       {
       if( $this->hasIdentity() == false )
          {
          return false;
          }
       else
          {

          }
       return true;
       }

    public function setId( $id )
       {
       $this->_id = (int)$id;
       return $this;
       }

    public function getId()
       {
       $this->_valuesNotSet();
       return $this->_id;
       }

    public function setUsername( $name )
       {
       $this->_username = (string)$name;
       return $this;
       }

    public function getUsername()
       {
       $this->_valuesNotSet();
       return $this->_username;
       }

    public function setRealname( $name )
       {
       $this->_realname = (string)$name;
       return $this;
       }

    public function getRealname()
       {
       $this->_valuesNotSet();
       return $this->_realname;
       }

    public function setRole( $name )
       {
       $this->_role = (string)$name;
       return $this;
       }

    public function getRole()
       {
       $this->_valuesNotSet();
       return $this->_role;
       }

    public function newPassword( $password )
       {
       $this->setPassword_salt( sha1( (string)$password ) );
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
       $this->_valuesNotSet();
       return $this->_password;
       }

    public function setPassword_salt( $salt )
       {
       $this->_password_salt = (string)$salt;
       return $this;
       }

    public function getPassword_salt()
       {
       $this->_valuesNotSet();
       return $this->_password_salt;
       }

    public function setEmail( $email )
       {
       $this->_email = (string)$email;
       return $this;
       }

    public function getEmail()
       {
       $this->_valuesNotSet();
       return $this->_email;
       }

    public function setTermsOfUse( $accepted )
       {
       $this->_termsOfUse = (bool) $accepted;
       return $this;
       }

    public function getTermsOfUse()
       {
       $this->_valuesNotSet();
       return $this->_termsOfUse;
       }

    public function setLastAccessed( $timestamp )
       {
       return $this;
       }

    public function getLastAccessed()
       {
       }

    }

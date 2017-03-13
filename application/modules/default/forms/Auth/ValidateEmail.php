<?php

class Default_Form_Auth_ValidateEmail extends Zend_Form
   {
   public function init()
      {
      $this->setName("login");
      $this->setMethod('post');
             
      $this->addElement( 'text', 
                         'email', 
                          array( 'filters'    => array( 'StringTrim', 
                                                        'StringToLower'
                                                       ),
                                 'validators' => array( array( 'StringLength', 
                                                                false, 
                                                                array(0, 50)
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
                         'validationCode', 
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength', 
                                                                false, 
                                                                array( 20, 20 )
                                                              ),
                                                       ),
                                  'required'   => false,
                                  'label'      => 'Validation Code:',
                                 )
                        );


      $this->addElement( 'submit', 
                         'validate', 
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Validate',
                                )
                        ); 
      $this->addElement( 'submit',
                         'reSend',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Resend Validation Code',
                                )
                        );

      $this->setDecorators( array( 'FormElements',
                                    array( 'HtmlTag', 
                                            array('tag' => 'dl', 'class' => 'zend_form')
                                          ),
                                    array( 'Description', 
                                            array('placement' => 'prepend') 
                                          ),
                                   'Form'
                                  )
                            );
      }

   public function isValid( $data )   
      {

      $dbAdapter = new Default_Model_AuthMapper();

      if( parent::isValid( $data ) != true )
         {
         return false; 
         }

      // if were sending a new code the validation is different
      if( $this->reSend->isChecked() )
         {
         if( $dbAdapter->emailExists( $data['email'] ) != null )
            {
            return true;
            }
         $this->email->addError( "The email address you entred is not associated with a valid account" );
         return false;
         }

      $id = $dbAdapter->validateEmail( $data['email'], $data['validationCode'] );

      if( $id != null )
         {
         $user = new Default_Model_Auth();
         $user = $dbAdapter->find( $id, $user );
         if( $user != null )
            {
            $dbAdapter->validEmail( $user );
            return true;
            }
         }

      $this->email->addError( "Unable to validate email address! Please check that you entred the correct email" );
      $this->validationCode->addError( "Unavle to validate code! Please check that you entred the correct validation code." );
      return false;
      }
   }

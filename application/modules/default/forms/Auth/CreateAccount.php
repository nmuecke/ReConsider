<?php

class Default_Form_Auth_CreateAccount extends Zend_Form
   {

   public function init()
      {
      $this->setName("Create Account");
      $this->setMethod('post');

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

      $this->addElement( 'password',
                         'password',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array( 0, 50 )
                                                              ),
                                                       ),
                                  'required'   => true,
                                  'label'      => 'Password:',
                                 )
                        );
      $this->addElement( 'password',
                         'password2',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'Identical',  
                                                                false, 
                                                                array( 'token' => 'password' )
                                                              ),
                                                       ),
                                  'required'   => true,
                                  'label'      => 'Retype Password:',
                                 )
                        );
      $this->addElement( 'submit',
                         'createAccount',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Create Account',
                                )
                        );
      }


   }


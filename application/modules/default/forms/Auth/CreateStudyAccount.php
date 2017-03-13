<?php

class Default_Form_Auth_CreateStudyAccount extends Zend_Form
   {

   public function init()
      {
      $this->setName("Create Account");
      $this->setMethod('post');
/*
      $this->addElement( 'hidden',
                         'real_name'
                         );
*/
      $this->addElement( 'text',
                         'username',
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
                         'email',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'Identical',  
                                                                false, 
                                                                array( 'token' => 'username' )
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Retype Email:',
                                )
                         );

      $select = new Zend_Form_Element_Select( 'gender',
                                               array( 'required' => true,
                                                      'label'    => "Please indicate your ".
                                                                    "gender: ",
                                                     )
                                             );
      $options = array( ''   => 'Please select one...',
                        'F'  => 'Female',
                        'M'  => 'Male'
                       ) ;

      $select->addMultiOptions( $options );
      $select->setValue( array( '' ) );

      $this->addElement( $select );


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

      $select = new Zend_Form_Element_Select( 'real_name',
                                               array( 'required' => true,
                                                      'label'    => "Please select the ".
                                                                    "university you study at: ",
                                                     )
                                             );
      $options = array( ''   => 'Please select one...',
                        'deakin'    => 'Deakin University', 
                        'latrobe'   => 'La Trobe University', 
                        'monash'    => 'Monash University', 
                        'rmit'      => 'RMIT University', 
                        'ballarat'  => 'University of Ballarat', 
                        'melbourne' => 'University of Melbourne', 
                        'vu'        => 'Victoria University', 
                        'other'     => 'Other' 
                       ) ;

      $select->addMultiOptions( $options );
      $select->setValue( array( '' ) );

      $this->addElement( $select );

      $this->addElement( 'submit',
                         'createAccount',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Create Account',
                                )
                        );
      }



   public function isValid( $data )
      {
      $res = true;
      $res = parent::isValid( $data );

      $authAddapter = new Default_Model_AuthMapper();

      if( $authAddapter->findByEmail( $data['username'], new Default_Model_Auth() ) != null )
         {
         $this->username->addError( "Invalid email entered! Email already exists." ); 
         $res = false;
         }

      return $res;
      }


   }

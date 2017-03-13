<?php

class Default_Form_Auth_LostAccount extends Zend_Form
   {

   public function init()
      {
      $this->setName("LostAccount");
      $this->setMethod('post');

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

      $this->addElement( 'submit',
                         'send',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'send',
                                )
                        );
      }

   public function isValid( $data )
      {
      $res = true;
      $res = parent::isValid( $data );

      $authAddapter = new Default_Model_AuthMapper();
      $user         = new Default_Model_Auth();

      $user = $authAddapter->findByEmail( $data['email'], $user );
       
      if( $user == NULL )
         {
         $this->email->addError( "Please check that you have entred you email address correctly." );
         $res = false;
         }

      return $res;
      }
   }


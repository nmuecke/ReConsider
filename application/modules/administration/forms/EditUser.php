<?php

class Administration_Form_EditUser extends Zend_Form
   {
 
   public function init()
      {
      $this->setName("EditUser");
      $this->setMethod('post');

      $this->addElement( 'text',
                         'FirstName',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(2, 20)
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'First Name:',
                                )
                         );

      $this->addElement( 'text',
                         'LastName',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(2, 20)
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Last Name:',
                                )
                         );

      $this->addElement( 'text',
                         'Uni',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(2, 20)
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Uni:',
                                )
                         );

      $this->addElement( 'hidden',
                         'hid',
                          array( 'required'   => true  )
                         );

      $this->addElement( 'submit',
                         'change',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Change',
                                )
                        );

      $this->addElement( 'submit',
                         'back',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Back',
                                )
                        );
 
      }

   }

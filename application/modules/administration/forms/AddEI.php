<?php

class Administration_Form_AddEI extends Zend_Form
   {
 
   public function init()
      {
      $this->setName("AddEI");
      $this->setMethod('post');

      $this->addElement( 'text',
                         'eiid',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(4, 12)
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Test ID:',
                                )
                         );

      $this->addElement( 'text',
                         'password',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(4, 20)
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Password:',
                                )
                         );

      $this->addElement( 'submit',
                         'add',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Add',
                                )
                        );
  
      }

   }

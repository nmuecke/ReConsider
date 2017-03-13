<?php

class Default_Form_Dispute_Initiate extends Zend_Form
   {

   public function init()
      {
      $this->setName("InitiateDispute");
      $this->setMethod('post');

      $this->addElement( 'text',
                         'name',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(0, 150)
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Name of oponent:',
                                )
                         );

      $this->addElement( 'text',
                         'email',
                          array( 'filters'    => array( 'StringTrim' ),
                                 'validators' => array( array( 'StringLength',
                                                                false,
                                                                array(0, 60)
                                                              ),
                                                       ),
                                 'required'   => true,
                                 'label'      => 'Their Email:',
                                )
                         );


      $options = array(
                       'divorce' => 'Percentage Split following a divorce'
                       );

      $select = new Zend_Form_Element_Select( 'DisputeType',
                                               array( 'required' => true,
                                                      'label'    => "Please select the ".
                                                                    "type of dispute you ".
                                                                    "wish to initiate." 
                                                     ) 
                                             );
      $select->addMultiOptions( $options );
      $select->setValue( array( 'divorce' ) );

      $this->addElement( $select );


      $this->addElement( 'submit',
                         'procced',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Procced',
                                )
                        );

      }

   }


<?php

class Engine_Form_ExitDispute extends Zend_Form
   {
   public function init()
      {
      $this->setName("ExitDispute");
      $this->setMethod('post');

      $this->addElement( 'submit',
                         'exit',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Exit Dispute',
                                )
                        );

      $this->setElementDecorators( array( 'ViewHelper',
                                          array('Label', 
                                          array('tag' => null, 'style'=>'display:none' )),
                                          array( array('row' => 'HtmlTag'), 
                                                 array('tag' => null)
                                                 ),
                                          )
                                  );

      $this->setDecorators( array( 'FormElements',
                                    array( 'HtmlTag',
                                            array('tag' => 'div')
                                          ),
                                    array( 'Description',
                                            array('placement' => false)
                                          ),
                                   'Form'
                                  )
                            );

      }

   }

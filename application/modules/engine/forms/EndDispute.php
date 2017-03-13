<?php

class Engine_Form_EndDispute extends Zend_Form
   {
   public function init()
      {
      $this->setName("EndDispute");
      $this->setMethod('post');

      $decline = new Zend_Form_Element_Submit( 'decline' );
      $decline->setLabel( 'Yes, I wish to end the dispute.' );

      $accept = new Zend_Form_Element_Submit( 'accept' );
      $accept->setLabel( 'No, let me keep trying.' );

      $decline->setDecorators( array(
                                      'ViewHelper',
                                      'Description',
                                      'Errors',
                                      array(array('data'=>'HtmlTag'),array('tag'=>'dd', 'class'=>'center', 'openOnly'=>'true'))
                               ));

      $accept->setDecorators( array(
                                      'ViewHelper',
                                      'Description',
                                      'Errors',
                                      array(array('data'=>'HtmlTag'),array('tag'=>'dd', 'class'=>'center', 'closeOnly'=>'true'))
                               ));

      $this->addElement( $decline );
      $this->addElement( $accept );
 
      }

   }

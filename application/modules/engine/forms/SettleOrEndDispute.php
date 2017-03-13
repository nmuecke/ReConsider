<?php

class Engine_Form_SettleOrEndDispute extends Zend_Form
   {
   public function init()
      {
      $this->setName("SettleDispute");
      $this->setMethod('post');

      $decline = new Zend_Form_Element_Submit( 'decline' );
      $decline->setLabel( 'Decline' );

      $accept = new Zend_Form_Element_Submit( 'accept' );
      $accept->setLabel( 'Accept' );

      $end = new Zend_Form_Element_Submit( 'end_dispute' );
      $end->setLabel( 'I feel this dispute can not be resolved' );

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
      $this->addElement( $end );
 
      }

   }

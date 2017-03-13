<?php

class Default_Form_RevocationOfConsent_LeaveStudy extends Zend_Form
   {
   public function init()
      {
      $this->setName( 'RevocationOfConsent' );
      $this->setMethod('post');
             


      $el = new Zend_Form_Element_Checkbox( 'RevocationConsent' );

      $el->setLabel( "I wish to leal this study" );

      $el->setCheckedValue( 'I wish to leave' )
         ->setUnCheckedValue( null );


      $el->addDecorators( array( 'ViewHelper',
                                 'Errors',
                                 array('HtmlTag', array('tag' => 'span')),
                                 array('Label', array('tag' => 'span')),
                                )
                         );

      $el->getDecorator('Label')->setOption('placement', 'append');

      $el->setRequired( true )
         ->addErrorMessage( "You must check the box if you wish to proceed." );

      $this->addElement( $el );

      $next = new Zend_Form_Element_Submit( 'next' );
      $next->setLabel( 'Withdraw' );

      $back = new Zend_Form_Element_Submit( 'back' );
      $back->setLabel( 'Cancel' );

      $next->setDecorators( array(
                                      'ViewHelper',
                                      'Description',
                                      'Errors',
                                      array(array('data'=>'HtmlTag'),array('tag'=>'dd', 'openOnly'=>'true'))
                               ));

      $back->setDecorators( array(
                                      'ViewHelper',
                                      'Description',
                                      'Errors',
                                      array(array('data'=>'HtmlTag'),array('tag'=>'dd', 'closeOnly'=>'true'))
                               ));

      $this->addElement( $next );
      $this->addElement( $back );

      }


   }

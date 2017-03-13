<?php

class Default_Form_SignUp_AcceptCondition extends Zend_Form
   {
   protected $_formName;
   protected $_checkName;
   protected $_labelMessage;
   protected $_buttonMessage = 'Next';

   public function setFormName( $val )
      {
      $this->_formName = $val;
      }

   public function setCheckName( $val )
      {
      $this->_checkName = $val;
      }

   public function setLabelMessage( $val )
      {
      $this->_labelMessage = $val;
      $this->build();
      }

   public function setButtonMessage( $val )
      {
      $this->_buttonMessage = $val;
      $this->build();
      }


   public function init()
      {
      }

   public function build()
      {
      $this->setName( $this->_formName );
      $this->setMethod('post');
             


      $el = new Zend_Form_Element_Checkbox( $this->_checkName );

      $el->setLabel( $this->_labelMessage );

      $el->setCheckedValue( 'I Agree' )
         ->setUnCheckedValue( null );


      $el->addDecorators( array( 'ViewHelper',
                                 'Errors',
                                 array('HtmlTag', array('tag' => 'span')),
                                 array('Label', array('tag' => 'span')),
                                )
                         );

      $el->getDecorator('Label')->setOption('placement', 'append');

      $el->setRequired( true )
         ->addValidator( 'identical', false, 'I Agree'  )
         ->addErrorMessage( "You must agree to the terms if you with to proceed." );

      $this->addElement( $el );


      $next = new Zend_Form_Element_Submit( 'next' );
      $next->setLabel( $this->_buttonMessage );
                  
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

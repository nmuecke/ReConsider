<?php

class Default_Form_ButtonLink extends Zend_Form
   {
   private $_buttons;

   public function init()
      {
      }

   public function addButton( $name, $label, $value )
      {
      $button = new Zend_Form_Element_Button( $name,  array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => $label,
                                ));
      $button->setValue( 'value' );

      $this->addElement( $button );
      }

   public function bulidForm( $name )
      {
      $this->setMethod('post')->setAttrib('enctype', 'multipart/form-data');  
      $this->setName( $name );
 
      }

   }

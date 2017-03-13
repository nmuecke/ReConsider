<?php

class Administration_Form_MailUser extends Zend_Form
   {
 
   public function init()
      {
      $this->setName("MailUser");
      $this->setMethod('post');
      $this->setAttrib('enctype', 'multipart/form-data');

      $to = new Zend_Form_Element_Select( 'to' );
      $to->setlabel( "To: " )
         ->setRequired(true);

      $options = array( ''   => 'Please to a user...',
                       ) ;

      $to->addMultiOptions( $options );
      $to->setValue( array( '' ) );

      $this->addElement( $to );

      $subject = new Zend_Form_Element_Text( 'subject' );
      $subject->setlabel( "Subject: " )
              ->setAttrib( 'size', 61 )
              ->addFilter( 'StringTrim'  )
              ->addValidator( 'StringLength', false, array(1, 61 ) )
              ->setRequired(true);
           
      $this->addElement( $subject, 'subject' );


      $message = new Zend_Form_Element_Textarea( 'message' );
      $message->setLabel( 'Message:' )
               ->setRequired( true )
               ->setAttrib( 'COLS', 70 )
               ->setAttrib( 'ROWS', 15 );

      $this->addElement( $message, 'message' );

      $attachment = new Zend_Form_Element_File( 'doc_path' );
      $attachment->setLabel( 'Attach a file:' )
                 ->setDestination( Zend_Registry::get( 'config' )->app->uploadPath )
                 ->addValidator( 'Count', false, 1 )
                 ->addValidator( 'Size', false, 5242880 );

      //$attachment->addValidator( 'Extension', false, 'pdf' );

      $this->addElement( $attachment, 'doc_path' );

      $this->addElement( 'submit',
                         'send',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'send',
                                )
                        );
  
      }

   }

<?php

class Default_Form_Survey_Questions extends Zend_Form
   {

   public function init()
      {
      $this->setName("Survey_Questions");
      $this->setMethod('post');
     
      $questionsDB = new Default_Model_SurveyQuestionsMapper();
      foreach( $questionsDB->findAll() as $q )
         {
         switch( strtolower($q->getType()) )
            {
            case 'rank10':
               $element = $this->_createRank10( $q->getName(), 
                                                'Q' . $q->getOrder() . ': ' . $q->getLabel(),
                                                $q->getHighLabel(),
                                                $q->getLowLabel() 
                                                );
               break;

            case 'yesno':
               $element = $this->_createYesNo( $q->getName(),
                                               'Q' . $q->getOrder() . ': ' . $q->getLabel(),
                                               $q->getHighLabel(),
                                               $q->getLowLabel()
                                               );
               break;

            case 'longanswer':
               $element = $this->_createLongAnswer( $q->getName(),
                                                    'Q' . $q->getOrder() . ': ' . $q->getLabel(),
                                                    $q->getLowLabel(),
                                                    $q->getHighLabel()

                                                    );
               break;

            default:
               throw new Exception( "Error! invalid type of survey question." );
            }
         $element->setRequired( (bool)$q->getRequired() );
         $this->addElement( $element );
         } 

      $this->addElement( 'submit',
                         'login',
                          array( 'required' => false,
                                 'ignore'   => true,
                                 'label'    => 'Submit',
                                )
                        );
      }



   public function isValid( $data )
      {
      $res = true;
      $res = parent::isValid( $data );


      return $res;
      }

   protected function _createLongAnswer( $name, $label, $required = true, $COLS = '40', $ROWS = '5' )
      {
      $txt = new Zend_Form_Element_Textarea( $name );
      $txt->setLabel( $label )
          ->setRequired( $required )
          ->setAttrib( 'COLS', $COLS )
          ->setAttrib( 'ROWS', $ROWS );
      return $txt;
      }

   protected function _createRank10( $name, $label, $high = '(high)', $low = '(low)' )
      {
      $values = array('0 '. $low, 1,2,3,4,5,6,7,8,9, '10 ' . $high );

      return $this->_createRankX( $name, $label, $values, $high = '(high)', $low = '(low)' );
      }

   protected function _createYesNo( $name, $label, $high = 'Yes', $low = 'No' )
      {
      $values = array( true=>$high, false=>$low );

      return $this->_createRankX( $name, $label, $values, $high = '(high)', $low = '(low)' );
      }

   protected function _createRankX( $name, $label, array $values, $high = '(high)', $low = '(low)' )
      {
      $rank = new Zend_Form_Element_Radio( $name );
      $rank->setLabel( $label )
            ->addMultiOptions( $values )
            ->setSeparator( ' ' );

      return $rank;
      }
    
   }

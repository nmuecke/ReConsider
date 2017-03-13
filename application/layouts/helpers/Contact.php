<?php
class Layout_Helper_Contact extends Zend_View_Helper_Abstract 
   {
   /*
    * note use an '_' to indicate that no label should be used
    */
   public function Contact( $who, array $email   = array(), 
                                  array $phone   = array(), 
                                  array $address = array(), 
                                  array $other   = array() )
      {
      $html = "<dl class='contacts'>";

      $html .= "   <dt>" . $who . "</dt>";
     
      $html .= $this->_getItems( $email );
      $html .= $this->_getItems( $phone );
      $html .= $this->_getItems( $address );
      $html .= $this->_getItems( $other );

      $html .= "</dl>";
      return $html;
      }

   protected function _getItems( array $items )
      {
      $html = "";
      foreach( $items as $label=>$value )
         {
         $html .= "     <dd>";

         if( substr( $label, 0,1 ) != "_" )
            { 
            $html .= "<b>" . $label . "</b> ";
            }

         $html .= $value . "</dd>";
         }

      return $html;
      }
   }



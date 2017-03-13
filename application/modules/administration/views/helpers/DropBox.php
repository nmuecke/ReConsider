<?php
class Administration_View_Helper_DropBox extends Zend_View_Helper_Abstract
   {

   public function DropBox( array $options, $default = null, $name = null )
      {
      $html = "";
      $html .= "<select name='" . $name . "'>\n";

      foreach( $options as $key=>$option )
         {
         if( $key == $default )
            {
            $html .= "<option name     = '" . $key . "' "
                  .  "        value    = '" . $key . "' 
                  .           selected = true     >" 
                  .  $option 
                  .  "</option>\n";
            }
         else 
            { 
            $html .= "<option name  = '" . $key . "' "
                  .  "        value = '" . $key . "' >" 
                  .  $option 
                  .  "</option>\n";
            }
         }

      $html .= "</select>\n";
      return $html;
      }

   }

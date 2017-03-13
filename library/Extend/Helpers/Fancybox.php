<?php

class Extend_Helpers_Fancybox
   {
   protected $boxOptions  = array();
   protected $boxRegister = array();

   public function __construct( array $options = array() )
      {
      $this->boxOptions = $options;
      }
      

   public function createLink( array $properties, $url = '', $opentext = 'open' )
      {
      if( !isset( $properties['id'] ) )
         {
         throw new Exception( "unable to create fancybox, no id provided" );
         }
      // the link that will open the popup box
      $link = "";

      // open using a link
      $link = "<a href  = '" . $url . "#data_" . $properties['id'] . "' ";
      foreach( $properties as $tag=>$value )
         {
         $link .=  $tag . " = '" . $value . "' ";
         }
      $link .= "  > " . $opentext  . "</a>\n";
     
      // open using a button 
      // TODO

      $this->registerBox( $properties['id'] );

      return $link;
      } 

   public function createContent( $id, $content )
      {
      // the html to display when the link is pressed
      $html = "";

      $html = "<div style = 'display:none;'>\n"
            . "   <div id    = 'data_" . $id . "' "
            . "       > \n"
            .     $content . "\n"
            . "   </div>\n"
            . "</div>\n";

      return $html;
      }

   public function createBox( array $properties, $content, $url = '', $opentext = 'open' )
      {
      $html = self::createLink( $properties, $url, $opentext ) 
            . self::createContent( $properties['id'], $class, $content );

      return $html;
      }
   protected function registerBox( $id, array $options = array() )
      {
      $javascript  = ""
                   // link options and reg
                   . "      $('a#" . $id . "').fancybox( \n"
                   . "         { \n"
                   . " ";
      foreach( $this->boxOptions as $option=>$value )
         {
         $javascript .=  "        " 
                      . $this->quoted( $option ) 
                      . " : " 
                      . $this->quoted( $value ) 
                      . ", \n";
         }
         
      $javascript .= " "
                   . "         }); \n"
                   . " \n";
 
      $this->boxRegister[] = $javascript;
      }

   public function getJavaScript()
      {
      $javascript  = " "
                   . "   $(document).ready(function() \n"
                   . "      { \n"
                   . " ";
      foreach( $this->boxRegister as $box )
         {
         $javascript .= $box;
         }
                            // opener
      $javascript .= " "
                   . "       $('a.group').fancybox( \n"
                   . "          { \n"
                   . " \n";
/* these options seem to have no effect
      foreach( array() as $option=>$value )
         {
         $javascript .=  "          "
                      . $this->quoted( $option )
                      . " : "
                      . $this->quoted( $value )
                      . ", \n";
         }
*/
      $javascript .= " "
                   . "          }); \n"
                   . "       }); \n"
                   . " \n";

      return $javascript; 
      }

   /*
    * makes sure that any sring is quoted in single quots
    */
   protected function quoted( $value )
      {
      // make sure to that if quotes exist they are not included
      $value = str_replace( array( '"', "'" ), "", $value );
      if( is_numeric( $value ) )
         {
         return $value;
         }
      return "'" . $value . "'";
      } 
   }

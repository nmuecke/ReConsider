<?php
class Extend_Struct_View_Helper_HtmlTable extends Zend_View_Helper_Abstract
   {

   public function HtmlTable( Extend_Struct_Model_Table $table )
      {
      $html = "";
  
      $rows = $table->getRows();
      
      $html .= "<table>";
 
      foreach( $rows as $row )
        {
        $html .= "<tr>";

        $cols = $row->getRow();
        foreach( $cols as $cell )
           {
           $html .= "<td>";
           $html .= $cell;
           $html .= "</td>";
           }
        $html .= "</tr>";
        }

      $html .= "</table>";

      return $html;
      }

   }

<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Node_View_DivConclusion extends Engine_Node_View_DivAbstract
   {
   protected $_node;

    public function viewClaims()
      {
      return "";
      }

   public function viewNode()
      {
      return "<div class='conclusionNode' id='"   . self::getNode()->getId() . "'>"
           . self::viewTitle()        
           . "<div class='nodeBody'>"  
           . self::viewPrefix()
           . self::viewSuffix()
           . self::viewRelevance()
           . self::viewMoreInfo()
           . "</div>"
           . "</div>";
      }

   }

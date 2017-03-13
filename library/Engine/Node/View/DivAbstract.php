<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

abstract class  Engine_Node_View_DivAbstract extends Engine_Node_View_Abstract
   {
   protected $_autoSubmit;
   abstract public function useAltClaimIndicator();
  
   // {{{ public function autoSubmit( $value )
   public function autoSubmit( $value = null )
      {
      if( $value != null && ($value == true || $value == false ) )
         {
         $this->_autoSubmit = $value;
         } 
      return $this->_autoSubmit;
      }
   // }}}
   // {{{ public function viewPrefix()
   public function viewPrefix()
      {
      $html = "";

      $html .= "<div class = 'node-prefix'>" 
            .   $this->_node->getPrefix()
            .  "</div>\n";

      return $html;
      }

   // }}}
   // {{{ public function viewSuffix()
   public function viewSuffix()
      {
      $html = "";

      $html .= "<div class = 'node-suffix'>" 
            .   $this->_node->getSuffix() 
            .  "</div>\n";

      return $html;
      }

   // }}}
   // {{{ public function viewClaims()
   public function viewClaims()
      {
      $html = "";

      $html .= "<div class = 'node-claims'>" 
            .  "</div>\n";

      return $html;
      }

   // }}}
   // {{{ public function viewActiveClaim()
   public function viewActiveClaim()
      {
      $html = "";

      $html .= "<div class = 'node-activeClaim'>" 
            .  "</div>\n";

      return $html;
      }

   // }}}
   // {{{ public function viewRelevance()
   public function viewRelevance()
      {

      $html = "";
      $relevance = $this->_node->getRelevance();
     
      if( $relevance != null && str_replace( " ", "", $relevance ) != "")
         {
         $html .= "<div class = 'node-relevance'>" 
               .  "<h4 class = 'node-sub-title'>Relevance of issue to dispute</h4>"
               .  $relevance
               .  "</div>\n";
         }
      return $html;
      }

   // }}}
   // {{{ public function viewMoreInfo()
   public function viewMoreInfo()
      {
      $html = "";
      $moreInfo = $this->_node->getMoreInfo();
     
      if( $moreInfo != "" && $moreInfo != "NULL" )
         {
         $html .= "<div class = 'node-moreinfo'>" 
               .  "<h4 class = 'node-sub-title'>Additional information about the issue</h4>"
               .   $moreInfo
               .  "</div>\n";
         }
      return $html;
      }

   // }}}
   // {{{ public function viewTitle()
   public function viewTitle()
      {
      $html = "";

      $html .= "<div class = 'node-title-" . $this->_node->getStatus() . "'>" 
            .  "<a class='node-anchor' name='anchor_" . $this->getNode()->getID() . "'>"
            .  "</a>"
            .   $this->_node->getTitle() 
            .  "</div>\n";

      return $html;
      }

   // }}}
   // {{{ public function viewStatus()
   public function viewStatus()
      {
      $html = "";
      switch( $this->_node->getStatus() )
         {
         case "POTENTUAL_AGREED":
         case "AGREED":
            $status = "All parties involved in the dispute are in agreement on this issue.";
            break;

         case "DISAGREED":
            $status = "The parties involved in the dispute currently do not agree on this issue.";
            break;

         case "DISPUTED":
            $status = "A difference has been found in the claims made by those involved in the "
                    . "dispute over this issue. You are now required to investigate the sub-issues involved.";
            break;

         case "ASSERTED":
            $status = "You have asserted a claim for this issue, however, the other party is yet to do so.";
            break;

         case "PENDING_REVIEW":
            $status = "You need to review your claim for this issue and decide whether or not you "
                    . "wish to keep your current claim or change it to something else.";
            break;


         default:
            $status = "You are yet to assert a claim for this node.";
         }

      $html .= "<div class = 'node-status'>" 
            .  "<h4 class='node-sub-title'>Status of issue:</h4>"
            .   $status 
            .  "</div>\n";

      return $html;
      }

   // }}}
   // {{{ public function viewRecomendation()
   public function viewRecomendation()
      {
      $html = "";
      $changeClaim = "It is recomended that you consider changing your claim to: ";
      $noChange    = "It is recomended at this time, that you do not change you current claim";

      $recomendedClaim = $this->getNode()->getRecomendedClaim();      

      if( $recomendedClaim == null )
         { 
         return "";
         }
      else if( $recomendedClaim->getClaimID() == $this->getNode()->getActiveClaim() )
         {
         $recomendation = $noChange;
         }
      else
         {
         $recomendation = $changeClaim . "<div class='node-inference'>" . $recomendedClaim->getValue() . "</div>";
         } 

      $html .= "<div class = 'node-infered'>" 
            .  $recomendation
            .  "</div>\n";

      return $html;
      }

   // }}}
   // {{{ public function viewNode( $url )
   public function viewNode( $url, $buttonText = "Update Issue" )
      {
      $html = "";

      $html .= "<form method  = 'post' "
            .  "      action  = '" . $url . "' "
            .  "      enctype = 'application/x-www-form-urlencoded'>\n";
      $html .= "<div class = 'node' id='node-id" . $this->getNode()->getID() . "'>\n"
            .  $this->viewTitle()
            .  "<div class='node-body-" . $this->_node->getStatus() . "'>"
            .  "<div class = 'node-question'>" 
            .  $this->viewPrefix()
            .  $this->viewClaims()
            .  $this->viewSuffix()
            .  "</div>";

      $html .= $this->viewRecomendation();

      if( $this->autoSubmit() == true )
         {
         $html .= "<input type='hidden' name='nodeID' value='". $this->getNode()->getId() . "' />\n";
         }
      else
         {
         $html .= "<button type  = 'submit' "
               .  "        class = 'node-submit' "
               .  "        name  = 'nodeID' "
               .  "        value = '" . $this->getNode()->getID() . "'";
         if( $this->getNode()->getStatus() == "DISPUTED" )
            {  
            $html .= " disabled='disabled' "
                  .  "alt='This function has been disable until all sub-issues have been examined' ";
   
            }
         $html .=  ">"
               .  $buttonText
               .  "</button>\n";
         }
      $html .= $this->viewStatus();

      $html .= $this->viewMoreInfo()
            .  $this->viewRelevance()
            .  "</div>\n"
            .  "</div>\n";     
      $html .= "</form>\n";

      return $html;
      }

   // }}}
   }

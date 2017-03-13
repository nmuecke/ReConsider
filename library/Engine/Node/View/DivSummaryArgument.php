<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Node_View_DivSummaryArgument extends Engine_Node_View_DivAbstract
   {
    public function viewActiveClaim()
      {
      $html = '';
      $claim = $this->getNode()->getClaim( $this->getNode()->getActiveClaim() );
      if( $this->getNode()->getStatus() != "AGREED" )
         {
         if( ( $recomended = $this->getNode()->getRecomendedClaim() ) != null )
            {
            $claim = $recomended;
            }
         }

      if( $claim != null )
         {
         $html .= "<div class = 'node-activeClaim'>"
               .  $claim->getValue()
               .  "</div>\n";
                 
         }
      return $html;
      }

    public function viewClaims()
      {
      $html = '';
        
      //$html .= "<select name     = 'SELECT_" . $this->getNode()->getId() . "' "
      $html .= "<div class = 'node-claims' > ";

      $claim = $this->getNode()->getClaim( $this->getNode()->getActiveClaim() ); 

      if( $claim != null )
         {
         $html .= "<div class = 'node-claimSummary'> "
               .  $claim->getValue()
               .  "</div>";
         }
      else
         {
         $html .= "<b> No active claim found! </b>";
         }        
      $html .= "</div>\n";

      return $html;
      }

   public function useAltClaimIndicator()
      {
      if( $this->getNode()->getStatus() != "AGREED" )
         {
         $claim = $this->getNode()->getRecomendedClaim();
         if( $claim == null )
            {
            return true; 
            }
         } 
      return false;
      }

   public function viewRecomendation()
      {
      $html = "";
      $yourClaim = "As you were unable to reach an agreement on this issue, instead of using the claim "
                 . "you asserted, the system used both your claim and that of the other party to infere "
                 . "a claim of ";

      if( $this->getNode()->getStatus() != "AGREED" )
         {
         $html .= "<div class = 'node-infered'>";
         $claim = $this->getNode()->getRecomendedClaim();
         if( $claim != null )
            {
            if( $claim->getClaimID() != $this->getNode()->getActiveClaim() ) 
               {
               $html .= $yourClaim;
               $html .= "<div class = 'node-claimSummary'> "
                  .  $claim->getValue()
                  .  "</div>";
               }
            }     
         $html .= "</div>\n";
         }

      return $html;
      }

   }

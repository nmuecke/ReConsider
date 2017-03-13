<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Node_View_DivArgument extends Engine_Node_View_DivAbstract
   {
    public function viewActiveClaim()
      {
      $html = '';
      $claim = $this->getNode()->getClaim( $this->getNode()->getActiveClaim() );
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

      $html .= "<select name = 'claimID' ";
      if( $this->autoSubmit() == true )
         {
         $html .=  " onChange = 'javascript:this.form.submit(); ' ";
         }
      if( $this->getNode()->getStatus() == "DISPUTED" )
         {
         $html .= " disabled='disabled' "
               .  "alt='This function has been disable until all sub-issues have been examined' ";
         }
     $html .= " class = 'nodeOptions' > \n";

      $options = "";
      $hasActiveClaim = false;
      foreach( $this->getNode()->getClaims() as $key => $claim ) 
         {

         if( $this->getNode()->getActiveClaim() == $claim->getClaimId() ) //key )
            {
            $options .= "   <option value='" . $claim->getClaimId() . "' selected='true'>" 
                     .  $claim->getValue() 
//                   .  " - user infer: " . $claim->getUserInference() 
//                   .  " - sys infer: " . $claim->getSysInference() 
                     .  "</option>\n";
            $hasActiveClaim = true;
            }
         else
            {
            $options .= "   <option value='" . $claim->getClaimId() . "' >"
                     .   $claim->getValue()
//                   .  " - user infer: " . $claim->getUserInference() 
//                   .  " - sys infer: " . $claim->getSysInference() 
                     .  "</option>\n";

            }              
         }
      if( $hasActiveClaim == false )
         {
         $options = "   <option value='" . NULL . "'"
                  . "           selected='true'"
                  . "           disabled='true'"
                  . "   >"
                  . "No claim selected yet."
                  . "</option>\n"
                  . $options;

         }    
       
      $html .= $options;
      $html .= "</select>\n"; 
      $html .= "</div>\n";

      return $html;
      }

   public function useAltClaimIndicator()
      {
      return false;
      }
   }

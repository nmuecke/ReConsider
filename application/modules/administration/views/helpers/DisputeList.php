<?php
class Administration_View_Helper_DisputeList extends Zend_View_Helper_Abstract
   {

   public function DisputeList( array $disputes )
      {
      $html = "";
      foreach( $disputes as $dispute )
         {
         $html .= "<dt>";
         $html .= "  <p class='disputeID'>";
         $html .= "    <b>Dispute ID:</b>                     " . $dispute['disputeID']; 
         $html .= "  </p>";
         $html .= "  <p class='type'>";
         $html .= "    <b>Type:</b>                           " . $dispute['type']; 
         $html .= "  </p>";
         $html .= "  <p class='status'>";
         $html .= "    <b>Status:</b>                         " . $dispute['status']; 
         $html .= "  </p>";
         $html .= "  <p class='numRejects'>";
         $html .= "    <b>Number of Rejected Resolutions:</b> " . $dispute['numRejections']; 
         $html .= "  </p>";
         $html .= "  <p class='view'>";
         $html .= "    <a href='' class='button'>View</a>";
         $html .= "  </p>";

         foreach( $dispute['users'] as $user )
            {
            $html .= "<dd>";
            $html .= "  <p class='userID'>";
            $html .= "    <b>User ID:</b> " . $user['userID'];  
            $html .= "  </p>";
            $html .= "  <p class='role'>";
            $html .= "    <b>Role:</b>    " . $user['role']; 
            $html .= "  </p>";
            $html .= "  <p class='group'>";
            $html .= "    <b>Group:</b>   " . $user['group']; 
            $html .= "  </p>";
            $html .= "  <p class='gender'>";
            $html .= "    <b>Gender:</b>  " . $user['gender'];
            $html .= "  </p>";
            $html .= "</dd>";
            }
         
         $html .= "</dt>";
         }

      return $html;
      }

   }

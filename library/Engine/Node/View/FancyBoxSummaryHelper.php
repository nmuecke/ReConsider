<?php

class Engine_Node_View_FancyBoxSummaryHelper extends Engine_Node_View_FancyBoxHelper
   {
   protected $_viewTypePrefix;
   protected $_viewRootnode;
   protected $_url;
   protected $_fancybox;
   protected $_claimIndicatorAlt;
     
   public function __construct( $viewTypePrefix, $url, $fancyboxOption = array(), $viewRootnode = false )
      {
      $this->_viewTypePrefix    = $viewTypePrefix;
      $this->_url               = $url;
      $this->_viewRootnode      = $viewRootnode;
      $this->_fancybox          = new Extend_Helpers_Fancybox( $fancyboxOption );
      $this->_buttonLabel       = "View";
      $this->_claimIndicator    = "The outcome for this issue was found to be: ";
      $this->_claimIndicatorAlt = "Your response to this issue is: ";
      }


   public function getMinimalListView( Engine_Node_View_DivAbstract $node, $openNode )
      {
      $html = "";

      $claimIndicator = $this->_claimIndicator;
      if( $node->useAltClaimIndicator() )
         {
         $claimIndicator = $this->_claimIndicatorAlt;
         }

      $html .= "<div class = 'node' >\n"
            .  $node->viewTitle() 
            .  "<div class = 'node-list'>"
            .  " " 
            .  $openNode 
            .  " " 
            .  $claimIndicator
            .  " " 
            .  $node->viewActiveClaim()  
            .  "</div>"
            .  "</div>\n";

      return $html;
      }

   public function setDefaultClaimIndicatorAlt( $val )
      {
      $this->_claimIndicatorAlt = $val;
      return $this;
      }
   }


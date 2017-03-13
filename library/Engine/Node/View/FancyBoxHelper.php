<?php

class Engine_Node_View_FancyBoxHelper extends Engine_Node_View_Helper

   {
   protected $_viewTypePrefix;
   protected $_viewRootnode;
   protected $_url;
   protected $_fancybox;
   protected $_buttonLabel;
   protected $_claimIndicator;
     
   public function __construct( $viewTypePrefix, $url, $fancyboxOption = array(), $viewRootnode = false )
      {
      $this->_viewTypePrefix = $viewTypePrefix;
      $this->_url            = $url;
      $this->_viewRootnode   = $viewRootnode;
      $this->_fancybox = new Extend_Helpers_Fancybox( $fancyboxOption );
      $this->_buttonLabel = "View/Change claim";
      $this->_claimIndicator = "Your response to this issue is: ";
      }


   public function viewNode( Engine_Node_Model_Abstract $node )
      {
      #todo conclusion nodes if( $node )

      $html = "";

      $class = $this->makeClass( "Argument" );      

	
      $nodeView = new $class( $node );
      //$nodeView->autoSubmit(true);

      $button_label  = $this->_buttonLabel;
      $update_button = 'Update Issue';
      if( $node->getStatus() == 'DISPUTED' )
         {
         $button_label  = "View";
         $update_button = "Exit";
         }
      else if( $node->getStatus() == 'PENDING_REVIEW' )
         {
         $button_label  = "Review Issue";
         $update_button = "Accept/Update";
         }
   
      //$html .= $this->getListView( $nodeView, $this->_fancybox->createLink( array( 'id'=>$node->getID() ), "", "View" ) );
      $html .= $this->getMinimalListView( $nodeView, 
                                          $this->_fancybox->createLink( array( 'id'    => $node->getID(), 
                                                                              'class' => 'a-button' ), 
                                                                       "", 
                                                                       $button_label ) 
                                         );
      $html .= $this->_fancybox->createContent( $node->getID(), $nodeView->viewNode( $this->_url, $update_button ) );

      return $html;
      }

   public function getListView( Engine_Node_View_Abstract $node, $openNode )
      {
      $html = "";       

      $html .= "<div class = 'node' >\n"
            .  "<h3>" . $node->viewTitle() . "</h3>\n"
            .  "<div class = 'node-list'>Status: " . $node->viewStatus() . "</div>\n"
            .  "<div class = 'node-list'>Claim Asserted: " . $node->viewActiveClaim() . "</div>\n"  
            .  "<div class = 'node-list'>" . $openNode . "</div>\n"
            .  "</div>\n";
 
      return $html;
      }

   public function getMinimalListView( Engine_Node_View_Abstract $node, $openNode )
      {
      $html = "";

      $html .= "<div class = 'node' >\n"
            .  $node->viewTitle() 
            .  "<div class = 'node-list'>"
            .  " " 
            .  $openNode 
            .  " " 
            .  $this->_claimIndicator
            .  " " 
            .  $node->viewActiveClaim()  
            .  "</div>"
            .  "</div>\n";

      return $html;
      }

   public function viewSubNodes( Engine_Node_Model_Abstract $node )
      {
      $html = "<div class='node-subNode' >";

      $subNodes = $node->getSubNodes( );
      if( $subNodes == null )
         {
         return null;
         }

      foreach( $subNodes as $key => $subNode )
         {
         $html .= $this->viewNode( $subNode );
         $html .= $this->viewSubNodes( $subNode );
         }
      $html .= "</div>";
 
      return $html;
      }

   public function getJaveScript()
      {
      return $this->_fancybox->getJavaScript();
      }

   public function setDefaultButtonLabel( $val )
      {
      $this->_buttonLabel = $val;
      return $this;
      }

   public function setDefaultClaimIndicator( $val )
      {
      $this->_claimIndicator = $val;
      return $this;
      }
   }


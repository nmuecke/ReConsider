<?php

class Engine_Node_View_Helper
   {
   protected $_viewTypePrefix;
   protected $_viewRootnode;
   protected $_url;
     
   public function __construct( $viewTypePrefix, $url, $fancyboxOption = array(), $viewRootnode = false )
      {
      $this->_viewTypePrefix = $viewTypePrefix;
      $this->_url            = $url;
      $this->_viewRootnode   = $viewRootnode;
      }

   public function setViewRootnode( $val )
      {
      $this->_viewRootnode = $val;
      return $this; 
      }
   
   public function getViewRootnode()
      {
      return $this->_viewRootnode;
      }

   #assemble the class name from the prefix and suffix
   protected function makeClass( $typeSuffix )
      {
      return $this->_viewTypePrefix . $typeSuffix;
      }

   public function viewNode( Engine_Node_Model_Abstract $node )
      {
      #todo conclusion nodes if( $node )

      $html = "";

      $class = $this->makeClass( "Argument" );      
	
      $nodeView = new $class( $node );

      $html .= $nodeView->viewNode( $this->_url );

      return $html;
      }

   public function viewNodeTree( Engine_Node_Model_Root $rootnode ) 
      {
      $html = "";
      
      if( $this->getViewRootnode() == true )
         { 
         $html .= $this->viewNode( $rootnode );
         }

      $html .= $this->viewSubNodes( $rootnode );
      
      return $html;
      } 

   public function viewSubNodes( Engine_Node_Model_Abstract $node )
      {
      $html = "<div class='subLevel' >";

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
   }


<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

abstract class Engine_Node_View_Abstract
   {
   protected $_node;

   // {{{ public __construct( $node)
   public function __construct( Engine_Node_Model_Abstract $node )
      {
      self::setNode( $node );
      }

   // }}}
   // {{{ protected setNode( $node )
   protected function setNode( Engine_Node_Model_Abstract $node )
      {
      $this->_node = $node;
      return $this;
      }

   // }}}
   // {{{ protected getNode()
   protected function getNode()
      {
      return $this->_node;
      }

   // }}}
   // {{{ public function viewPrefix()
   abstract public function viewPrefix();

   // }}}
   // {{{ public function viewSuffix()
   abstract public function viewSuffix();

   // }}}
   // {{{ public function viewClaims()
   abstract public function viewClaims();

   // }}}
   // {{{ public function viewRelevance()
   abstract public function viewRelevance();

   // }}}
   // {{{ public function viewMoreInfo()
   abstract public function viewMoreInfo();

   // }}}
   // {{{ public function viewTitle()
   abstract public function viewTitle();

   // }}}
   // {{{ public function viewNode( $url )
   abstract public function viewNode( $url );

   // }}}

   }

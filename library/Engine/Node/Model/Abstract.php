<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

abstract class Engine_Node_Model_Abstract
   {
   protected $_id;
   protected $_nodeID;
   protected $_parentNodeID;
   protected $_counterNodeID;
   protected $_title;
   protected $_prefix;
   protected $_suffix;
   protected $_relevance;
   protected $_moreInfo;
   protected $_action;
   protected $_status;
   protected $_claims;
   protected $_viewable;

   // {{{ public __construct( $id = null, $nodeID = null, array $values = null, array $claims = null )
   public function __construct( $id = null, $nodeID = null, array $values = null, array $claims = null )
      {
      self::setId( $id );
      self::setNodeID( $nodeID );

      if( $values != null )
         {
         self::_loadValues( $values );
         }

      if( $claims != null )
         {
         self::addClaims( $claims );
         }

      }

   // }}}
   // {{{ protected  _loadValues( array $values )
   protected function _loadValues( array $values )
      {
      foreach( $values as $key => $value )
         {
         $method = 'set' . $key;
         if( method_exists( $this, self::$method ) )
            {     
            self::$method( $value );
            }
         else
            {
            throw new Exception( "invalid option passed to node. " . 
                                  Zend_Debug::Dump( $values ) );
            }   
         }
      return $this;
      }


   // }}}
   // {{{ public setID( $value )
   public function setID( $value )
      {
      $this->_id = $value;
      return $this;
      }

   // }}}
   // {{{ public getID()
   public function getID()
      {
      return $this->_id;
      }

   // }}}
   // {{{ public setNodeID( $value )
   public function setNodeID( $value )
      {
      $this->_nodeID = $value;
      return $this;
      }

   // }}}
   // {{{ public getNodeID()
   public function getNodeID()
      {
      return $this->_nodeID;
      }

   // }}}
   // {{{ public setParentNodeID( $value )
   public function setParentNodeID( $value )
      {
      $this->_parentNodeID = $value;
      return $this;
      }

   // }}}
   // {{{ public getParentNodeID()
   public function getParentNodeID()
      {
      return $this->_parentNodeID;
      }

   // }}}
   // {{{ public setCounterNodeID( $value )
   public function setCounterNodeID( $value )
      {
      $this->_counterNodeID = $value;
      return $this;
      }

   // }}}
   // {{{ public getCounterNodeID()
   public function getCounterNodeID()
      {
      return $this->_counterNodeID;
      }

   // }}}
   // {{{ public setTitle( $value )
   public function setTitle( $value )
      {
      $this->_title = $value;
      return $this;
      }

   // }}}
   // {{{ public getTitle()
   public function getTitle()
      {
      return $this->_title;
      }

   // }}}
   // {{{ public setPrefix( $value )
   public function setPrefix( $value )
      {
      $this->_prefix = $value;
      return $this;
      }

   // }}}
   // {{{ public getPrefix()
   public function getPrefix()
      {
      return $this->_prefix;
      }

   // }}}
   // {{{ public setSuffix( $value )
   public function setSuffix( $value )
      {
      $this->_suffix = $value;
      return $this;
      }

   // }}}
   // {{{ public getSuffix()
   public function getSuffix()
      {
      return $this->_suffix;
      }

   // }}}
   // {{{ public setRelevance( $value 
   public function setRelevance( $value )
      {
      $this->_relevance = $value;
      return $this;
      }

   // }}}
   // {{{ public getRelevance()
   public function getRelevance()
      {
      return $this->_relevance;
      }

   // }}}
   // {{{ public setMoreInfo( $value )
   public function setMoreInfo( $value )
      {
      $this->_moreInfo = $value;
      return $this;
      }

   // }}}
   // {{{ public getMoreInfo()
   public function getMoreInfo()
      {
      return $this->_moreInfo;
      }

   // }}}
   // {{{ public setAction( $value )
   public function setAction( $value )
      {
      $this->_action = $value;
      return $this;
      }

   // }}}
   // {{{ public getAction()
   public function getAction()
      {
      return $this->_action;
      }

   // }}}
   // {{{ public setViewable( $value )
   public function setViewable( $value )
      {
      $this->_viewable = (bool)$value;
      return $this;
      }

   // }}}
   // {{{ public getViewable()
   public function getViewable()
      {
      return $this->_viewable;
      }

   // }}}
   // {{{ public setStatus( $value )
   public function setStatus( $value )
      {
      $this->_status = $value;
      return $this;
      }

   // }}}
   // {{{ public getStatus()
   public function getStatus()
      {
      return $this->_status;
      }

   // }}}
   // {{{ public addClaim( $claim )
   public function addClaim( $claim )
      {
      if(  is_object( $claim ) 
      && ( get_class( $claim ) == 'Engine_Node_Model_Claim' 
        || is_subclass_of( $claim, 'Engine_Node_Model_Claim' ) ) )
         {
         //$this->_claims[(int)$claim->getClaimID()] = $claim;
         $this->_claims[(int)$claim->getClaimID()] = $claim;
         }
      else
         {
         throw new Exception( "Unable to add non-claim object to node (id: ".
                               self::getID()." )!\n " .Zend_Debug::Dump( $claim ) );
         }
      return $this;
      }

   // }}}
   // {{{ public addClaims( array $claims )
   public function addClaims( array $claims )
      {
      foreach( $claims as $claim )
         {
         self::addClaim( $claim );
         }

      return $this;
      }

   // }}}
   // {{{ public findClaim( $id )
   public function findClaim( $id )
      {
      if( isset( $this->_claims[$id] ) )
         {
         return $this->_claims[$id];
         }
      return null;
      }

   // }}}
   // {{{ public getClaims( )
   public function getClaims( )
      {
      return $this->_claims;
      }

   // }}}
   // {{{ public getClaim( )
   public function getClaim( $id )
      {
      if( !is_null($id) && isset( $this->_claims[$id] ) )
         {
         return $this->_claims[$id];
         }
      return null;
      }

   // }}}

   }

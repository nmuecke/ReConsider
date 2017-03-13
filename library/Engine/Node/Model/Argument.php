<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Node_Model_Argument extends Engine_Node_Model_Abstract
   {
   protected $_subNodes;
   protected $_activeClaimID;

   // {{{ public __construct( $id = null, $nodeID = null, array $values = null, array $claims = null, array $subNodes = null )
   public function __construct( $id = null, $nodeID = null, array $values = null, array $claims = null, array $subNodes = null  )
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

      self::setActiveClaim( null );
      }

   // }}}
   // {{{ public addSubNode( $subNode )
   public function addSubNode( $subNode )
      {
      if( is_object( $subNode ) 
       && is_subclass_of( $subNode, 'Engine_Node_Model_Abstract' ) )
         {
         $this->_subNodes[$subNode->getID()] = $subNode;
         }
      else
         {
         throw new Exception( "Unable to add non-Node object to Node (id: ".
                               self::getID()." )!\n " .Zend_Debug::Dump( $subNode ) );
         }
      return $this;
      }

   // }}}
   // {{{ public getSubNode( $id )
   public function getSubNode( $id )
      {
      if( isset( $this->_subNodes[$id] ) )
         {
         return $this->_subNodes[$id];
         }
      return null;
      }

   // }}}
   // {{{ public getSubNodes( )
   public function getSubNodes( )
      {
      if( !is_array( $this->_subNodes ) ) 
         {
         return null;
         }

      return $this->_subNodes;
      }

   // }}}
   // {{{ public setActiveClaim( $claimID )
   public function setActiveClaim( $claimID )
      {
      if( isset( $this->_claims[$claimID] ) || $claimID == null)
         {
         $this->_activeClaimID = $claimID;
         return $this;
         }
      
      throw new Exception( "Invalad claim id provided: Node: " . $this->getNodeId() ." ClaimID: " . $claimID. "<br /> " . Zend_Debug::Dump($this, false ) );
      }

   // }}}
   // {{{ public getActiveClaim( )
   public function getActiveClaim( )
      {
      return $this->_activeClaimID;
      }

   // }}}
   // {{{ public getRecomendedClaim()
   public function getRecomendedClaim()
      {
      $sysClaim = new Engine_Node_Model_Claim( -99 );
      $sysClaim->setUserInference( -99 )
               ->setSysInference( -99 )
               ->setValue( -99 );
      $userClaim = clone $sysClaim;

      foreach( $this->getClaims() as $claim )
         {
         // check for and compare sys inferences
         if( $sysClaim                    != null 
          && $claim->getSysInference() != null )
             {
             if( $sysClaim->getSysInference() < $claim->getSysInference() )
               {
               $sysClaim = clone $claim;
               }
            }
         // check for and compare user inference
         else if( $claim->getUserInference() != null && $claim->getUserInference() > -99 )
            {
            if( $userClaim->getUserInference() < $claim->getUserInference() )
               {
               $sysClaim = null; 
               $userClaim = clone $claim;
               }
            }
         // no inferences exist
         else
            {
            return null;
            }
         } 
      if( $sysClaim != null )
         { 
         return $sysClaim;
         }
      return $userClaim;
      }

   // }}}
   }


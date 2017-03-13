<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Node_Assembly
   {
   private $_nodesAdapter;
   private $_claimsAdapter;
   private $_claimStateAdapter;
   private $_userID;
   private $_disputeID;

   // {{{ public __construct()
   public function __construct( $disputeID, $userID )
      {
      $this->_userID    = $userID;
      $this->_disputeID = $disputeID;
      }

   // }}}
   // {{{ final public getUserID( )
   final public function getUserID()
      {   
      return $this->_userID;
      }

   // }}}
   // {{{ final public getDispueID( )
   final public function getDisputeID()
      {   
      return $this->_disputeID;
      }

   // }}}
   // {{{ protected getNodesAdapter( )
   protected function getNodesAdapter( )
      {   
      if( null === $this->_nodesAdapter )
         {   
         $this->_nodesAdapter = new Engine_Node_Model_NodeMapper();
         }
      return $this->_nodesAdapter;
      }

   // }}}
   // {{{ protected getClaimsAdapter( )
   protected function getClaimsAdapter( )
      {   
      if( null === $this->_claimsAdapter )
         {   
         $this->_claimsAdapter = new Engine_Node_Model_ClaimMapper();
         }
      return $this->_claimsAdapter;
      }

   // }}}
   // {{{ protected getClaimStateAdapter( )
   protected function getClaimStateAdapter( )
      {   
      if( null === $this->_claimStateAdapter )
         {   
         $this->_claimStateAdapter = new Engine_Models_ClaimStateMapper();
         }

      return $this->_claimStateAdapter;
      }

   // }}}
   // {{{ protected addClaimsTo( Engine_Node_Model_Abstract $node )
   protected function addClaimsTo( Engine_Node_Model_Abstract $node ) 
      {
      $claims = $this->getClaimsAdapter()->findNodesClaims( $node->getNodeID() );
      
      $node->addClaims( $claims );

      return $node;
      }

   // }}}
   // {{{ protected setActiveClaimFor( Engine_Node_Model_Argument $node,  )
   protected function setActiveClaimFor( Engine_Node_Model_Argument $node, Engine_Models_ClaimState $claim = null )
      {
      //test if a claim needs to be found 
      if( $claim == null )
         {
         $claim = $this->getClaimStateAdapter()->findActiveClaimFor( $this->getDisputeID(), $this->getUserID(), $node->getID() );
         }
 
      if( $claim != null )
         {
         $node->setActiveClaim( $claim->getClaimID() );
         $node->setStatus( $claim->getStatus() );
         }
/*
      else
         {
         $unselectedClaim = new Engine_Node_Model_Claim();
         $unselectedClaim->setID( -1 )
                         ->setNodeID( null ) //$node->getNodeID )
                         ->setClaimID( -1 )
                         ->setValue( "No claim selected" );

         $node->addClaim( $unselectedClaim );
         $node->setActiveClaim( $unselectedClaim->getClaimID() );
         }
*/
      return $node;
      }

   // }}}
   // {{{ public initClaimsFor( $node )
   public function initClaimsFor( $node )
      {
      $claims = $this->getClaimStatesFor( $node );

      if( empty( $claims ) == true )
         {
         // create the claims
         //Zend_Debug::Dump( "creating claims for node id: ".$node->getID() );
         foreach( $node->getClaims() as $claim )
            {
            $clamState = new Engine_Models_ClaimState();
            $clamState->setDisputeID( $this->getDisputeID() )
                      ->setUserID( $this->getUserID() )
                      ->setNodeID( $node->getID() )
                      ->setClaimID( $claim->getClaimID() )
                      ->setSysInference( NULL )
                      ->setUserInference( NULL )
                      ->setStatus( NULL );
  
            $this->getClaimStateAdapter()->save( $clamState );
            }
         }
      else
         {
         //Zend_Debug::Dump( "reinit claims for node id: ".$node->getID() );
         $node = $this->initClaimStateValues( $node, $claims );
         }

      return $node;
      }

   // }}}
   // {{{ protected getClaimStatesFor( $node )
   protected function getClaimStatesFor( $node )
      {
      // check if the claimshave been initialised
      $claims = $this->getClaimStateAdapter()->findClaimsFor( $this->getDisputeID(), $this->getUserID(), $node->getID() );

      return $claims;
      }
   // }}}
   // {{{ protected getSubNodesFor( $nodeID )
   public function getSubNodesFor( $nodeID )
      {
      $nodes = $this->getNodesAdapter()->findSubNodesOf( $nodeID );     

      return $nodes;
      }
   // }}}
   // {{{ public getRootNode( $disputeType )
   public function getRootNode( $disputeType )
      {
//      $rootnodeAdapter = new Engine_Models_DisputesToNodesMapper()
 //     $rootnodeID = $rootnodeAdapter->findRootNodeID( $disputeType );

      $rootnode = new Engine_Node_Model_Root();
      $rootnode = $this->getNodesAdapter()->findRootNode( $disputeType, $rootnode );
      $rootnode = $this->addClaimsTo( $rootnode );
      $rootnode = $this->setActiveClaimFor( $rootnode );
      $rootnode = $this->initClaimsFor( $rootnode );

      return $rootnode; 
      }

   // }}}
   // {{{ public addSubNodesTo( Engine_Node_Model_Abstract $node )
   public function addSubNodesTo( Engine_Node_Model_Abstract $parentNode, $recurcivly = false )
      {
      $nodes = $this->getSubNodesFor( $parentNode->getNodeID() );
      foreach( $nodes as $node )
         {
         $node = $this->addClaimsTo( $node );
         
         $node = $this->setActiveClaimFor( $node );

         $node = $this->initClaimsFor( $node );
        
         if( $recurcivly == true ) 
            {
            $node = $this->addSubNodesTo( $node, $recurcivly );
            }

         $parentNode->addSubNode( $node ); 
         }
       
      return $parentNode;
      }

   // }}}
   // {{{ public addExistingNodeTo( Engine_Node_Model_Abstract $node )
   public function addExistingNodeTo( Engine_Node_Model_Abstract $parentNode )
      {
      $nodes = $this->getSubNodesFor( $parentNode->getNodeID() );

      foreach( $nodes as $node )
         {
         $node = $this->addClaimsTo( $node );
       
         $claims = $this->getClaimStatesFor( $node );
 
         if( empty( $claims ) != true )
            {
             //$node = $this->setActiveClaimFor( $node );
            $node = $this->initClaimStateValues( $node, $claims );
            $node = $this->addExistingNodeTo( $node );
            $parentNode->addSubNode( $node ); 
            }
         }
       
      return $parentNode;
      }

   // }}}
   // {{{ public initSubNodesClaims( $nodeID )
   public function initSubNodesClaims( $nodeID )
      {
      $parentNode = $this->getNodesAdapter()->find( $nodeID );
      if( $parentNode == null )
         return null;

      $parentNode = $this->addSubNodesTo( $parentNode );

      return $parentNode;
      }

   // }}}
   // {{{ public initClaimStateValues( Engine_Node_Model_Abstract $node, array $claimStates )
   public function initClaimStateValues( Engine_Node_Model_Abstract $node, array $claimStates )
      {
      $nodeClaims = $node->getClaims();
      foreach( $claimStates as $claimState )
         {
         if( isset( $nodeClaims[$claimState->getClaimID()] ) )
            {   
            // update the inferences
            $nodeClaims[$claimState->getClaimID()]->setSysInference( $claimState->getSysInference() );
            $nodeClaims[$claimState->getClaimID()]->setUserInference( $claimState->getUserInference() );

            if( $claimState->getStatus() != null )
               {
               $node = $this->setActiveClaimFor( $node, $claimState );
               }

            // this will overwright the existing claim
            $node->addClaim( $nodeClaims[$claimState->getClaimID()] ); 
            }
         }

      return $node;
      }

   // }}}
   // {{{ public assembleExistingTree( Engine_Node_Model_Root $node )
   /*
    * This will build only those node which have already been created
    */
   public function assembleExistingTree( Engine_Node_Model_Root $rootnode )
      {
      $nodes = $this->getNodesAdapter()->findSubNodesOf( $rootnode->getNodeID() );     
      foreach( $nodes as $node )
         {
         $node = $this->addClaimsTo( $node );
         $node = $this->addExistingNodeTo( $node );
         if( $node != null )
            {
            $node = $this->setActiveClaimFor( $node );
            $node = $this->initClaimsFor( $node );
            $rootnode->addSubNode( $node );
            }
         }
       
      return $rootnode;
      }

   // }}}
   // {{{ public assembleTree( Engine_Node_Model_Root $node )
   /*
    * This will create the whole tree and all of it's nodes
    */
   public function assembleTree( Engine_Node_Model_Root $rootnode )
      {
      $nodes = $this->getNodesAdapter()->findSubNodesOf( $rootnode->getNodeID() );     
      foreach( $nodes as $node )
         {
         $node = $this->addClaimsTo( $node );
         $node = $this->addSubNodesTo( $node, true );
         $node = $this->setActiveClaimFor( $node );
         $node = $this->initClaimsFor( $node );
         $rootnode->addSubNode( $node );
         }
       
      return $rootnode;
      }

   // }}}
   // {{{ public createNode( $nodeID )
   public function createNode( $nodeID )
      {
      // try making the node using it's id
      if( is_int( $nodeID ) )
         {
         $node = $this->getNodesAdapter()->find( $nodeID );     
         }

      // if the Id provided was not an int or could not be found try by the node name id
      if( is_string( $nodeID ) || $node == null )
         {
         $node = $this->getNodesAdapter()->findNode( $nodeID );     
         }

      if( $node == null )
         return null;
      
      $node = $this->addClaimsTo( $node );

      return $node;
      }

   // }}}
   // {{{ public createParentNode( $nodeID )
   public function createParentNode( $nodeID )
      {
      $childNode = $this->createNode( $nodeID );
      if( $childNode == null )
         return null;
     
      $node = $this->createNode( (string)$childNode->getParentNodeID() );
     
      if( $node == null )
         return null; 
 
      $node = $this->addClaimsTo( $node );

      return $node;
      }

   // }}}
   }

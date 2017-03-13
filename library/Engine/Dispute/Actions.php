<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Dispute_Actions
   {
   protected $_disputeType;
   protected $_disputeID;
   private $_assembly;
   private $_rootnode;
   private $_claimStateAdapter;
   private $_claimStateLogAdapter;
   private $_nodesAdapter;
   private $_claimsAdapter;

   // {{{ public constructor( $disputeType, Engine_Node_Assembly $nodeAssembly )
   public function __construct( $disputeType, Engine_Node_Assembly $nodeAssembly )
      {
      $this->_disputeType          = $disputeType;
      $this->_disputeID            = $nodeAssembly->getDisputeID();
      $this->_assembly             = $nodeAssembly;
      $this->_rootnode             = null;
      $this->_claimStateAdapter    = null; 
      $this->_claimStateLogAdapter = null; 


      return $this;
      }

   // }}}
   // {{{ public getNode( $nodeID )
   public function getNode( $nodeID )
      {
      $node = $this->getAssembly()->createNode( $nodeID );
      return $node;
      }
 

   // }}}
   // {{{ protected  _removeNoClaim( Engine_Models_ClaimState $claimState )
   protected function _removeNoClaim( Engine_Models_ClaimState $claimState )
      {
      $removeState = clone $claimState;
      $removeState->setClaimID( -1 )
                  ->setSysInference( NULL )
                  ->setUserInference( NULL )
                  ->setStatus( NULL );

      return self::getClaimStateAdapter()->removeClaim( $removeState );
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
   // {{{ public getClaimStateAdapter()
   public function getClaimStateAdapter()
      {
      if( $this->_claimStateAdapter == null )
         {
         $this->_claimStateAdapter = new Engine_Models_ClaimStateMapper();
         }
         
      return $this->_claimStateAdapter;
      }


   // }}}
   // {{{ public getClaimStateLogAdapter()
   public function getClaimStateLogAdapter()
      {
      if( $this->_claimStateLogAdapter == null )
         {
         $this->_claimStateLogAdapter = new Engine_Models_ClaimStateLogMapper();
         }

      return $this->_claimStateLogAdapter;
      }


   // }}}
   // {{{ public getParentNode( $childNodeID )
   public function getParentNode( $childNodeID )
      {
      $node = $this->getNodesAdapter( )->findParentNode( $childNodeID );
      return $node;
      }
 

   // }}}
   // {{{ public inferClaim()
   public function inferClaim( Engine_Inference_Abstract $inferenceEngine, Engine_Models_ClaimState $claim )
      {
      $node = $this->getAssembly()->createNode( $claim->getNodeID() );
      $this->inferNode($inferenceEngine, $node, $claim->getUserID() );
      return $claim;
      }
 

   // }}}
   // {{{ public inferUserNode( Engine_Inference_Abstract $inferenceEngine, Engine_Node_Model_Abstract $parentNode, $userID )
   public function inferUserNode( Engine_Inference_Abstract $inferenceEngine, Engine_Node_Model_Abstract $parentNode, $userID )
      {
      // user inference
      $childNodes = $this->getNodesAdapter( )->findSubNodesOf( $parentNode->getNodeID() );

      if( $childNodes == null || empty( $childNodes) == true )
         return $parentNode;

      $probs = $inferenceEngine->infer( $parentNode, $childNodes, $userID, $this->_disputeID );
      
      // nothing infered
      if( $probs == null )
         return $parentNode;
     
      foreach( $probs as $claimID=>$prob )
         {
         //Zend_Debug::Dump( $prob * 100, "Infered Prob for Claim: " . $claimID . " Node: "  . $parentNode->getNodeID() );
         $claimState = self::getClaimStateAdapter()->findClaimFor( $this->_disputeID, $userID, $parentNode->getID(), $claimID );
        
         // check that a claim exists         
         if( $claimState == null ){
            throw new exception( "Unable to update probs for non-existant node" ); 
            }

         // update the inference         
         $claimState->setUserInference( $prob );

         // update the claimstate
         $this->updateClaim( $claimState );
         }

/*
*/ 
      return $parentNode;
      }

   // }}}
   // {{{ public inferSysNode( Engine_Inference_Abstract $inferenceEngine, Engine_Node_Model_Abstract $parentNode, $userID )
   public function inferSysNode( Engine_Inference_Abstract $inferenceEngine, Engine_Node_Model_Abstract $parentNode, $userID )
      {
      // user inference
      $childNodes = $this->getNodesAdapter( )->findSubNodesOf( $parentNode->getNodeID() );

      if( $childNodes == null || empty( $childNodes) == true )
         return $parentNode;

      $probs = $inferenceEngine->inferSys( $parentNode, $childNodes, $userID, $this->_disputeID );
     
 
      // nothing infered
      if( $probs == null )
         return $parentNode;
     
      foreach( $probs as $claimID=>$prob )
         {
         //Zend_Debug::Dump( $prob * 100, "Infered Prob for Claim: " . $claimID . " Node: "  . $parentNode->getNodeID() );
         $claimState = self::getClaimStateAdapter()->findClaimFor( $this->_disputeID, $userID, $parentNode->getID(), $claimID );
        
         // check that a claim exists or we will need to create it 
         if( $claimState == null ){
            $claimState = new Engine_Models_ClaimState();
            $claimState->setDisputeID( $this->_disputeID )
                       ->setUserID( $userID )
                       ->setNodeID( $parentNode->getID() )
                       ->setClaimID( $claimID );
            }

         // get other users claim to update
         $notUserClaim  = self::getClaimStateAdapter()->findClaimFor( $this->_disputeID, $userID, $parentNode->getID(), $claimID, true );

         // update the inference         
         $claimState->setSysInference( $prob );

         // update the claimstate
         $this->updateClaim( $claimState );

         // todo make sure that this is working correctly
         if( $notUserClaim != null )
            {
            $notUserClaim->setSysInference( $prob );
            // update the claimstate
            $this->updateClaim( $notUserClaim );
            }
         else
            {
            }

         }

/*
*/ 
      return $parentNode;
      }

   // }}}
   // {{{ public conclude()
   public function conclude() 
      {
      }


   // }}}
   // {{{ public updateClaim( Engine_Models_ClaimState $claimState )
   public function updateClaim( Engine_Models_ClaimState $claimState )
      {
      if( is_null( $claimState->getClaimID() ) )
         {
         return null;
         }
      if( $claimState->getID() == null )
         {
         $temp = self::getClaimStateAdapter()->findClaimFor( $this->_disputeID,
                                                             $claimState->getUserID(),
                                                             $claimState->getNodeID(),
                                                             $claimState->getClaimID() );
         if( $temp != null )
            {
            $claimState->setID( $temp->getID() );
            }
         // make sure to not remove any set inferences
         if( $claimState->getSysInference() == null && $temp->getSysInference() != null )
            {
             $claimState->setSysInference( $temp->getSysInference() );
            }
         if( $claimState->getUserInference() == null && $temp->getUserInference() != null )
            {
             $claimState->setUserInference( $temp->getUserInference() );
            }
         }
      //Zend_Debug::Dump( "node: " . $claimState->getNodeID() . " Claim: " . $claimState->getClaimID(), "Update Claim" );
      return self::getClaimStateAdapter()->save( $claimState );
      }


   // }}}
   // {{{ public updateStatus( Engine_Models_ClaimState $claimState ) 
   public function updateStatus( Engine_Models_ClaimState $claimState ) 
      {
      $userClaim   = self::getClaimStateAdapter()->findActiveClaim( $claimState );
      $notUserClaim = self::getClaimStateAdapter()->findActiveClaim( $claimState, true );
      
      // no active claim found
      if( $userClaim == null )
         {
         $userClaim = $claimState;
         $userClaim->setStatus( "ASSERTED" );
         }
      // active claim dose not match the new claim
      else if( $claimState->getClaimID() != $userClaim->getClaimID() )
         {
         // unset the current claim
         $userClaim->setStatus( NULL );
         self::updateClaim( $userClaim );

         // set the new claim
         $userClaim->setClaimID( $claimState->getClaimID() );
         $userClaim->setStatus( "ASSERTED" );
         }

      // remove any no claim enteries in the db
      $this->_removeNoClaim( $userClaim );

      // compare the two users claims
      // if the not user's claim is inactive (null)
      if( $notUserClaim == null )
         {
         $userClaim->setStatus( "ASSERTED" );
         }
      else
         {
         $counterClaim = $this->getClaimsAdapter()->findClaim( $userClaim->getNodeID(), 
                                                               $userClaim->getClaimID(), 
                                                               new Engine_Node_Model_Claim() );
         //Zend_Debug::Dump( "  C: " . $userClaim->getClaimID()
         //                . " CC: " . $counterClaim->getClaimID() 
         //                . " NC: " . $notUserClaim->getClaimID(), "Counter Claims: " );

         // if the claim are equle
         if( $counterClaim->getClaimID() == $notUserClaim->getClaimID() )
            {  
            $userClaim->setStatus( "AGREED" );
            $notUserClaim->setStatus( "AGREED" );
            }
         // if the user has revired a node, make sure the other user also does so.
         else if( $notUserClaim->getStatus() == 'PENDING_REVIEW' )
            {
            $userClaim->setStatus( 'PENDING_OUTCOME' );
            }
         // if they are not
         else
            {
            $userClaim->setStatus( "DISPUTED" );
            $notUserClaim->setStatus( "DISPUTED" );

            // if the system has made an inference they both users
            // must have aserted claim and any disput from here on is
            // a dissagrement
            $node = self::getNodesAdapter()->find( $userClaim->getNodeID() );
            if( $node == null )
               {
               throw new Exception( 'invalid node suplyed' );
               }
            if( $userClaim->getSysInference() != null || self::getNodesAdapter()->hasChild( $node->getNodeID() ) == false )
               {

               $userClaim->setStatus( "DISAGREED" );
               $notUserClaim->setStatus( "DISAGREED" );
               }
            }
////Zend_Debug::Dump( $notUserClaim );
         self::updateClaim( $notUserClaim );
         }
      self::updateClaim( $userClaim );

      return $userClaim;
      }


   // }}}
   // {{{ public updateParentStatus( Engine_Models_ClaimState $claimState ) 
   public function updateParentClaimStatus( Engine_Models_ClaimState $claimState ) 
      {
      // test if there is an assetred claim and that it's not the root node
      $userClaim = self::getClaimStateAdapter()->findActiveClaim( $claimState );
      if( $userClaim == null )
         {
         return $claimState;
         }

      // test if there is an assetred claim
      $notUserClaim = self::getClaimStateAdapter()->findActiveClaim( $claimState, true );
      if( $notUserClaim == null )
         {
         return $claimState;
         }

      $node = self::getNodesAdapter()->find( $userClaim->getNodeID() );
   
      // make sure sub claims have been asserted
      $subNodes = self::getNodesAdapter()->findSubNodesOf( $node->getNodeID() );
      if( count( $subNodes ) <= 0 )
         {
         return $claimState;
         }

      // have a look at the sub claims check their state
      foreach( $subNodes as $subNode )
         {
         $childClaimState = new Engine_Models_ClaimState();
         $childClaimState->setDisputeID( $userClaim->getDisputeID() )
                         ->setNodeID( $subNode->getID() )
                         ->setUserID( $userClaim->getUserID() );

         $childClaimState = self::getClaimStateAdapter()->findActiveClaim( $childClaimState );
         if( $childClaimState == null )
            {
            $userClaim->setStatus( 'DISPUTED' );
            $notUserClaim->setStatus( 'DISPUTED' );
            self::updateClaim( $userClaim );
            self::updateClaim( $notUserClaim );
            return $userClaim;
            }

         switch( $childClaimState->getStatus() )
            {
            // the node is in a state which permits advancement
            case 'DISAGREED':
            case 'AGREED':
               break;
            // a node is in dispute or is unassested, so nothing to do
            // check if a node is pending a review
            case 'ASSERTED':
            case 'DISPUTED':
            case 'PENDING_REVIEW':
            default:
               $userClaim->setStatus( 'DISPUTED' );       
               $notUserClaim->setStatus( 'DISPUTED' );       
               self::updateClaim( $userClaim ); 
               self::updateClaim( $notUserClaim ); 
               return $userClaim;
            }
         }

      $userClaim->setStatus( 'PENDING_REVIEW' );      
      $notUserClaim->setStatus( 'PENDING_REVIEW' );      
      //$userClaim->setStatus( 'DISAGREED' );      
      //$notUserClaim->setStatus( 'DISAGREED' );      
      self::updateClaim( $userClaim ); 
      self::updateClaim( $notUserClaim ); 
      
      $parentNode = $this->getParentNode( (int)$userClaim->getNodeID( ));

      if( $parentNode != null )
         {
         $parentNodeClaim = new Engine_Models_ClaimState();
         $parentNodeClaim->setDisputeID( $userClaim->getDisputeID() )
                         ->setUserID(    $userClaim->getUserID()    )
                         ->setNodeID(    $parentNode->getID()       );

         $this->updateParentClaimStatus( $parentNodeClaim );
         }
      

      return $userClaim;
      }


   // }}}
   // {{{ public logClaim( Engine_Models_ClaimState $claimState )
   public function logClaim( Engine_Models_ClaimState $claimState )
      {
      $claimLog = new Engine_Models_ClaimStateLog( $claimState );
      return self::getClaimStateLogAdapter()->save( $claimLog );
      }


   // }}}
   // {{{ public getAssembly()
   public function getAssembly()
      {
      return $this->_assembly;
      }


   // }}}
   // {{{ public getRootNode()
   public function getRootNode()
      {
      if( $this->_rootnode == null )
         {
         $this->_rootnode = self::getAssembly()->getRootNode( $this->_disputeType );
         }

      return $this->_rootnode;
      }


   // }}}
   // {{{ public getDispute()
   public function getDispute()
      {
      //$this->_rootnode = self::getAssembly()->assembleTree( self::getRootnode() );
      // $this->_rootnode = self::getAssembly()->assembleExistingTree( self::getRootnode() );

      // this creates the default dispute node layout ( the root and it's children )
      $this->_rootnode = self::getAssembly()->addSubNodesTo( self::getRootnode(), false );
      ////Zend_Debug::Dump( self::getRootnode()->getSubNodes() );
 
      // add any aditional node to the layout if they exist
      foreach( self::getRootnode()->getSubNodes() as $node )
         {    
         self::getAssembly()->addExistingNodeTo( $node );
         }

      return self::getRootnode();
      } 

   // }}}
   // {{{ public initSubNodesClaims( $nodeID )
   public function initSubNodesClaims( $nodeID )
      {
      $node = $this->getAssembly()->initSubNodesClaims( $nodeID );
      return $node;
      } 

   // }}}
   // {{{ public getClaimsInDispute( )
   public function getClaimsInDispute( $userID )
      { 
      $status = "DISPUTED";
      $claims = $this->getClaimsInStateOf( $userID, $status );

      return $claims;
      } 
   // }}}
   // {{{ public getClaimsInAgreedment( )
   public function getClaimsInAgreement( $userID )
      { 
      $status = "AGREED";
      $claims = $this->getClaimsInStateOf( $userID, $status );

      return $claims;
      } 
   // }}}
   // {{{ public getClaimsInDisagreement( )
   public function getClaimsInDisagreemnt( $userID )
      { 
      $status = "DISAGREED";
      $claims = $this->getClaimsInStateOf( $userID, $status );

      return $claims;
      } 
   // }}}
   // {{{ public getClaimsAsserted( )
   public function getClaimsAsserted( $userID )
      { 
      $status = "ASSERTED";
      $claims = $this->getClaimsInStateOf( $userID, $status );

      return $claims;
      } 
   // }}}
   // {{{ public getUnclaimedClaims( )
   public function getUnclaimedClaims( $userID )
      { 
      $claims = $this->getClaimStateAdapter()->findUnclaimedNodes( $this->_disputeID, $userID );
      return $claims;
      } 
   // }}}
   // {{{ public getClaimsInStateOf(  $userID, $status )
   public function getClaimsInStateOf( $userID, $status )
      { 
      $claims = $this->getClaimStateAdapter()->findClaimsWithStatusOf( $this->_disputeID, $userID, $status );
      return $claims;
      } 
   // }}}
   // {{{ public porentualResolution(  )
   public function porentualResolution(  )
      { 
      // fetch the state of the systems inferecen of the rootnode 
      // compare and return outcome

      $rootnode = $this->getRootNode();      
      $rootnode = $this->getAssembly()->addSubNodesTo( $rootnode );
      if( $rootnode == null || $rootnode->getSubNodes() == null )
         {
         //Zend_Debug::Dump( "ACTIVE", "Dispute Status" );
         return null;
         }

      foreach( $rootnode->getSubNodes() as $node )
         {
         if( $node->getStatus() != "AGREED" && $node->getStatus() != "DISAGREED" )
            {
            // todo update dispute status
            //Zend_Debug::Dump( "DIAGREEDMENT", "Dispute Status" );
            return null;
            }
         }
       
      //Zend_Debug::Dump( "POTENTUAL AGREEMENT", "Dispute Status" );
      return true;
      } 
   // }}}
   // {{{ public agreemenReached( $disputeID, $userID )
   public function agreemenReached( $disputeID, $userID )
      { 
      // fetch users rootnode states
      // compare and return outcome


      $userClaim    = new Engine_Models_ClaimState(); 
      $notUserClaim = new Engine_Models_ClaimState();

      $userClaim->setDisputeID( $disputeID )
                ->setUserID( $userID )
                ->setNodeID( $this->getRootNode()->getID() )
                ->setClaimID( $this->getRootNode()->getActiveClaim() );

      $userClaim = $this->getClaimStateAdapter()->findActiveClaim( $userClaim, false );
      if( $userClaim == null )
         {
         return false;
         }
      $notUserClaim = $this->getClaimStateAdapter()->findActiveClaim( $userClaim, true );

      // probably should replace this with an outcome class to handle
      // the finer points of recording and updating the outcome
      $disputesAdapter = new Default_Model_DisputesMapper();
      $disputeStatus    = new Default_Model_Disputes();
      $disputeStatus = $disputesAdapter->find( $disputeID, $disputeStatus );
      $disputeStatus->setOutcomeID( NULL );

      if( $userClaim == null || $notUserClaim == null )
         {
         // no  agreement found to dispute found
         $disputeStatus->setStatus( "PENDING" );
         $disputesAdapter->update( $disputeStatus );
         return false;
         }
      else if( $userClaim->getClaimID() == $notUserClaim->getClaimID()
            && $userClaim->getStatus() == "ACCEPTED" 
            && $notUserClaim->getStatus() == "ACCEPTED" )
         {
         // areement found / dispute resolved (in theory)

         $disputeStatus->setStatus( "RESOLVED" )
                       ->setOutcomeID( 0 );
         $disputesAdapter->update( $disputeStatus );

         return true;
         }

      
      $disputeStatus->setStatus( "PENDING" );
      $disputesAdapter->update( $disputeStatus );

      // no agreement found
      return false;
      } 
   // }}}
   // {{{ public agreementaAccepted( $disputeID, $userID )
   public function agreementAccepted( $disputeID, $userID )
      { 
      // make sure that there is only one status set for a node
      $this->getClaimStateAdapter()->removeClaimStatus( $disputeID, $this->getRootNode()->getID(), $userID );
      $claim = $this->getRootNode()->getRecomendedOutcome();
      $claimState = $this->getClaimStateAdapter()->findClaimFor( $disputeID, 
                                                                 $userID, 
                                                                 $this->getRootNode()->getID(), 
                                                                 $claim->getClaimID() 
                                                                 );
      $claimState->setStatus( "ACCEPTED" );

      $this->updateClaim( $claimState );
      return true;
      } 
   // }}}
   // {{{ public agreementRejected( $disputeID, $userID )
   public function agreementRejected( $disputeID, $userID )
      { 
      // unset rootnode, unset root child claims
      $rootnode = $this->getRootNode();
      $this->getClaimStateAdapter()->removeClaimStatus( $disputeID, $rootnode->getID() );
      $rootnode = $this->getAssembly()->addSubNodesTo( $rootnode );

      // unset the claims asserted by the users to force them to reconsider their claims
      foreach( $rootnode->getSubNodes() as $node )
         {
         if( $node->getStatus() != 'AGREED' )
            {
            $this->getClaimStateAdapter()->changeClaimStatus( $disputeID, $node->getID(), 'PENDING_REVIEW' );
            
           
            // $this->getClaimStateAdapter()->removeClaimStatus( $disputeID, $node->getID() );
            }
         }

      // probably should replace this with an outcome class to handle
      // the finer points of recording and updating the outcome
      $disputesAdapter = new Default_Model_DisputesMapper();
      $disputeStatus   = new Default_Model_Disputes();
      $disputeStatus   = $disputesAdapter->find( $disputeID, $disputeStatus );

      $disputeStatus->setStatus( "ACTIVE" )
                    ->setOutcomeID( NULL )
                    ->incromentNumRejections();
      $disputesAdapter->update( $disputeStatus );
      $claim = $this->getRootNode()->getRecomendedOutcome();
      $claimState = $this->getClaimStateAdapter()->findClaimFor( $disputeID,
                                                                 $userID,
                                                                 $this->getRootNode()->getID(),
                                                                 $claim->getClaimID()
                                                                 );
      $claimState->setStatus( "REJECTED" );

      $this->updateClaim( $claimState );

      return true;
      } 
   // }}}
   }

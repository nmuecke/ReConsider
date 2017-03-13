<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

abstract class Engine_Inference_BayesianAbstract extends Engine_Inference_Abstract
   {
   protected $_parentClaimProb;
   protected $_childClaimsProb;
   protected $_bayesProbsAdapter;

   static public function getInstance( $class = __CLASS__ )
      {
      return parent::getInstance( $class );
      }

   // $claim is a point of reference only
   // {{{ public inferClaim( Engine_Models_ClaimState $claim )
   public function inferClaim( Engine_Models_ClaimState $claim )
      {
      $claimStateAdapter = new Engine_Models_ClaimStateMapper();

      // get the claims we need
      $claims = $claimStateAdapter->findClaimsFor( $claim->getNodeID(), $claim->getUserID() );

      $childClaims = $claimStateAdapter->findActiveChildClaimsFor( $claim->getNodeID(), $claim->getUserID() );

    
      // no claim to infer
      if( $claims == null || $childClaims == null )
         {
         return null;
         }

      // create arrays with the probs needed
      $jointProbs = array();
      $conProbs   = array();
      foreach( $childClaims as $childClaim )
         {
         $jointProbs[$childClaim->getNodeID()] =  $this->getPriorP( $childClaim );
         $conProbs[$childClaim->getNodeID()]   =  $this->getConditionalP( $claim, $childClaim );
         }
        
      $results = array();
      foreach( $claims as $claim )
         {
         $claimProb = $this->getPriorP( $claim );
         $results[$claim->getClaimID()] = $this->calcBayesProb( $conProbs, $claimProb, $jointProbs );
         }

      return $results;
      }


   // }}}
   // {{{ public inferSys( Engine_Node_Model_Abstract $parentNode, array $childNodeIDs, $userID )
   public function inferSys( Engine_Node_Model_Abstract $parentNode, array $childNodes, $userID, $disputeID )
      {
      $claimStateAdapter = new Engine_Models_ClaimStateMapper();

      // get the claims we need
      $claims = $claimStateAdapter->findClaimsFor( $disputeID, $userID, $parentNode->getID() );

      // no claim to infer
      if( $claims == null || empty($claims) == true  )
         {
         return null;
         }

      foreach( $childNodes as $childNode )
         {
         $claimState = $claimStateAdapter->findActiveClaimFor( $disputeID, $userID, $childNode->getID() );
         if( $claimState == null )
            return null;
    
         $childClaimsActive[] = $claimState;
         }
   
      $parentPriorPs = $this->getAllPriorP( $claims[0] ); 
      $conProbs   = array();
      foreach( $childClaimsActive as $childClaim )
         {
         $conProbs[$childClaim->getNodeID()]['probs']       = $this->getAllConditionalP( $claims[0], $childClaim );
         if( $childClaim->getStatus() == "ASSERTED" )
            {
            return null;
            } 
         else if( $childClaim->getStatus() == "AGREED" ) 
            {
            $conProbs[$childClaim->getNodeID()]['activeClaim'] = $childClaim->getClaimID();            
            }
         else
            {
            $conProbs[$childClaim->getNodeID()]['activeClaim'] = null;
            }
         }
      $jointProb = $this->calcJointP( $conProbs, $parentPriorPs );

      $results = array();
      foreach( $claims as $claim )
         {
         // make sure that a no set claim is passed
         if( $claim->getClaimID() != -1 )   
            {                                                //  all con P, all parent P,  active parent P, 
            //$results[$claim->getClaimID()] = $this->calcBayesProb( $conProbs, $parentPriorPs, $parentPriorPs[$claim->getClaimID()] );
            $results[$claim->getClaimID()] = $this->calcBayesProb( $conProbs, $jointProb, $parentPriorPs[$claim->getClaimID()] );
            }
         }

      return $results;
      }


   // }}}
   // {{{ public infer( Engine_Node_Model_Abstract $parentNode, array $childNodeIDs, $userID )
   public function infer( Engine_Node_Model_Abstract $parentNode, array $childNodes, $userID, $disputeID )
      {
      $claimStateAdapter = new Engine_Models_ClaimStateMapper();

      // get the claims we need doesn't matter which user they come from
      $claims = $claimStateAdapter->findClaimsFor( $disputeID, $userID, $parentNode->getID() );

      // no claim to infer
      if(  $claims == null || empty($claims) == true  )
         {
         return null;
         }

      foreach( $childNodes as $childNode )
         {
         $claimState = $claimStateAdapter->findActiveClaimFor( $disputeID, $userID, $childNode->getID() );
         if( $claimState == null )
            return null;
    
         $childClaimsActive[] = $claimState;
         }
   
      $parentPriorPs = $this->getAllPriorP( $claims[0] ); 
      $conProbs   = array();
      foreach( $childClaimsActive as $childClaim )
         {
         $conProbs[$childClaim->getNodeID()]['probs']       = $this->getAllConditionalP( $claims[0], $childClaim );
         $conProbs[$childClaim->getNodeID()]['activeClaim'] = $childClaim->getClaimID();
         }
      $jointProb = $this->calcJointP( $conProbs, $parentPriorPs );

      $results = array();
      foreach( $claims as $claim )
         {
         // make sure that a no set claim is passed
         if( $claim->getClaimID() != -1 )                     
            {                                                  //  all con P, all parent P,  active parent P, 
            //$results[$claim->getClaimID()] = $this->calcBayesProb( $conProbs, $parentPriorPs, $parentPriorPs[$claim->getClaimID()] );
            $results[$claim->getClaimID()] = $this->calcBayesProb( $conProbs, $jointProb, $parentPriorPs[$claim->getClaimID()] );
            }
         }

      return $results;
      }


   // }}}
   // {{{ protected calcBayesProb( $claimProb, $childProbs )
   /**
    * Uses the Probability Matrix to calculate the probalities
    * The calculation is workedout using the following formulare
    * <pre>
    *                              P(Bz, Ci, ..., Ny|Ax) x P(Ax) 
    *    P(Ax|Bz, Ci, ..., Ny) = ---------------------------------
    *                                  P(Bz, Ci, ...., Ny )
    *    Where: Bz,Ci and Ny represent the selected claims for diferent nodes
    *    and    Ax represents the claim for the node to be infered.
    *
    *                              P(Bz|Ax) x P(Ci|Ax) x P(...)x P(Ny|Ax) x P(Ax) 
    *    P(Ax|Bz, Ci, ..., Ny) = --------------------------------------------------
    *                              P(Bz|Ax) x P(Ci|Ax) x P(...)x P(Ny|Ax) x P(Ax) +
                                   P(Bz|!Ax) x P(Ci|!Ax) x P(...)x P(Ny|!Ax) x P(!Ax)
    *   
    * </pre>
    *
    * @access public
    *
    * @param 
    * @return 
    */

   //protected function calcBayesProb( $conProbs, $allParentPriorPs, $activeParentPriorP )
   protected function calcBayesProb( $conProbs, $jointP, $activeParentPriorP )
      {
      $prob =       ( $this->calcConditionalP( $conProbs, $activeParentPriorP )  ) 
                                / $jointP; //$this->calcJointP( $conProbs, $allParentPriorPs );

      //Zend_Debug::Dump( "conp(  " .  $this->calcConditionalP( $conProbs, $activeParentPriorP ) . " ) * "
      //                . "priorP(" . $activeParentPriorP->getProb() . ") / "
      //                . "JointP(" . $jointP . ")" ); //$this->calcJointP( $conProbs,  $allParentPriorPs ) . ")" );

      return $prob;
      }
   // }}}
   // {{{ abstract functions to get the probabilities

   abstract protected function getConditionalP( Engine_Models_ClaimState $parentClaim, Engine_Models_ClaimState $childClaim );
   abstract protected function getPriorP( Engine_Models_ClaimState $claim );

   // }}}
   // {{{ calcConditionalP( array $eAvents, $givenX, $isEquileTo ) [abstract]
   /**
    * Findes the conditional probability for events E1 to En given X
    *
    * @access protected
    *
    * @param array      a 2d array that contains the id of a node and 
    *                   the state of the node or just can be the probability if known 
    *                   ( has be infered )
    *                   *Note: a null state should be considers as unknown
    * @param string     the id of the event whos condidional probability is to be found
    * @param string     the value that the event equils
    * @return double
    *
    * Example:
    * P( A = t, B = .35%, C | X = t ) : this would be put into the function
    * as: getConditionalP( array( .1, .3, .23 ), .34 )
    */
    protected function calcConditionalP( array $childClaims, $activeParentPriorP )
       {
       $conditionalProb = 1; 

       foreach( $childClaims as $claim )
          {
          $active = $claim['activeClaim'];
          if( $active != null )
             {
             $probs  = $claim['probs'];
  
             $prob = $probs[$activeParentPriorP->getClaimID()][$active]->getProb();
             $conditionalProb *= $prob;
             }
          }
       $conditionalProb *= $activeParentPriorP->getProb();
     
       return $conditionalProb;
       }
   // }}}
   // {{{ calcJointP( array $events )
   /**
    * calculats the probability of all events be true for their given states
    *
    * @access protected
    *
    * @param array a array that contains the probs
    * @return double
    *
    * Example:
    * P( A, B, C ): this would be put in to the functon as
    * calcJointP( array( 'A' = .1, 'B' = .4 'C' = null ) )
    */
    protected function calcJointP( array $childClaims, array $allParentPriorP )
       {
       $jointProb = 0;

       foreach( $allParentPriorP as $parentClaim )
          {
          $marginalProb = 1;
          foreach( $childClaims as $claim )
             {
             $active = $claim['activeClaim'];
             if( $active != null )
                {
                $probs  = $claim['probs'];

                $prob = $probs[$parentClaim->getClaimID()][$active]->getProb();
             
                $marginalProb *= $prob;
                }
             }
          $jointProb += $marginalProb * $parentClaim->getProb();
          }

       return $jointProb;
       }

   // }}}
   }
?>

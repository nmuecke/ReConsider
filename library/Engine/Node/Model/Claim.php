<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */

class Engine_Node_Model_Claim
   {
   private $_id;
   private $_claimID;
   private $_nodeID;
   private $_value;
   private $_weight;
   private $_threshold;
   private $_action;
   private $_sysInference;
   private $_userInference;


   // {{{ __CONSTRUCT( $id, $value, $weight = null , $threshold = null, $action = null )
   /**
    * Creates a new value object 
    *
    * @access  public
    *
    * @param string
    * @param string
    * @param doouble|Node
    * @param double
    * @param Mixed
    * @return void
    */

   public function __construct( $id = null, $value = null, $weight = null, $threshold = null, $action = null )
      {
      self::setId( $id );
      self::setValue( $value );
      self::setWeight( $weight );
      self::setThreshold( $threshold );
      self::setAction( $action );
      self::setSysInference( null );
      self::setUserInference( null );
      }
   // }}}
 // {{{ setId( $id )
   /**
    * Sets the id for the value 
    *
    * @access  public
    *
    * @param string
    * @return void
    */

   public function setId( $id ){
      $this->_id = $id;
      return $this;
      }


   // }}}
   // {{{ getId()
   /**
    * Returns the value's id 
    *
    * @access  public
    *
    * @param void 
    * @return string
    */
   public function getId(){
      return $this->_id;
      }


   // }}}
 // {{{ setClaimID( $id )
   /**
    * Sets the id for the value 
    *
    * @access  public
    *
    * @param string
    * @return void
    */

   public function setClaimID( $id ){
      $this->_claimID = $id;
      return $this;
      }


   // }}}
   // {{{ getClaimID()
   /**
    * Returns the value's id 
    *
    * @access  public
    *
    * @param void 
    * @return string
    */
   public function getClaimID(){
      return $this->_claimID;
      }


   // }}}
 // {{{ setCounterClaimID( $id )
   /**
    * Sets the id for the value 
    *
    * @access  public
    *
    * @param string
    * @return void
    */

   public function setCounterClaimID( $id ){
      $this->_counterClaimID = $id;
      return $this;
      }


   // }}}
   // {{{ getCounterClaimID()
   /**
    * Returns the value's id 
    *
    * @access  public
    *
    * @param void 
    * @return string
    */
   public function getCounterClaimID(){
      return $this->_counterClaimID;
      }


   // }}}
 // {{{ setNodeID( $id )
   /**
    * Sets the id for the value 
    *
    * @access  public
    *
    * @param string
    * @return void
    */

   public function setNodeID( $id ){
      $this->_nodeID = $id;
      return $this;
      }


   // }}}
   // {{{ getNodeID()
   /**
    * Returns the value's id 
    *
    * @access  public
    *
    * @param void 
    * @return string
    */
   public function getNodeID(){
      return $this->_nodeID;
      }


   // }}}
   // {{{ setValue( $value )
   /**
    * Sets the value value for a value 
    *
    * @access  public
    *
    * @param string
    * @return void
    */
   public function setValue( $value ){
      $this->_value = $value;
      return $this;
      }


   // }}}
   // {{{ getValue()
   /**
    * Returns the value value 
    *
    * @access  public
    *
    * @param void
    * @return string
    */
   public function getValue(){
      return $this->_value;
      }


   // }}}
   // {{{ setSysInference( $sysInfrence )
   /**
    * Sets the sysInfrence for a node 
    *
    * @access  public
    *
    * @param double|Node
    * @return void
    */
   public function setSysInference( $sysInfrence ){
      $this->_sysInfrence = $sysInfrence;
      return $this;
      }


   // }}}
   // {{{ getSysInference()
   /**
    * Returns the sysInfrence of the value or the next node in a decision tree
    *
    * @access  public
    *
    * @param void
    * @return double|Node
    */
   public function  getSysInference(){
      return $this->_sysInfrence;
      }


   // }}}
   // {{{ setUserInference( $userInfrence )
   /**
    * Sets the userInfrence for a node 
    *
    * @access  public
    *
    * @param double|Node
    * @return void
    */
   public function setUserInference( $userInfrence ){
      $this->_userInfrence = $userInfrence;
      return $this;
      }


   // }}}
   // {{{ getUserInference()
   /**
    * Returns the userInfrence of the value or the next node in a decision tree
    *
    * @access  public
    *
    * @param void
    * @return double|Node
    */
   public function  getUserInference(){
      return $this->_userInfrence;
      }


   // }}}
   // {{{ setWeight( $weight )
   /**
    * Sets the weight for a node 
    *
    * @access  public
    *
    * @param double|Node
    * @return void
    */
   public function setWeight( $weight ){
      $this->_weight = $weight;
      return $this;
      }


   // }}}
   // {{{ getWeight()
   /**
    * Returns the weight of the value or the next node in a decision tree
    *
    * @access  public
    *
    * @param void
    * @return double|Node
    */
   public function  getWeight(){
      return $this->_weight;
      }


   // }}}
   // {{{ setThreshold( $threshold )
   /**
    * Sets the threshold for a value 
    *
    * @access  public
    *
    * @param double
    * @return void
    */
   public function setThreshold( $threshold ){
      $this->_threshold = $threshold;
      return $this;
      }


   // }}}
 // {{{ getThreshold()
   /**
    * Returns the threshold for a value
    *
    * @access  public
    *
    * @param void
    * @return double
    */
   public function getThreshold(){
      return $this->_threshold;
      }

   // }}}
 // {{{ setAction( $action )
   /**
    * Sets the values action value
    *
    * @access  public
    *
    * @param string
    * @return void
    */

   public function setAction( $action ){
      $this->_action = $action;
      return $this;
      }


   // }}}
   // {{{ getAction()
   /**
    * Returns the value's action 
    *
    * @access  public
    *
    * @param void 
    * @return string
    */
   public function getAction(){
      return $this->_action;
      }


   // }}}
   // {{{ toArray()
   /**
    * Returns the value's action 
    *
    * @access  public
    *
    * @param void 
    * @return string
    */
   public function toArray(){
      $claim = array();

      $claim['id'] = $this->_id;
      $claim['nodeID'] = $this->_nodeID;
      $claim['claimID'] = $this->_claimID;
      $claim['counterClaimID'] = $this->_counterClaimID;
      $claim['value'] = $this->_value;
      $claim['weight'] = $this->_weight;
      $claim['threshold'] = $this->_threshold;
      $claim['action'] = $this->_action;
      }


   // }}}
  
   }

<?php
/* vim: set expandtab tabstop=3 shiftwidth=3 softtabstop=3 foldmethod=marker: */
// {{{ copyright & disclaimer
/**
 *   JustReason - A decision support program and associated tools.         
 *   Copyright (C) 2005 by Nial Muecke and JustSys Pty Ltd                 
 *   nmuecke@justsys.com.au                                                
 *                                                                         
 *   This program is free software; you can redistribute it and/or modify  
 *   it under the terms of the GNU General Public License as published by  
 *   the Free Software Foundation; either version 2 of the License, or     
 *   (at your option) any later version.                                   
 *                                                                         
 *   This program is distributed in the hope that it will be useful,       
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of        
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         
 *   GNU General Public License for more details.                          
 *                                                                         
 *   You should have received a copy of the GNU General Public License     
 *   along with this program; if not, write to the                         
 *   Free Software Foundation, Inc.,                                      
 *   59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.   
 *
 *
 * @package JustReason_Engine_Extentions
 * @version 4.0
 *
 * @author Nial Muecke <nmuecke@justsys.com.au>
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */
// }}}
// {{{ Include Files
/** 
 * Include Files
 */

// }}}

/**
 * Class SAMIAM_Node
 * A Bayesian Belife Network node based on the SAMIAM file structure
 *
 * @package JustReason_Engine_Extentions
 * @subpackage Infer
 */
class SAMIAM_Node {
   // {{{ class varibles
      // {{{ private $id;
      /**   
       * the node id
       * @access private
       * @var string
       */
      private $id;
      // }}}
      // {{{ private $label
      /**   
       * the label/title of the node
       * @access private
       * @var string
       */
      private $label;
      // }}}
      // {{{ private position
      /**   
       * the position of the node in the diagram
       * @access private
       * @var array pos['x'], pos['y']
       */
      private $position;
      // }}}
      // {{{ private $excludepolicy
      /**   
       * excludepolicy of the node
       * @access private
       * @var string
       */
      private $excludepolicy;
      // }}}
      // {{{ private ismapvariable
      /**   
       * if the node is maped
       * @access private
       * @var string
       */
      private $ismapvariable;
      // }}}
      // {{{ private $type
      /**   
       * the node type
       * @access private
       * @var string
       */
      private $type;
      // }}}
      // {{{ private $diagnosistype
      /**   
       * the node diagnosistype
       * @access private
       * @var string
       */
      private $diagnosistype;
      // }}}
      // {{{ private $states
      /**
       * an array of the posible states a node can be in
       * @access private
       * @var array of the posible states
       */
      private $states;
      // }}}
      // {{{ private $prob
      /**
       * the nodes probability matrix
       * @access private
       * @var array
       */
      private $prob;
      // }}}
      // {{{ private $prior
      /**
       * The prior probability for a node
       * @access private
       * @var array
       */
      private $prior;
      // }}}
      // {{{ private $prior
      /**   
       * These are node that this node is dependant on in order to make an inference
       * @access private
       * @var array
       */
      private $depNodes;
      // }}}
   // }}}
   // {{{ __CONSTRUCT( $id )  
   /**
    * Constructor of SAMIAM_Node class
    *
    * @access public
    *
    * @param string node id
    * @param string node lable/title
    * @param array  position of the node
    * @param string node excludepolicy
    * @param string node ismap
    * @param string node type
    * @param string nodediagnosis type
    * @return void
    */ 
   public function __CONSTRUCT(  $id, 
                                 $label = "",
                                 array $position = array( 0,0 ), 
                                 $excludepolicy = "include whole CPT",
                                 $ismapvariable = "false",
                                 $type = "AUXILIARY",
                                 $diagnosistype = "AUXILIARY"
                               ){

      $this->id            = $id;
      $this->position      = $position;
      $this->excludepolicy = $excludepolicy;
      $this->ismapvariable = $ismapvariable;
      $this->label         = $label;
      $this->type          = $type;
      $this->diagnosistype = $diagnosistype;

      $this->states        = array();
      $this->prob          = array();
      $this->prior         = array();
      $this->depNodes      = array();
      }


   // }}}
   // {{{ setPosition( $var )
   /**
    * sets the position 
    *
    * @access public
    *
    * @param string
    * @return void
    */
   public function setPosition( $var ){
      $this->position = $var;
      }


   // }}}
   // {{{ setExcludepolicy( $var )
   /**
    * sets the excludepolicy
    *
    * @access public
    *
    * @param string
    * @return void
    */
   public function setExcludepolicy( $var ){
      $this->excludepolicy = $var;
      }


   // }}}
   // {{{ setIsMap( $var )
   /**
    * sets the the map var
    *
    * @access public
    *
    * @param string
    * @return void
    */
   public function setIsMap( $var ){
      $this->ismapvariable = $var;
      }


   // }}}
   // {{{ setLabel( $var )
   /**
    * sets the lable/title
    *
    * @access public
    *
    * @param string
    * @return void
    */
   public function setLabel( $var ){
      $this->label = $var;
      }


   // }}}
   // {{{ setType( $var )
   /**
    * sets the type
    *
    * @access public
    *
    * @param string
    * @return void
    */
   public function setType( $var ){
      $this->type = $var;
      }


   // }}}
   // {{{ setDiagnosisType( $var )
   /**
    * sets the diagnosis type
    *
    * @access public
    *
    * @param string
    * @return void
    */
   public function setDiagnosisType( $var ){
      $this->diagnosistype = $var;
      }


   // }}}
   // {{{ addState( $state )
   /**
    * adds a state to the states list
    *
    * @access public
    *
    * @param string
    * @return void
    */
   public function addState( $state ){
      array_push( $this->states, (string)$state );
      }


   // }}}
   // {{{ getStates(  )
   /**
    * returns the states
    *
    * @access public
    *
    * @param void
    * @return array of states
    */
   public function getStates( ){
      return $this->states;
      }


   // }}}
   // {{{ getStateAt( $index )
   /**
    * returns the state at a given index
    *
    * @access public
    *
    * @param string array index
    * @return string|false state
    */
   public function getStateAt( $index ){
      if( isset( $this->states[$index] ) ){
         return $this->states[$index];
         }
      return false;
      }


   // }}}
   // {{{ numStates(  )
   /**
    * returns the number of states
    *
    * @access public
    *
    * @param void
    * @return int
    */
   public function numStates( ){
      return sizeof($this->states );
      }


   // }}}
   // {{{ addDependent( & $node  )
   /**
    * adds prob to the prob array
    *
    * @access public
    *
    * @param SAMIAM_Node
    * @return void
    */
   public function addDependent( & $node ){
      $this->depNodes[sizeof($this->depNodes)] =& $node;
      }


   // }}}
   // {{{ getDependentAt( $index  )
   /**
    * getss prob from the prob array at a given array index
    *
    * @access public
    *
    * @param int array index
    * @return SAMIAM_Node
    */
   public function & getDependentAt( $index ){
      if( !empty( $this->depNodes[$index] ) && isset( $this->depNodes[$index] ) ){

         return  $this->depNodes[$index];
         }
      $false = false;
      return $false;
      }


   // }}}
   // {{{ addPrior( $prob )
   /**
    * adds a prior prob to the prior prob array at the givent label
    *
    * @access public
    *
    * @param array of prior probs
    * @return void
    */
   public function addPrior( array $probs ){
       $this->prior = $probs;
      }


   // }}}
   // {{{ getPriors( )
   /**
    * Returns the prior prob array
    *
    * @access public
    *
    * @return array of the prior probs
    */
   public function getPriors( ){
      return $this->prior;
      }


   // }}}
   // {{{ getPriorAt( $label )
   /**
    * Returns the prior prob for a givent label
    *
    * @access public
    *
    * @param string the label that forms the array index
    * @return double the prior prob at the label index else -99 if label is not found
    */
   public function getPriorAt( $label ){
      if( isset( $this->prior[$label] ) ){
         return $this->prior[$label];
         }
      return null;
      }


   // }}}
   // {{{ addProb( array $prob )
   /**
    * adds prob to the prob matrix
    *
    * @access public
    *
    * @param array of doubles
    * @return void
    */
   public function addProb( $prob ){
       $this->prob =  $prob ;
      }


   // }}}
   // {{{ getProbs( )
   /**
    * returns the prob matrix
    *
    * @access public
    *
    * @param void
    * @return array 
    */
   public function getProbs( ){
      return $this->prob;
      }


   // }}}
   // {{{ getId()
   /**
    * returns the id of the node
    *
    * @access public
    * @param void
    * @return string
    */
   public function getId(){
      return $this->id;
      }
   //}}}
   // {{{ getPosition( )
   /**
    * gets the position 
    *
    * @access public
    *
    * @param void
    * @return array
    */
   public function getPosition( ){
      return $this->position;
      }


   // }}}
   // {{{ getExcludepolicy( )
   /**
    * gets the excludepolicy
    *
    * @access public
    *
    * @param void
    * @return string
    */
   public function getExcludepolicy( ){
      return $this->excludepolicy;
      }


   // }}}
   // {{{ getIsMap( )
   /**
    * gets the the map var
    *
    * @access public
    *
    * @param string
    * @return void
    */
   public function getIsMap( ){
      return $this->ismapvariable;
      }


   // }}}
   // {{{ getLabel( )
   /**
    * gets the lable/title
    *
    * @access public
    *
    * @param void
    * @return string
    */
   public function getLabel( ){
      return $this->label;
      }


   // }}}
   // {{{ getType( )
   /**
    * gets the type
    *
    * @access public
    *
    * @param void
    * @return string
    */
   public function getType( ){
      return $this->type;
      }


   // }}}
   // {{{ getDiagnosisType( )
   /**
    * gets the diagnosis type
    *
    * @access public
    *
    * @param void
    * @return string
    */
   public function getDiagnosisType( ){
      return $this->diagnosistype;
      }


   // }}}
   // {{{ numDepentents()
   /**
    * returns the number of depentent nodes
    *
    * @access public
    * @param void
    * @return int
    */
   public function numDependents(){
      return sizeof( $this->depNodes );
      }
   //}}}
   }
?>

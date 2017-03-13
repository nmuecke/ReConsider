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
require_once( "class.SAMIAM_Node.php"        );

// }}}

/**
 * Class SAMIAM_Data
 *    This is a representation of a Bayesian Belife Networt ( BBN ) data, based on the SAMIAM file structure 
 *
 * @package JustReason_Engine_Extentions
 * @subpackage Infer
 */
class SAMIAM_Data {
   // {{{ class variables
   /**
    * An array of BBN nodes
    * @access private
    * @var array of SAMIAM_Nodes
    */
   private $nodes;


   // }}} 
   // {{{ __CONSTRUCTOR( ) 
   /**
    * Constructor of SAMIAM_Data class
    *
    * @access public
    *
    * @param void
    * @return void
    */
   public function __CONSTRUCT(){
      $this->nodes = array();
      } 
 
   // }}}
   // {{{ addNode( $node )
   /**
    * Adds a node the the object
    *
    * @access public
    *
    * @param SAMIAM_Node
    * @return void
    * @throws Exception "SAMIAM_Data: Node Cannot be added, Node is not a valid SAMIAM Node"
    * @throws Exception "SAMIAM_Data: Duplicate node id! " 
    */
   public function addNode( $node ){
      if( get_class( $node ) == 'SAMIAM_Node' || is_subclass_of( $node, 'SAMIAM_Node' ) ){
         if( isset( $this->nodes[$node->getId()] ) ){
            throw new Exception( "SAMIAM_Data: Duplicate node id ( ".$node->getId()." ) ! " );
            }
         $this->nodes[$node->getId()] = $node;
         }
      else{
         throw new Exception( "SAMIAM_Data: Node Cannot be added, Node is not a valid SAMIAM Node" );
         }
      }
   // }}} 
   // {{{ getNode( $nodeId )
   /**
    * returns the node with the given id or false if the id is not in the node list
    *
    * @access public
    *
    * @param string node id
    * @return SAMIAM_Node|false if the node cannot be found
    */
   public function & getNode( $nodeId ){
      if( isset( $this->nodes[$nodeId] ) ){
         return $this->nodes[$nodeId];
         }
      $false = false;
      return $false;
      }


   // }}}
   // {{{ getNodes( )
   /**
    * returns the node with the given id or false if the id is not in the node list
    *
    * @access public
    *
    * @param string node id
    * @return SAMIAM_Node|false if the node cannot be found
    */
   public function getNodes(  ){
      return $this->nodes;
      }


   // }}}
   // {{{ isDependentOf( $depNodeId, $nodeId )
   /**
    * Makes on node dependrnt on another node
    *
    * @access public
    *
    * @param SAMIAM_Node the dependent node id
    * @param SAMIAM_Node the depended on node id
    * @return void
    * @throws Exception "SAMIAM_Data: Node Cannot be added, Node is not a valid SAMIAM Node"
    * @throws Exception "SAMIAM_Data: Dependant or Depended Node Cannot be added, Node can not be found in the Node list!"
    */
   public function isDependentOf( $depNodeId, $nodeId ){
      $depNode =& self::getNode( $depNodeId );
      $node    =& self::getNode( $nodeId    );
      if( $depNode == false || $node == false ){
         throw new Exception( "SAMIAM_Data: Dependent or Depended Node Cannot be added, Node can not be found in the Node list!" );
         }
      else if( get_class( $depNode ) != 'SAMIAM_Node' && is_subclass_of( $depNode, 'SAMIAM_Node' ) != true ||
               get_class( $node    ) != 'SAMIAM_Node' && is_subclass_of( $node,    'SAMIAM_Node' ) != true ){
      //else if ^
         throw new Exception( "SAMIAM_Data: Node Cannot be added, Node is not a valid SAMIAM Node" );
         }
      $depNode->addDependent( $node );
      }
   // }}} 
   // {{{ getPriorProbsFor( $nodeId, $where )
   public function getPriorProbsFor( $nodeId, $where ){
      if( ( $node = self::getNode( $nodeId ) ) == false ){
         throw new Exception( "SAMIAM_Data: node not found" );
         }

      if( ( $val = $node->getPriorAt( $where ) ) == -99 ){
         throw new Exception( "SAMIAM_Data: unable to find the array index provided (".$where.")" );
         }
      return $val;
      }


   // }}}
   // {{{ getProbsFor( $nodeId, array $where ){
   /**
    * Searched the states of a given node and returns the probabilities that match
    *
    * @access public
    *
    * @param string nodeid who's probs are desired
    * @param array a 2d array of  the nodeId's and set states of the desired probs
    * @return array
    */
   public function getProbsFor( $nodeId, array $where ){
      if( ( $node = self::getNode( $nodeId ) ) == false ){
         throw new Exception( "SAMIAM_Data: node not found" );
         }
      $probs = array();

      // gets the prob for a node with out any dependents
      // this should only ever be a root node and will have 
      // 1d array where each data item maches up to each one
      // of the nodes states
      if( $node->numDependents() == 0 ){
         foreach( $where as $event ){
            if( $event[0] == $nodeId ){
               $data = $node->getProbs();
               if( $event[1] == null ){
                  $probs = $data;
                  }
               else{
                foreach( $data as $key=>$item ){
                    if( strcasecmp( $event[1], $key ) == 0 ){
                        array_push( $probs, $item );
                        }
                     }
                  }
               }
            }
         }
      // this is to handle all other events where there is a 
      // multi dimention array
      else{
         $orderedEvents = array(); // a new array that will have the key index mach up with the depNodes pos for that state

         foreach( $where as $key=>$event ){

            //$orderedEvents[array_search( $event[0], $node->getProb() )] = $event[1];
            for( $index = 0; $index < $node->numDependents(); $index++ ){
               if( self::getDepedentForNodeAt( $node->getId(), $index )->getId() == $event[0]){
                  $orderedEvents[$index] = $event[1];
                  } 
               }
            if( $event[0] == $node->getId() ){
               $state = $event[1];
               }
            }
         // this is to build the  states to search for
         array_push( $orderedEvents, $state );
         $probs = self::getProbs( $node->getProbs(), $orderedEvents );
         }
      return $probs;
      }  


   // }}}
   // {{{ getProbs( $data )
   /**
    * gets the probabilities that are found in the data which match the states of found in the events array
    *
    * @access private
    * 
    * @param array|double  the data to be serched
    * @param array         the events that are to be found in the data
    * @param int           an internal counter, leave blank or use 0
    * @return array        an array of doubles that are the proberbilities   
    */
   private function getProbs( $data, $events, $eventLev = 0 ){ 
      $probs = Array();

      if( !is_array( $data ) ){
         array_push( $probs, $data );   
         }
      else{
         if( isset( $events[$eventLev] ) && $events[$eventLev] == null || empty(  $events[$eventLev] )  ){
            foreach( $data as $dItem ){      
               $probs  = array_merge( $probs, self::getProbs( $dItem, $events, ( $eventLev + 1 ) ));
               }
            }
         else{
            foreach( $data as $key=>$item ){
               if( strcasecmp( $key, $events[$eventLev] ) == 0 ){
                  $probs = array_merge( $probs, self::getProbs( $item, $events, ( $eventLev + 1 ) ));
                  }
               }
            }
         }
      return $probs;   
      }

   // }}}
   // {{{ getDepedentForNodeAt( $nodeId, $index )
   /**
    * returns the dependent node at the supplyed node's index
    *
    * @access public
    *
    * @param string id of node 
    * @param int array index 
    * @returns SAMIAM_Node
    */
   public function & getDepedentForNodeAt( $nodeId, $index ){
      return self::getNode( $nodeId )->getDependentAt( $index );
      }
   // }}}
   // {{{ getPriorProb( $node )
   public function getPriorProb( & $node ){

      }
   // }}}
   // {{{ function setPriorProb()
   public function setPriorProb(){
      foreach( $this->nodes as & $node ){
         self::priorStates( $node );
         }
      }
   // }}}
   // {{{ function priorStates( & $node )
   private function priorStates( & $node ){
     for( $xx = 0; $xx < $node->numDependents(); $xx++ ){
         $dep =& $node->getDependentAt( $xx );

         if( count( $dep->getPriors()) <= 0 ){
            self::priorStates( $dep ); 
            }
         }
         if( count( $node->getPriors() ) <= 0 ){
            $priors = array();
            self::calcPriorProb( $node, $priors, $node->getProbs() );
            $node->addPrior( $priors );
            }
      }
   // }}}
   // {{{ calcPriorProb( $node, array & $priors, array $probss, $lev = 0, $factor = 0 )
   private function calcPriorProb( $node, array & $priors, array $probs, $lev = 0, $factor = 0 ){
      foreach( $probs as $key=>$prob ){
         if( $lev == 0 ){
            $factor = 1;
            }

         if( is_array( $prob ) ){
            self::calcPriorProb( $node, $priors, $prob, ($lev + 1), ($factor * $node->getDependentAt( $lev )->getPriorAt( $key )) );
            }
         else{
            if( !isset( $priors[$key] ) ){
               $priors[$key] = 0;
               }
            $priors[$key] += $prob * $factor;
            }
         }
      }

   // }}}
   }// end class
?>

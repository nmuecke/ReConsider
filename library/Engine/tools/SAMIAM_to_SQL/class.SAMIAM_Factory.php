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
require_once( "class.SAMIAM_Data.php"        );
//require_once( "class.LinkedException.php"    );

// }}}

/**
 * Class SAMIAM_Factory
 *    This is a representation of a Bayesian Belife Networt ( BBN ) data, based on the SAMIAM file structure 
 *
 * @package JustReason_Engine_Extentions
 * @subpackage Infer
 */
class SAMIAM_Factory {

   // {{{ loadSAMIAM( $filename )
   /**
    * A function that loads the data from the spesifyed file into a SAMIAM_Data object
    * 
    * @param   string path tto file
    * @return  SAMIAM_Data
    * @throws  LinkedException "Unable to open file"
    * @throws  LinkedException "Unable to add node data"
    */
   public static function factory( $filename ){
      if( ($fd = fopen( $filename, "r")) == false ){
         throw new Exception( "SAMIAM_Factory: Unable to open file ( ".$filename." ) check that pthe ath and file name are correct." );
         }

      $SAMIAM = new SAMIAM_Data();      

      while( !FEOF( $fd ) ){

         $tok = split( " ",  str_replace( array( "\t", "\n" ), '', fgets( $fd ) ) );

         // creats the blank nodes for the node definition isn the file
         if( $tok[0] == 'node' ){
            $node = new SAMIAM_Node( $tok[1] ); 
            while( !FEOF( $fd ) && strcmp( $tok[0], '}' ) != 0 ){ // enter node loop
               $tok = split( " = ", str_replace( array( "\t", "\n" ), '', fgets( $fd ) ) );
 
               // extracts the state lines
               // todo there may be an issue if the states in the file span more than one line
               if( strcmp( str_replace( " ", "", $tok[0] ), "states" ) == 0 ){
                              

                  // removes some of the symples that we dont want
                  $line = str_replace( array( '(', ')', ';' ), '', $tok[1] );
                  //echo "line is: ".$line."<br />";

                  $states = split( "\" ", $line );
                  foreach( $states as $state ){
                     $state = str_replace( "\"", "", $state );
                     if( str_replace( " ", "", $state ) != '' ){
                        //echo "state is: ".$state ."<br />";
                        $node->addState( $state );
                        }
                     }
                  }
               else if( strcmp( str_replace( " ", "", $tok[0] ), "position" ) == 0 ){
                  $line = str_replace( array( '(', ')', ';' ), '', $tok[1] );
                  $positions = split( " ", $line );
                  $pos = array();

                  foreach( $positions as $p ){
                     if( str_replace( " ", "", $p ) != '' ){
                        array_push( $pos, $p );
                        }
                     }

                  $node->setPosition( $pos );
                  }
               else if( strcmp( str_replace( " ", "", $tok[0] ), "excludepolicy" ) == 0 ){
                  $node->setExcludepolicy( str_replace( array(  '"', ';' ), '', $tok[1] ));
                  }
               else if( strcmp( str_replace( " ", "", $tok[0] ), "ismapvariable" ) == 0 ){
                  $node->setIsMap( str_replace( array(  '"', ';' ), '', $tok[1] ));
                  }
               else if( strcmp( str_replace( " ", "", $tok[0] ), "label" ) == 0 ){
                  $node->setLabel( str_replace( array(  '"', ';' ), '', $tok[1] ));
                  }
               else if( strcmp( str_replace( " ", "", $tok[0] ), "DSLxEXTRA_DEFINITIONxDIAGNOSIS_TYPE" ) == 0 ){
                  $node->setType( str_replace( array(  '"', ';' ), '', $tok[1] ));
                  }
               else if( strcmp( str_replace( " ", "", $tok[0] ), "diagnosistype" ) == 0 ){
                  $node->setDiagnosisType( str_replace( array(  '"', ';' ), '', $tok[1] ));
                  }

               }// end node loop
            $SAMIAM->addNode( $node );
            }// end if
         else if( $tok[0] == "potential" ){

            if( $node =& $SAMIAM->getNode( $tok[2] ) === false ){
               throw new Exception( "SAMIAM_Factory: unable to add data to node ( ".$toc[2]." ), Node not found!" );
               }
            for( $xx = 4; $xx < sizeof( $tok ) -1; $xx++ ){
               if( ( $dependedNodeId = str_replace( array( '(', ')', '|' ), "", $tok[$xx] )) != "" ){
                  $SAMIAM->isDependentOf( $node->getId(), $dependedNodeId );
                  }
               }
            $tok = split( " ",  str_replace( array( "\t", "\n" ), '', fgets( $fd ) ) );

            if( $tok[0] == "{" ){
               $data     = array();
               while( !FEOF( $fd ) && strcmp( $tok[0], '}' ) != 0 ){ // enter node loop

                  $tok = split( "\t",  str_replace( array( "\n", '(', ')', '"', '=', ';', ' ', "data" ), '', fgets( $fd )  ) );
                  // extracts the labes from the node

                  for( $xx = 0; $xx < sizeof( $tok ) -1 ; $xx++ ){
                  
                      if( $tok[$xx] != "" && $tok[$xx] != '}' ){
                        array_push( $data, (double)$tok[$xx] );
                        }
                     }
                  } // end while loop for data
                  //print_r( $data );
                  //echo "<br />";
                  if( !empty( $data ) ){
                     $dataArray = array();
                     self::getState( $node, $dataArray, $data );
                     $node->addProb( $dataArray );
                     }
               }
            }// end else if
         }// end while
         
         // calculates the prior probs for each node
         $SAMIAM->setPriorProb();
/*
echo "<pre>";
print_r(  $SAMIAM );
echo "</pre>";
*/
      return $SAMIAM;
      }


   // }}}
   // {{{ getState( $node, & $dataArray, $data, $depNodePos = 0, & $dataPos = 0  )
   private function getState( $node, & $dataArray, $data, $depNodePos = 0, & $dataPos = 0  ){
         //echo "<h4> ".$node->getId()."</h4>";
      if( $node->numDependents() > $depNodePos ){
         $depNode = $node->getDependentAt( $depNodePos );
        // echo "<h5> ".$depNode->getId()."</h5>";

         for( $xx = 0; $depNode->getStateAt( $xx ) != false; $xx++ ){
            //echo ":> xx: ".$xx." :: P state: ".$depNode->getStateAt( $xx )." :: depPos: ".$depNodePos." :: dataPos: ".$dataPos." <<:: </br>";
            self::getState( $node, $subData, $data, ($depNodePos + 1) , $dataPos );

            $dataArray[($depNode->getStateAt( $xx ))] = $subData;
            }
         }
      else{
         for( $yy = 0;  $node->getStateAt( $yy ) != false; $yy++ ){
            //echo "::> yy: ".$yy." :: State: ".$node->getStateAt( $yy )." :: Data: ".$data[$dataPos]." <:: <br />";
            $dataArray[$node->getStateAt( $yy )] = $data[$dataPos++];
            
            }
         }
      
      }
   // }}}
   }
?>

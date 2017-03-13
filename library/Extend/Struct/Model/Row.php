<?php
/**
 *
 * LICENSE
 *
 * @category   
 * @package    
 * @copyright  
 * @license    
 * @version    
 */


/**
 * @category   
 * @package    
 * @copyright  
 * @license    
 */
class Extend_Struct_Model_Row
   {
   private $_cols;

   private $_id;
   private $_emptyCell;
   private $_data;

   public function __Construct( $id = null, $cols = 0, array $data = array(), $emptyCell = "" )
      {
      $this->_id= $id;
      $this->_cols = $cols;
      $this->_emptyCell = $emptyCell;

      if( $this->_cols > 0 )
         {
         if( $this->_cols > count( $data ) )
            {
            $data = array_pad( $data, $this->_cols, $emptyCell ) ;
            }
         }
   
      $this->_data = $data;
      }

   public function setEmptyValue( $val )
      {
      $this->_emptyCell = $val;
      return $this;
      }

   public function addCol( $data )
      {
      $this->_cols++;
      $this->_data[ $this->_cols ] = $data ;
      return $this;
      }

   public function setCol( $pos, $data )
      {
      if( $pos > $this->_cols )
         {
         $this->padRow( ($pos - $this->_cols ) );
         }

      $this->_data[$pos] = $data;

      return $this;
      }


   public function insertCol( $pos, $data, $replace = false )
      {
      if( $pos > $this->_cols )
         {
         $this->padRow( $pos - $this->_cols );
         $this->addCol( $data );

         return $this;
         }
      // move the existing data
      for( $xx = $this->_cols; $xx >= $pos; $xx-- )
         {
         $this->_data[$xx + 1] = $this->_data[$xx];
         }   


      $this->_data[$pos] = $data;
      $this->_cols++;

      return $this; 
      }


   public function delCol( $pos ) 
      {
      if( $pos > $this->_cols )
         {
         return $this;
         }
       
      unset( $this->_data[$pos] );

      for( $xx = $pos; $xx < $this->_cols; $xx++ )
         {
         $this->_data[$xx] = $this->_data[$xx + 1];
         unset( $this->_data[$xx + 1] );
         } 
       $this->_cols--;

      return $this;

      }

   public function setID( $val ) 
      {
      $this->_id = $val;
      return $this;
      }

   public function getID(  ) 
      {
      return $this->_id;
      }

   public function getRow(  ) 
      {
      return $this->_data;
      }

   public function getNumCols()
      {
      return $this->_cols;
      }

   public function getCol( $pos ) 
      {
      if( !isset( $this->_data[$pos] ) )
         {
         return null;
         }
      return $this->_data[$pos];
      }
   

   public function transform()
      {
      return $this;
      }


   // add x number of additional cells
   public function padRow( $addXCols )       
      {
      for( $xx = 0; $xx < $addXCols; $xx++ )
         {
         $this->addCol( $this->_emptyCell );
         }

      return $this;
      }
   }

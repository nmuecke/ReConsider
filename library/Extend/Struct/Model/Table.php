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
class Extend_Struct_Model_Table
   {
   private $_rows;
   private $_cols;

   private $_rowIndex;
   private $_colIndex;

   private $_emptyCell;

   private $_data;

   public function __Construct( $rows = null, $cols = null, $emptyCell = "" )
      {
      $this->_rows = $rows;
      $this->_cols = $cols;

      $this->_rowIndex = array();
      $this->_colIndex = array();

      $this->_empteyVal = $emptyCell;
   
      $this->_data = array();
      }

   public function setEmptyValue( $val )
      {
      $this->_emptyCell = $val;

      foreach( $this->_data as $row )
         {
         $row->setEmptyValue( $val );
         }

      return $this;
      }

   public function addRow( Extend_Struct_Model_Row $row = null, $rowIndex = null )
      {
      $this->_rows++;

      if( $row == null )
         {
         $row = new Extend_Struct_Model_Row( $this->_rows, $this->_cols, array(), $this->_emptyCell );         
         }

      $cols = $row->getNumCols();
      if( $cols > $this->_cols )
         {
         $this->padRows( $cols - $this->_cols );
         $this->_cols = $cols;
         }
      else if( $cols < $this->_cols )
         {
         $row->padRow( $this->_cols - $cols );
         }

      $this->_data[ $this->_rows ] = $row ;
      $this->setRowIndex( $this->_rows, $rowIndex );

      return $this;
      }

   public function addCol( $data )
      {
      return $this;
      }

   public function setRowIndex( $rowID, $rowIndexVal )
      {
      if( $rowIndexVal == null )
        {
        $this->_rowIndex[ $rowID ] = $this->_data[ $rowID ]->getID( );

        return $this;
        }

      $this->_rowIndex[ $rowID ] = $rowIndexVal;
      $this->_data[ $rowID ]->setID( $rowIndexVal );

      return $this;
      }
   
   public function updateData( $data, $rowID, $colID, $rowIndex = null, $colIndex = null )
      {
      // row exists
      if( isset( $this->_data[$rowID] ) )
         {
         $row = $this->_data[$rowID];
         $row->setCol( $colID, $data );

         $this->setRowIndex( $rowID, $rowIndex );

         return $this;
         }

      // make a new row;
      $row = new Extend_Struct_Model_Row( $this->_rows, $this->_cols, array(), $this->_emptyCell );

      // add the data
      $row->setCol( $colID, $data );
      // add the row
      $this->insertRow( $rowID, $row );
      $this->setRowIndex( $rowID, $rowIndex );

      return $this;
      }

   public function insertData( $data, $rowID, $colID, $rowIndex = null, $colIndex = null )
      {
      // row exists
      if( isset( $this->_data[$rowID] ) )
         {
         $row = $this->_data[$rowID];
         $row->insertCol( $colID, $data );

         $this->setRowIndex( $rowID, $rowIndex );

         return $this;
         }

      // make a new row;
      $row = new Extend_Struct_Model_Row( $this->_rows, $this->_cols, array(), $this->_emptyCell );         
      $this->insertRow( $rowID, $row );

      // add the data
      $this->insertData( $data, $rowID, $colID );

      return $this;
      }

   public function insertRow( $rowID, Extend_Struct_Model_Row $data, $rowIndex = null )
      {
      // if we are larger than the current size
      if( $rowID > $this->_rows )
         {
         for( $xx = $this->_rows; $xx < $rowID - $this->_rows; $xx++ )
            {
            $this->addRow();
            }
         }
      else
         {      
         for( $xx = $this->_rows; $xx >= $rowID; $xx-- )
            {
            $this->_data[$xx + 1] = $this->_data[ $xx ];
            $this->_rowIndex[$xx + 1] = $this->_rowIndex[ $xx ];
            }

         $this->_rows++;
         }

      $this->_data[$rowID] = $data; 
      $this->setRowIndex( $rowID, $rowIndex );

      return $this;
      }

   public function getRowID( $rowIndex )
      {
      return array_search( $rowIndex, $this->_rowIndex );
      }

   public function insertCol( $pos, $data )
      {
      return $this;
      }

   public function delRow( $rowID ) 
      {
      if( isset( $this->_data[ $rowID ] ) )
         {
         unset( $this->_data[ $rowID ] );
         unset( $this->_rowIndex[ $rowID ] );
         }
      return $this;
      }

   public function delCol( $pos ) 
      {
      return $this;
      }

   public function getRow( $pos ) 
      {
      if( isset( $this->_data[ $pos ] ) )
         {
         return $this->_data[ $pos ];
         }
      return null;
      }

   public function getRows()
      {
      return $this->_data;
      }

   public function getCol( $pos ) 
      {
      return $this;
      }
   
   public function getTable( $pos ) 
      {
      return $this;
      }

   public function transform()
      {
      return $this;
      }

   public function padRows( $addXCols )       
      {
      for( $xx = 0; $xx < count( $this->_data ); $xx++ )
         {
         $row = $this->_data[$xx];
         $row->padRow( $addXCols  );
         }

      return $this;
      }
   }

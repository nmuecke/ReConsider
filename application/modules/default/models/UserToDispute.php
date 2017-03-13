<?php
// application/models/UserToDispute.php

class Default_Model_UserToDispute extends Extend_Db_AbstractTableModel
{
       
          protected $_disputeID;
          protected $_userID;
       
          public function setDisputeID( $id )
          {
              $this->_disputeID = (int)$id;
              return $this;
          }
       
          public function getDisputeID()
          {
              return $this->_disputeID;
          }

          public function setUserID( $id )
          {
              $this->_userID = (int)$id;
              return $this;
          }
       
          public function getUserID()
          {
              return $this->_userID;
          }


      }


<?php

abstract class Engine_QueueMail
   {
 
   private $_accessAdapter;
   private $_changeAdapter;
   private $_userID;


   public function __construct( $userID, $disputeID, $accessAdapter, $changeAddapter )
      {
      $this->_accessAdapter = $accessAdapter;
      $this->_changeAdapter = $changeAddapter;
      $this->_userID        = $userID;
      $this->_disputeID     = $disputeID;
      }

   /*
    * chech if the user's dispute is up to date.
    *  - a dispute is up to date if they made the last change
    */
   public function isUpToDate()
      {
      $res = $this->_changeAdapter->findLastChangeToDispute( $this->_dispute )
      if( $res != null )
         {
         return null;
         }
      if( $res->getUserID() == $this->_userID )
         {
         return true;
         }
      return false;
      }

   /*
    * compires the time since last accessed
    * to the max session length
    * 
    */
   public finction isLoggedIn()
      {
      }

   /*
    *
    */
   public function update()
      {
      }

   abstract public function notifyOther()
      {
      }
  
 
   }

<?php
class Administration_IndexController extends Zend_Controller_Action
   {
   public function init()
      {
        /* Initialize action controller here */

      }

   public function indexAction()
      {
      $urlHistory = new Extend_Helpers_RefPage();
      $urlHistory->save( $this->_request->getRequestUri() );
      }
   }

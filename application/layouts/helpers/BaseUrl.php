<?php
class Layout_Helper_BaseUrl extends Zend_View_Helper_Abstract
   {

   public function baseUrl()
      {
      return Zend_Registry::get('config')->app->baseUrl;
      }

   }

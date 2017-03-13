<?php
/*
 * Adapted from: http://hewmc.blogspot.com/2010/08/simple-way-to-store-last-visited-url-in.html
 */
class Extend_Helpers_RefPage
   {
   

   /**
    */
   public static function save( $url )
      {
      $lastPage = new Zend_Session_Namespace('url_history');
      if( !isset($lastPage->all) )
         {
         $lastPage->all = array();
         }

      // cut any anchor from the url
      if( ($pos = strpos( $url, '#' )) !== false )
         {
         $url = strstr( $url, 0, $pos ); 
         }

      if( $url == '/javascript/fancybox/jquery.easing-1.4.pack.js' )
         return null;

      $lastPage->last = $url;
      $lastPage->all[] = $url;
      }

   /**
    */
   public static function getLastVisited() 
      {
      $lastPage = new Zend_Session_Namespace('url_history');

      if( isset( $lastPage->last ) ) 
         {
         $path = $lastPage->last;
         
         return $path;
         }

      return null;
      }

   public static function getLoggedPages()
      {

      $lastPage = new Zend_Session_Namespace('url_history');
      if( !isset($lastPage->all) )
         {
         $lastPage->all = array();
         }
      return $lastPage->all;
      }
   }


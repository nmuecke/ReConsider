<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
   {

   #
   # init the bootstrap configeration
   #
   protected function _initRegistrySettings()
     {
     //path of configuration file
     $path         = APPLICATION_PATH . '/configs/';
     $contactsFile =  'contacts.ini';
     $messagesFile =  'messages.ini';
     $ei_testFile  =  'ei_test_info.ini';

     //Get the configuration data
     $contacts = new Zend_Config_Ini( $path . $contactsFile );
     $messages = new Zend_Config_Ini( $path . $messagesFile );
     $ei_test  = new Zend_Config_Ini( $path . $ei_testFile );

     //Filter by data by environement value (production, staging, testing, development)
     $contacts = $contacts->get( APPLICATION_ENV );
     //$messages = $messages->get( APPLICATION_ENV );

     // register the ini settins
     Zend_Registry::set( 'contacts', $contacts );
     Zend_Registry::set( 'messages', $messages );
     Zend_Registry::set( 'ei_test', $ei_test );

     }
   #
   # init the session configeration
   #
   protected function _initSession()
     {
     //path of configuration file
     $path = APPLICATION_PATH . '/configs/session.ini';

     //Get the configuration data
     $config = new Zend_Config_Ini($path);

     //Filter by data by environement value (production, staging, testing, development)
     $config = $config->get(APPLICATION_ENV);

      Zend_Session::setOptions( $config->toArray() );

      Zend_Session::start();
      }
 
   #
   # set the document type
   # 
   protected function _initDoctype()
      {
      $this->bootstrap('view');
      $view = $this->getResource('view');
      $view->doctype('XHTML1_STRICT');

      $view->addHelperPath( '../application/layouts/helpers', 'Layout_Helper' );
      }

   protected function _initConfig() 
      {
      //path of configuration file
      $path = APPLICATION_PATH . '/configs/application.ini';

      //Get the configuration data
      $config = new Zend_Config_Ini($path);

      //Filter by data by environement value (production, staging, testing, development)
      $config = $config->get(APPLICATION_ENV);

      //Store data in the registry to access it everywhere
      Zend_Registry::set('config', $config);

      
      }

   protected function _initPlugins()
      {
      $frontController = Zend_Controller_Front::getInstance();
      $frontController->registerPlugin( new Extend_Administration_ACL( ) );
      }
   }


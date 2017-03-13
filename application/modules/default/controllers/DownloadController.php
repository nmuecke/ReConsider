<?php
 
class DownloadController extends Zend_Controller_Action
{
   public function init()
      {
       // track url
       $urlHistory = new Extend_Helpers_RefPage();
       $this->previousPage = $urlHistory->getLastVisited();
       $urlHistory->save( $this->_request->getRequestUri() );
       $this->currentPage  = $urlHistory->getLastVisited();


      }

   public function indexAction()
      {
      $file = $this->getRequest()->getParam( 'file' );
      //$path = $this->view->baseUrl() . "/Download/";
      $path = APPLICATION_PATH . "/../public/Download/";
       
      //  Zend_Debug::Dump( $path.$file, "Requested File" );
      //  Zend_Debug::Dump( APPLICATION_PATH . "/../public/Download/" . $file, "File location" );
      //  Zend_Debug::Dump(file_exists( APPLICATION_PATH . "/../public/Download/" . $file ), "File found" );

      if( file_exists( APPLICATION_PATH . "/../public/Download/" . $file ) )
         {
         $mtype = '';
	 // fileinfo module installed?
	 if ( function_exists( 'finfo_file' )) 
            {
	    $finfo = finfo_open(FILEINFO_MIME); // return mime type
	    $mtype = finfo_file($finfo, $path.$file);
	    finfo_close($finfo); 
  	    }

         //header($mtype);
	 header( 'Content-Disposition: attachment; filename = "' . $file . '"' );
         header("Content-Type: application/force-download");
         header('Content-Description: File Transfer'); 
         readfile( $path.$file );
 
         // disable layout and view
         $this->view->layout()->disableLayout();
         $this->_helper->viewRenderer->setNoRender( true );
         } 
      }

        
}

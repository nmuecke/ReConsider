<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    // track last page
    $urlHistory = new Extend_Helpers_RefPage();
    $urlHistory->save( $this->_request->getRequestUri() );

    }

    public function indexAction()
    {
    
    }



}


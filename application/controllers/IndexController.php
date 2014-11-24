<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Session::start();
    	// initialize session and define current language
    	$oSession = new Zend_Session_Namespace('system_session');
    }

    public function indexAction()
    {
      // action body
    	$layout = $this->_helper->layout()->disableLayout();
    	
    	$oSession = new Zend_Session_Namespace('system_session');
    	 
    	// check if user has logged in
    	     	if ($oSession->username != null) {
    	     		// redirect to login page
    	     		return $this->_helper->redirector('product-list', 'Index');
    	     	}
    	   
    	
    	$aFlashMsg = $this->_helper->FlashMessenger->getMessages ();
    	$this->view->aFlashMsg = $aFlashMsg;
    	// check flash messenger exist
    	if (count ( $aFlashMsg ) > 0) {
    		 
    		// send array to view
    		$this->view->aFlashMsg = $aFlashMsg;
    	}
    }

    public function resetPasswordFormAction()
    {
    // action body
    	$layout = $this->_helper->layout()->disableLayout();
    	$aFlashMsg = $this->_helper->FlashMessenger->getMessages ();
    	$this->view->aFlashMsg = $aFlashMsg;
    	
    	// check flash messenger exist
    	if (count ( $aFlashMsg ) > 0) {
    		 
    		// send array to view
    		$this->view->aFlashMsg = $aFlashMsg;
    	}
    }

    public function changePasswordFormAction()
    {
    // action body
    	$layout = $this->_helper->layout()->disableLayout();
    	// action body
    	$oHttpRequest = $this->getRequest ();
    	//	Zend_Debug::dump ($aFormData);
    	//	die();
    	// check data request (Post)
    	if ($oHttpRequest->isGet () == FALSE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( 'error' );
    		die ();
    	}
    	 
    	$aFormData = $oHttpRequest->getParams ();
    	$this->view->id = $aFormData['id'];
    	
    	$aFlashMsg = $this->_helper->FlashMessenger->getMessages ();
    	$this->view->aFlashMsg = $aFlashMsg;
    	 
    	// check flash messenger exist
    	if (count ( $aFlashMsg ) > 0) {
    		 
    		// send array to view
    		$this->view->aFlashMsg = $aFlashMsg;
    	}
    	
    }

    public function registerAction()
    {
        // action body
        
    	// action body
    	
    	$aFlashMsg = $this->_helper->FlashMessenger->getMessages ();
    	$this->view->aFlashMsg = $aFlashMsg;
    	 
    	// check flash messenger exist
    	if (count ( $aFlashMsg ) > 0) {
    		 
    		// send array to view
    		$this->view->aFlashMsg = $aFlashMsg;
    	}
    }


}








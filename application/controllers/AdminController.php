<?php

class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function unbanAction()
    {
        // action body
    	 
    	// when user press unban from unbanform redirect to this page
    	// update database if valid
    	// change status of user from 1[banned] to 0[not banned]
    	// disabled layout & view
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ( true );
    	 $oHttpRequest = $this->getRequest();
    	// get http request
    	
    	// die();
    	//	Zend_Debug::dump ($aFormData);
    	//	die();
    	// check data request (Post)
     	if ($oHttpRequest->isPost () == FALSE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( 'error' );
    		die ();
    	}
    	 
    	 
    	 
    	try {
    		// aFormData has [bid = buyer id]
    		$aFormData = $oHttpRequest->getParams ();
    		// initialize validate form data
    		//Zend_Debug::dump($aFormData);
    		//die();
    		$oValidateNotEmpty = new Zend_Validate_NotEmpty ( Zend_Validate_NotEmpty::ALL );
    		 
    		// validate empty
    		 
    		// validate condition
    		if (! $oValidateNotEmpty->isValid ( $aFormData ['bid'] ) ) {
    					$aFormData ['flashStatus'] = '901'; // need to adjust
    					$sErrorMsg = 'Data Empty!';
    					
    					throw new Exception ( $sErrorMsg );
    				}
    				
    				
    					
    				//validate bid if the buyer is banned from buyer table
    				$oBuyerTbl = new Application_Model_DbTable_BuyerTbl ();
    				$oSelectBuyer = $oBuyerTbl->select ()->where ( 'uid = ?', $aFormData ['bid'] )
    				->where('status = ?', '1');
    				
    				$oCurrentBuyer = $oBuyerTbl->fetchRow ( $oSelectBuyer );
    				
    				
    				if ($oCurrentBuyer == null) {
    					$aFormData ['flashStatus'] = '897'; // need to adjust
    					$sErrorMsg = 'This user account is not banned!';
    					throw new Exception ( $sErrorMsg );
    				}
    	} catch ( Exception $e ) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( $e->getMessage () );
    		$this->_helper->FlashMessenger ( $aFormData );
    		return $this->_helper->redirector ( 'ban-list', 'admin' ); //need to adjust
    	}
    	 
    	// The input is correct, we are now going to insert feedback into feedback table
    	 
    	// set current datetime format
    	$sCurrentDateTime = Zend_Date::now ()->toString ( 'yyyy-MM-dd HH:mm:ss' );
    	 
    	$aBuyEditData = array ();
    	$aBuyEditData ['status'] = '0';
    	$aBuyEditData ['wrong_amount'] ='0';
    	$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
    	$oDb->beginTransaction ();
    	 
    	try {
    		// create row and insert new data
    		
    		$oBuyerTbl = new Application_Model_DbTable_BuyerTbl ();
    		$oSelectBuyer = $oBuyerTbl->select ()->where ( 'uid = ?', $aFormData ['bid'] )
    		->where('status = ?', '1');
    		$oCurrentBuyer = $oBuyerTbl->fetchAll ( $oSelectBuyer );
    		$oBuyerRow 		= $oBuyerTbl-> getAdapter ()->quoteInto ( 'uid = ?', $aFormData ['bid'] );
    		$iBuyerRowId 	= $oBuyerTbl->update ($aBuyEditData, $oBuyerRow);
    		
    		if (null !== $iBuyerRowId) {
    			 
    			// commit database
    			$oDb->commit ();
    			$oTypeTbl = null;
    			$aFormData ['flashStatus'] = '900'; // need to adjust
    			$this->_helper->FlashMessenger ( $aFormData );
    			return $this->_helper->redirector ( 'index', 'index' ); // need to adjust
    		}
    		else {
    			// throw error
    			throw new Exception ( 'save feedback data error please try again!' );
    			return;
    		}
    	
    	} catch ( Exception $e ) {
    		 
    		// rollback if error happened
    		$oDb->rollBack ();
    		$sMessage = $e->getMessage ();
    		throw new Exception ( $sMessage );
    		return;
    	}
    }

    public function banListAction()
    {
    	try{
    	$oBuyerTbl = new Application_Model_DbTable_BuyerTbl ();
    	$oSelectBuyer = $oBuyerTbl->select ()->where('status = ?', '1');
    	$oCurrentBuyer = $oBuyerTbl->fetchAll ( $oSelectBuyer );
    	$iBuyerCount = $oCurrentBuyer->count();
    	//Zend_Debug::dump($oCurrentBuyer);
    	//Zend_Debug::dump($iBuyerCount);
    	
    	$oUserTbl = new Application_Model_DbTable_UserTbl ();
    	$aBanUserId = array();
    	//$aBanUsername = array();
    	for($i = 0 ; $i < $iBuyerCount ; $i++) {
    		$iUserId = $oCurrentBuyer[$i]['uid'];
    		$aBanUserId[$i][0] = $iUserId;
    		$oSelectUser = $oUserTbl->select ('username')->where ( 'id = ?', $iUserId );
    		$oUser = $oUserTbl->fetchAll ( $oSelectUser );
    		$aBanUserId[$i][1] = $oUser[0]['username'];
    	}
    
    	//Zend_Debug::dump($aBanUserId);
    	//Zend_Debug::dump($aBanUsername);    	
    	//$this->view->aBannedUsername = $aBanUsername;
    	$this->view->aBannedUserId = $aBanUserId;
    	//Zend_Debug::dump($aBanUserId);
    	}catch( Exception $e){
    		//
    	}
    }

    public function complainListAction()
    {
    try {
			$oComplainTbl = new Application_Model_DbTable_ComplainRelationTbl ();
			$oSelectComplain = $oComplainTbl->select ();
			$oAllComplain = $oComplainTbl->fetchAll ( $oSelectComplain );
			$iComplainCount = $oAllComplain->count ();
			
			$oUserTbl = new Application_Model_DbTable_UserTbl ();
			
			$aComplain = array ();
			
			for($i = 0; $i < $iComplainCount; $i ++) {
				
				$oSelectUser = $oUserTbl->select ()->where ( 'id = ?', $oAllComplain [$i] ['buyer_id'] );
				
				$oAllUser = $oUserTbl->fetchAll ( $oSelectUser );
				
				$sBuyerName = $oAllUser [0] ['username'];
				
				$aComplain [$i] [0] = $sBuyerName;
				
				$oSelectedUser = $oUserTbl->select ()->where ( 'id = ?', $oAllComplain [$i] ['seller_id'] );
				
				$oAllUsers = $oUserTbl->fetchAll ( $oSelectedUser );
				
				$sSellerName = $oAllUsers [0] ['username'];
				
				$aComplain [$i] [1] = $sSellerName;
				
				$sReason = $oAllComplain [$i] ['reason'];
				
				$aComplain [$i] [2] = $sReason;
			}
			// Zend_Debug::dump ( $aComplain );
			
			$this->view->aComplainList = $aComplain;
		} catch ( Exception $e ) {
			//
		}
    }


}






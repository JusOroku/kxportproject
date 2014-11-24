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

    public function productListAction()
    {
         // action body
		    	
        //init database table connection
    	$oProductTbl 		= new Application_Model_DbTable_ProductTbl();
    	$oBuyoutProductTbl 	= new Application_Model_DbTable_BuyoutProductTbl();
    	$oBidProductTbl 	= new Application_Model_DbTable_BidProductTbl();
    	
    	//fetch all product data
    	$oSelectAllProductData = $oProductTbl->select ()->where ( 'id > ?', 0 )
													    	->where ( 'deleted <> ?', 1 )
    														->from ( array ('b' => 'product_tbl'), array (
		    			'id',
		    			'product_name',
		    			'seller_id',
		    			'brand',
		    			'quantity',
		    			'properties',
    					'pay_status',
		    			'order_price'
		    	) );
    	$aFetchAllProductData 		= $oProductTbl->fetchAll($oSelectAllProductData);
    	$iAllProductData 			= count($aFetchAllProductData);
    	if($iAllProductData == 0){
    		Zend_Debug::dump("Error : 0");
    	}else{
//     		print("iAllProduct = ".$iAllProductData.'<br>');
    	}
    	$aAllProductData = array();
    	for($i=0;$i<$iAllProductData;$i++){
    		$aAllProductData[$i]['id'] 				= $aFetchAllProductData[$i]['id'];
    		$aAllProductData[$i]['product_name'] 	= $aFetchAllProductData[$i]['product_name'];
    		$aAllProductData[$i]['seller_id'] 		= $aFetchAllProductData[$i]['seller_id'];
    		$aAllProductData[$i]['properties'] 		= $aFetchAllProductData[$i]['properties'];
    		$aAllProductData[$i]['quantity'] 		= $aFetchAllProductData[$i]['quantity'];
    		$aAllProductData[$i]['brand'] 			= $aFetchAllProductData[$i]['brand'];
    		$aAllProductData[$i]['order_price'] 	= $aFetchAllProductData[$i]['order_price'];
    		$aAllProductData[$i]['pay_status'] 		= $aFetchAllProductData[$i]['pay_status'];
    		//check if this product is bid/buyout
    		$oSelectBuyoutProductData 	= $oBuyoutProductTbl->select ()->where ( 'pid = ?', $aFetchAllProductData[$i]['id'] )
    																	->where ( 'deleted <> ?', 1 )
    		->from ( array ('c' => 'buyout_product_tbl'),array (
    				'pid',
    				'price'
    		) );
    		$aFetchBuyoutProductData 	= $oBuyoutProductTbl->fetchAll($oSelectBuyoutProductData);
    		$iBuyoutProductData 		= count($aFetchBuyoutProductData);
    		if($iBuyoutProductData > 1){
    			die("Error : iBuyoutProduct ".$i." Dupplicated");
    		}else if($iBuyoutProductData == 1){
    			$aAllProductData[$i]['saleType'] = 'buyout';
    			$aAllProductData[$i]['showPrice'] = $aFetchBuyoutProductData[0]['price'];
    		}else{
    			$oSelectBidProductData 	= $oBidProductTbl->select ()->where ( 'pid = ?', $aFetchAllProductData[$i]['id'] )
    			->where ( 'deleted <> ?', 1 )
    			->from ( array ('d' => 'bid_product_tbl'),array (
    					'pid',
    					'start_price',
    					'current_price',
    					'current_winner',
    					'finishdate'
    			) );
    			$aFetchBidProductData 		= $oBidProductTbl->fetchAll($oSelectBidProductData);
    			$iBidProductData 			= count($aFetchBidProductData);
    			$aAllProductData[$i]['finishdate'] = $aFetchBidProductData['finishdate'];
    			if($iBidProductData >1){
    				die("Error : iBidProduct ".$i." Dupplicated");
    			}else if($iBidProductData = 1){
    				$aAllProductData[$i]['saleType'] = 'bid';
    				$aAllProductData[$i]['showPrice'] = $aFetchBidProductData[0]['current_price'];
    			}else{
    				die("Unexpected Error");
    			}
    		}
    	}
    	
    	//for check
//     	for($i=0;$i<$iAllProductData;$i++){
//     		Zend_Debug::dump($aAllProductData[$i]);
//     	}
//     	die;
    	for($i=0;$i<$iAllProductData;$i++){
    		if($aAllProductData[$i]['saleType']=='bid'){
	    		$sFinishDate = $aAllProductData[$i]['finishdate'];
	    		$aAllProductData[$i]['finishDate'] = $sFinishDate;
    		}
    	}
    	
    	//set data to view
    	$this->view->oAllProductData 	= $aAllProductData;
    }


}










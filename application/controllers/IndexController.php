<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Session::start();
    	// initialize session and define current language
    	$oSession = new Zend_Session_Namespace('system_session');
    	if($oSession->permission == "admin"){
    	return $this->_helper->redirector('ban-list', 'admin');
    	}
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

    public function buyoutProductListAction()
    {
          // action body
    	$oProductTbl 		= new Application_Model_DbTable_ProductTbl();
    	$oBuyoutProductTbl 	= new Application_Model_DbTable_BuyoutProductTbl();
    	
    	$oSelectBuyoutProductData 	= $oBuyoutProductTbl->select ()	->where ( 'pid > ?', 0 )
															    	->where ( 'deleted <> ?', 1 )
															    	->from ( array ('c' => 'buyout_product_tbl'),array (
															    			'pid',
															    			'price'
															    	) );
    	$aFetchBuyoutProductData 	= $oBuyoutProductTbl->fetchAll($oSelectBuyoutProductData);
    	$iBuyoutProductData 		= count($aFetchBuyoutProductData);
//     	Zend_Debug::dump($aFetchBuyoutProductData);
		$aBuyoutProductData = array();
    	for($i=0;$i<$iBuyoutProductData;$i++){
    		$aBuyoutProductData[$i]['pid'] 				= $aFetchBuyoutProductData[$i]['pid'];
    		$aBuyoutProductData[$i]['price'] 			= $aFetchBuyoutProductData[$i]['price'];
    		$aBuyoutProductData[$i]['showPrice']		= $aFetchBuyoutProductData[$i]['price'];
    	}
    	for($i=0;$i<$iBuyoutProductData;$i++){
    		$oSelectProductData 	= $oProductTbl->select ()	->where ( 'id = ?', $aBuyoutProductData[$i]['pid'] )
    															->where ( 'deleted <> ?', 1 )
												    		->from ( array ('c' => 'product_tbl'),array (
												    				'id',
													    			'product_name',
													    			'seller_id',
													    			'brand',
													    			'quantity',
													    			'properties',
												    				'pay_status',
													    			'order_price'
    		) );
    		$aFetchProductData 	= $oProductTbl->fetchAll($oSelectProductData);
//     		Zend_Debug::dump($aFetchProductData);
    		$aBuyoutProductData[$i]['id'] 			= $aFetchProductData[0]['id'];
			$aBuyoutProductData[$i]['product_name'] = $aFetchProductData[0]['product_name'];
			$aBuyoutProductData[$i]['seller_id'] 	= $aFetchProductData[0]['seller_id'];
			$aBuyoutProductData[$i]['brand'] 		= $aFetchProductData[0]['brand'];
			$aBuyoutProductData[$i]['quantity'] 	= $aFetchProductData[0]['quantity'];
			$aBuyoutProductData[$i]['properties'] 	= $aFetchProductData[0]['properties'];
			$aBuyoutProductData[$i]['order_price'] 	= $aFetchProductData[0]['order_price'];
			$aBuyoutProductData[$i]['pay_status'] 	= $aFetchProductData[0]['pay_status'];
    	}
//     	Zend_Debug::dump($aBuyoutProductData);
//     	die;
		//set data to view
    	$this->view->oAllProductData 	= $aBuyoutProductData;
    }

    public function bidProductListAction()
    {
        // action body
    	// action body
    	$oProductTbl 		= new Application_Model_DbTable_ProductTbl();
    	$oBidProductTbl 	= new Application_Model_DbTable_BidProductTbl();
    	 
    	$oSelectBidProductData 	= $oBidProductTbl->select ()		->where ( 'pid > ?', 0 )
															    	->where ( 'deleted <> ?', 1 )
															    	->from ( array ('c' => 'bid_product_tbl'),array (
															    			'pid',
															    			'current_price',
															    			'current_maxbid_price',
															    			'finishdate',
															    			'current_winner',
															    			'start_price'
															    	) );
    	$aFetchBidProductData 		= $oBidProductTbl->fetchAll($oSelectBidProductData);
    	$iBidProductData 			= count($aFetchBidProductData);
    	//     	Zend_Debug::dump($aFetchBuyoutProductData);
    	$aBidProductData = array();
    	for($i=0;$i<$iBidProductData;$i++){
    		$aBidProductData[$i]['pid'] 				= $aFetchBidProductData[$i]['pid'];
    		$aBidProductData[$i]['price'] 				= $aFetchBidProductData[$i]['current_price'];
    		$aBidProductData[$i]['showPrice']			= $aFetchBidProductData[$i]['current_price'];
    		$aBidProductData[$i]['start_price']			= $aFetchBidProductData[$i]['start_price'];
    		$aBidProductData[$i]['finishdate']			= $aFetchBidProductData[$i]['finishdate'];
    	}
    	for($i=0;$i<$iBidProductData;$i++){
    		$oSelectProductData 	= $oProductTbl->select ()	->where ( 'id = ?', $aBidProductData[$i]['pid'] )
    		->where ( 'deleted <> ?', 1 )
    		->from ( array ('c' => 'product_tbl'),array (
    				'id',
    				'product_name',
    				'seller_id',
    				'brand',
    				'quantity',
    				'properties',
    				'pay_status',
    				'order_price'
    		) );
    		$aFetchProductData 	= $oProductTbl->fetchAll($oSelectProductData);
    		//     		Zend_Debug::dump($aFetchProductData);
    		$aBidProductData[$i]['id'] 				= $aFetchProductData[0]['id'];
    		$aBidProductData[$i]['product_name'] 	= $aFetchProductData[0]['product_name'];
    		$aBidProductData[$i]['seller_id'] 		= $aFetchProductData[0]['seller_id'];
    		$aBidProductData[$i]['brand'] 			= $aFetchProductData[0]['brand'];
    		$aBidProductData[$i]['quantity'] 		= $aFetchProductData[0]['quantity'];
    		$aBidProductData[$i]['properties'] 		= $aFetchProductData[0]['properties'];
    		$aBidProductData[$i]['order_price'] 	= $aFetchProductData[0]['order_price'];
    		$aBidProductData[$i]['pay_status'] 	= $aFetchProductData[0]['pay_status'];
    	}
//     	    	Zend_Debug::dump($aBuyoutProductData);
//     	    	die;
    	//set data to view
    	$this->view->oAllProductData 	= $aBidProductData;
    }


}














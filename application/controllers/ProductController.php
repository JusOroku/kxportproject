<?php

class ProductController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function productBuyoutAction()
    {
        // action body
    	$oHttpRequest = $this->getRequest ();
    	// check data request (Post)
    	if ($oHttpRequest->isGet () == TRUE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( "Error : Can't use 'GET'" );
    		die ();
    	}
    	$sInput = $oHttpRequest->getParams ();
//     	print_r($sInput);
    	
    	$sProductId = $sInput['id'];
    	$oBuyProductTbl = new Application_Model_DbTable_BuyoutProductTbl ();
    	$oSelectProduct = $oBuyProductTbl->select ()->where ( 'pid = ?', $sProductId );
    	$oProduct = $oBuyProductTbl->fetchAll ( $oSelectProduct );
    	$this->view->sPrice = $oProduct [0] ['price'];
    	
    		
    	$oProductTbl = new Application_Model_DbTable_ProductTbl ();
    	$oSelectedProduct = $oProductTbl->select ()->where ( 'id = ?', $sProductId );
    	$oProducts = $oProductTbl->fetchAll ( $oSelectedProduct );
    	$this->view->iProductId = $sProductId;
    	$this->view->sProductName = $oProducts [0] ['product_name'];
    	$this->view->pProductImage = $oProducts [0] ['product_image'];
    	$this->view->sBrand = $oProducts [0] ['brand'];
    	$this->view->sModel = $oProducts [0] ['model'];
    	$this->view->sProperty = $oProducts [0] ['properties'];
    	$this->view->sSize = $oProducts [0] ['size'];
    	$this->view->sQuantity = $oProducts [0] ['quantity'];
    	$this->view->sPacking = $oProducts [0] ['packing_method'];
    	$this->view->sShipping = $oProducts [0] ['shipping_method'];
    	$this->view->sReturn = $oProducts [0] ['return_policy'];
    	$this->view->sCondition = $oProducts [0] ['condition'];
    		
    	$sSellerId = $oProducts [0] ['seller_id'];
    		
    	$oUserTbl = new Application_Model_DbTable_UserTbl ();
    	$oSelectSeller = $oUserTbl->select ()->where ( 'id = ?', $sSellerId );
    	$oSeller = $oUserTbl->fetchAll ( $oSelectSeller );
    		
    	$this->view->sSellerName = $oSeller [0] ['username'];
    }

    public function productBidAction()
    {
         // action body
    	$oHttpRequest = $this->getRequest ();
    	// check data request (Post)
    	if ($oHttpRequest->isGet () == TRUE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( "Error : Can't use 'GET'" );
    		die ();
    	}
    	$sInput = $oHttpRequest->getParams ();
//     	print_r($sInput);
    	
    	$sProductId = $sInput ['id'];
    	$oBidProductTbl = new Application_Model_DbTable_BidProductTbl();
    	$oSelectProduct = $oBidProductTbl->select ()->where ( 'pid = ?', $sProductId );
    	$oProduct = $oBidProductTbl->fetchAll ( $oSelectProduct );
    	$this->view->sCurrentPrice = $oProduct [0] ['current_price'];
    	$this->view->sFinishDate = $oProduct [0] ['finishdate'];
    		
    	$oProductTbl = new Application_Model_DbTable_ProductTbl ();
    	$oSelectedProduct = $oProductTbl->select ()->where ( 'id = ?', $sProductId );
    	$oProducts = $oProductTbl->fetchAll ( $oSelectedProduct );
    	$this->view->iProductId = $sProductId;
    	$this->view->sProductName = $oProducts [0] ['product_name'];
    	$this->view->pProductImage = $oProducts [0] ['product_image'];
    	$this->view->sBrand = $oProducts [0] ['brand'];
    	$this->view->sModel = $oProducts [0] ['model'];
    	$this->view->sProperty = $oProducts [0] ['properties'];
    	$this->view->sSize = $oProducts [0] ['size'];
    	$this->view->sQuantity = $oProducts [0] ['quantity'];
    	$this->view->sPacking = $oProducts [0] ['packing_method'];
    	$this->view->sShipping = $oProducts [0] ['shipping_method'];
    	$this->view->sReturn = $oProducts [0] ['return_policy'];
    	$this->view->sCondition = $oProducts [0] ['condition'];
    		
    	$sSellerId = $oProducts [0] ['seller_id'];
    		
    	$oUserTbl = new Application_Model_DbTable_UserTbl ();
    	$oSelectSeller = $oUserTbl->select ()->where ( 'id = ?', $sSellerId );
    	$oSeller = $oUserTbl->fetchAll ( $oSelectSeller );
    	$this->view->sSellerName = $oSeller [0] ['username'];
    	
    }

    public function searchProductAction()
    {
         // action body
    	$oHttpRequest = $this->getRequest ();
    	// check data request (Post)
    	if ($oHttpRequest->isGet () == TRUE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( "Error : Can't use 'GET'" );
    		die ();
    	}
    	$sInput = $oHttpRequest->getParams ();
//     	print_r($sInput['sSearchName']);
		$sSearchInput = $sInput['sSearchName'];
		
    	$oProductTbl 			= new Application_Model_DbTable_ProductTbl();
    	$oBuyoutProductTbl 		= new Application_Model_DbTable_BuyoutProductTbl();
    	$oBidProductTbl 		= new Application_Model_DbTable_BidProductTbl();
    	
    	$oSelectAllProductData 	= $oProductTbl->select ()->where ( 'id > ?', 0 )
    	-> where ( 'deleted <> ?', 1 )
    	-> where ('product_name LIKE ?','%'.$sSearchInput.'%')
    	->from ( array ('b' => 'product_tbl'), array (
    			'id',
    			'product_name',
    			'seller_id',
    			'brand',
    			'quantity',
    			'properties',
    			'order_price'
    	) );
    	$aFetchAllProductData 		= $oProductTbl->fetchAll($oSelectAllProductData);
    	$iAllProductData 			= count($aFetchAllProductData);
// 		print("iAllProductData = ".$iAllProductData);
    	$aAllProductData = array();
    	for($i=0;$i<$iAllProductData;$i++){
    		$aAllProductData[$i]['id'] 				= $aFetchAllProductData[$i]['id'];
    		$aAllProductData[$i]['product_name'] 	= $aFetchAllProductData[$i]['product_name'];
    		$aAllProductData[$i]['seller_id'] 		= $aFetchAllProductData[$i]['seller_id'];
    		$aAllProductData[$i]['properties'] 		= $aFetchAllProductData[$i]['properties'];
    		$aAllProductData[$i]['quantity'] 		= $aFetchAllProductData[$i]['quantity'];
    		$aAllProductData[$i]['brand'] 			= $aFetchAllProductData[$i]['brand'];
    		$aAllProductData[$i]['order_price'] 	= $aFetchAllProductData[$i]['order_price'];
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
    			}else{
    				die("Unexpected Error");
    			}
    		}
    	}
    	 
//     	//for check
//     	    	for($i=0;$i<$iAllProductData;$i++){
//     	    		Zend_Debug::dump($aAllProductData[$i]);
//     	    	}
//     	    	die;
    	//set data to view
    	$this->view->oAllProductData = $aAllProductData;
    	$this->view->sSearchProduct = $sSearchInput;
    }

    public function buyAction()
    {
         // action body
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ( true );
    	 
    	/*get http request*/
    	$oHttpRequest = $this->getRequest ();
    	 
    	// check data request (Post)
    	if ($oHttpRequest->isGet () == TRUE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( 'get error' );
    		die ();
    	}
    	 
    	$aProduct = $oHttpRequest->getParams ();
//     	print_r($aProduct);
    	$iId 				= $aProduct['id'];
    	$sCurrentDateTime 	= Zend_Date::now ()->toString ( 'yyyy-MM-dd HH:mm:ss' );
    	 
    	$aProductSaveData = array();
    	$aProductSaveData['pay_status'] 	= 1;
    	$aProductSaveData['updatedate'] 	= $sCurrentDateTime;
    	 
    	$oProductTbl 	= new Application_Model_DbTable_ProductTbl();
    	$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
    	$oDb->beginTransaction ();
    	
    	try {
    		/*update row*/
    		$oProductRow 	= $oProductTbl-> getAdapter ()->quoteInto ( 'id = ?', $iId );
    		$iProductRowId = $oProductTbl->update ($aProductSaveData, $oProductRow);
    		 
    		/*if all successful commit to DB*/
    		if (null !== $iProductRowId) {
    	
    			/*commit database*/
    			$oDb->commit ();
    		} else {
    			/*throw error*/
    			throw new Exception ( 'buy product error please try again!' );
    			return;
    		}
    	} catch ( Exception $e ) {
    		/*rollback if error happened*/
    		$oDb->rollBack ();
    		$sMessage = $e->getMessage ();
    		throw new Exception ( $sMessage );
    		return;
    	}
    	Zend_Debug::dump("Success!");
    	die;
    }

    public function bidAction()
    {
    // action body
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ( true );
    	
    	/*get http request*/
    	$oHttpRequest = $this->getRequest ();
    	
    	// check data request (Post)
    	if ($oHttpRequest->isGet () == TRUE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( 'get error' );
    		die ();
    	}
    	
    	$aProduct = $oHttpRequest->getParams ();
//     	print_r($aProduct);
//     	die;
    	$iId 				= $aProduct['id'];
    	$ibidCurrent 		= $aProduct['bidCurrent'];
    	if(!is_numeric($ibidCurrent)){
    		die("number only");
    	}
    	
    	$oBidProductTbl 	= new Application_Model_DbTable_BidProductTbl();
    	$oSelectBidProductData 	= $oBidProductTbl->select ()->where ( 'pid = ?', $iId )
    														->where ( 'deleted <> ?', 1 )
    	->from ( array ('d' => 'bid_product_tbl'),array (
    			'pid',
    			'start_price',
    			'current_price',
    			'current_winner',
    			'current_maxbid_price',
    			'finishdate'
    	) );
    	$aFetchBidProductData 		= $oBidProductTbl->fetchAll($oSelectBidProductData);
    	$iBidProductData 			= count($aFetchBidProductData);
//     	Zend_Debug::dump($aFetchBidProductData);
//     	die;
		$iAdd = 0;
		if($aFetchBidProductData[0]['current_price']<=1){
			$iAdd = 0.05;
		}else if($aFetchBidProductData[0]['current_price']<=5){
			$iAdd = 0.1;
		}else if($aFetchBidProductData[0]['current_price']<=10){
			$iAdd = 0.25;
		}else if($aFetchBidProductData[0]['current_price']<=50){
			$iAdd = 0.5;
		}else{
			$iAdd = 5;
		}
    	
    	
		if($aFetchBidProductData[0]['current_price']+$iAdd>=$ibidCurrent){
			die("lower");
		}else{
			$sCurrentDateTime 	= Zend_Date::now ()->toString ( 'yyyy-MM-dd HH:mm:ss' );
			 
			$aProductSaveData = array();
			if($ibidCurrent>$aFetchBidProductData[0]['current_maxbid_price']){
			$aProductSaveData['current_price'] 			= $ibidCurrent;
			$aProductSaveData['current_maxbid_price'] 	= $ibidCurrent;
			$aProductSaveData['updatedate'] 			= $sCurrentDateTime;
			}else if($ibidCurrent>$aFetchBidProductData[0]['current_price']+$iAdd){
			$aProductSaveData['current_price'] 			= $ibidCurrent;
			$aProductSaveData['updatedate'] 			= $sCurrentDateTime;
			}
			 
// 			$oProductTbl 	= new Application_Model_DbTable_ProductTbl();
			$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$oDb->beginTransaction ();
			
			try {
				/*update row*/
				$oBidProductRow 	= $oBidProductTbl-> getAdapter ()->quoteInto ( 'pid = ?', $iId );
				$iBidProductRowId 	= $oBidProductTbl->update ($aProductSaveData, $oBidProductRow);
				 
				/*if all successful commit to DB*/
				if (null !== $iBidProductRowId) {
			
					/*commit database*/
					$oDb->commit ();
				} else {
					/*throw error*/
					throw new Exception ( 'bid product error please try again!' );
					return;
				}
			} catch ( Exception $e ) {
				/*rollback if error happened*/
				$oDb->rollBack ();
				$sMessage = $e->getMessage ();
				throw new Exception ( $sMessage );
				return;
			}
			Zend_Debug::dump("success!");
			die;
		}
    }

    public function bidAutoAction()
    {
       // action body
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ( true );
    	
    	/*get http request*/
    	$oHttpRequest = $this->getRequest ();
    	
    	// check data request (Post)
    	if ($oHttpRequest->isGet () == TRUE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( 'get error' );
    		die ();
    	}
    	
    	$aProduct = $oHttpRequest->getParams ();
//     	print_r($aProduct);
//     	die;
    	$iId 				= $aProduct['id'];
    	$ibidCurrent 		= $aProduct['bidCurrent'];
    	if(!is_numeric($ibidCurrent)){
    		die("number only");
    	}
    	
    	$oBidProductTbl 	= new Application_Model_DbTable_BidProductTbl();
    	$oSelectBidProductData 	= $oBidProductTbl->select ()->where ( 'pid = ?', $iId )
    														->where ( 'deleted <> ?', 1 )
    	->from ( array ('d' => 'bid_product_tbl'),array (
    			'pid',
    			'start_price',
    			'current_price',
    			'current_winner',
    			'current_maxbid_price',
    			'finishdate'
    	) );
    	$aFetchBidProductData 		= $oBidProductTbl->fetchAll($oSelectBidProductData);
    	$iBidProductData 			= count($aFetchBidProductData);
//     	Zend_Debug::dump($aFetchBidProductData);
//     	die;

    	$iAdd = 0;
    	if($aFetchBidProductData[0]['current_price']<=1){
    		$iAdd = 0.05;
    	}else if($aFetchBidProductData[0]['current_price']<=5){
    		$iAdd = 0.1;
    	}else if($aFetchBidProductData[0]['current_price']<=10){
    		$iAdd = 0.25;
    	}else if($aFetchBidProductData[0]['current_price']<=50){
    		$iAdd = 0.5;
    	}else{
    		$iAdd = 5;
    	}
    	
		if($aFetchBidProductData[0]['current_price']>$ibidCurrent){
			die("lower");
		}else if($aFetchBidProductData[0]['current_maxbid_price']<$ibidCurrent){
			$sCurrentDateTime 	= Zend_Date::now ()->toString ( 'yyyy-MM-dd HH:mm:ss' );
			$aProductSaveData = array();
			$aProductSaveData['current_maxbid_price'] 	= $ibidCurrent;
			$aProductSaveData['current_price'] 	= $aFetchBidProductData[0]['current_price']+$iAdd;
			$aProductSaveData['updatedate'] 			= $sCurrentDateTime;
		}else{
			die("still not max");
		}
// 			$oProductTbl 	= new Application_Model_DbTable_ProductTbl();
			$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
			$oDb->beginTransaction ();
			
			try {
				/*update row*/
				$oBidProductRow 	= $oBidProductTbl-> getAdapter ()->quoteInto ( 'pid = ?', $iId );
				$iBidProductRowId 	= $oBidProductTbl->update ($aProductSaveData, $oBidProductRow);
				 
				/*if all successful commit to DB*/
				if (null !== $iBidProductRowId) {
			
					/*commit database*/
					$oDb->commit ();
				} else {
					/*throw error*/
					throw new Exception ( 'bid product error please try again!' );
					return;
				}
			} catch ( Exception $e ) {
				/*rollback if error happened*/
				$oDb->rollBack ();
				$sMessage = $e->getMessage ();
				throw new Exception ( $sMessage );
				return;
			}
			Zend_Debug::dump("success!");
			die;
    }


}














<?php

class BuyerController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function editProfileAction()
    {
     // action body
        
    	//get id from session
    	$oSession = new Zend_Session_Namespace('system_session');
    	$userId = $oSession->userId;
    	
    	$oUserTbl = new Application_Model_DbTable_UserTbl ();
    	$oSelect = $oUserTbl->select ()->where ( 'id = ?', $userId );
    	$oCurrentUser = $oUserTbl->fetchRow ( $oSelect );
    	
    	$sFirstName = $oCurrentUser->firstname;
    	$sLastName  = $oCurrentUser->lastname;
    	$sAddress   = $oCurrentUser->Address;
    	$sEmail = $oCurrentUser->email;
    	$sTelNumber = $oCurrentUser->Tel;
    	
    	$this->view->sFirstName = $sFirstName;
    	$this->view->sLastName = $sLastName;
    	$this->view->sAddress = $sAddress;
    	$this->view->sEmail = $sEmail;
    	$this->view->sTelNumber = $sTelNumber;
    	 
    	

    	$aFlashMsg = $this->_helper->FlashMessenger->getMessages ();
    	 
    	// check flash messenger exist
    	if (count ( $aFlashMsg ) > 0) {
    		 
    		// send array to view
    		$this->view->aFlashMsg = $aFlashMsg;
    	}
    	
    }

    public function paymentAction()
    {
    	
    	$aFlashMsg = $this->_helper->FlashMessenger->getMessages ();
    	$this->view->aFlashMsg = $aFlashMsg;
    // $oHttpRequest = $this->getRequest ();
    
    	
		try {
			// $aFormData = $oHttpRequest->getParams ();
			// $aFormData should getparam();
			
			$oSession = new Zend_Session_Namespace('system_session');
			$userId = $oSession->userId;
			$aFormData = $oSession->userId;
			
			$oBidRelationTbl = new Application_Model_DbTable_BidRelationTbl ();
			$oSelectBidRelation = $oBidRelationTbl->select ()->where ( 'bid = ?', $aFormData );
			$oCurrentBidRelation = $oBidRelationTbl->fetchAll ( $oSelectBidRelation );
			$sBidProductCount = $oCurrentBidRelation->count ();
			
			$oBuyoutRelationTbl = new Application_Model_DbTable_BuyoutRelationTbl ();
			$oSelectBuyoutRelation = $oBuyoutRelationTbl->select ()->where ( 'bid = ?', $aFormData );
			$oCurrentBuyoutRelation = $oBuyoutRelationTbl->fetchAll ( $oSelectBuyoutRelation );
			$sBuyProductCount = $oCurrentBuyoutRelation->count ();
			
			$aProductList = array ();
			
			// Product Table Buyout
			
			$oProductTbl = new Application_Model_DbTable_ProductTbl ();
			$oBuyProductTbl = new Application_Model_DBTable_BuyoutProductTbl ();
			
			// Buyout
			$j = 0;
			if ($sBuyProductCount != 0) {
				
				// loop Buyout
				for($i = 0; $i < $sBuyProductCount; $i ++) {
					$oSelectedProductBuy = $oProductTbl->select ()->where ( 'id = ?', $oCurrentBuyoutRelation [$i] ['bpid'] );
					$oProductBuy = $oProductTbl->fetchAll ( $oSelectedProductBuy );
					
					// Buyout Table
					
					$oSelectBuyProduct = $oBuyProductTbl->select ()->where ( 'pid = ?', $oCurrentBuyoutRelation [$i] ['bpid'] );
					$oBuyProduct = $oBuyProductTbl->fetchAll ( $oSelectBuyProduct );
					
					if ($oProductBuy [$i] ['pay_status'] == "1") {
						// getData buyout
						$aProductList [$j] = array ();
						$aProductList [$j] [0] = $oProductBuy [$i] ['product_name'];
						$aProductList [$j] [1] = $oProductBuy [$i] ['product_image'];
						$aProductList [$j] [2] = $oProductBuy [$i] ['quantity'];
						$aProductList [$j] [3] = $oBuyProduct [$i] ['price'];
						$aProductList [$j] [4] = $oProductBuy [$i] ['id'];
						$j ++;
					}
				}
			}
			
			if ($sBidProductCount != 0) {
				
				// loop Bid
				for($i = $sBuyProductCount; $i < $sBuyProductCount + $sBidProductCount; $i ++) {
					
					$oSelectedProductBid = $oProductTbl->select ()->where ( 'id = ?', $oCurrentBidRelation [$i - $sBuyProductCount] ['biid'] );
					$oProductBid = $oProductTbl->fetchAll ( $oSelectedProductBid );
					
					// Bid Table
					$oBidProductTbl = new Application_Model_DBTable_BidProductTbl ();
					$oSelectBidProduct = $oBidProductTbl->select ()->where ( 'pid = ?', $oCurrentBidRelation [$i - $sBuyProductCount] ['biid'] );
					$oBidProduct = $oBidProductTbl->fetchAll ( $oSelectBidProduct );
					
					// get data bid
					if ($oProductBid [$i - $sBuyProductCount] ['pay_status'] == "1") {
						$aProductList [$j] = array ();
						$aProductList [$j] [0] = $oProductBid [$i - $sBuyProductCount] ['product_name'];
						$aProductList [$j] [1] = $oProductBid [$i - $sBuyProductCount] ['product_image'];
						$aProductList [$j] [2] = $oProductBid [$i - $sBuyProductCount] ['quantity'];
						$aProductList [$j] [3] = $oBidProduct [$i - $sBuyProductCount] ['current_price'];
						$aProductList [$j] [4] = $oProductBid [$i - $sBuyProductCount] ['id'];
						$j ++;
					}
				}
			}
			
			$this->view->aData = $aProductList;
			Zend_Debug::dump ( $this->view->aData );
		} catch ( Exception $e ) {
		}
    }

    public function paymentClearAction()
    {
       $oHttpRequest = $this->getRequest ();		
		$aFormData = $oHttpRequest->getParams ();
		
		$oProductTbl = new Application_Model_DbTable_ProductTbl ();
		
		
		$aProductData = array();
		$aProductData['pay_status'] = 2;
		$oSelectBatchData 	= $oProductTbl->select ('id','pay_status')
		->where ( 'id = ?',$aFormData['id']) ;
		$oBatchData 			= $oProductTbl->fetchAll ( $oSelectBatchData );
			
		$oBatchRow 		= $oProductTbl-> getAdapter ()->quoteInto ( 'id = ?', $aFormData['id'] );
		 
		$iBatchRowId 	= $oProductTbl->update ($aProductData, $oBatchRow);
		
		
		//send email
		$aFlashMsg = $this->_helper->FlashMessenger->getMessages ();
		require_once ( 'C:\xampp\php\PHPMailer-master\PHPMailerAutoload.php' ); // Add the path as appropriate
		$Mail = new PHPMailer();
		$Mail->IsSMTP(); // Use SMTP
		$Mail->Host        = "smtp.gmail.com"; // Sets SMTP server
		$Mail->SMTPDebug   = 2; // 2 to enable SMTP debug information
		$Mail->SMTPAuth    = TRUE; // enable SMTP authentication
		$Mail->SMTPSecure  = "tls"; //Secure conection
		$Mail->Port        = 587; // set the SMTP port
		$Mail->Username    = 'kxportproject@gmail.com'; // SMTP account username
		$Mail->Password    = 'kxportproject1234'; // SMTP account password
		$Mail->Priority    = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
		$Mail->CharSet     = 'UTF-8';
		$Mail->Encoding    = '8bit';
		$Mail->Subject     = 'Pay Product Summary';
		$Mail->ContentType = 'text/html; charset=utf-8\r\n';
		$Mail->From        = 'kxportproject@gmail.com';
		$Mail->FromName    = 'kxport system';
		$Mail->WordWrap    = 900; // RFC 2822 Compliant for Max 998 characters per line
		$ToEmail = 'kxportproject@gmail.com';
		$MessageHTML = "You completed your purchase on product id " . $aFormData['id'] .
				 " Please give your feedback to seller by clicking on this link <a href='http://localhost:2222/buyer/feedback?id=" . $aFormData['id'] . "'>click</a>";
		$MessageTEXT = "You completed your purchase on product id " . $aFormData['id'] .
				 " Please give your feedback to seller by clicking on this link <a href='http://localhost:2222/buyer/feedback?id=" . $aFormData['id'] . "'>click</a>";
		$Mail->AddAddress( $ToEmail ); // To:
		$Mail->isHTML( TRUE );
		$Mail->Body    = $MessageHTML;
		$Mail->AltBody = $MessageTEXT;
		$Mail->Send();
		$Mail->SmtpClose();
		 
		if ( $Mail->IsError() ) {
			echo "ERROR<br /><br />";
		}
		else {
			echo "OK<br /><br />";
		}
		
		//send email
		$aSendData = array('status' => 900);
		 $this->_helper->FlashMessenger($aSendData);
		 Zend_Debug::dump($aSendData);
	//	 die();
		 return $this->_helper->redirector ( 'payment', 'buyer' );
    }

    public function complainAction()
    {
    	$aFlashMsg = $this->_helper->FlashMessenger->getMessages ();
    	$this->view->aFlashMsg = $aFlashMsg;
    }

    public function complainBackendAction()
    {
        // action body
    	$oHttpRequest = $this->getRequest ();
    	
    	$aFormData = $oHttpRequest->getParams ();
    	
    	// get sellerID
    	$oUserTbl = new Application_Model_DbTable_UserTbl ();
    	$oSellerTbl = new Application_Model_DbTable_SellerTbl();
    	$oSelectedUser = $oUserTbl->select ()->where ( 'username = ?', $aFormData ['username'] );
    	$sUserID = $oUserTbl->fetchAll ( $oSelectedUser );
    	$iCountUser = $sUserID->count();
    	if ($iCountUser == 0) {
    		$aFormData['status'] = 901;
    		$this->_helper->FlashMessenger($aFormData);
    			
    		return $this->_helper->redirector ( 'complain', 'buyer' );
    	}
    	
    	$oSelectedSeller = $oSellerTbl->select ()->where ( 'uid = ?', $sUserID[0]['id'] );
    	$sSellerID = $oUserTbl->fetchAll ( $oSelectedSeller );
    	$iCountSeller = $sSellerID->count();
    	
    	
    	if ($iCountSeller == 0) {
    		$aFormData['status'] = 901;
    		$this->_helper->FlashMessenger($aFormData);
    		return $this->_helper->redirector ( 'complain', 'buyer' );
    	}
    	
    	$oSession = new Zend_Session_Namespace('system_session');
    	
    	
    	$aComplainData = array ();
    	// $sCurrentDateTime = Zend_Date::now ()->toString ( 'yyyy-MM-dd HH:mm:ss' );
    	
    	// getSession buyerID from login
    	$aComplainData ['buyer_id'] = $oSession->userId;
    	$aComplainData ['seller_id'] = $sSellerID [0] ['uid'];
    	$aComplainData ['reason'] = $aFormData ['reason'];
    	// $aComplainData ['createdate'] = $sCurrentDateTime;
    	// $aComplainData ['updatedate'] = $sCurrentDateTime;
    	$aComplainData ['deleted'] = 0;
    	
    	$oComplainTbl = new Application_Model_DbTable_ComplainRelationTbl ();
    	
    	// open transaction
    	$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
    	$oDb->beginTransaction ();
    	
    	// create row and insert new data
    	$oComplainRow = $oComplainTbl->createRow ();
    	$oComplainRow->setFromArray ( $aComplainData );
    	// Zend_Debug::dump($oComplainRow);
    	$iComplainClear = $oComplainRow->save ();
    	
    	if (null !== $iComplainClear) {
    		$oDb->commit ();
    		$aFormData['status'] = 900;
    		$this->_helper->FlashMessenger($aFormData);
    		return $this->_helper->redirector ( 'complain', 'buyer' );
    	
    }
    }


}












<?php

class SystemController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function loginAction()
    {
     // action body
    	$layout = $this->_helper->layout()->disableLayout();
    	
    	
    	$oHttpRequest = $this->getRequest ();
    	
    	if ($oHttpRequest->isPost () == FALSE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( 'error' );
    		die ();
    	}
    	
    	
    	if ($oHttpRequest->isPost()) {
    		 
    		// set form data to array
    		$aFormData = $oHttpRequest->getParams();
    		 
    		// check data form
    		if (empty($aFormData['sUsername']) || empty($aFormData['sPassword'])) {
    			 
    			// return form data via flash messenger
    			$aFormData['status'] = 901;
    			$this->_helper->FlashMessenger($aFormData);
    			// go to privous page
    			return $this->_helper->redirector('index', 'index');
    		}
    		 
    		// initialize control panel table
    		$oUserTbl = new Application_Model_DbTable_UserTbl();
    		
    		// validate data table exist
    		$oSelect = $oUserTbl->select()
    		->where('username = ?', $aFormData['sUsername'])
    		->where('password = ?', $aFormData['sPassword']);
    		$oCurrentUser = $oUserTbl->fetchRow($oSelect);
    			
    		// count array variable
    		$iUsersFound = count($oCurrentUser);
    	//	Zend_Debug::dump($oCurrentUser);
    	
    		
    		//username not found case
    		if(($iUsersFound == 0)||($oCurrentUser->deleted == 1)){
    				
    			//returning to first page without making any log.
    			$aFormData['status'] = 3;
    			$this->_helper->FlashMessenger($aFormData);
    			return $this->_helper->redirector('index', 'index');
    		
    		}
    		
    		
    		//username and password both exist
    		
    		else if (($iUsersFound == 1)
    		AND (0 == $oCurrentUser->activate_status)
    		AND ($oCurrentUser !== null)
    		AND (0 == $oCurrentUser->deleted)){
    			
    			$aFormData['status'] = 5;
    			$this->_helper->FlashMessenger($aFormData);
    			// go to privous page
    			return $this->_helper->redirector('index', 'index');
    			
    			
    		}
    		else if (($iUsersFound == 1)
    		AND ($oCurrentUser !== null)
    		AND (0 == $oCurrentUser->deleted)) {

    		//check id status	
    		$oBuyerTbl = new Application_Model_DbTable_BuyerTbl();	
    		$oSellerTbl = new Application_Model_DbTable_SellerTbl();
    		$oAdminTbl = new Application_Model_DbTable_AdminTbl();
    		
    		$oSelect = $oBuyerTbl->select()
    		->where('uid = ?', $oCurrentUser->id);
    		$oCurrentBuyer = $oBuyerTbl->fetchRow($oSelect);
    		 
    		// count array variable
    		$iBuyerFound = count($oCurrentBuyer);
    		 
    		
    		$oSelect = $oSellerTbl->select()
    		->where('uid = ?', $oCurrentUser->id);
    		$oCurrentSeller = $oSellerTbl->fetchRow($oSelect);
    		 
    		// count array variable
    		$iSellerFound = count($oCurrentSeller);
    		
    		$oSelect = $oAdminTbl->select()
    		->where('uid = ?', $oCurrentUser->id);
    		$oCurrentAdmin = $oAdminTbl->fetchRow($oSelect);
    		 
    		// count array variable
    		$iAdminFound = count($oCurrentAdmin);
    		
    		$permissionStatus;
    		if($iAdminFound > 0) {
    			$permissionStatus = "admin";
    		}
    		
    		else if ($iBuyerFound > 0) {
    			$permissionStatus = "buyer";
    			if($oCurrentBuyer->status != 0) {
    				//banned
    				$aFormData['status'] = 999;
    				$this->_helper->FlashMessenger($aFormData);
    				// go to privous page
    				return $this->_helper->redirector('index', 'index');
    			}
    		}
    		else if ($iSellerFound > 0) {
    			$permissionStatus = "seller";
    		}
    		
    		
    		
    		
    		$oSession = new Zend_Session_Namespace('system_session');
    		$oSession->password = $oCurrentUser->password;
    		$oSession->userId = $oCurrentUser->id;
    		$oSession->username = $oCurrentUser->username;
    		$oSession->permission = $permissionStatus;
    			
    		// go to main pages
    		return $this->_helper->redirector('product-list', 'index');
    }
    
    else { // incorrect password
    
    	//returning to first page and making a log.
    
    	// return form data via flash messenger
    	$aFormData['status'] = 3;
    	$this->_helper->FlashMessenger($aFormData);
    	// go to privous page
    	return $this->_helper->redirector('index', 'index');
    }
    }
    	
    }

    public function logoutAction()
    {
         // action body
        
    	// disabled layout
    	$this->_helper->layout()->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(true);
    	
    	// initialize session and unset all session
    	$oSession = new Zend_Session_Namespace('system_session');
    	$oSession->unsetAll();
    	 
    	// generate new session ID after logout to prevent usage of old session
    	Zend_Session::regenerateId();
		
    	
    	$aFormData = array("status" => 99);
    	Zend_Debug::dump($aFormData);
    	$this->_helper->FlashMessenger($aFormData);
    	// go to privous page
    	return $this->_helper->redirector('index', 'index');
    }

    public function sendResetPasswordAction()
    {
	
    	// action body
    	
    	// disabled layout & view
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ( true );
    	 
    	// get http request
    	$oHttpRequest = $this->getRequest ();
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
    		$aFormData = $oHttpRequest->getParams ();
    		// initialize validate form data
    		$oValidateNotEmpty = new Zend_Validate_NotEmpty ( Zend_Validate_NotEmpty::ALL );
    		 
    		// validate empty
    		 
    		// validate condition
    		if (! $oValidateNotEmpty->isValid ( $aFormData ['email'] )) {
    			$aFormData ['flashStatus'] = '901';
    			$sErrorMsg = 'Data Empty!';
    			throw new Exception ( $sErrorMsg );
    		}
    		 
    		 
    	}
    	catch ( Exception $e ) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( $e->getMessage () );
    		$this->_helper->FlashMessenger ( $aFormData );
    		return $this->_helper->redirector ( 'reset-password-form', 'Index' );
    	}
    	
    	//sent email to request for reset password
    	
    	$oUserTbl = new Application_Model_DbTable_UserTbl ();
    	$oSelect = $oUserTbl->select ()->where ( 'email = ?', $aFormData ['email'] );
    	$oCurrentUser = $oUserTbl->fetchRow ( $oSelect );
    	
    	//no user although we notify that we sent mail
    	if($oCurrentUser == null) {
    		$aFormData ['status'] = '700';
    		$this->_helper->FlashMessenger ( $aFormData );
    		return $this->_helper->redirector ( 'index', 'Index' );
    	}
    	
    	$id = $oCurrentUser->id;
    	
    	//sent email to email that registered with reset password link
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
    	$Mail->Subject     = 'Reset Password link for id ' . $oCurrentUser->username ;
    	$Mail->ContentType = 'text/html; charset=utf-8\r\n';
    	$Mail->From        = 'kxportproject@gmail.com';
    	$Mail->FromName    = 'kxport system';
    	$Mail->WordWrap    = 900; // RFC 2822 Compliant for Max 998 characters per line
    	$ToEmail = 'kxportproject@gmail.com';
    	$MessageHTML = "Please click the following link to reset your password"
    			. " <a href='http://localhost:2222/index/change-password-form?id=" . $id . "'>click</a>";
    	$MessageTEXT = "Please click the following link to activate your id"
    			. " <a href='http://localhost:2222/index/change-password-form?id=" . $id . "'>click</a>";
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
    	
    	$aFormData ['status'] = '700';
    	$this->_helper->FlashMessenger ( $aFormData );
    	return $this->_helper->redirector ( 'index', 'Index' );
    	
    }

    public function changePasswordAction()
    {
        // action body
    	
    	// when user press register from registerform redirect to this page to
    	// update database if valid
    	
    	// disabled layout & view
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ( true );
    	
    	// get http request
    	$oHttpRequest = $this->getRequest ();
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
    		$aFormData = $oHttpRequest->getParams ();
    		// initialize validate form data
    		$oValidateNotEmpty = new Zend_Validate_NotEmpty ( Zend_Validate_NotEmpty::ALL );
    			
    		// validate empty
    			
    		// validate condition
    		if (! $oValidateNotEmpty->isValid ( $aFormData ['password'] ) ||
    		! $oValidateNotEmpty->isValid ( $aFormData ['passwordConfirm'] ))
    		{
    			$aFormData ['flashStatus'] = '900';
    			$sErrorMsg = 'Data Empty!';
    			throw new Exception ( $sErrorMsg );
    		}
    		
    		//validate password
    		if (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$', $aFormData ['password'])) {
    			$aFormData ['flashStatus'] = '901';
    			$sErrorMsg = 'Password!';
    			throw new Exception ( $sErrorMsg );
    		}
    		
    		
    		if ($aFormData ['password'] != $aFormData ['passwordConfirm']) {
    			$aFormData ['flashStatus'] = '900';
    			$sErrorMsg = 'Data Empty!';
    			throw new Exception ( $sErrorMsg );
    		}
    	
    	
    	}
   	 	catch ( Exception $e ) {
    	/* DANGER CASE */
    	// return reset action
    	Zend_Debug::dump ( $e->getMessage () );
    	$this->_helper->FlashMessenger ( $aFormData );
    	$aRedirectArray = array (
    			'id' => $aFormData ['id']
    	);
    	return $this->_helper->redirector ( 'change-password-form', 'Index', null, $aRedirectArray );
    	}
    	
    	$oUserTbl = new Application_Model_DbTable_UserTbl ();
    	$aUserSaveData = array ();
    	$aUserSaveData['password'] = $aFormData['password'];
    	
    	$where = $oUserTbl->getAdapter()->quoteInto('id = ?', $aFormData['id']);
    	 
    	 
    	$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
    	$oDb->beginTransaction ();
    	 
    	try {
    		// create row and insert new data
    		$oUserTbl->update($aUserSaveData, $where);
    		$oDb->commit();
    		 
    	} catch ( Exception $e ) {
    	
    		// rollback if error happened
    		$oDb->rollBack ();
    		$sMessage = $e->getMessage ();
    		throw new Exception ( $sMessage );
    		return;
    	}
    	 
    	$aFormData['status'] = '699';
    	$this->_helper->FlashMessenger($aFormData);
    	return $this->_helper->redirector ( 'index', 'index' );
    }

    public function sendRegisterEmailAction()
    {
    // action body
		
		// when user press register from registerform redirect to this page to
		// update database if valid
		
		// disabled layout & view
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		
		// get http request
		$oHttpRequest = $this->getRequest ();
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
			$aFormData = $oHttpRequest->getParams ();
			// initialize validate form data
			$oValidateNotEmpty = new Zend_Validate_NotEmpty ( Zend_Validate_NotEmpty::ALL );
			
			// validate empty
			
			// validate condition
			if (! $oValidateNotEmpty->isValid ( $aFormData ['username'] ) || 
			! $oValidateNotEmpty->isValid ( $aFormData ['password'] ) || 
			! $oValidateNotEmpty->isValid ( $aFormData ['passwordConfirm'] ) || 
			! $oValidateNotEmpty->isValid ( $aFormData ['userType'] ) || 
			! $oValidateNotEmpty->isValid ( $aFormData ['firstname'] ) || 
			! $oValidateNotEmpty->isValid ( $aFormData ['lastname'] ) || 
			! $oValidateNotEmpty->isValid ( $aFormData ['address'] ) || 
			! $oValidateNotEmpty->isValid ( $aFormData ['country'] ) || 
			! $oValidateNotEmpty->isValid ( $aFormData ['telnumber'] ) || 
			! $oValidateNotEmpty->isValid ( $aFormData ['email'] )) {
				$aFormData ['flashStatus'] = '901';
				$sErrorMsg = 'Data Empty!';
				throw new Exception ( $sErrorMsg );
			}
			
			//validate address
			if (!preg_match("/^[a-zA-Z0-9., ]+$/", $aFormData ['address'])) {
				$aFormData ['flashStatus'] = '908';
				$sErrorMsg = 'Password!';
				throw new Exception ( $sErrorMsg );
			}
			
			//validate password
			if (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$', $aFormData ['password'])) {
				$aFormData ['flashStatus'] = '905';
				$sErrorMsg = 'Password!';
				throw new Exception ( $sErrorMsg );
			}
			
			//validate name 
			
			if (!preg_match("/^[a-zA-Z]+$/", $aFormData ['firstname'])) {
				$aFormData ['flashStatus'] = '906';
				$sErrorMsg = 'Password!';
				throw new Exception ( $sErrorMsg );
			}
			
			//validate lastname
			if (!preg_match("/^[a-zA-Z]+$/", $aFormData ['lastname'])) {
				$aFormData ['flashStatus'] = '907';
				$sErrorMsg = 'Password!';
				throw new Exception ( $sErrorMsg );
			}
			
		
				
			
			if ($aFormData ['userType'] == 2) {
				if (! $oValidateNotEmpty->isValid ( $aFormData ['accountnumber'] )) {
					$aFormData ['flashStatus'] = '902';
					$sErrorMsg = 'No Account Number!';
					throw new Exception ( $sErrorMsg );
				}
			}
			
			if ($aFormData ['userType'] == 1) {
				
				if ($oValidateNotEmpty->isValid ( $aFormData ['accountnumber'] )) {
					$aFormData ['flashStatus'] = '903';
					$sErrorMsg = 'Must Not Have Account Number!';
					throw new Exception ( $sErrorMsg );
				}
			}
			
			if ($aFormData ['password'] != $aFormData ['passwordConfirm']) {
				$aFormData ['flashStatus'] = '898';
				$sErrorMsg = 'Data Empty!';
				throw new Exception ( $sErrorMsg );
			}
			
			$oUserTbl = new Application_Model_DbTable_UserTbl ();
			$oSelect = $oUserTbl->select ()->where ( 'username = ?', $aFormData ['username'] );
			$oCurrentUser = $oUserTbl->fetchRow ( $oSelect );
			
			
			
			if ($oCurrentUser !== null) {
				$aFormData ['flashStatus'] = '897';
				$sErrorMsg = 'This username have already existed!';
				throw new Exception ( $sErrorMsg );
			}
			
			$oSelect = $oUserTbl->select ()->where ( 'email = ?', $aFormData ['email'] );
			$oCurrentEmail = $oUserTbl->fetchRow ( $oSelect );
				
			if ($oCurrentEmail !== null) {
				$aFormData ['flashStatus'] = '896';
				$sErrorMsg = 'This username have already existed!';
				throw new Exception ( $sErrorMsg );
			}
			
		} catch ( Exception $e ) {
			/* DANGER CASE */
			// return reset action
			Zend_Debug::dump ( $e->getMessage () );
			$this->_helper->FlashMessenger ( $aFormData );
			return $this->_helper->redirector ( 'register', 'Index' );
		}
		
		
		
		$oUserTbl = new Application_Model_DbTable_UserTbl ();
		
		// set current datetime format
		$sCurrentDateTime = Zend_Date::now ()->toString ( 'yyyy-MM-dd HH:mm:ss' );
		
		$aUserSaveData = array ();
		$aUserSaveData ['id'] = '';
		$aUserSaveData ['username'] = $aFormData ['username'];
		$aUserSaveData ['firstname'] = $aFormData ['firstname'];
		$aUserSaveData ['lastname'] = $aFormData ['lastname'];
		$aUserSaveData ['email'] = $aFormData ['email'];
		$aUserSaveData ['password'] = $aFormData ['password'];
		$aUserSaveData ['Address'] = $aFormData ['address'];
		$aUserSaveData ['Tel'] = $aFormData ['telnumber'];
		$aUserSaveData ['Country'] = $aFormData ['country'];
		$aUserSaveData ['activate_status'] = '0';
		
		$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$oDb->beginTransaction ();
		
		try {
			// create row and insert new data
			$oUserRow = $oUserTbl->createRow ();
			$oUserRow->setFromArray ( $aUserSaveData );
			$iUserId = $oUserRow->save ();
			
			if (null !== $iUserId) {
				
				// commit database
				$oDb->commit ();
				$oTypeTbl = null;
				// add buyer
				if ($aFormData ['userType'] == 1) {
					$oTypeTbl = new Application_Model_DbTable_BuyerTbl();
					$aUserTypeSaveData = array ();
					$aUserTypeSaveData ['uid'] = $iUserId;
					Zend_Debug::dump($iUserId);
				}
				
				// add seller
				if ($aFormData ['userType'] == 2) {
					$oTypeTbl = new Application_Model_DbTable_SellerTbl();
					$aUserTypeSaveData = array ();
					$aUserTypeSaveData ['uid'] = $iUserId;
					$aUserTypeSaveData ['account_number'] = $aFormData ['accountnumber'];
					Zend_Debug::dump($iUserId);
				}
				
				// commit to database
				
				$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
				$oDb->beginTransaction ();
				
				try {
					// create row and insert new data
					$oTypeRow = $oTypeTbl->createRow ();
					$oTypeRow->setFromArray ( $aUserTypeSaveData );
					$itypeId = $oTypeRow->save ();
					
					// if all successful commit to DB
					if (null !== itypeId) {
						
						// commit database
						$oDb->commit ();
						
						$aFormData ['flashStatus'] = '900';
						$aFormData ['activate_id'] = $iUserId; 
						$this->_helper->FlashMessenger ( $aFormData );
						return $this->_helper->redirector ( 'send-activate-email', 'System' );
					} else {
						// throw error
						throw new Exception ( 'save user data error please try again!' );
						return;
					}
				} catch ( Exception $e ) {
					
					// rollback if error happened
					$oDb->rollBack ();
					$sMessage = $e->getMessage ();
					throw new Exception ( $sMessage );
					return;
				}
			} else {
				// throw error
				throw new Exception ( 'save user data error please try again!' );
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

    public function sendActivateEmailAction()
    {
     // action body
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
    	$Mail->Subject     = 'Activate Email to activate member status';
    	$Mail->ContentType = 'text/html; charset=utf-8\r\n';
    	$Mail->From        = 'kxportproject@gmail.com';
    	$Mail->FromName    = 'kxport system';
    	$Mail->WordWrap    = 900; // RFC 2822 Compliant for Max 998 characters per line
    	$ToEmail = 'the_super_arm@hotmail.com';
    	$MessageHTML = "Please click the following link to activate your id"
    			. " <a href='http://localhost:3458/system/verify-account?id=" . $aFlashMsg[0]['activate_id'] . "'>click</a>";
    	$MessageTEXT = "Please click the following link to activate your id"
    			. " http://localhost:2222/system/verifyaccount?id=" . $aFlashMsg[0]['activate_id'];;		
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
    }

    public function verifyAccountAction()
    {
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
    	
    	$oUserTbl = new Application_Model_DbTable_UserTbl ();
    	$aUserSaveData = array ();
    	$aUserSaveData['activate_status'] = 1;
		
    	$where = $oUserTbl->getAdapter()->quoteInto('id = ?', $aFormData['id']);
    	
    	
    	$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
    	$oDb->beginTransaction ();
    	
    	try {
    		// create row and insert new data
    		$oUserTbl->update($aUserSaveData, $where);
    		$oDb->commit();
    	
    		} catch ( Exception $e ) {
    			 
    			// rollback if error happened
    			$oDb->rollBack ();
    			$sMessage = $e->getMessage ();
    			throw new Exception ( $sMessage );
    			return;
    		}
    		 
    		$aFormData['status'] = '1000';
    		$this->_helper->FlashMessenger($aFormData);
    		return $this->_helper->redirector ( 'index', 'index' );
    }

    public function editProfileAction()
    {
         // action body
        $oSession = new Zend_Session_Namespace('system_session');
    	// action body
    	
    	// when user press register from registerform redirect to this page to
    	// update database if valid
    	
    	// disabled layout & view
    	$this->_helper->layout ()->disableLayout ();
    	$this->_helper->viewRenderer->setNoRender ( true );
    	
    	// get http request
    	$oHttpRequest = $this->getRequest ();
    	// check data request (Post)
    	if ($oHttpRequest->isPost () == FALSE) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( 'error' );
    		die ();
    	}
    	
    	
    	
    	try {
    		$aFormData = $oHttpRequest->getParams ();
    		// initialize validate form data
    		//Zend_Debug::dump ($aFormData);
    		//die();
    		 
    		$oValidateNotEmpty = new Zend_Validate_NotEmpty ( Zend_Validate_NotEmpty::ALL );
    			
    		// validate empty
    			
    		// validate condition
    		if (
    		! $oValidateNotEmpty->isValid ( $aFormData ['firstname'] ) ||
    		! $oValidateNotEmpty->isValid ( $aFormData ['lastname'] ) ||
    		! $oValidateNotEmpty->isValid ( $aFormData ['address'] ) ||
    		! $oValidateNotEmpty->isValid ( $aFormData ['country'] ) ||
    		! $oValidateNotEmpty->isValid ( $aFormData ['telnumber'] ) ||
    		! $oValidateNotEmpty->isValid ( $aFormData ['email'] ) ||
    		! $oValidateNotEmpty->isValid ( $aFormData ['password'] )) {
    			$aFormData ['flashStatus'] = '901';
    			$sErrorMsg = 'Data Empty!';
    			throw new Exception ( $sErrorMsg );
    		}
    			
    		//validate address
    		if (!preg_match("/^[a-zA-Z0-9., ]+$/", $aFormData ['address'])) {
    			$aFormData ['flashStatus'] = '908';
    			$sErrorMsg = 'Password!';
    			throw new Exception ( $sErrorMsg );
    		}
    		
    		if (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$', $aFormData ['newPassword']) && $aFormData['newPassword'] != "") {
    			$aFormData ['flashStatus'] = '905';
    			$sErrorMsg = 'Password!';
    			throw new Exception ( $sErrorMsg );
    		}
    		
    			
    			
    		//validate name
    			
    		if (!preg_match("/^[a-zA-Z]+$/", $aFormData ['firstname'])) {
    			$aFormData ['flashStatus'] = '906';
    			$sErrorMsg = 'Password!';
    			throw new Exception ( $sErrorMsg );
    		}
    			
    		//validate lastname
    		if (!preg_match("/^[a-zA-Z]+$/", $aFormData ['lastname'])) {
    			$aFormData ['flashStatus'] = '907';
    			$sErrorMsg = 'Password!';
    			throw new Exception ( $sErrorMsg );
    		}
    			
    	
    	
    			
    			
    		if ($aFormData ['newPassword'] != $aFormData ['newPasswordConfirm']) {
    			$aFormData ['flashStatus'] = '898';
    			$sErrorMsg = 'Data Empty!';
    			throw new Exception ( $sErrorMsg );
    		}
    		$oUserTbl = new Application_Model_DbTable_UserTbl ();
    		 
    		
    		$oSelect = $oUserTbl->select ()->where ( 'id = ?',  $oSession->userId)
    		->where('password = ?', $aFormData['password']);
    		$oCurrentEm = $oUserTbl->fetchRow ( $oSelect );
			
    		
    		if ($oCurrentEm == null) {
    			$aFormData ['flashStatus'] = '895';
    			$sErrorMsg = 'password wrong';
    			throw new Exception ( $sErrorMsg );
    		}
    		 
    		
    			
    		$oSelect = $oUserTbl->select ()->where ( 'email = ?', $aFormData ['email'] )
    		->where('id != ?', $oSession->userId);
    		$oCurrentEmail = $oUserTbl->fetchRow ( $oSelect );
    	
    		if ($oCurrentEmail !== null) {
    			$aFormData ['flashStatus'] = '897';
    			$sErrorMsg = 'This username have already existed!';
    			throw new Exception ( $sErrorMsg );
    		}
    			
    	} catch ( Exception $e ) {
    		/* DANGER CASE */
    		// return reset action
    		Zend_Debug::dump ( $e->getMessage () );
    		$this->_helper->FlashMessenger ( $aFormData );
    		return $this->_helper->redirector ( 'edit-profile', 'Buyer' );
    	}
    	
    	$oUserTbl = new Application_Model_DbTable_UserTbl ();
    	
    	$aUserSaveData = array ();
    	$aUserSaveData ['firstname'] = $aFormData ['firstname'];
    	$aUserSaveData ['lastname'] = $aFormData ['lastname'];
    	$aUserSaveData ['email'] = $aFormData ['email'];
    	$aUserSaveData ['Address'] = $aFormData ['address'];
    	$aUserSaveData ['Tel'] = $aFormData ['telnumber'];
    	$aUserSaveData ['Country'] = $aFormData ['country'];
    	 
    	if($aFormData['newPassword'] != ""){
    	$aUserSaveData['password'] = $aFormData['newPassword'];
    	}
    	
    	$where = $oUserTbl->getAdapter()->quoteInto('id = ?', $oSession->userId);
    	 
    	 
    	$oDb = Zend_Db_Table_Abstract::getDefaultAdapter ();
    	$oDb->beginTransaction ();
    	 
    	try {
    		// create row and insert new data
    		$oUserTbl->update($aUserSaveData, $where);
    		$oDb->commit();
    		 
    	} catch ( Exception $e ) {
    	
    		// rollback if error happened
    		$oDb->rollBack ();
    		$sMessage = $e->getMessage ();
    		throw new Exception ( $sMessage );
    		return;
    	}
    	 
    	$aFormData['flashStatus'] = '1000';
    	$this->_helper->FlashMessenger($aFormData);
    	return $this->_helper->redirector ( 'edit-profile', 'Buyer' );
    }


}


















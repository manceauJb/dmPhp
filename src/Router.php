<?php

require_once("view/View.php");
require_once("view/PrivateView.php");
require_once("view/AdminView.php");
require_once("control/Controller.php");
require_once("model/MotoStorage.php");
require_once("acc/Account.php");
require_once("acc/AuthenticationManager.php");

class Router
{
	private $_string;
	private $view;
	private $accessUser = array("accueil","liste","id","action","logout","account","infos");
	private $accessAdmin = array("accueil","liste","id","action","logout","manage","infos");
	private $accessVisitor = array("accueil","login","loged","liste","userCreation","infos");
	private $accessSession;

	function __construct(){
		$this->_string = null;
	}


	public function main(MotoStorage $motoStorage, AuthenticationManager $authenticationManager){
		session_start();

		$feedback = key_exists('feedback', $_SESSION) ? $_SESSION['feedback'] : '';
		$_SESSION['feedback'] = '';
		$id = key_exists('id',$_GET) ? $_GET['id'] : null; 

		//////////// Créer la vue pour chaque type d'utilisateur.
		if(key_exists('user',$_SESSION) and $_SESSION['user']!=null and $authenticationManager->isUserConnected()){
			$this->view = new PrivateView($this,$feedback,$_SESSION['user']);
			$this->accessSession = $this->accessUser;
		} elseif (key_exists('user',$_SESSION) and $_SESSION['user']!=null and $authenticationManager->isAdminConnected()) {
			$this->view = new AdminView($this,$feedback,$_SESSION['user']);
			$this->accessSession = $this->accessAdmin;
		}
		else{
			$this->view = new View($this, $feedback);
			$this->accessSession = $this->accessVisitor;
		}
		
		$ctl = new Controller($this->view,$motoStorage, $authenticationManager);
		try{
			$keysGET = array_keys($_GET);
			//////////	Redirection /////////////
			if(sizeof($keysGET)!=0){
				if(in_array($keysGET[0],$this->accessSession)){
					if (key_exists("accueil",$_GET)){
						$this->view->makeHomePage();
					} elseif (key_exists('id', $_GET) and !key_exists('action',$_GET)) {
						$ctl->showInformation($_GET["id"]);
					} elseif (key_exists('liste',$_GET)) {
						$ctl->showList();
					} elseif (key_exists('login',$_GET)) {
						$ctl->showLogin();
					} elseif (key_exists('loged',$_GET) and $_POST != null) {
						$ctl->logedVerif($_POST);
					} elseif (key_exists('logout',$_GET)) {
						$ctl->logouVerif();
					} elseif (key_exists('userCreation',$_GET) and $_POST != null){
						$ctl->saveNewUser($_POST);
					} elseif (key_exists('manage',$_GET)){
						$ctl->showManage();
					} elseif (key_exists('account',$_GET)){
						$ctl->showAccount();
					} elseif (key_exists('infos',$_GET)){
						$this->view->showInfos();
					} elseif (key_exists('action',$_GET)){
						if($_GET["action"]==="nouveau"){
							$ctl->showCreationPage();
						}elseif($_POST!=null and !$authenticationManager->accessVerification($_GET['action'],$_POST,$motoStorage)){
							$ctl->wrongAccess();
						} elseif($_GET["action"]==="sauverNouveau" and $_POST!=null){
							$ctl->saveNewMoto($_POST);
						} elseif($_GET["action"]==="deleteConfirmation" and $_POST!=null){
							$ctl->confimationDelete($_POST);
						} elseif($_GET["action"]==="delete" and $_POST!=null){
							$ctl->deleteMoto($_POST);
							$ctl->showList();
						} elseif($_GET["action"]==="modifier"){
							$ctl->showModifPage($id);
						} elseif($_GET["action"]==="saveModif" and $_POST!=null){
							$ctl->saveModifMoto($_POST);
						} elseif($_GET["action"]==="manageUpdate" and $_POST!=null){
							$ctl->managerUpdate($_POST);
						} elseif($_GET["action"]==="saveModifUser" and $_POST!=null){
							$ctl->saveModifUser($_POST);
						} else {
							$this->view->makeErrorPage();
						}
					} else {
						$this->view->makeErrorPage();
					}
				}else{
					$ctl->wrongAccess();
				}
			} else {
				$this->view->makeHomePage();
			}
		} catch (Exception $e){
			$this->view->makeErrorPage();
		}
		$this->view->render();
	}

	//////  Lien de rediction //////

	public function homePage(){
		return "?accueil";
	}

	public function getUrl($id){
		return "?id=".$id;
	}

	public function getListUrl(){
		return "?liste";
	}

	public function getLoginPage(){
		return "?login";
	}

	public function getLogout(){
		return "?logout";
	}

	public function getConfirmUser(){
		return "?loged";
	}

	public function getMotoCreationURL(){
		return "?action=nouveau";
	}
	
	public function getMotoSaveURL(){
		return "?action=sauverNouveau";
	}

	public function getMotoAskDeletionURL($id){
		return "?action=deleteConfirmation";
	}

	public function getModifPage($id){
		return "?action=modifier&id=".$id;
	}

	public function updateModifiedMoto($id){
		return "?action=saveModif";
	}

	public function getMotoDeletionURL($id){
		return "?action=delete";
	}

	public function getCreationUser(){
		return "?userCreation";
	}

	public function getManagerUrl(){
		return "?manage";
	}

	public function getAccountUrl(){
		return "?account";
	}

	public function updateUser(){
		return "?action=saveModifUser";
	}

	public function getManageUpdate(){
		return "?action=manageUpdate";
	}

	public function POSTredirect($url, $feedback) {
		$_SESSION['feedback'] = $feedback;
		header("Location: ".htmlspecialchars_decode($url), true, 303);
		die;
	}

}


?>
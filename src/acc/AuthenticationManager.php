<?php
require_once("acc/AccountStorageMySql.php");

class AuthenticationManager{

	private $data;


	function __construct (){
		$this->data = new AccountStorageMySql();
	}

    /*
     * Permet de vérifié si le login et le password sont correct, en le vérifiant dans la base grace à checkAuth.
     */
	public function connectUser($login,$password){
		$_SESSION['user'] = $this->data->checkAuth($login,$password);
		if($_SESSION['user'] ==  null){
			return false;
		}
		return true;
	}

    /*
     * Permet de vérifié si l'utilisateur à bien le droit d'accèdé à l'action demmandé.
     */
	public function accessVerification($action,$moto,$motoStorage){
		if($this->isUserConnected()){
			if($action === "sauverNouveau" or $action === "nouveau" or $action === "saveModifUser"){
				return true;
			}elseif(($action==="delete" or $action==="deleteConfirmation" or $action ==="modifier" or $action === "saveModif") and $motoStorage->read($moto['id'])->getAuthor() === $_SESSION['user']->getName()){
				return true;
			}
			return false;
		} elseif($this->isAdminConnected()){
			return true;
		}
		return false;
	}

    /*
     * Permet d'ajouter un nouvel utilisateur.
     * Retourne faux si il y a eu un problèle (non disponible,ou mauvaise syntax).
     * vrai si tout c'est bien passé.
     */
	public function addNewUser($data){
		if($this->isValid($data) and $this->data->isDispo($data["login"])){ // vérifie la syntax.
			$acc = $this->data->addUser($data["name"],$data["login"],$data["password"]);
			if($acc == null){ // si $acc == null, c'est que ce login est déja utilisé.
				return false;
			}else{
				return true;
			}
		}
		return false;
	}

    /*
     * Permet de mettre à jour un utilisateur.
     * Méthode utilisé par un admin.
     */
	public function update($data){
		$data["password"]="123"; // mot de passe pour pouvoir vérifié la syntax, mais celui-ci ne sera pas utilisé.
		if($this->isValid($data)){ // vérifie la syntax et si login disponible.
			$this->data->update($data['id'],$data['name'],$data['login'],isset($data['admin'])); // on met à jour.
			return true; // tout c'est bien passé.
		}
		return false; // Problème.
	}

    /*
     * Permet de mettre à jour un utilisateur.
     * Méthode utilisé par un user.
     */
	public function userUpdate($data){
		if($data['password']==="" and $data["password2"]===""){ // l'user n'as pas inscrit de mot de passe.
			$data['password']="123"; // mot de passe pour pouvoir vérifié la syntax, mais celui-ci ne sera pas utilisé.
			if($this->isValid($data)){ // Vérifie si la syntax est correct et si il est le login est disponible(isValid).
				$this->data->updateUser($data); // On mets à jour dans la base le nom et login.
				$this->connectUser($data['login'],$_SESSION['user']->getMdp()); // On le reconnecte pour avoir le nouveaux nom/login.
				return true; // Tout c'est bien passé.
			}else{
				return false; // Problème.
			}
		} elseif ($data['password']!==null and $data['password2']!==null and $data['password']===$data['password2'] and $this->isValid($data)){ // L'user à changé son mot de passe, donc on vérifie si ils sont identique, si les champs ont syntax est valide et si il est le login est disponible(isValid).
			$this->data->updateUser($data); // On mets à jour dans la base le nom et login.
			$this->data->updatePassword($data); // On mets à jour dans la base le mot de passe..
			$this->connectUser($data['login'],$data['password']); // On le reconnecte pour avoir le nouveaux nom/login.
			return true; // Tout c'est bien passé.
		}
		return false; // Problème.
	}

    /*
     * Permet de supprimé un utilisateur.
     * Méthode utilisé par un admin.
     */
	public function delete($id){
		if($this->data->delete($id) and $this->data->isDispo($id)){ // On le supprime et on vérifie si il est disponible (!!! ne pas changer l'ordre !!!).
			return true;
		}
		return false;
		
	}
    
    /*
     * Permet de retourne le nombre de compte dans la table.
     */
	public function getNbAcc(){
		return $this->data->nbAcc()[0];
	}

    /*
     * Permet de retourner les accounts.
     */
	public function getUsers(){
		return $this->data->readAll();
	}

    /*
     * Permet de vérifier si la syntax des champs sont valide, ainsi que si le login n'est pas utilisé.
     */
	public function isValid($data){
        $char = '#^[a-zA-Z0-9]*$#';
        //echo ($data['login'] . "     ".$this->data->getLoginFromId($data['id'])[0]. "     " . ($data['login']==$this->data->getLoginFromId($data['id'])[0]));
        if (!preg_match_all($char, $data["name"]) || !preg_match_all($char, $data["login"]) || !preg_match_all($char, $data["password"])){ // Si syntax est correct.
            return false;
    	} elseif($data["name"]==null or $data["login"]==null or $data["password"]==null){ // si un des champs est nul.
    		return false;
    	} elseif (!key_exists('id',$data) and $this->data->isDispo($data['login'])){ // si id existe pas c'est que c'est une création de compte, donc si dispo -> True
    		return true;
    	} elseif (!key_exists('id',$data) and !$this->data->isDispo($data['login'])){// si id existe pas c'est que c'est une création de compte, donc si pas dispo -> False
    		return false;
    	} elseif ($data['login']==$this->data->getLoginFromId($data['id'])[0]) { // id existe donc c'est une modification, on vérifie si nouveau login est le même qu'avant -> True
    		return true;
    	} elseif (!($data['login']!=$this->data->getLoginFromId($data['id'])[0] and $this->data->isDispo($data['login']))) { // id existe donc c'est une modification, on vérifie si nouveau login est différent de celui d'avant, et que ce nouveau login n'est pas dispo alors -> False.
    		return false;
    	}
    	return true;
    }

    /*
     * Permet de vérifier si Utilisateur est un User.
     */
	public function isUserConnected(){
		return $_SESSION['user']->isUser();
	}

    /*
     * Permet de vérifier si Utilisateur est un Admin.
     */
	public function isAdminConnected(){
		return $_SESSION['user']->isAdmin();
	}

    /*
     * Permet de retourner le nom de l'Utilisateur.
     */
	public function getUserName(){
		return $_SESSION['user']->getName();
	}

    /*
     * Permet de déconnecté l'utilisateur.
     */
	public function disconnectUser(){
		unset($_SESSION['user']); // on vide la $_SESSION['user'].
		session_destroy(); // on détruit la session.
	}

}
<?php
//include('model/charger.php');
require_once("acc/AccountStorage.php");
require_once("acc/Account.php");

class AccountStorageMySql implements AccountStorage{

	private $bd;
	private $data;

	function __construct(){
		$this->bd = $this->chargerbd();
		//$this->data = $this->getData();
	}

    /*
     * Permet de charger la base de données 21713189_dev.
     */
	private function chargerbd(){
		$dsn = '*********************';
		try{
			return new PDO($dsn,'**********','**********************');
		}
		catch(Exception $e){
			die($e.getMessage());
		}
	}

    /*
     * Permet de vérifié si le mot de passe correspond avec le login, et si c'est le cas retourne l'Account.
     */
	public function checkAuth($login,$password){
		$request = 'SELECT * FROM account WHERE login = :login';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":login",$login,PDO::PARAM_STR);
		$statement->execute();
		$result = $statement->fetch();
		//// Permet de vérifier si le mot de passe rentré par l'utilisateur est le même que celui inscrit dans la base qui est encrypté avec BCRYPT.
		if(password_verify($password, $result['password']) or $password === $result['password']){
			return new Account($result['name'],$result['login'],$result['password'],$this->userOrAdmin($result['admin']),$result['id']);
		}
		return null;;
	}

    /*
     * Permet de lire tout les comptes présent dans la table account.
     */
	public function readAll(){
		$request = "SELECT * FROM account";
		$statement = $this->bd->prepare($request);
		$statement->execute();
		$result = $statement->fetchAll();
		return $result;
	}

    /*
     * Permet de retourner le dernière ID de la table account.
     */
	public function lastId(){
		$request = "SELECT id FROM account WHERE id = (SELECT MAX(id) FROM account)";
		$statement = $this->bd->prepare($request);
		$statement->execute();
		$result = $statement->fetch();
		if($result['id'] == null){
			return 0;
		}
		return $result['id'];
	}

    /*
     * Permet de retourner ne nombre de compte présent dans la table account.
     */
	public function nbAcc(){
		$request = 'SELECT COUNT(*) FROM account';
		$statement = $this->bd->prepare($request);
		$statement->execute();
		$result = $statement->fetch();
		return $result;
	}

    /*
     * Permet de vérifier en fonction de $val("0" ou "1") soit admin dans la table account, si c'est un user ou admin.
     */
	private function userOrAdmin($val){
		if($val === "0"){
			return 'user';
		} else {
			return 'admin';
		}
	}

    /*
     * Permet de vérifié si $login est déja utilisé ou non dans la table account.
     */
	public function isDispo($login){
		$request = 'SELECT name FROM account WHERE login = :login';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":login",$login,PDO::PARAM_STR);
		$statement->execute();
		if($statement->fetch()==null){
			return true;
		}
		return false;
	}

    /*
     * Permet de le nom de l'utilisateur ayant pour id $id.
     */
	private function getNameFromId($id){
		$request = 'SELECT name FROM account WHERE id = :identifiant';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":identifiant",$id,PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetch();
	}

	    /*
     * Permet de le login de l'utilisateur ayant pour id $id.
     */
	public function getLoginFromId($id){
		$request = 'SELECT login FROM account WHERE id = :identifiant';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":identifiant",$id,PDO::PARAM_INT);
		$statement->execute();
		return $statement->fetch();
	}



    /*
     * Permet de permet de retourner un tableaux contenant tout les Accounts. !=readAll.
     */
	private function getData(){
		$request = 'SELECT * FROM account';
		$statement = $this->bd->prepare($request);
		$statement->execute();
		$i = 0;
		while ($result = $statement->fetch()) {
			$this->data[$i] = new Account($result['name'],$result['login'],$result['password'],$this->userOrAdmin($result['admin']),$result['id']);
			$i++;
		}
	}

    /*
     * Permet d'ajouter un nouvel Account à la table account. Et retourne cette Account.
     */
	public function addUser($name,$login,$mdp){
		$newid = (int)$this->lastId()+1;
		$request = 'INSERT INTO account(id,name,login,password) VALUES (:id, :name, :login, :password)';
		$statement = $this->bd->prepare($request);
		$hash = password_hash($mdp, PASSWORD_BCRYPT);
		$statement->bindValue(":id",$newid,PDO::PARAM_INT);
		$statement->bindValue(":name",$name,PDO::PARAM_STR);
		$statement->bindValue(":login",$login,PDO::PARAM_STR);
		$statement->bindValue(':password',$hash,PDO::PARAM_STR);
		$statement->execute();
		return new Account($name,$login,$hash,"user",$newid);
	}

	/*
     * Permet de mettre à jour le nom,login,et si admin ou non de l'utilisateur ayant pour id $id.
     * Méthode utilisé par un Admin.
     */
	public function update($id,$name,$login,$admin){
		$request = 'UPDATE account SET name=:name, login=:login, admin=:admin WHERE id=:id';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":id",$id,PDO::PARAM_INT);
		$statement->bindValue(":admin",$admin,PDO::PARAM_BOOL);
		$statement->bindValue(":name",$name,PDO::PARAM_STR);
		$statement->bindValue(":login", $login,PDO::PARAM_INT);
		$statement->execute();
	}

    /*
     * Permet de mettre à jour le nom et login de l'utilisateur ayant pour id $id.
     * Méthode utilisé par un User.
     */
	public function updateUser($data){
		$request = 'UPDATE account SET name=:name, login=:login WHERE id=:id';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":id",$data['id'],PDO::PARAM_INT);
		$statement->bindValue(":name",$data['name'],PDO::PARAM_STR);
		$statement->bindValue(":login", $data['login'],PDO::PARAM_INT);
		$statement->execute();
	}
    /*
     * Permet de modifié le mot de passe de l'utilisateur $id,
     * Ce mot de passe est encrypté ici avec BCRYPT.
     */
	public function updatePassword($data){
		$request = 'UPDATE account SET password=:password WHERE id=:id';
		$statement = $this->bd->prepare($request);
		$hash = password_hash($data['password'], PASSWORD_BCRYPT);
		$statement->bindValue(":id",$data['id'],PDO::PARAM_INT);
		$statement->bindValue(":password",$hash,PDO::PARAM_STR);
		$statement->execute();
	}

    /*
     * Permet de supprime l'utilisateur ayant comme id $id.
     * Méthode utilisé par un Admin.
     */
	public function delete($id){
		if($this->readAllFrom($id)[0]==='0'){
			$request = 'DELETE FROM account WHERE id = :identifiant';
			$statement = $this->bd->prepare($request);
			$statement->bindValue(":identifiant",$id,PDO::PARAM_INT);
			$statement->execute();
			return true;
		}
		return false;
	}

    /*
     * Permet de retourner toutes les motos de $user.
     */
	public function readAllFrom($user){
		$request = 'SELECT COUNT(*) FROM motos WHERE owner=:owner';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":owner",$user,PDO::PARAM_STR);
		$statement->execute();
		$result = $statement->fetch();
		return $result;
	}
}

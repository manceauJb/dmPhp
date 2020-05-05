<?php

class Account
{

	private $name;
	private $login;
	private $mdp;
	private $statut;
	private $id;


	function __construct ($name,$login,$mdp,$statut,$id){
		$this->name = $name;
		$this->login = $login;
		$this->mdp = $mdp;
		$this->statut = $statut;
		$this->id = $id;
	}

	public function getName(){
		return $this->name;
	}

	public function getlog(){
		return $this->login;
	}

	public function getMdp(){
		return $this->mdp;
	}

	public function getStatut(){
		return $this->statut;
	}

	public function getId(){
		return $this->id;
	}

	public function isUser(){
		if($this->statut == 'user'){
			return true;
		}
		return false;
	}

	public function isAdmin(){
		if($this->statut == 'admin'){
			return true;
		}
		return false;
	}
}

?>
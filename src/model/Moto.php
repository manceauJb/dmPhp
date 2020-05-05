<?php

class Moto
{

	private $name;
	private $species;
	private $age;
	private $author;
	private $img;


	function __construct ($modele,$marque,$annee,$cyl,$hp,$author,$img){
		$this->modele = $modele;
		$this->marque = $marque;
		$this->annee = $annee;
		$this->cyl = $cyl;
		$this->hp = $hp;
		$this->author = $author;
		$this->img = $img;
	}

	public function getModele(){
		return $this->modele;
	}

	public function getMarque(){
		return $this->marque;
	}

	public function getAnnee(){
		return $this->annee;
	}

	public function getCyl(){
		return $this->cyl;
	}

	public function getHp(){
		return $this->hp;
	}

	public function getAuthor(){
		return $this->author;
	}

	public function gotImg(){
		if($this->img === "0"){
			return false;
		}
		return true;
	}

	public function returnImg(){
		$img_name = $this->getModele().$this->getMarque().$this->getAnnee().$this->getCyl();
		$img_name = str_replace(' ', '', $img_name);
		$fil = scandir('upload/',1);
		foreach ($fil as $key => $value) {
			if(strpos($value, $img_name)!== false){
				return 'upload/'.$value;
			}
		}
		return null;
	}

	public function setModele($modele){
		$this->modele = $modele;
	}

	public function setMarque($marque){
		$this->marque = $marque;
	}

	public function setAnnee($annee){
		$this->annee = $annee;
	}

	public function setCyl($cyl){
		$this->cyl = $cyl;
	}

	public function setHp($hp){
		$this->hp = $hp;
	}

	public function setImg($img){
		$this->img = $img;
	}
}

?>
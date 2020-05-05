<?php

require_once("model/Moto.php");


class MotoBuilder {
	protected $data;
	protected $errors;

	const MODELE_REF = "modele";
	const MARQUE_REF = "marque";
	const ANNEE_REF = "annee";
	const CYL_REF = "cyl";
	const HP_REF = "hp";
	const AUTHOR_REF = "author";
	const IMG_REF = "img";

	public function __construct($data){
		$this->data = $data;
		$this->errors = null;
	}

    /*
     * Permet de retourner toutes les valeurs de data, donc d'une moto.
     */
	public function getData(){
		return $this->data;
	}

	public function getErrors(){
		return $this->errors;
	}

    /*
     * Permet de créer une moto, en vérifiant plusieurs si data contient toutes les données.
     */
	public function createMoto(){		
		if ( !key_exists(self::MODELE_REF, $this->data) ||
			 !key_exists(self::MARQUE_REF, $this->data) ||
			 !key_exists(self::ANNEE_REF, $this->data)  ||
			 !key_exists(self::CYL_REF, $this->data)	||
			 !key_exists(self::HP_REF, $this->data))
			throw new Exception("Missing something");
		return new Moto($this->data[self::MODELE_REF],$this->data[self::MARQUE_REF],$this->data[self::ANNEE_REF],$this->data[self::CYL_REF],$this->data[self::HP_REF],$this->data['author'],$this->data[self::IMG_REF]);
	}

	public function getModele(){
		return $this->data[self::MODELE_REF];
	}


	public function getMarque(){
		return $this->data[self::MARQUE_REF];
	}

	public function getAnnee(){
		return $this->data[self::ANNEE_REF];
	}

	public function getCyl(){
		return $this->data[self::CYL_REF];
	}

	public function getHp(){
		return $this->data[self::HP_REF];
	}

    /*
     * Permet de retourner si la moto contient une image ou non.
     */
	public function gotImg(){
		if($this->data[self::IMG_REF] === "0"){
			return false;
		}
		return true;
	}

    /*
     * Permet de le chemin où ce situe l'image.
     */
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

    /*
     * Permet de vérifier si les champs sont correct pour la création d'une moto.
     */
	public function isValid(){
        $char = '#^[a-zA-Z0-9\s]*$#';
        $int = '#^[0-9]*$#';
        if (!preg_match_all($char, $this->data[self::MODELE_REF]) 	|| 
        	!preg_match_all($char, $this->data[self::MARQUE_REF]) 	|| 
        	!preg_match_all($int, $this->data[self::ANNEE_REF]) 	|| 
        	!preg_match_all($int, $this->data[self::CYL_REF])  		|| 
        	!preg_match_all($int, $this->data[self::HP_REF])){
            $this->errors = "syntax";
            return false;
    	}elseif($this->data[self::MODELE_REF]==null ||
    			$this->data[self::MARQUE_REF]==null ||
    			$this->data[self::ANNEE_REF]==null 	||
    			$this->data[self::CYL_REF]==null 	||
    			$this->data[self::HP_REF]==null){
    		$this->errors = "missing";
    		return false;
    	}
    	return true;
    }

    public function setErrorImg(){
    	$this->errors = "img";
    }

    public function setErrorExist(){
    	$this->errors = "exist";
    }
    /*
     * Permet de mettre à jour les champs de la Moto $a avec les champs contenu dans $this->data.
     */
    public function updateMoto(Moto $a){
    	if(key_exists(self::MODELE_REF,$this->data)){
    		$a->setModele($this->data[self::MODELE_REF]);
    	}
    	if(key_exists(self::MARQUE_REF,$this->data)){
    		$a->setMarque($this->data[self::MARQUE_REF]);
    	}
    	if(key_exists(self::ANNEE_REF,$this->data)){
    		$a->setAnnee($this->data[self::ANNEE_REF]);
    	}
    	if(key_exists(self::CYL_REF,$this->data)){
    		$a->setCyl($this->data[self::CYL_REF]);
    	}
    	if(key_exists(self::HP_REF,$this->data)){
    		$a->setHp($this->data[self::HP_REF]);
    	}
    	if(key_exists(self::IMG_REF,$this->data)){
    		$a->setImg($this->data[self::IMG_REF]);
    	}
    }
}
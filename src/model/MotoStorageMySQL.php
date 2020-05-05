<?php

require_once("model/MotoBuilder.php");
define('ABSPATH', '/users/***********/www-dev/dm-inf5c-2019/');

class MotoStorageMySQL implements MotoStorage{
	private $bd;

	function __construct(){
		$this->bd = $this->chargerbd();
	}

    /*
     * Permet de charger la base de données 21713189_dev
     */
	private function chargerbd(){
		$dsn = '**************************';
		try{
			return new PDO($dsn,'***********','********************');
		}
		catch(Exception $e){
			die($e.getMessage());
		}
	}

    /*
     * Permet de retourner la moto ce trouvant la table motos, qui a pour id $id.
     */
	public function read($id){
		if($this->exist($id)){
			$request = 'SELECT modele,marque,annee,cyl,hp,name,img FROM motos INNER JOIN account ON motos.owner = account.id WHERE motos.id = :identifiant';
			$statement = $this->bd->prepare($request);
			$statement->bindValue(":identifiant",$id,PDO::PARAM_INT);
			$statement->execute();
			$result = $statement->fetch();
			return new Moto($result[MotoBuilder::MODELE_REF],$result[MotoBuilder::MARQUE_REF],$result[MotoBuilder::ANNEE_REF],$result[MotoBuilder::CYL_REF],$result[MotoBuilder::HP_REF],$result['name'],$result[MotoBuilder::IMG_REF]);
		} else {
			return null;
		}
	}

    /*
     * Permet de retourner toutes les motos de la tables motos.
     */
	public function readAll(){
		$request = "SELECT * FROM motos ORDER BY marque, modele, annee DESC";
		$statement = $this->bd->prepare($request);
		$statement->execute();
		$result = $statement->fetchAll();
		return $result;
	}

    /*
     * Permet de retourner toutes les motos de $user.
     */
	public function readAllFrom($user){
		$request = 'SELECT id, modele, annee FROM motos WHERE owner=:owner ORDER BY modele, annee DESC';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":owner",$user,PDO::PARAM_STR);
		$statement->execute();
		$result = $statement->fetchAll();
		return $result;
	}

    /*
     * Permet de retourner le dernier id de la table motos.
     */
	public function lastId(){
		$request = "SELECT id FROM motos WHERE id = (SELECT MAX(id) FROM motos)";
		$statement = $this->bd->prepare($request);
		$statement->execute();
		$result = $statement->fetch();
		if($result == null){
			return 0;
		}
		return $result['id'];
	}

	/*
     * Permet de retourner si la moto avec comme id $id existe.
     */
    public function exist($id){
    	$request = 'SELECT * FROM motos WHERE id = :identifiant';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":identifiant",$id,PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetch();
		if($result[MotoBuilder::MARQUE_REF]===null and $result[MotoBuilder::MODELE_REF]===null){
			return false;
		}
		return true;
    }

    /*
     * Permet d'ajoute une nouvelle Moto $a à la table motos, et retoure l'id de celle-ci.
     */
	public function create(Moto $a){
		$newid = (int)$this->lastId()+1;
		$request = 'INSERT INTO motos (id, modele, marque, annee, cyl, hp, owner,img) VALUES ( :id, :modele, :marque, :annee, :cyl, :hp, :owner, :img)';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":id",$newid,PDO::PARAM_INT);
		$statement->bindValue(":modele",$a->getModele(),PDO::PARAM_STR);
		$statement->bindValue(":marque",$a->getMarque(),PDO::PARAM_STR);
		$statement->bindValue(":annee", $a->getAnnee(),PDO::PARAM_INT);
		$statement->bindValue(":cyl", $a->getCyl(),PDO::PARAM_INT);
		$statement->bindValue(":hp", $a->getHp(),PDO::PARAM_INT);
		$statement->bindValue(":owner",$a->getAuthor(),PDO::PARAM_INT);
		$statement->bindValue(":img",$a->gotImg(),PDO::PARAM_BOOL);
		$statement->execute();
		return $newid;
	}


    /*
     * Permet de supprimer la moto avec comme id $id.
     */
	public function delete($id){
		$request = 'DELETE FROM motos WHERE id = :identifiant';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":identifiant",$id,PDO::PARAM_INT);
		$statement->execute();
	}

    /*
     * Permet de mettre à jour dans la tables motos, la moto avec m'id $id avec les nouveaux caractéristiques
     * de la Moto $a.
     */	
	public function update($id,Moto $a){
		$motoBase = $this->read($id);
		if($motoBase->gotImg()){
			$img_base = $motoBase->returnImg();
		}
		$request = 'UPDATE motos SET modele=:modele, marque=:marque, annee=:annee, cyl=:cyl, hp=:hp, img=:img WHERE id=:id';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":id",$id,PDO::PARAM_INT);
		$statement->bindValue(":modele",$a->getModele(),PDO::PARAM_STR);
		$statement->bindValue(":marque",$a->getMarque(),PDO::PARAM_STR);
		$statement->bindValue(":annee", $a->getAnnee(),PDO::PARAM_INT);
		$statement->bindValue(":cyl", $a->getCyl(),PDO::PARAM_INT);
		$statement->bindValue(":hp", $a->getHp(),PDO::PARAM_INT);
		$statement->bindValue(":img",$a->gotImg(),PDO::PARAM_BOOL);
		$statement->execute();
		if($a->gotImg() and isset($img_base)){
			$ext = pathinfo($img_base,PATHINFO_EXTENSION);
			$img_name = $a->getModele().$a->getMarque().$a->getAnnee().$a->getCyl();
			$img_name = str_replace(' ', '', $img_name);
			rename(ABSPATH.$img_base,ABSPATH.'upload/'.$img_name.'.'.$ext); // Permet de renommé l'image avec les nouveaux caractéristique.
		}
	}

	/*
     * Permet de vérifié si $moto est déja utilisé ou non dans la table motos.
     */
	public function isDispo($moto){
		$request = 'SELECT id FROM motos WHERE modele=:modele and marque=:marque and annee=:annee';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":modele",$moto[MotoBuilder::MODELE_REF],PDO::PARAM_STR);
		$statement->bindValue(":marque",$moto[MotoBuilder::MARQUE_REF],PDO::PARAM_STR);
		$statement->bindValue(":annee",$moto[MotoBuilder::ANNEE_REF],PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetch();
		if($result==null or $result['id']==$moto['id']){
			return true;
		}
		return false;
	}

    /*
     * Permet de vérifié si il est possible de créer un nouvelle moto en vérifiant si il existe pas une moto avec le même modèle, marque et année.
     */
	public function createVerif($data){
		$request = 'SELECT COUNT(*) FROM motos WHERE modele=:modele and marque=:marque and annee=:annee';
		$statement = $this->bd->prepare($request);
		$statement->bindValue(":modele",$data[MotoBuilder::MODELE_REF],PDO::PARAM_STR);
		$statement->bindValue(":marque",$data[MotoBuilder::MARQUE_REF],PDO::PARAM_STR);
		$statement->bindValue(":annee",$data[MotoBuilder::ANNEE_REF],PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetch();
		if($result[0]==='0'){
			return true;
		}
		return false;
	}
}

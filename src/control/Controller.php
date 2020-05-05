<?php
require_once("view/View.php");
require_once("model/Moto.php");
require_once("model/MotoBuilder.php");
class Controller
{
	protected $view;
	protected $motoStorage;
	protected $authenticationManager;

	function __construct(View $view,MotoStorage $motoStorage,AuthenticationManager $authenticationManager){
		$this->view = $view;
		$this->motoStorage = $motoStorage;
		$this->currentNewMoto = key_exists('currentNewMoto',$_SESSION) ? $_SESSION['currentNewMoto'] : new MotoBuilder(null);
		$this->authenticationManager = $authenticationManager;
	}

	function __destruct(){
		$_SESSION['currentNewMoto'] = $this->currentNewMoto;
	}


	/*
     * Permet de remplir le contenue de la vue avec la méthode appelé de celle ci makeMotoPage.
     */
	public function showInformation($id) {
		$moto = $this->motoStorage->read($id);
		if($moto != null){
			$this->view->makeMotoPage($id,$moto);
		}

		else{
			$this->view->makeUnknownMotoPage();
		}

	}

	/*
     * Permet de remplir le contenue de la vue avec la méthode appelé de celle ci makeListPage.
     */
	public function showList(){
		$this->view->makeListPage($this->motoStorage->readAll());
	}

	/// etc....
	public function showLogin(){
		$this->view->makeLoginFormPage();
	}

	/// etc....
	public function showManage(){
		$this->view->makeManagePage($this->authenticationManager->getNbAcc(),$this->authenticationManager->getUsers(),$this->motoStorage);
	}

	public function showCreationPage(){
		$this->view->makeMotoCreationPage($this->currentNewMoto);
	}

	public function showModifPage($id){
		$this->view->makeMotoModifPage($id,$this->motoStorage->read($id));
	}

	public function showAccount(){
		$this->view->makeAccountPage();
	}

	/*
	 * Permet de sauvegarder une nouvelle moto.
	 */
	public function saveNewMoto(array $data){
		// On vérifie si cette moto a une image.
		if($_FILES['img']['error']===0){
			$data['img']='1';
		}else{
			$data['img']='0';
		}
		$this->currentNewMoto = new MotoBuilder($data);
		if($this->currentNewMoto->isValid() and $this->motoStorage->createVerif($data)){ // Vérifie si disponible et bonne syntax.
			if($this->saveImgMoto($data)){ // On vera plus bas.
				$moto = $this->currentNewMoto->createMoto(); // Retourne une moto avec les champs contenu dans data.
				$motoId = $this->motoStorage->create($moto); // L'ajoute à la table, et retourne son id dans celle-ci.
				$this->currentNewMoto = new MotoBuilder(null); // CurrentMoto = Moto(null), on voit çà tout de suite.
				$this->view->displayMotoCreationSuccess($motoId); // Redirection vers la page de la moto, avec un feedback positif.
			}
			$this->currentNewMoto->setErrorImg(); // il y a eu une erreur avec l'image.
			$this->view->displayMotoCreationError(); // Retour sur la page de création avec currentNewMoto, pour conserver les champs déjà rentrer par l'utilisateur. Ainsi qu'un feedback.
		}
		else{
			if(!$this->motoStorage->createVerif($data)){
				$this->currentNewMoto->setErrorExist(); // il y a eu une erreur, elle existe.
			}	
			$this->view->displayMotoCreationError(); // Retour sur la page de création avec currentNewMoto, pour conserver les champs déjà rentrer par l'utilisateur. Ainsi qu'un feedback.
		}
	}

	/*
	 * Permet de sauvegarder une nouvelle image.
	 */
	public function saveImgMoto($data){
		$taille_max = 1000000; // taille max de l'image.
		$extensions = array('.png','.gif','.jpg','.jpeg'); // extensions possible de l'image

		$img = basename($_FILES['img']['name']); // image uploadé.
		$img_taille = filesize($_FILES['img']['tmp_name']); // taille de l'image uploadé.
		$img_extension = strrchr($_FILES['img']['name'],'.'); // extension de l'image uploadé.

		$new_imgName = $data[MotoBuilder::MODELE_REF].$data[MotoBuilder::MARQUE_REF].$data[MotoBuilder::ANNEE_REF].$data[MotoBuilder::CYL_REF].$img_extension; // Créer le nom de l'image.
		$new_imgName = str_replace(' ', '', $new_imgName); // Supprime tout les espaces, pour éviter les conflits.

		if(in_array($img_extension, $extensions) and $img_taille<$taille_max and $_FILES['img']['error']===0){ // vérifie si la taille est bonne et que l'extension est correcte et pas d'erreur.
			move_uploaded_file($_FILES['img']['tmp_name'], 'upload/'.$new_imgName); // on déplace l'image dans le répertoire upload avec son nouveau nom.
			return true; // tout c'est bien passé.
		}elseif($_FILES['img']['error']===4){ // Si erreur == 4, cela veut dire qu'il n'y a pas d'image.
			return true; // tout c'est bien passé. Bah oui pas forcé d'avoir une image.
		}
		return false; // La par contre il à un problème.(taille, ou extension).
	}

	/*
	 * Permet de supprimer l'image de $moto.
	 */
	public function removeImg($moto){	
		unlink($moto->returnImg()); // on la supprime dans le dossier.
	}

	/*
	 * Permet de mettre à jour une Moto.
	 */
	public function saveModifMoto($data){
		$remove = false; // de base on ne supprime pas l'image.
		if(isset($data['deleteImg'])){ // si deleteImg existe. Cela veut dire que User à cocher la checkbox "Supprimer".
			$data['img']='0'; // On modifie les champs pour mettre à jour dans la table.
			$remove = true; // l'image va être supprimé.
		}elseif($_FILES['img']['error']===4 and $data['img']==='1'){ //si pas d'image uploadé mais que de base il y en as une.
			$data['img']='1'; // Dans la base elle est toujours là
			$new = false; // pas de nouvelle image.
		}elseif($_FILES['img']['error']===0){ // Pas d'image de base mais une viens d'être uploadé.
			$data['img']='1'; // On va le mettre à jour dans la table.
			$new = true; // 
		}else{
			$data['img']='0';
			$new = false;
		}
		$moto = $this->motoStorage->read($data["id"]);
		$moto->setImg($data['img']);
		$builder = new MotoBuilder($data);
		if($builder->isValid() and $this->motoStorage->isDispo($data)){
			$builder->updateMoto($moto);
			$this->motoStorage->update($data["id"],$moto);
			if($remove === true){ // on supprime si il faut la supprimé
				$this->removeImg($moto);
			}
			if($builder->gotImg() and $new===true){ // Si la moto à une image et que cele ci est nouvelle
				$this->saveImgMoto($data); // on la sauvegarde
			}

			$this->view->displayMotoModifiedSuccess($data["id"]); //modifié avec succès
		} else {
			$this->view->displayMotoModifiedError($data["id"]); // Petit soucis.
		}
	}

	/*
	 * Permet d'ajouter un nouvelle utilisateur..
	 */
	public function saveNewUser($data){
		if($this->authenticationManager->addNewUser($data)){ // On l'ajoute.
			$this->logedVerif($data); // On essaye de le connecté.
		}
			$this->view->displayUsersCreationError($data); // Petit soucis.
	}

	/*
	 * Permet de voir si un User peut bien ce connecté, si c'est le cas, le connecte.
	 */
	public function logedVerif($data){
		if($this->authenticationManager->connectUser($data['name'],$data['password'])){ // on essaye de le connecté
			$this->view->displayUsersConnected(); // C'est bon il est connecté
		} else {
			$this->view->displayUsersConnectionFailed(); // Non c'est loupé, on retourne à la page pour ce reconnecter et on recommence.
		}
	}

	/*
	 * Permet de ce déconnecter.
	 */
	public function logouVerif(){
		$this->authenticationManager->disconnectUser(); // On le déconnecte.
		$this->view->displayUsersDiconnected(); // On le redirige.
	}

	/*
	 * Permet d'afficher une confirmation pour la suppression d'une moto.
	 */
	public function confimationDelete($id){
		if(key_exists('id', $id)){
			$this->view->makeMotoDeletion($id['id']);
		}else{
			$this->view->makeUnknownMotoPage();
		}
	}

	/*
	 * Permet de supprimer la moto.
	 */
	public function deleteMoto($id){
		$moto = $this->motoStorage->read($id['id']); // On la lit, et on stocke cette moto.
		if($moto->gotImg()){	// On vérifie si elle as une image.
			$this->removeImg($moto); // Si c'est le cas, on la supprime.
		}
		$this->motoStorage->delete($id['id']); // On supprime la moto dans la base.
		$this->view->displayMotoDeletedSuccess(); // On affiche la suppression avec succès.
		
	}
	/*
	 * Permet de réaliser l'action réalisé par un Admin.
	 */
	public function managerUpdate($user){
		if($user['action']==="update"){ // si action est de mettre à jours.
			if($this->authenticationManager->update($user)){ // On mets à jour.
				$this->view->displayAccountUpdateSuccess(); // C'est OK.
			} else {
				$this->view->displayAccountUpdateError(); // Il y a eu un problème.
			}
		} elseif($user['action']==="delete"){ // si action est de supprimer.
			if($this->authenticationManager->delete($user['id'])){ // On supprime.
				$this->view->displayAccountDeleteSuccess(); // C'est OK.
			} else {
				$this->view->displayAccountDeleteError(); // Il y a eu un problème, l'account qu'il veut supprimer a encore des motos.
			}
		}
	}

	/*
	 * Permet de mettre à jour User.
	 */
	public function saveModifUser($data){
		if($this->authenticationManager->userUpdate($data)){ // On mets à jour.
			$this->view->displayUserUpdateSuccess(); // C'est OK.
		} else {
			$this->view->displayUserUpdateError(); // Il y a eu un problème.
		}
	}
	
	// Pas le droit d'accédé à cette page.
	public function wrongAccess(){
		$this->view->accessDenied();
	}
}

?>

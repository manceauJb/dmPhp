<?php

require_once("Router.php");
require_once("model/Moto.php");
require_once("acc/Account.php");

class AdminView extends View{

	private $title; /// Titre
	private $content; /// Contenue
	private $router; /// Routeur
	private $authManager; /// Authentification Manager
	private $account; /// Utitisateur connecté
	private $marque; /// Liste contenant toutes les marques de motos.(Amélioration : Utilisé un BD).



	function __construct(Router $router, $feedback, Account $account){
		$this->feedback = $feedback;
		$this->title = null;
		$this->content = null;
		$this->router = $router;
		$this->account = $account;
		$this->marque = array("Suzuki","Yamaha","KTM","Harley Davidson","Triumph","Kawasaki","Ducati","Honda","Solex","BMW","Piaggio","Aprilia","Peugeot","MV Agusta","Husqvarna");
	}

	/*
	 * Génère la page avec le contenu préalablement charger.
	 */
	public function render(){
		$title = $this->title;
		$content = $this->content;
		include("Squellette.php");
	}

	/*
	 * Remplis le contenu avec les informations sur la moto $a, avec son id $id.
	 */
	public function makeMotoPage($id, Moto $a){
		$this->title = " Page sur " . $a->getModele();
		$this->content  = '<article>';
		$this->content .= "<p>".$a->getModele() . " est un moto de chez " . $a->getMarque() . " sortie en " . $a->getAnnee(). " .</p>";
		$this->content .= "<p>Cylindrée : ". $a->getCyl()." cm².</p>";
		$this->content .= "<p>Puissance : ". $a->getHp()." chevaux</p>";
		$this->content .= '<p>Son auteur est '.$a->getAuthor().".</p>";
		$this->content  .= '</article>';
		/*
	     * Si il y a une image, alors on l'ajoute au contenu. 
	     */
		if($a->gotImg()){
			$this->content.= '<figure>
								<img src="'.$a->returnImg().'" alt="img">
							  </figure>';
		}
		$this->content .= '<form class="showForm" action="'.$this->router->getModifPage($id). '" method=POST>
						   <input type="hidden" name="id" value="'.$id.'"/>						   
						   <input type="submit" value="Modifier"/>
						   </form>';
		$this->content .= '<form class="showForm" action="'.$this->router->getMotoAskDeletionURL($id). '" method=POST>
						   <input type="hidden" name="id" value="'.$id.'"/>						   
						   <input type="submit" value="delete"/>
						   </form>';
	}

	/*
	 * Remplis le contenu avec la page d'accueil.
	 */
	public function makeHomePage(){
		$this->title = "Home";
		$this->content = '<div class="middle">
							<h2>Bonjour '.$_SESSION['user']->getName().',</h2>
							<p>Content de vous revoir sur la page des motos.</p>
							<p>Allez voir si de nouveaux modèles se sont ajouté à la liste !</p>
							<p>Si vous ne voyez pas un modèle, n\'hésiter pas à l\'ajouter avec sa photo.</p>
							<p> <a href="'.$this->router->getListUrl().'"> Lien vers liste motos</a></p>';
	}


	/*
	 * Remplis le contenu avec un formulaire pour la création d'une moto.
	 */
	public function makeMotoCreationPage(MotoBuilder $moto){
		$this->title = "Création Moto";
		$this->content = '<div class="middle" ><h2>Création moto :</h2>
						<form action="'.$this->router->getMotoSaveURL().'" enctype="multipart/form-data" method="POST">
						  <p>Modele </p> <input type="text" name='.MotoBuilder::MODELE_REF.' value="'.$moto->getModele().'"/>
						  <p>Marque </p> <select name="'.MotoBuilder::MARQUE_REF.'">
						  					<option value=""> Select Marque </option>';
						foreach($this->marque as $name){
							$this->content .= '<option value="'.$name.'">'.$name.'</option>';
						}
		$this->content .= '</select>
						  <p>Annee </p> <input type="text" name='.MotoBuilder::ANNEE_REF.' value="'.$moto->getAnnee().'"/>
						  <p>Cylindrée </p> <input type="text" name='.MotoBuilder::CYL_REF.' value="'.$moto->getCyl().'"/>
						  <p>Puissance </p> <input type="text" name='.MotoBuilder::HP_REF.' value="'.$moto->getHp().'"/>
						  <p>Image(optionel)</p><input type="file" name="img"/>
						  <input type="hidden" name="author" value="'.$_SESSION['user']->getId().'"/>
						  <br /><input type="submit" value="Créer" />
						</form>';
	   /*
	 	* vérifie si il y a une erreur, et si il y en a une affiche cette erreur. 
	 	*/
		if($moto->getErrors()==='missing'){
			$this->content .= "<h3>Il vous manque un item !!</h3>";
		}elseif($moto->getErrors()==='syntax'){
			$this->content .= "<h3>Interdit !!!</h3>";
		}elseif($moto->getErrors()==='img'){
			$this->content .= "<h3>Une erreur c'est produite avec img</h3>";
		}elseif($moto->getErrors()==='exist'){
			$this->content .= "<h3>Cette moto existe déjà dans la base</h3>";
		}
		$this->content .= '</div>';

	}

	/*
	 * Remplis le contenu avec un formulaire qui confirme la suppression de la moto avec id=$id.
	 */
	public function makeMotoDeletion($id){
		$this->title = "Confirmation";
		$this->content = '<div class="middle" ><h2>Are you sure ?</h2>
						  <form action="'.$this->router->getMotoDeletionURL($id).'" method=POST>
						  <input type="hidden" name="id" value="'.$id.'"/>
						  <input type="submit" value="Confirmer"/>
						  </form></div>';
	}

	/*
	 * Remplis le contenu avec un formulaire pour modifier la moto $moto avec pour id $id.
	 */
	public function makeMotoModifPage($id,Moto $moto){
		$this->title = "Modifier la Moto";
		$this->content = '<div class="middle"><h2>Modifier votre Moto </h2>
							<form class="modif" action="'. $this->router->updateModifiedMoto($id).'" enctype="multipart/form-data" method="POST">
						  		<p>Modele </p> <input type="text" name='.MotoBuilder::MODELE_REF.' value="'.$moto->getModele().'"/>
						  		<p>Marque </p> <select name="'.MotoBuilder::MARQUE_REF.'">
						  					<option value="'.$moto->getMarque().'"> '.$moto->getMarque().'</option>';
						foreach($this->marque as $name){
							if($name !== $moto->getMarque())
								$this->content .= '<option value="'.$name.'">'.$name.'</option>';
							}
		$this->content .= '</select>
						  		<p>Annee </p> <input type="text" name='.MotoBuilder::ANNEE_REF.' value="'.$moto->getAnnee().'"/>
						  		<p>Cylindrée </p> <input type="text" name='.MotoBuilder::CYL_REF.' value="'.$moto->getCyl().'"/>
						  		<p>Puissance </p> <input type="text" name='.MotoBuilder::HP_REF.' value="'.$moto->getHp().'"/>
						  		<input type="hidden" name="img" value="'.($moto->gotImg() ? "1":"0").'"/>
						  		<input type="hidden" name="id" value="'.$id.'"/>
						  		<p>Image('.($moto->gotImg() ? "Remplacera l'ancienne":"Optionel").')</p><input type="file" name="img"/>'.
						  		($moto->gotImg() ? '<input type="checkbox" id="delete" name="deleteImg"/>
								<label for="delete">Supprimer Image</label>':"").'
							  	<br /><input type="submit" value="Modifier"/>
						  	</form>';
		/*
	 	 * Si $moto a une image, elle sera affiché. 
	 	 */		
		if($moto->gotImg()){
			$this->content.= '<figure>
								<img src="'.$moto->returnImg().'" alt="img">
							  </figure>';
		}
		$this->content .= "</div>";
	}

	/*
	 * Remplis le contenu avec une liste des motos classé par marque. 
	 */
	public function makeListPage($moto){
		$this->title = "Liste";
		$string = '<div class="middle"> <ul class="liste">';
		
		$tmpMarque = null;
		foreach ($moto as $key => $value) {
			if(strcasecmp($value[MotoBuilder::MARQUE_REF],$tmpMarque)){
				if($tmpMarque !== null){
					$string .= "</ul>";
				}
				$tmpMarque = $value[MotoBuilder::MARQUE_REF];
				$string .= "<li><h2>".$tmpMarque ."</h2><ul>";
			}
			$string .= '<li> <a href="'. $this->router->getUrl($value['id']) . '">'.$value[MotoBuilder::MODELE_REF]." - ".$value[MotoBuilder::ANNEE_REF]."</a></li>";
		}
		$string .= '</ul></ul> <a href="'.$this->router->getMotoCreationURL().'"> Lien vers création moto</a></div>';
		$this->content = $string;
	}

	/*
	 * Permet de créer le menu avec les différents liens Accessible pour cette Utilisateur.
	 */
	public function getMenu(){
		$menu = array(
			"Accueil"	=> $this->router->homePage(),
			"Liste"		=> $this->router->getListUrl(),
			"Ajouter"	=> $this->router->getMotoCreationURL(),
			"Manage"	=> $this->router->getManagerUrl(),
			"Logout"	=> $this->router->getLogout());
		return $menu;
	}

	/*
	 * Remplis le contenu la liste de tout les Users, et leurs pages perso. 
	 */
	public function makeManagePage($nb,$tab,$storage){
		$this->title = "Manager Page";
		$this->content = "<h2>Nombres d'Account total : ".$nb." users.</h2>";
		$this->content .= '<div class="middle manage" ><h3>Liste users :</h3>';
		foreach ($tab as $key => $value) {
			if($value['admin']==="0"){
				$checked = "";
			} else {
				$checked = "checked";
			}
			$this->content .= '	<form action="'.$this->router->getManageUpdate().'" method="POST">
									<input type="hidden" name="id" value="'.$value['id'].'"/>
									Name
									<input type="text" name="name" value="'.$value['name'].'"/>
									Login
									<input type="text" name="login"  value="'.$value['login'].'"/>
									
									<input type="checkbox" name="admin" value="admin" '.$checked.' />
									Admin
									<input type="submit" name="action" value="update"/>
									<input type="submit" name="action" value="delete"/>
								</form>';
			$getAccountMoto = $storage->readAllFrom($value['id']);
			if($getAccountMoto!=null){
				$this->content .= "<p>User's moto :</p><ul>";
				foreach ($getAccountMoto as $key => $value) {
					$this->content .= '<li> <a href="'. $this->router->getUrl($value['id']) . '">'.$value[MotoBuilder::MODELE_REF]." - ".$value[MotoBuilder::ANNEE_REF]."</a></li>";
				}
				$this->content .= "</ul>";
			}
		}
		$this->content .= "</div>";
	}

	/*
	 * Remplis le contenu avec plusieurs informations sur ce site.
	 */
	public function showInfos(){
		$this->title = "Informations";
		$this->content = '<div class="middle">';
		$this->content .= '<h1>Informations :</h1>';
		$this->content .= '<h3>Numétu : 21713189</h3>';
		$this->content .= '<ul>';
		$this->content .= '<li>Ce site permet d\' ajouter/modifier/retirer des motos.</li>';
		$this->content .= '<li>Ayant chacune un modèle, une marque, année, puissance, cylindrée et la possibilité d\' avoir une image.</li>';
		$this->content .= '	<li>Deux types de compte sont possibles :
							<ul>
								<li>User</li>
								<li>Admin</li>
							</ul></li>';
		$this->content .= '	<li>Pour <b>User</b>, possibilité de :
							<ul>
								<li>D\' ajouter une/des moto(s).</li>
								<li>De la/les modifier.</li>
								<li>De la/les supprimer.</li>
								<li>UNIQUEMENT les siennes.</li>
								<li>Modifier son nom, login, password.</li>
							</ul>
							</li>';
		$this->content .= '	<li>Pour <b>Admin</b>, possibilité de :
							<ul>
								<li>D\' ajouter une/des moto(s).</li>
								<li>De la/les modifier.</li>
								<li>De la/les supprimer.</li>
								<li>Peu importe l\' auteur.</li>
								<li>Accès à une page permettant de gérer les comptes.</li>
								<li>Mais il lui est impossible de supprimer un compte ayant encore des motos sur le site.</li>
							</ul>
							</li>';
		$this->content .= '<li>Chacun disposant d\'une vue différente.</li>';
  		$this->content .= '<li>Un invité arrivant sur le site peut ce créer un compte (auto en mode User), avec un mdp encrypté avec BCRYPT. En étant invité il ne peut que voir la liste des motos mais pas les pages indiv.</li>';
		$this->content .= '<li>Deux tables sont utilisées, motos et account dans la  base 21713189_dev.</li>';
		$this->content .= '<li>Si on modifie le nom d\' un Account et qu\' il possède un page sur une moto, son nom sera directement modifié aussi.</li>';
		$this->content .= '<li>S\'il y a une image, elle sera stockée dans le dossier upload, et renommé automatiquement en fonction de sa marque, modèle, année, cylindrée.</li>';
		$this->content .= '<li>Les droits d\'accès sont gérés par le Router,le controlleur et authManager.</li>';
		$this->content .= '<li>Si on modifie un des critères cité précédemment, ce changement sera appliqué à l\' image directement.</li>';
		$this->content .= '<li>Le site n\' pas responsive design (ou vraiment très peu), le style n\' est pas très élaboré et ce site ne contient pas de JS.</li>
			</ul>';
		$this->content .= '<h3>Comptes déjà existant :</h3>';
		$this->content .= '<ul>
								<li><b>Login</b>: admin <b>Password:</b> admin</li>
								<li><b>Login</b>: user1 <b>Password:</b> user1</li>
								<li><b>Login</b>: user2 <b>Password:</b> user2</li>
							</ul>';

		$this->content .= '<h3>Sources :</h3>';
		$this->content .= '	<ul>
								<li><a href="https://www.php.net/">PHP</a></li>
								<li><a href="https://openclassrooms.com/en/courses/918836-concevez-votre-site-web-avec-php-et-mysql"> OpenClassRooms</a></li>
								<li><a href="https://github.com/">Github</a> Beaucoup trop de liens.</li>
								<li><a href="https://stackoverflow.com/">Stackoverglow</a> Beaucoup trop de liens.</li>
							</ul>';
		$this->content .= '<h3>Amélioration :</h3>';
		$this->content .= '	<ul>
								<li>Table pour les différentes marques.</li>
								<li>Page pour avoir des infos sur une marque</li>
								<li>Pouvoir rajouter des marques.</li>
							</ul>';

		$this->content .= '</div>';
	}
	
	public function makeErrorPage(){
		$this->title = "ERROR";
		$this->content = "<p>QQCH c'est mal passé</p>";
	}

	public function makeUnknownMotoPage(){
		$this->title = "ERROR";
		$this->content = "<p>Cet moto n'existe pas</p>";
	}

	/////////////// Toutes les méthodes permettant d'afficher un feedback en fonction d'une action. ///////////////

	public function displayMotoCreationSuccess($id){
		$this->router->POSTredirect($this->router->getUrl($id),"Moto as bien été Créer");
	}

	public function displayMotoModifiedSuccess($id){
		$this->router->POSTredirect($this->router->getUrl($id),"Moto as bien été Modifier");
	}

	public function displayMotoDeletedSuccess(){
		$this->router->POSTredirect($this->router->getListUrl(),"Moto deleted SUCCESS");
	}

	public function displayMotoModifiedError($id){
		$this->router->POSTredirect($this->router->getModifPage($id),"ERROR dans le formulaire");
	}

	public function displayMotoCreationError(){
		$this->router->POSTredirect($this->router->getMotoCreationURL(),"ERROR dans le formulaire");
	}

	public function displayUsersConnected(){
		$this->router->POSTredirect($this->router->homePage(),"Bienvenue ".$_SESSION['user']->getName());		
	}

	public function displayUsersConnectionFailed(){
		$this->router->POSTredirect($this->router->getLoginPage(),"Erreur dans les ID");		
	}

	public function displayUsersDiconnected(){
		$this->router->POSTredirect($this->router->homePage(),"A bientot !");
	}

	public function accessDenied(){
		$this->router->POSTredirect($this->router->homePage(),"Access DENIED, vous n'avez pas les droits !!!");
	}

	public function displayAccountUpdateSuccess(){
		$this->router->POSTredirect($this->router->getManagerUrl(),"Update Success");
	}
	public function displayAccountUpdateError(){
		$this->router->POSTredirect($this->router->getManagerUrl(),"Update ERROR");
	}
	public function displayAccountDeleteSuccess(){
		$this->router->POSTredirect($this->router->getManagerUrl(),"Delete Success");
	}
	public function displayAccountDeleteError(){
		$this->router->POSTredirect($this->router->getManagerUrl(),"Delete ERROR");
	}

}




?>
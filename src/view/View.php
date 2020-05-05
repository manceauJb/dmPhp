<?php

require_once("Router.php");
require_once("model/Moto.php");
class View
{
	private $title; /// Titre
	private $content; /// Contenue
	private $router; /// Routeur
	private $authManager; /// Authentification Manager


	function __construct(Router $router, $feedback){
		$this->feedback = $feedback;
		$this->title = null;
		$this->content = null;
		$this->router = $router;
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

	public function makeErrorPage(){
		$this->title = "ERROR";
		$this->content = "<p>QQCH c'est mal passé</p>";
	}

	/*
	 * Remplis le contenu avec la page d'accueil.
	 */
	public function makeHomePage(){
		$this->title = "Home";
		$this->content = '<div class="middle">
							<h2> Bienvenue,</h2>
							<p>sur la page des motos.</p>
							<p>Pour une meilleur expérience il est préférable d\'avoir un compte.</p>
							<p><a href="'.$this->router->getLoginPage().'">Onglet connexion</a></p>
						  </div>';
	}

	/*
	 * Remplis le contenu avec un formulaire pour ce connecter,
	 * ou pour ce créer un compte.
	 */
	public function makeLoginFormPage($data = array("name"=>null,"login"=>null)){
		$this->title = "Login";
		$this->content = 	'<div class="middle"><p>Connectez-vous:</p>
							 <form action="'.$this->router->getConfirmUser().'" method=POST name="login">
							 	Login: <input type="text" name="name"/>
							 	Password: <input type="password" name="password"/>
							 	<input type="submit" value="Connexion"/>
							 </form>
							 ';
		$this->content .=   '<p>Si ce n est pas le cas créer vous un compte :</p>
							<form action="'.$this->router->getCreationUser().'" method=POST name="creation">
								<p>Name  <input type="text" name="name" value="'.$data["name"].'"/></p>
								<p>Login <input type="text" name="login" value="'.$data["login"].'"/></p>
								<p>Passw <input type="password" name="password"/></p>
								<br /><input type="submit" value="S inscrire"/>
							</form>
						</div>';

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
		$this->content .= '<li>Le site n\' pas responsive design(ou vraiment très peu), le style n\' est pas très élaboré et ce site ne contient pas de JS.</li>
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

	/*
	 * Permet de créer le menu avec les différents liens Accessible pour cette Utilisateur.
	 */
	public function getMenu(){
		$menu = array(
			"Accueil"	=> $this->router->homePage(),
			"Liste"		=> $this->router->getListUrl(),
			"Login"		=> $this->router->getLoginPage());
		return $menu;
	}


	/////////////// Toutes les méthodes permettant d'afficher un feedback en fonction d'une action. ///////////////
	public function displayUsersConnected(){
		$this->router->POSTredirect($this->router->homePage(),"Bienvenue ".$_SESSION['user']->getName());		
	}

	public function displayUsersConnectionFailed(){
		$this->router->POSTredirect($this->router->getLoginPage(),"Erreur dans Login/Password");		
	}

	public function accessDenied(){
		$this->router->POSTredirect($this->router->homePage(),"Access DENIED");
	}

	public function displayUsersCreationError($data){
		$this->router->POSTredirect($this->router->getLoginPage($data),"Account non disponible");
	}
}
?>

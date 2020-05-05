<!DOCTYPE html>
<html lang="fr">
<head>
	<title><?php echo $title ?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" href="skin/moto.css" />
</head>
<body>
	<nav >
		<ul class="menu">
		<?php
		foreach ($this->getMenu() as $text => $link) {
			echo '<li><a href="'.$link.'">'.$text.'</a></li>';
		}
		?>
		</ul>
	</nav>
	<main>
	<?php
		if($this->feedback !=='')
			echo '<div class="feedback">'.$this->feedback.'</div>';	
	
	echo $content; ?>
	</main>
	<footer>
		<section>
			<?php echo '<a href="?infos" class="info">A propos</a>';?>
			
		</section>

			<h5>©2019. 21713189</h5>

	</footer>
</body>
</html>
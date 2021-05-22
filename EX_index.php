<html>
<head>
	<title>Exemple d'utilisation de VidPHPconvert</title>
	<style type="text/css" media="all">
		@import "data/css.css";
	</style>
	
	<script type="text/javascript" src="data/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="data/js.js" ></script>
</head>
<body>
	<div id="title">
		<h1>Exemple d'utilisation de VidPHPconvert</h1>
	</div>
	<div id="selection"><form action="javascript:form_submit();" method="post">
		Liste fichier a convertir :
		
		<select id="allfile">
			<option value="0">Rafraichir liste</option>
		</select>
		
		<br /><span id="secondchoix" class="inactive">resultat :
		
		<select id="method" disabled="disabled">
			<option value="3">Video web</option>
			<option value="2">Gif animé</option>
			<option value="1">Image aléatoire</option>
		</select></span>
		
		<br /><input type="submit" value="Executer" />
	</form></div>
	<div id="avancement">
	</div>
</body>
</html>

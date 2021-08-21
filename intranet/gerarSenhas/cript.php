<!DOCTYPE HTML>
<html>
	<head>
		<title>Cript</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	</head>
	<body>
		<?php
		    /*
	    CRIOPTOGRAFIA DUPLA POR EDUARDO BORGES
	    */
	    function encripta($senha){
	    // VEJA QUE PRIMEIRO EU VOU GERAR UM SALT JÁ ENCRIPTADO EM MD5
	    $salt = md5($_POST['login']);
	     
	    //PRIMEIRA ENCRIPTAÇÃO ENCRIPTANDO COM crypt
	    $codifica = crypt($senha,$salt);
	     
	    // SEGUNDA ENCRIPTAÇÃO COM sha512 (128 bits)
	    $codifica = hash('sha512',$codifica);
	     
	    //AGORA RETORNO O VALOR FINAL ENCRIPTADO
	    return $codifica;
	     
	    }
	    // EXEMNPLO DE USO
	    echo  "Sua senha criptografada no banco fica assim:   ". encripta($_POST['senha']);
		echo "<br>";
        echo utf8_encode($_POST['c']);
      ?>
	</body>
</html>
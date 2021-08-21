<?php
$banco = new PDO('mysql:host=mysql.nutribemrefeicoescoletivas.com.br;dbname=nutribemrefeic01', 'nutribemrefeic01','ADMintranet1748')or print (mysql_error());
print "Conexão Efetuada com sucesso!";
?>
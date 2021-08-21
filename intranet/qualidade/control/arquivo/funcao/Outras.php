<?php
    ini_set('default_charset','UTF-8');
    define("BASE",'Http://www.nutribemrefeicoescoletivas.com.br/intranet/qualidade/');
    function anti_injection($sql){
    $sql = preg_replace("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/", "" ,$sql);
    $sql = trim($sql);
    $sql = strip_tags($sql);
    $sql = (get_magic_quotes_gpc()) ? $sql : addslashes($sql);
    return $sql;
    }
    function Cript($login,$senha){
        // VEJA QUE PRIMEIRO EU VOU GERAR UM SALT JÁ ENCRIPTADO EM MD5
        $salt = md5($login);
        //PRIMEIRA ENCRIPTAÇÃO ENCRIPTANDO COM crypt
        $codifica = crypt($senha,$salt);
        // SEGUNDA ENCRIPTAÇÃO COM sha512 (128 bits)
        $codifica = hash('sha512',$codifica);
        //AGORA RETORNO O VALOR FINAL ENCRIPTADO
        return $codifica; 
    }
    
?>
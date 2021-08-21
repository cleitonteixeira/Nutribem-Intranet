<?php
    setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1","pt_BR.utf-8", "portuguese");
    date_default_timezone_set('America/Sao_Paulo');
    ini_set('default_charset','UTF-8');
    define("BASE",'Http://www.nutribemrefeicoescoletivas.com.br/intranet/manutencao/');
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
    
    function buscaSuperior( $control ){
        $conexao = conexao::getInstance();
        $sql = "SELECT idusuarios FROM usuarios WHERE Superior = ?;";
        if ($control->rowCount() > 0){
            $superior = $control->fetchAll(PDO::FETCH_OBJ);
            foreach($superior as $x){
                array_unique($_SESSION['dados']);
                array_push($_SESSION['dados'], $x->idusuarios);
            }
            foreach($superior as $s){
                $stmt = $conexao->prepare($sql);
                $stmt->bindParam(1, $s->idusuarios);
                $stmt->execute();
                $control = $stmt;
                buscaSuperior( $control );
            }
        }
    }
?>
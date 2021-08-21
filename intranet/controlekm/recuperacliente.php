<?php
/* prepara o documento para comunicação com o JSON, as duas linhas a seguir são obrigatórias 
	  para que o PHP saiba que irá se comunicar com o JSON, elas sempre devem estar no ínicio da página */
//header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=utf-8"); 
if(true){
    require("../control/banco/conexao.php");
    require("../control/arquivo/funcao/Dados.php");
    $conexao = conexao::getInstance();
    $d = array();
    $sql = "SELECT * FROM contratante ct";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $contador = $stmt->rowCount();
    
    $sort = rand(2,$contador);
    try{
        
        $sql = "SELECT cd.Nome, cd.CNPJ FROM contratante ct INNER JOIN cadastro cd ON cd.idCadastro = ct.Cadastro_idCadastro WHERE ct.idContratante = ? LIMIT 1";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(1, $sort, PDO::PARAM_INT);
        $stmt->execute();
        $rs = $stmt->fetch(PDO::FETCH_OBJ);
        $Itens = [
            "Nome"	 	=> utf8_decode($rs->Nome),
            "CNPJ"   	=> utf8_decode(CNPJ_Padrao(str_pad($rs->CNPJ,14,0, STR_PAD_LEFT)))
        ];
    
        $dados_identificador = array('user' => $Itens);
        //$dados_identificador = array('results' => $dados_identificador);
        
        $dados_js = json_encode($dados_identificador);
        
        $fp = fopen("dados.json","w");
        $salva  = fwrite($fp,$dados_js);
        fclose($fp);
    //echo json_encode($Itens);

    }catch(PDOException $e){
        echo "ERROR: ". $e->getMessage();
    }
    try{
        $file = file_get_contents("dados.json");
        
        //$json = json_decode($file);
        header("Content-Type: application/json");
        echo $file;
    }catch(PDOException $r){
        echo "ERROR:". $r->getMessage();
    }
   
}else{
    exit;
}
?>
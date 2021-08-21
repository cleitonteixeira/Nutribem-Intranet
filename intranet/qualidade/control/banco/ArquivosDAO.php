<?php
date_default_timezone_set('America/Sao_Paulo');
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    
    require_once("conexao.php");
    $conexao    = conexao::getInstance();
    require_once("../arquivo/funcao/Outras.php");
    require_once("../arquivo/header/Header.php");
    try{
        $conexao->beginTransaction();
        $Trocas     = array(' ','-','.',')','(','/');
        $troca      = array('linha', 'line','');
        $x = 0;
        $c = array("1");
        if(!empty($_POST['hidden1'])){
            $cj = str_replace($troca,'',$_POST['hidden1']);
            $cj  = explode(",",$cj);
            foreach($cj as $cct){
                array_push($c, $cct);
            }
        }
        $cont   = count($c);
        $x = 0;
        while($x < $cont){
            $n = 'Arq'.$c[$x];
            $Arquivo    = $_FILES[$n];
            $cat = $_POST['categoria'.$c[$x]];
            $Real = $Arquivo["name"];
            //$Extensao   = strrchr($Arquivo["name"], '.');
            //$Nome = md5(uniqid(time())) . $Extensao;
            $data = date("Y-m-d H:i:s");
            $data2 = date("Y-m-d_H_i_s");
            if(!is_dir( '../../documentos/'.$data2 )){
                mkdir('../../documentos/'.$data2, 0777, true);
            }
            $NomePasta  = '../../documentos/'.$data2.'/'.$Real;
            if(move_uploaded_file($Arquivo["tmp_name"], $NomePasta)){
                $SQL = "INSERT INTO arquivoqualidade (Categoria_idCategoria, Usuario_idUsuario, Nome, DataHora) VALUES (?, ?, ?, ?);";
                $stmt = $conexao->prepare($SQL);
                $stmt->bindParam(1, $cat);
                $stmt->bindParam(2, $_SESSION['idusuarios']);
                $stmt->bindParam(3, $Real);
                $stmt->bindParam(4, $data);
                $ar = $stmt->execute();
                if($ar){
                    $x += 1;
                }
            }
        }
        $conexao->commit();
        echo '
            <div class="alert alert-success">
                <p><strong>Sucesso!</strong>Cadastrado com sucesso!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'cadastros/Arquivo.php">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'cadastros/Arquivo.php');exit;
    }catch(PDOException $erro_cad){
        $conexao->rollBack();
        echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao tentar realizar Cadastro...</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$erro_cad->getMessage().'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'cadastros/Arquivo.php">aqui</a>.</p>
			</div>
			';
        header('Refresh: 5;URL='.BASE.'cadastros/Arquivo.php');exit;
    }
}
?>
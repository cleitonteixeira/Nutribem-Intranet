<?php
if (!isset($_SESSION)) session_start();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
$Trocas = array(' ','-','.',')','(','/');
$troca = array('linha', 'line',',');
$conexao = conexao::getInstance();
if(isset($_POST['dContrato']) && $_POST['dContrato']  ==  "Modificar"){
    if(isset($_POST['Finalizar'])){
        try{
            $conexao->beginTransaction();
            $q = "SELECT Finalizado FROM contrato WHERE idContrato = ?";
            $stmt = $conexao->prepare($q);
            $stmt->bindParam(1, $_POST['ContratoF']);
            $stmt->execute();
            $r = $stmt->fetch(PDO::FETCH_OBJ);
            if($r->Finalizado == 'N'){
                $sql    = "UPDATE contrato SET Finalizado = 'S' WHERE idContrato = ?;";
                $tipo   = utf8_encode("DESATIVANDO CONTRATO");
                $evento = "desativado";
            }else{
                $sql    = "UPDATE contrato SET Finalizado = 'N' WHERE idContrato = ?;";
                $tipo   = utf8_encode("ATIVANDO CONTRATO");
                $evento = "ativado";
            }
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $_POST['ContratoF']);
            $stmt->execute();
            
            $sql = "SELECT * FROM contrato WHERE idContrato = ?;";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $_POST['ContratoF']);
            $stmt->execute();
            $rs = $stmt->fetch(PDO::FETCH_OBJ);
            
            
            $data = date("Y-m-d");
            $descricao = 'No dia <strong>'.date("d/m/Y", strtotime($data)).'</strong> o contrato : <strong>'.$rs->nContrato.'</strong> foi '.$evento.' por : <strong>'.$_SESSION['Nome'].'</strong>.';
            $descricao = utf8_encode($descricao);
            $sql = "INSERT INTO historial (Contratante_idContratante, Usuario_idUsuario, Tipo, DataVis, Descricao, DataCad) VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $rs->Contratante_idContratante);
            $stmt->bindParam(2, $_SESSION['idusuarios']);
            $stmt->bindParam(3, $tipo);
            $stmt->bindParam(4, $data);
            $stmt->bindParam(5, $descricao);
            $stmt->bindParam(6, $data);
            $stmt->execute();
            $conexao->commit();
            echo '
                <div class="alert alert-success">
                    <p><strong>Sucesso!</strong> Sucesso ao Ativar/Desativar Contrato!</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'clientes/ModificarContrato.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'clientes/ModificarContrato.php');exit;
        }catch(PDOException $er){
            $conexao->rollBack();
            echo '
                <div class="alert alert-danger">
                    <p><strong>Falha!</strong> Falha ao tentar Ativar/Desativar Contrato!</p>
                    <p><strong>O sistema apresentou o seguinte erro:</strong>'.$er.'</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/ModificarContrato.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'clientes/ModificarContrato.php');exit;
        }
    }elseif(isset($_POST['data'])){
        try{
            /*
            echo "<pre>";
            var_dump($_POST);
            echo "</pre>";
            */
            $conexao->beginTransaction();
            //$data1 = date("Y-m-d", strtotime("+".$_POST['qMeses']." month", strtotime($_POST['fVigencia'])));
            //echo $data1;
            
            $sql = "UPDATE contrato SET VigenciaFim = ? WHERE idContrato = ?;";
            //echo "UPDATE contrato SET VigenciaFim = '".$data1."' WHERE idContrato = ".$_POST['idContrato'].";";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $_POST['data']);
            $stmt->bindParam(2, $_POST['idContrato']);
            $stmt->execute();
            
            $sql = "SELECT * FROM contrato WHERE idContrato = ?;";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $_POST['idContrato']);
            $stmt->execute();
            $rs = $stmt->fetch(PDO::FETCH_OBJ);
            
            $tipo = utf8_encode("PRORROGACAO DE CONTRATO");
            $data = date("Y-m-d");
            $descricao = 'No dia <strong>'.date("d/m/Y", strtotime($data)).'</strong> o contrato : <strong>'.$rs->nContrato.'</strong> foi prorrogado atá a data: <strong>'. date("d/m/Y",strtotime($_POST['data'])).'</strong> meses por <strong>'.$_SESSION['Nome'].'</strong>.';
            $descricao = utf8_encode($descricao);
            $sql = "INSERT INTO historial (Contratante_idContratante, Usuario_idUsuario, Tipo, DataVis, Descricao, DataCad) VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $rs->Contratante_idContratante);
            $stmt->bindParam(2, $_SESSION['idusuarios']);
            $stmt->bindParam(3, $tipo);
            $stmt->bindParam(4, $data);
            $stmt->bindParam(5, $descricao);
            $stmt->bindParam(6, $data);
            $stmt->execute();
            $conexao->commit();
            echo '
                <div class="alert alert-success">
                    <p><strong>Sucesso!</strong> Sucesso ao Prorrogar Contrato!</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'clientes/ModificarContrato.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'clientes/ModificarContrato.php');exit;
        }catch(PDOException $er){
            $conexao->rollBack();
            echo '
                <div class="alert alert-danger">
                    <p><strong>Falha!</strong> Falha ao tentar Prorrogar Contrato!</p>
                    <p><strong>O sistema apresentou o seguinte erro:</strong>'.$er.'</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/ModificarContrato.php">aqui</a>.</p>
                </div>
                ';
           header('Refresh: 5;URL='.BASE.'clientes/ModificarContrato.php');exit;
        }
    }elseif(isset($_POST['iContrato']) && $_POST['iContrato'] == "Modificar"){
        try{
            $conexao->beginTransaction();
            $Obs = utf8_decode($_POST['obsInstrucoes']);

            $sql = "UPDATE contrato SET Obs = ? WHERE idContrato = ?;";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $Obs);
            $stmt->bindParam(2, $_POST['idContrato']);
            $stmt->execute();

           $conexao->commit();
            echo '
                <div class="alert alert-success">
                    <p><strong>Sucesso!</strong> Sucesso ao modificar Contrato!</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'clientes/ModificarContrato.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'clientes/ModificarContrato.php');exit;
        }catch(PDOException $er){
            $conexao->rollBack();
            echo '
                <div class="alert alert-danger">
                    <p><strong>Falha!</strong> Falha ao tentar modificar Contrato!</p>
                    <p><strong>O sistema apresentou o seguinte erro:</strong>'.$er.'</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/ModificarContrato.php">aqui</a>.</p>
                </div>
                ';
            //header('Refresh: 5;URL='.BASE.'clientes/ModificarContrato.php');exit;
        }
    }elseif(isset($_POST['iContrato']) && $_POST['iContrato'] == "Editar"){
        try{
            $conexao->beginTransaction();
            $Empresa = $_POST['empresa'];
            $CentroC = utf8_decode($_POST['ceCusto']);
            $Fechamento = utf8_decode($_POST['pFechamento']);
            $pCompra = utf8_decode($_POST['pCompra']);

            $sql = "UPDATE contrato SET Empresa_idEmpresa = ?, cCusto = ?, pCompra = ?, Fechamento = ? WHERE idContrato = ?;";
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $Empresa);
            $stmt->bindParam(2, $CentroC);
            $stmt->bindParam(3, $pCompra);
            $stmt->bindParam(4, $Fechamento);
            $stmt->bindParam(5, $_POST['idContratoEd']);
            $stmt->execute();

            $conexao->commit();
            echo '
                <div class="alert alert-success">
                    <p><strong>Sucesso!</strong> Sucesso ao modificar Contrato!</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'clientes/ModificarContrato.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'clientes/ModificarContrato.php');exit;
        }catch(PDOException $er){
            $conexao->rollBack();
            echo '
                <div class="alert alert-danger">
                    <p><strong>Falha!</strong> Falha ao tentar modificar Contrato!</p>
                    <p><strong>O sistema apresentou o seguinte erro:</strong>'.$er.'</p>
                    <p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/ModificarContrato.php">aqui</a>.</p>
                </div>
                ';
            header('Refresh: 5;URL='.BASE.'clientes/ModificarContrato.php');exit;
        }
    }else{
        header("Location: Http://www.nutribemrefeicoescoletivas.com.br/instranet/gestorcontratos/");
    }
}
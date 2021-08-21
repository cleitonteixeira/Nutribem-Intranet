<?php
if (!isset($_SESSION)) session_start();
require_once("../arquivo/funcao/Outras.php");
require_once("../arquivo/header/Header.php");
require_once("conexao.php");
$Trocas     = array(' ','-','.',')','(','/');
$troca      = array('linha', 'line','');
$conexao    = conexao::getInstance();

/*
echo "<pre>";
var_dump($a);
echo "</pre>";
*/
$x  = 0;
$x1 = 0;
$c = array("1");
$c1 = array("1");

if(!empty($_POST['hidden1'])){
    $cj = str_replace($troca,'',$_POST['hidden1']);
    $cj  = explode(",",$cj);
    foreach($cj as $cct){
        array_push($c, $cct);
    }
}
if(!empty($_POST['hidden5'])){
    $cj = str_replace($troca,'',$_POST['hidden5']);
    $cj  = explode(",",$cj);
    foreach($cj as $cct){
        array_push($c1, $cct);
    }
}

$cont   = count($c);
$cont1  = count($c1);

if(isset($_POST['Cliente']) && $_POST['Cliente']  ==  "Proposta"){
    try{
        $conexao->beginTransaction();
        if(isset($_POST['cProposta'])){
            $Data  			= date("Y-m-d");
            $Cod			= anti_injection(utf8_encode($_POST['CodContratante']));
            $nProposta  	= anti_injection(utf8_encode($_POST['nProposta']));
            $tReajuste		= anti_injection(utf8_encode($_POST['tReajuste']));
            $fMedicao		= anti_injection(utf8_encode($_POST['fechamento']));
            $pVigencia		= anti_injection(utf8_encode($_POST['tVigencia']));
            $Consolidada    = utf8_encode("S"); 
            $SQL = "INSERT INTO proposta (Contratante_idContratante, dProposta, nProposta, tReajuste, pVigencia, Consolidada) VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $Cod);
            $stmt->bindParam(2, $Data);
            $stmt->bindParam(3, $nProposta);
            $stmt->bindParam(4, $tReajuste);
            $stmt->bindParam(5, $pVigencia);
            $stmt->bindParam(6, $Consolidada);
            $Cadastro   = $stmt->execute();
            $idProposta = $conexao->lastInsertId();
            $Texto 	    = anti_injection(utf8_encode("A seguinte proposta foi aprovada pelo o cliente Proposta Nº: ".$nProposta));
            $Data  	    = anti_injection(utf8_encode(date("Y-m-d")));
            $Tipo   	= anti_injection(utf8_encode("Proposta"));
            $Cod        = anti_injection(utf8_encode($_POST['CodContratante']));
            $DataCad    = date("Y-m-d");
            $SQL = "INSERT INTO historial (Contratante_idContratante, Usuario_idUsuario, Tipo, DataVis, Descricao, DataCad) VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $Cod);
            $stmt->bindParam(2, $_SESSION['idusuarios']);
            $stmt->bindParam(3, $Tipo);
            $stmt->bindParam(4, $Data);
            $stmt->bindParam(5, $Texto);
            $stmt->bindParam(6, $DataCad);
            $Cadastro    = $stmt->execute();
            $idHistorial = $conexao->lastInsertId();
            if($Cadastro){
                $SQL = "INSERT INTO itensproposta (Proposta_idProposta, Servico, ValorUni) VALUES (?,?,?);";
                $stmt = $conexao->prepare($SQL);
                $Itens = 0;
                while($x < $cont){
                    $Servico = utf8_encode(anti_injection($_POST['Evento'.$c[$x]]));
                    $Valor = str_replace(".","",$_POST['valor'.$c[$x]]);
                    $Valor = str_replace(",",".", $Valor);
                    $stmt->bindParam(1, $idProposta);
                    $stmt->bindParam(2, $Servico);
                    $stmt->bindParam(3, $Valor);
                    $ItemServ = $stmt->execute();
                    if($ItemServ){
                        $Itens +=1;
                    }
                    $x+=1;
                }
                $Itens1 = 0;
                while($x1 < $cont1){
                    $n = 'Arquivo'.$c1[$x1];
                    $Arquivo    = $_FILES[$n];
                    $Extensao   = strrchr($Arquivo["name"], '.');
                    $Nome = md5(uniqid(time())) . $Extensao;
                    $NomePasta  = '../../clientes/docs/'.$Nome;
                    if(move_uploaded_file($Arquivo["tmp_name"], $NomePasta)){
                        $SQL = "INSERT INTO dochistorial (Historial_idHistorial, Documento) VALUES (?, ?);";
                        $stmt = $conexao->prepare($SQL);
                        $stmt->bindParam(1, $idHistorial);
                        $stmt->bindParam(2, $Nome);
                        $ar = $stmt->execute();
                        if($ar){
                            $x1 += 1;
                        }
                    }
                }
            }
        }else{
            $Data  			= date("Y-m-d");
            $Cod			= anti_injection(utf8_encode($_POST['CodContratante']));
            $nProposta  	= anti_injection(utf8_encode($_POST['nProposta']));
            $tReajuste		= anti_injection(utf8_encode("Sem dados"));
            $pVigencia		= anti_injection(utf8_encode("0"));
            $Consolidada    = utf8_encode("N"); 
            $SQL = "INSERT INTO proposta (Contratante_idContratante, dProposta, nProposta, tReajuste, pVigencia, Consolidada) VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $Cod);
            $stmt->bindParam(2, $Data);
            $stmt->bindParam(3, $nProposta);
            $stmt->bindParam(4, $tReajuste);
            $stmt->bindParam(5, $pVigencia);
            $stmt->bindParam(6, $Consolidada);
            $Cadastro       = $stmt->execute();
            $Texto 	        = anti_injection(utf8_encode("A seguinte proposta foi enviada ao cliente Proposta Nº: ".$nProposta));
            $Data  	        = anti_injection(utf8_encode(date("Y-m-d")));
            $Tipo  	        = anti_injection(utf8_encode("Proposta"));
            $Cod	        = anti_injection(utf8_encode($_POST['CodContratante']));
            $DataCad        = date("Y-m-d");
            $SQL = "INSERT INTO historial (Contratante_idContratante, Usuario_idUsuario, Tipo, DataVis, Descricao, DataCad) VALUES (?, ?, ?, ?, ?, ?);";
            $stmt = $conexao->prepare($SQL);
            $stmt->bindParam(1, $Cod);
            $stmt->bindParam(2, $_SESSION['idusuarios']);
            $stmt->bindParam(3, $Tipo);
            $stmt->bindParam(4, $Data);
            $stmt->bindParam(5, $Texto);
            $stmt->bindParam(6, $DataCad);
            $Cadastro       = $stmt->execute();
            $idHistorial    = $conexao->lastInsertId();
            if($Cadastro){
                $Itens1 = 0;
                while($x1 < $cont1){
                    $n = 'Arquivo'.$c1[$x1];
                    $Arquivo    = $_FILES[$n];
                    $Extensao   = strrchr($Arquivo["name"], '.');
                    $Nome = md5(uniqid(time())) . $Extensao;
                    $NomePasta = '../../clientes/docs/'.$Nome;
                    if(move_uploaded_file($Arquivo["tmp_name"], $NomePasta)){
                        $SQL    = "INSERT INTO dochistorial (Historial_idHistorial, Documento) VALUES (?, ?);";
                        $stmt   = $conexao->prepare($SQL);
                        $stmt->bindParam(1, $idHistorial);
                        $stmt->bindParam(2, $Nome);
                        $ar     = $stmt->execute();
                        if($ar){
                            $x1 += 1;
                        }
                    }
                }
            }
        }
        $conexao->commit();
        echo '
            <div class="alert alert-success">
                <p><strong>Sucesso!</strong>Cadastrado com sucesso!</p>
                <p>Você será redirecionado em alguns segundos. Caso não seja, clique <a href="'.BASE.'clientes/Proposta.php">aqui</a>.</p>
            </div>
            ';
        header('Refresh: 5;URL='.BASE.'clientes/Proposta.php');exit;
    }catch(PDOException $erro_cad){
        $conexao->rollBack();
        echo '
			<div class="alert alert-danger">
			  	<p><strong>Falha!</strong> Falha ao tentar realizar Cadastro...</p>
			  	<p><strong>O sistema apresentou o seguinte erro:</strong>'.$erro_cad.'</p>
				<p>Você será redirecionado em alguns segundos. Caso não seja clique <a href="'.BASE.'clientes/Proposta.php">aqui</a>.</p>
			</div>
			';
        header('Refresh: 5;URL='.BASE.'clientes/Proposta.php');exit;
    }
}else{
    header("Location: ".BASE);
}
?>
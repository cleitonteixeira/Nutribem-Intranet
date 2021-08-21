<?php
    require_once("../control/banco/conexao.php");
    function getEvento($x,$y,$z){
        $conexao = conexao::getInstance();
        $sql = 'SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->bindValue(2, $y);
        $stm->bindValue(3, $z);
        $stm->execute();
        $Dados = array();
		$t = 0;
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
			$total = $row->Quantidade*$row->ValorUni;
			$t += $total;
            $Itens = [
                "Servico" => utf8_decode($row->Servico),
                "Data" => date("d/m/Y", strtotime($row->dLancamento)),
                "ValorUni" => "R$ ".number_format($row->ValorUni,2,',','.'),
                "Quant" => $row->Quantidade,
                "Total" => $total
            ];
            array_push($Dados, $Itens);
        }
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    
    if(isset($_POST['contrato'])){
        $valor1 = isset( $_POST['contrato'] ) ? (int)$_POST['contrato'] : 0;
		$valor2x = explode('/',$_POST['dataIN']);
		$valor2 = $valor2x[2]."-".$valor2x[1]."-".$valor2x[0];
		$valor3x = explode('/',$_POST['dataFN']);
		$valor3 = $valor3x[2]."-".$valor3x[1]."-".$valor3x[0];
        getEvento($valor1,$valor2,$valor3);
    }
?>
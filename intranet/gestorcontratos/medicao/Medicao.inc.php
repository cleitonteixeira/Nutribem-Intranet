<?php
    require_once("../control/banco/conexao.php");
    function getNMedicao($x){
        $conexao = conexao::getInstance();
		$Ano = date("Y");
        $sql = "SELECT c.nContrato, Descricao AS fMedicao, Obs FROM contrato c INNER JOIN fechamento ON idFechamento = Fechamento INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cad ON cad.idCadastro = ct.Cadastro_idCadastro WHERE c.idContrato = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
        $ct = $stm->fetch(PDO::FETCH_OBJ);
		
        $Contrato = $ct->nContrato;
        $Obs = $ct->Obs;
        $pFechamento = $ct->fMedicao;
		$Quant = 1;
        
		$nMedicao = "MD.".str_pad($Quant, 3, 0, STR_PAD_LEFT)."/".$Ano."-".$Contrato;
        
        $sql = "SELECT Medicao FROM medicao WHERE Contrato_idContrato = ? AND Situacao = 'Aprovada' ;";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
        $medicao = array();
        //var_dump($stm->fetchAll(PDO::FETCH_OBJ));
        while($row = $stm->fetch(PDO::FETCH_OBJ)){
        	array_push($medicao, $row->Medicao);
        }
        
        while(in_array($nMedicao, $medicao)){
        	$Quant += 1;
			$nMedicao = "MD.".str_pad($Quant, 3, 0, STR_PAD_LEFT)."/".$Ano."-".$Contrato;
        }
		$Itens = [
                "nMedicao" => utf8_decode($nMedicao),
                "Contrato" => utf8_decode($Contrato),
                "pFechamento" => utf8_decode($pFechamento),
                "Obs" => utf8_decode($Obs)
		];
        $Dados = array();
		array_push($Dados, $Itens);
		
        echo json_encode($Dados);
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    function getNMedicao1($x){
        $conexao = conexao::getInstance();
		$Ano = date("Y");
        $sql = "SELECT c.nContrato, Descricao AS fMedicao FROM contrato c INNER JOIN fechamento ON idFechamento = Fechamento INNER JOIN contratante ct ON ct.idContratante = c.Contratante_idContratante INNER JOIN cadastro cad ON cad.idCadastro = ct.Cadastro_idCadastro WHERE c.idContrato = ?;";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
        $ct = $stm->fetch(PDO::FETCH_OBJ);
		
        $Contrato = $ct->nContrato;
        $pFechamento = $ct->fMedicao;
		$Quant = 1;
        
		$nMedicao = "MD.".str_pad($Quant, 3, 0, STR_PAD_LEFT)."/".$Ano."-".$Contrato;
        
        $sql = 'SELECT Medicao FROM medicao WHERE Contrato_idContrato = ?;';
        $stm = $conexao->prepare($sql);
        $stm->bindValue(1, $x);
        $stm->execute();
        $medicao = array();

        while($row = $stm->fetch(PDO::FETCH_OBJ)){
        	array_push($medicao, $row->Medicao);
        }
        while(in_array($nMedicao, $medicao)){
        	$Quant += 1;
			$nMedicao = "MD.".str_pad($Quant, 3, 0, STR_PAD_LEFT)."/".$Ano."-".$Contrato;
        }
		return $nMedicao;
        //echo json_encode($stm->fetchAll(PDO::FETCH_ASSOC));
        $pdo = null;	
    }
    function getEventos($x,$y,$z){
        $conexao = conexao::getInstance();
        $idContrato = $x;
        $dataInicial = $y;
        $dInicio = $y;
        $dFinal = $z;
        $sql = "SELECT i.Servico FROM contrato c INNER JOIN itensproposta i ON i.Proposta_idProposta = c.Proposta_idProposta WHERE c.idContrato = ?";
        $stmt1 = $conexao->prepare($sql);
        $stmt1->bindParam(1, $idContrato);
        $stmt1->execute();
        $itens = $stmt1->fetchAll(PDO::FETCH_OBJ);
        $t = 0;
        ob_start();
        ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Data</th>
                    <?php foreach($itens as $i){ ?>
                    <th><?php echo utf8_decode($i->Servico);?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $vTotal = 0;
                $dataInicial = $dInicio;
                while(strtotime($dataInicial) <= strtotime($dFinal)){
                    $sql = "SELECT * FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento = ?;";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bindParam(1, $idContrato);
                    $stmt->bindParam(2, $dataInicial);
                    $stmt->execute();
                    $rs = $stmt->fetchAll(PDO::FETCH_OBJ);
                    $contador1 = $stmt->rowCount();
                    $contador2 = count($itens);
                    if($stmt->rowCount() > 0){
                ?>
                <tr>
                    <td><?php echo date("d/m/Y", strtotime($dataInicial));?></td>
                    <?php
                        foreach($rs as $x){
                            $Total = $x->Quantidade * $x->ValorUni;
                            $vTotal += $Total;
                    ?>
                    <td><?php echo $x->Quantidade; ?></td>
                    <?php 
                        }
                    while($contador1 < $contador2){
                    ?>
                    <td>0</td>
                    <?php
                        $contador1++;
                    }
                    ?>
                </tr>
                <?php 
                    }else{
                ?>
                <tr>
                    <td><?php echo date("d/m/Y", strtotime($dataInicial));?></td>
                    <?php
                        foreach($itens as $i){
                    ?>
                    <td>0</td>
                    <?php
                        }
                    ?>
                </tr>
                <?php
                    }
                    $dataInicial = date("Y-m-d",strtotime('+1 day', strtotime($dataInicial)));
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Total:</strong></td>
                    <?php
                    foreach($itens as $i){

                        $sqli = "SELECT DISTINCT(Servico),SUM(Quantidade) AS Total FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? LIMIT 1;";
                        $stmt = $conexao->prepare($sqli);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $dInicio);
                        $stmt->bindParam(3, $dFinal);
                        $stmt->bindParam(4, $i->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetch(PDO::FETCH_OBJ);
                    ?>
                    <td><?php echo $rs->Total; ?></td>
                    <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td><strong>Valor Unitario:</strong></td>
                    <?php
                    foreach($itens as $i){

                        $sqli = "SELECT DISTINCT(Servico), ValorUni FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? LIMIT 1;";
                        $stmt = $conexao->prepare($sqli);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $dInicio);
                        $stmt->bindParam(3, $dFinal);
                        $stmt->bindParam(4, $i->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetch(PDO::FETCH_OBJ);
                    ?>
                    <td>R$ <?php echo number_format($rs->ValorUni,2,',','.'); ?></td>
                    <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td><strong>Valor Servico:</strong></td>
                    <?php
                    foreach($itens as $i){

                        $sqli = "SELECT DISTINCT(Servico), (SUM(Quantidade)*ROUND(ValorUni,2)) AS Total FROM lancamento WHERE Contrato_idContrato = ? AND dLancamento BETWEEN ? AND ? AND Servico = ? LIMIT 1;";
                        $stmt = $conexao->prepare($sqli);
                        $stmt->bindParam(1, $idContrato);
                        $stmt->bindParam(2, $dInicio);
                        $stmt->bindParam(3, $dFinal);
                        $stmt->bindParam(4, $i->Servico);
                        $stmt->execute();
                        $rs = $stmt->fetch(PDO::FETCH_OBJ);
                    ?>
                    <td>R$ <?php $t+=$rs->Total; echo number_format($rs->Total,2,',','.'); ?></td>
                    <?php
                    }
                    ?>
                </tr>
            </tfoot>
        </table>
        <p><strong>Valor Total:</strong> R$ <?php echo number_format($t,2,',','.'); ?></p>
<?php
    $html = ob_get_clean();
    $html = ["Dados" => $html, "Total" => number_format($t,2,',','.')];
    $dados = array();
    array_push($dados, $html);
    echo json_encode($dados);
        
    }
    if(isset($_POST['contrato'])){
        $valor = isset( $_POST['contrato'] ) ? (int)$_POST['contrato'] : 0;
        getNMedicao($valor);
    }
    if(isset($_POST['buscar'])){
        $valor1 = isset( $_POST['dcontrato'] ) ? (int)$_POST['dcontrato'] : 0;
		$valor2x = explode('/',$_POST['dataIN']);
		$valor2 = $valor2x[2]."-".$valor2x[1]."-".$valor2x[0];
		$valor3x = explode('/',$_POST['dataFN']);
		$valor3 = $valor3x[2]."-".$valor3x[1]."-".$valor3x[0];
        getEventos($valor1,$valor2,$valor3);
    }
?>
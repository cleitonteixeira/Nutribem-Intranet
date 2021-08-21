<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
	session_destroy();
    require_once("../control/funcao/Outras.php");
	header("Location: ".BASE);
else:
	require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#clientes').DataTable();
    } );
</script>
<?php
    $sql = "SELECT DISTINCT(l.Contrato_idContrato),MAX(l.dLancamento) AS dLancamento,cdc.Nome AS Cliente, ct.nContrato AS Contrato, ct.cCusto, cdu.Nome AS Unidade, m.Medicao, m.dFinal FROM lancamento l INNER JOIN contrato ct ON ct.idContrato = l.Contrato_idContrato INNER JOIN unidade u ON u.idUnidade = l.Unidade_idUnidade INNER JOIN cadastro cdu ON cdu.idCadastro = u.Cadastro_idCadastro INNER JOIN contratante co ON co.idContratante = ct.Contratante_idContratante INNER JOIN cadastro cdc ON cdc.idCadastro = co.Cadastro_idCadastro INNER JOIN medicao m ON m.Contrato_idContrato = ct.idContrato WHERE dLancamento >= (SELECT date_add(dFinal, interval 14 day) FROM medicao WHERE Contrato_idContrato = ct.idContrato ORDER BY idMedicao DESC LIMIT 1) GROUP BY ct.nContrato";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$dados = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12 conteudo">
            <div class="col-xs-12 col-lg-12 col-md-12">
                <h1 class="text-center">RELATÓRIO DE MEDIÇÕES PENDENTES</h1>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th colspan="4">DADOS</th>
                        <th colspan="2">DATAS</th>
                    </tr>
                    <tr>
                        <th>Nº CONTRATO</th>
                        <th>CLIENTE</th>
                        <th>UNIDADE</th>
                        <th>ULTIMA MEDIÇÃO</th>
                        <th>MEDIÇÃO</th>
                        <th>LANCAMENTO</th>
                    </tr>
                </thead>
                <tbody>
                   <?php 
                    if($stmt->rowCount() < 1){
                     ?>
                    <tr>
                        <td colspan="6">Sem dados para exibir.</td>
                    </tr>
                    <?php
                    }else{
                        foreach($dados as $d){
                    ?>
                    <tr>
                        <td class="inf_medicao1"><?=$d->Contrato?></td>
                        <td class="inf_medicao1"><?=$d->Cliente.' - <strong>Centro de Custo:</strong> '.$d->cCusto?></td>
                        <td class="inf_medicao1"><?=$d->Unidade?></td>
                        <td class="inf_medicao1"><?=$d->Medicao?></td>
                        <td class="inf_medicao1"><?=date("d/m/Y", strtotime($d->dFinal))?></td>
                        <td class="inf_medicao1"><?=date("d/m/Y", strtotime($d->dLancamento))?></td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
            <p>Relatório Gerado em <strong><?php echo date("d/m/Y H:i:s"); ?></strong>.</p>
        </div>
    </div>
</div>
<?php

endif;
?>
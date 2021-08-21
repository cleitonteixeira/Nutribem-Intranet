<?php
if (!isset($_SESSION)) session_start();
$_SESSION['contrato'] = true;
require_once("../control/arquivo/funcao/Outras.php");
require_once("../control/arquivo/header/Header.php");
require_once("../control/banco/conexao.php");
$conexao = conexao::getInstance();
$sql = "SELECT * FROM contrato INNER JOIN fechamento ON idFechamento = Fechamento WHERE idContrato = ?;";
$st = $conexao->prepare($sql);
$st->bindParam(1, $_GET['id']);
$st->execute();
$ct = $st->fetch(PDO::FETCH_OBJ);
?>
<div class=" text-justify">
    <div class="conteudo-ct">
        <div class="col-xs-12 col-md-12 col-lg-12 text-center"><h1>DADOS DO CONTRATO</h1></div>
        <div class="col-xs-12 col-md-12 col-lg-12">
            <div class="col-xs-4 col-lg-4 col-md-4">
                <p><strong>Nº Contrato: </strong><?php echo $ct->nContrato; ?></p>
            </div>
            <div class="col-xs-4 col-lg-4 col-md-4">
                <p><strong>Data Reajuste: </strong><?php echo date("d/m/Y", strtotime($ct->DataReajuste)); ?></p>
            </div>
            <?php
            $sqli = "SELECT * FROM proposta WHERE idProposta = ?;";
            $sti = $conexao->prepare($sqli);
            $sti->bindParam(1, $ct->Proposta_idProposta);
            $sti->execute();
            $pd = $sti->fetch(PDO::FETCH_OBJ);
            ?>
            <div class="col-xs-4 col-lg-4 col-md-4">
                <p>
                    <strong>Frequência do Reajuste: </strong>
                    <?php echo utf8_decode($pd->tReajuste); ?>
                </p>
            </div>
            <div class="col-xs-4 col-lg-4 col-md-4">
                <p>
                    <strong>Fechamento da Medição: </strong>
                    <small><?php echo utf8_decode($ct->Descricao); ?></small>
                </p>
            </div>
            <div class="col-xs-6 col-lg-6 col-md-6">
                <p>
                    <strong>Condição: </strong>
                    <small><?php echo utf8_decode($ct->Condicao); ?>, </small>
                    <small><?php echo utf8_decode($ct->fPagamento); ?></small>
                </p>
            </div>
            <div class="col-xs-5 col-lg-5 col-md-5">
                <p><strong>Itens do Contrato:</strong> Proposta: <?php echo $pd->nProposta; ?></p>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                $sqli = "SELECT * FROM itensproposta WHERE Proposta_idProposta = ?;";
                $sti = $conexao->prepare($sqli);
                $sti->bindParam(1, $ct->Proposta_idProposta);
                $sti->execute();
                $pd = $sti->fetchAll(PDO::FETCH_OBJ);
                foreach($pd as $p){
                ?>
                        <tr>
                            <td><?php echo $p->Servico ;?></td>
                            <td>R$ <?php echo number_format($p->ValorUni,2,',','.') ;?></td>
                        </tr>
                <?php
                }
                ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
require_once("../control/arquivo/footer/Footer.php");
?>
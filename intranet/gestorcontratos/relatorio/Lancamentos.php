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
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12 conteudo">
            <div class="col-xs-12 col-lg-12 col-md-12">
                <h1 class="text-center">ULTIMOS LANCAMENTOS POR UNIDADE</h1>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Unidade</th>
                        <th>Ultimo Lancamento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sqli = "SELECT DISTINCT(l.Unidade_idUnidade) AS Unidade, c.Nome, MAX(l.dCadastro) AS DataCadastro FROM lancamento l INNER JOIN unidadefaturamento u ON u.idUnidadeFaturamento = l.Unidade_idUnidade INNER JOIN cadastro c ON c.idCadastro = u.Cadastro_idCadastro GROUP BY l.Unidade_idUnidade ORDER BY l.dCadastro ASC;";
                    $stmt = $conexao->prepare($sqli);
                    $stmt->execute();
                    $res = $stmt->fetchAll(PDO::FETCH_OBJ);
                    foreach($res as $r){
                    ?>
                    <tr>
                        <td><?php echo $r->Unidade; ?></td>
                        <td><?php echo utf8_decode($r->Nome); ?></td>
                        <td><?php echo date("d/m/Y", strtotime($r->DataCadastro)); ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <p>Relatório Gerado em <strong><?php echo date("d/m/Y H:i:s"); ?></strong>.</p>
            <div class="text-center conteudo">
                <a target="_blank" href="lExcel.php" class="btn btn-primary"> Ultimos Lancamentos Excel <i class="fa fa-file-pdf-o"></i></a>
                <a target="_blank" href="lPDF.php" class="btn btn-success"> Ultimos Lancamentos PDF <i class="fa fa-file-pdf-o"></i></a>
            </div>
        </div>
    </div>
</div>
<?php

endif;
?>
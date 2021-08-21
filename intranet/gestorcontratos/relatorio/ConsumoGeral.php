<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
	session_destroy();
	require_once("../control/funcao/Outras.php");
	header("Location: ".BASE);
}else{
	require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#clientes').DataTable();
        $('.input-daterange').datepicker({
			todayBtn: "linked",
			language: "pt-BR"
		})
    } );
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12 conteudo">
            <div class="col-xs-12 col-lg-12 col-md-12">
                <h1 class="text-center">RELATÓRIO DE CONSUMO GERAL</h1>
            </div>
            <div class="conteudo"></div>
            <form  name="Form" role="form" action="RelConsumoGeral.php" target="_blank" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente1" name="FormCliente1" >
                <div class="form-group">
                    <label class="col-sm-2 control-label">Data:</label>
                    <div class="col-sm-3">
                        <div class="input-daterange input-group" id="datepicker">
                            <input type="text" class="form-control" required id="dataIN" name="dataIN" />
                            <span class="input-group-addon"> até </span>
                            <input type="text" class="form-control" required id="dataFN" name="dataFN" data-date-end-date="0d" />
                        </div>
                    </div>
                </div>
                <div class="text-left col-xs-offset-2">
                    <button class="btn btn-primary" type="submit">Gerar Relatório</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
}
?>
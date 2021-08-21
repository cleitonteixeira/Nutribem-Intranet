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
                <h1 class="text-center">RELATÓRIO DE CONSUMO POR UNIDADE E POR PERIODO</h1>
            </div>
            <div class="conteudo"></div>
            <form  name="Form" role="form" action="RelConsumo.php" target="_blank" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente1" name="FormCliente1" >
                <div class="form-group">
                    <label class="control-label col-sm-2" for="Unidade">Unidade: </label>
                    <div class="col-sm-7">
                        <select class="selectpicker form-control dropdown" required name="Unidade" id="Unidade" title="Selecione um Unidade" data-size="5" data-live-search="true" >
                            <?php
                            $sql = 'SELECT * FROM unidadefornecimento em';
                            $stm = $conexao->prepare($sql);
                            $stm->execute();
                            $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                            foreach($rs as $r):
                            ?>
                            <optgroup label="<?php echo utf8_decode($r->Nome); ?>" >
                                <?php
                                $sql = 'SELECT un.idUnidadeFaturamento, cd.Nome FROM unidadefaturamento un  INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE un.Fornecimento_idFornecimento = ? AND un.idUnidadeFaturamento IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $r->idUnidadeFornecimento);
                                $stm->bindParam(2, $_SESSION['idusuarios']);
                                $stm->execute();
                                while($row = $stm->fetch(PDO::FETCH_OBJ)):
                                ?>
                                <option <?php echo isset($_POST['Diario']) && $_POST['Diario'] == "Data-Unidade" && $_POST['Unidade'] == $row->idUnidadeFaturamento ? "selected" : ''; ?> value="<?php echo $row->idUnidadeFaturamento; ?>"><?php echo utf8_decode($row->Nome); ?></option>
                                <?php endwhile; ?>
                            </optgroup>
                            <?php endforeach; ?>
                        </select>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
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
<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<script>
$(document).ready(function(){
  $("#pesquisa").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#dContratos tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12 conteudo">
			<div class="conteudo"></div>
				<div class="col-xs-12 col-md-12 col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center">Todas Unidades</h1>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-responsive text-center" id="aso">
                            <thead>
                                <tr>
                                    <th colspan="3"><input class="form-control" id="pesquisa" type="text" placeholder="Search.."></th>
                                </tr>
                                <tr>
                                    <th>ID UNIDADE</th>
                                    <th>NOME</th>
                                    <th>ATIVA</th>
                                </tr>
                            </thead>
                            <tbody id="dContratos">
                                <?php
                                $sql = "SELECT uf.idUnidadeFaturamento, cdu.Nome AS Unidade, Ativa FROM unidadefaturamento uf INNER JOIN cadastro cdu ON cdu.idCadastro = uf.Cadastro_idCadastro ORDER BY idUnidadeFaturamento";
                                $stm = $conexao->prepare($sql);
                                $stm->execute();
                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                foreach($rs AS $r){
                                     ?>
                                <tr>
									<td><?php echo utf8_decode($r->idUnidadeFaturamento); ?></td>
                                    <td><?php echo utf8_decode($r->Unidade); ?></td>
									<td><?=$r->Ativa == 'S' ? 'Ativa' : 'Desativada' ?></td>
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
        
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>
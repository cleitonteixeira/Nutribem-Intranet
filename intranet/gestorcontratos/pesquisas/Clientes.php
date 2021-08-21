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
    $("#dClientes tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12 conteudo">
			<h2 class="text-center">Lista de Contratantes</h2>
			<table class="table table-striped table-bordered table-responsive text-center" name="clientes" id="clientes">
				<thead>
					<tr>
                        <th colspan="5"><input class="form-control" id="pesquisa" type="text" placeholder="Search.."></th>
                    </tr>
					<tr>
						<th>Empresa</th>
						<th>CNPJ</th>
						<th>Responsável Financeiro</th>
						<th>Responsável Comercial</th>
						<th>Extras</th>
					</tr>
				</thead>
				<tbody id="dClientes">
					<?php
					$sql = "SELECT c.idContratante, cd.Nome AS Cliente, cd.CNPJ AS CNPJ FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro ORDER BY cd.Nome;";
					$stm = $conexao->prepare($sql);
					//$stm->bindParam(1, $_SESSION['idusuarios']);
					$stm->execute();
					$rj = $stm->fetchAll(PDO::FETCH_OBJ);
					foreach($rj as $rs){
						$sql = "SELECT * FROM ccontratante WHERE Contratante_idContratante = ?;";
						$stm = $conexao->prepare($sql);
						$stm->bindParam(1, $rs->idContratante);
						$stm->execute();
						$rx = $stm->fetchAll(PDO::FETCH_OBJ);
					?>
					<tr>
						<td class="text-center"><?php echo str_pad($rs->idContratante,3,0,STR_PAD_LEFT).'-'.utf8_decode($rs->Cliente); ?></td>
						<td class="col_cnpj"><?php echo utf8_decode(CNPJ_Padrao(str_pad($rs->CNPJ,14,0, STR_PAD_LEFT))); ?></td>
						<?php
						foreach($rx as $c){
							$Res1 = explode(" ", $c->Responsavel);
							$Res = $Res1[0];
						?>
						<td><?php echo utf8_decode($Res); ?> / <?php echo strlen($c->Telefone) == 11 ? Cel_Padrao($c->Telefone) : Tel_Padrao($c->Telefone); ?></td>
						<?php
						}
						?>
						<td>
                            <a alt="VER DETALHES" title="VER DETALHES" href="<?php echo BASE; ?>pesquisas/DetalheCliente.php?cod=<?php echo $rs->idContratante; ?>"><i class="fa fa-folder-open" aria-hidden="true"></i></a>
                            <a alt="EDITAR" title="EDITAR" href="<?php echo BASE; ?>clientes/Editar.php?cod=<?php echo $rs->idContratante; ?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
						</td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
        </div>
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>
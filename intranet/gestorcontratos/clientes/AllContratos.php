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
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h1 class="panel-title text-center">Todos os Contratos</h1>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-responsive text-center" id="aso">
                            <thead>
                                <tr>
                                    <th colspan="6"><input class="form-control" id="pesquisa" type="text" placeholder="Search.."></th>
                                </tr>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Nº Contrato</th>
                                    <th>Unidade</th>
                                    <th>Data Reajuste</th>
                                    <th>Data Vencimento</th>
                                    <th>Detalhes</th>
                                </tr>
                            </thead>
                            <tbody id="dContratos">
                                <?php
                                $sql = "SELECT ca.Nome AS Cliente, cdt.Nome as Unidade, ct.nContrato, ct.DataReajuste AS Reajuste, ct.VigenciaFim AS Vencimento, ct.idContrato, ct.Contratante_idContratante FROM contrato ct INNER JOIN contratante co ON co.idContratante = ct.Contratante_idContratante INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro INNER JOIN unidadefaturamento un ON un.idUnidadeFaturamento = ct.Unidade_idUnidade INNER JOIN cadastro cdt ON cdt.idCadastro = un.Cadastro_idCadastro INNER JOIN cadastro cun ON cun.idCadastro = un.Cadastro_idCadastro INNER JOIN empresa em ON em.idEmpresa = ct.Empresa_idEmpresa INNER JOIN cadastro cem ON cem.idCadastro = em.Cadastro_idCadastro WHERE ct.Finalizado = 'N' AND ct.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadefuser WHERE Usuario_idUsuario = ?) ORDER BY cdt.Nome, ct.DataReajuste, ct.idContrato ASC";
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $_SESSION['idusuarios']);
                                $stm->execute();
                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                foreach($rs AS $r):
                                    $dReajuste 	= date('Y-m-d', strtotime($r->Reajuste));
                                    $data 		= strtotime(date('Y-m-d', strtotime($r->Reajuste)));
                                    $data1 		= strtotime(date('Y-m-d'));
                                    $diferenca = $data - $data1;
                                    $dias = (int)floor( $diferenca / (60 * 60 * 24));
                                	if($dias>= 1 and $dias <= 60){
								?>
								<tr class="nCliente" style="background: #E3CE2A">
								<?php }elseif($dias<= 0){?>
								<tr class="nCliente" style="background: #FF161D">
								<?php }else{ ?>
								<tr class="nCliente" style="background: #AAFF8A">
								<?php } ?>
									<td><?php echo utf8_decode($r->Cliente); ?></td>
                                    <td><?php echo utf8_decode($r->nContrato); ?> </td>
                                    <td><?php echo utf8_decode($r->Unidade); ?> </td>
                                    <td><?php echo date("d/m/Y", strtotime($r->Reajuste)); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($r->Vencimento)); ?></td>
                                    <td><a href="../pesquisas/DetalheCliente.php?cod=<?php echo $r->Contratante_idContratante; ?>"><span style="color: #fff !important"><i class="fas fa-external-link-alt"></i></span></a></td>
                                </tr>
                                <?php
                                endforeach;   
                                ?>
                            </tbody>
                        </table>
                        <small>
							<p>Próximos 5 reajustes. * Dias restantes.</p>
							<p><span style="color: #FF161D"><i class="fa fa-square" aria-hidden="true"></i></span> Já se encontra atrasado. Com 0 dias ou menos.</p>
							<p><span style="color: #E3CE2A"><i class="fa fa-square" aria-hidden="true"></i></span> Próximo ao Prazo de Vencimento. Entre 60 dias e 1 dia.</p>
							<p><span style="color: #3FD100"><i class="fa fa-square" aria-hidden="true"></i></span> Ainda vigente. 61 dias acima.</p>
						</small>
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
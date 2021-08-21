<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
require_once("../control/arquivo/funcao/Outras.php");
require_once("../control/banco/conexao.php");
require_once("../control/arquivo/header/Header.php");
require_once("../control/arquivo/Login.php");
else:
require_once("../control/Pacote.php");
$conexao = conexao::getInstance();
?>
<script>
    $(document).ready(function(){
		//Carrega Colaboradores
		$("select[name='unidade']").change(function(e){
			var unidade = $('#unidade').val();//pegando o value do option selecionado
			//alert(empresa);//apenas para debugar a variável

			$.getJSON('Unidade.inc.php?u='+unidade, function (dados){
				//alert(dados);
				if (dados.length > 0){ 	
					var option = '';
					$.each(dados, function(i, obj){
						option += '<tr>';
						option += '<td>'+obj.Colaborador+'</td>';
						option += '<td>'+obj.Cargo+'</td>';
						option += '<td>'+obj.Unidade+'</td>';
						option += '<td>'+obj.DataAso+'</td>';
						option += '<td>'+obj.Dias+'</td>';
						option += '</tr>';
					})
					$('#colaborador').html(option).show();
				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			var op = '';
			op += '<tr>';
			op += '<td colspan="5"><strong>Aguardando...</strong></td>';
			op += '</tr>';
			
			
			$('#colaborador').html(op).show();
		}
	});
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <div class="col-xs-12 text-center"><h1>ASO por Unidade</h1></div>
			<form class="form-horizontal text-center">
				<div class="form-group">
					<label class="col-sm-1 control-label" for="unidade">Unidade:</label>
					<div class="col-sm-6">
						<select class="selectpicker form-control" title="Selecione uma Unidade!" name="unidade" id="unidade" data-live-search="true" data-width="100%" data-size="5" data-actions-box="true" required>
							<?php
							$sql = 'SELECT DISTINCT(c.CNPJ) FROM unidade u INNER JOIN empresa e ON e.idEmpresa = u.Empresa_idEmpresa INNER JOIN cadastro c ON c.idCadastro = e.Cadastro_idCadastro WHERE u.idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?)';
							$stm = $conexao->prepare($sql);
							$stm->bindParam(1, $_SESSION['idusuarios']);
							$stm->execute();
							$rs = $stm->fetchAll(PDO::FETCH_OBJ);
							foreach($rs as $r):
							?>
							<optgroup label="<?php echo CNPJ_Padrao($r->CNPJ); ?>" >
								<?php
								$sql = 'SELECT un.idUnidade, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidade un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE ca.CNPJ = ? AND un.idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?) ORDER BY cd.Nome';
								$stm = $conexao->prepare($sql);
								$stm->bindParam(1, $r->CNPJ);
								$stm->bindParam(2, $_SESSION['idusuarios']);
								$stm->execute();
								while($row = $stm->fetch(PDO::FETCH_OBJ)):
								?>
								<option value="<?php echo $row->idUnidade; ?>" data-tokens="<?php echo str_pad($row->idUnidade,2,0, STR_PAD_LEFT)." - ".utf8_decode($row->Nome); ?>"><?php echo str_pad($row->idUnidade,2,0, STR_PAD_LEFT)." - ".utf8_decode($row->Nome); ?></option>
								<?php endwhile; ?>
							</optgroup>
							<?php endforeach; ?>
						</select>
						<div class="help-block with-errors"></div>
					</div>
				</div>
			</form>
            <div class="col-xs-12" id="lista-aso">
                <table class="table table-striped table-bordered table-responsive text-center" name="lista_asos" id="lista_asos">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Cargo</th>
                            <th>Unidade</th>
                            <th>Data Aso</th>
                            <th>Dias Restantes</th>
                        </tr>
                    </thead>
                    <tbody id="colaborador">
						<tr>
							<td colspan="5"><strong>Aguardando...</strong></td>
						</tr>
                    </tbody>
					<tfoot>
						<tr>
							<td colspan="5">Lista dos <strong>20 ASOS</strong>, com vencimento em até <strong>45 dias.</strong></td>
						</tr>
					</tfoot>
                </table>
            </div>
			<!--
			<button class="btn btn-primary" id="imprimir">Clique para imprimir</button>

			<script>
				document.getElementById('imprimir').onclick = function() {
					var conteudo = '<html><head><title>RELATÓRIO ASOS</title><link rel="stylesheet" href="../css/pdf.css"></head><body>'+document.getElementById('lista-aso').innerHTML+'</body></html>',
						tela_impressao = window.open('about:blank');

					tela_impressao.document.write(conteudo);
					tela_impressao.window.print();
					tela_impressao.window.close();
				};
			</script>	
			-->
        </div>
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
endif;
?>
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
    function setaDadosModal(valor) {
        document.getElementById('campo').value = valor;
    }
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-12 text-center"><h1>Lista de Funcionários</h1></div>
            <div class="col-xs-12">
                <table class="table table-striped table-bordered table-responsive text-center" name="lista_asos" id="lista_asos">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Unidade</th>
                            <th>Data Aso</th>
                            <th>Dias Restantes</th>
                            <th class="text-center"><span class="btn-label" title="Atualizar"><i class="fa fa-refresh"></i></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT cn.idContratacao, co.CodColaborador, (SELECT cad.Nome as Unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = cn.Unidade_idUnidade) AS Unidade, ca.Nome, cn.dAso FROM contratacao cn INNER JOIN colaborador co ON co.Contratacao_idContratacao = cn.idContratacao INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro INNER JOIN cargo cg ON cg.idCargo = cn.Cargo_idCargo WHERE cg.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = 1) AND cn.dDemissao IS NULL ORDER BY cn.dAso ASC LIMIT 10;";
                        $stm = $conexao->prepare($sql);
                        $stm->bindParam(1, $_SESSION['idusuarios']);
                        $stm->execute();

                       	$rs = $stm->fetchAll(PDO::FETCH_OBJ);
						foreach($rs AS $r):
							$dAso = date('Y-m-d', strtotime('+1 year', strtotime($r->dAso)));
							$data = strtotime((date('Y-m-d', strtotime('+1 year', strtotime($r->dAso)))));
							$data1 = strtotime(date('Y-m-d'));
							$diferenca = $data - $data1;
							$dias = (int)floor( $diferenca / (60 * 60 * 24));
						?>
						<tr>
							<td><?php echo utf8_decode($r->CodColaborador)."-".utf8_decode($r->Nome); ?> </td>    
							<td><?php echo utf8_decode($r->Unidade); ?> </td>    
							<td><?php echo Muda_Data($dAso); ?> </td>    
							<td>*<?php echo $dias; ?> </td>    
							<?php if($_SESSION['Acesso'] != 2):?><td>
								<a data-toggle="modal" data-target="#myModal" class="btn btn-primary" onclick="setaDadosModal('<?php echo $r->idContratacao; ?>')">
									<span class="btn-label" title="Atualizar"><i class="fa fa-refresh"></i></span>
								</a>
							</td><?php endif; ?> 
						</tr>
						<?php
						endforeach;     
						?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Atualizar ASO</h4>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data" rel="form" class="form-inline" data-toggle="validator" action="<?php echo BASE; ?>control/banco/AsoDAO.php">
                    <div class="form-group">
                        <label for="data">Data:</label>
                        <input type="date" required class="form-control" name="data" id="data">
                        <button class="btn btn-success" type="submit">Atualizar</button>
                        <div class="help-block with-errors"></div>
                    </div>
                    <input type="hidden" name="campo" id="campo" value="" >
                    <input type="hidden" name="pag" id="pag" value="seguranca" >
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
endif;
?>
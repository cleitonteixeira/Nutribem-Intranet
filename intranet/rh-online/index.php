<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("control/Pacote.php");
    $conexao = conexao::getInstance();
?>
<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-6 aso">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Próximos Vencimentos: ASO</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-responsive text-center" id="aso">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Data</th>
                                    <th>Dias</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT cn.idContratacao, co.CodColaborador, ca.Nome, cn.dAso FROM contratacao cn INNER JOIN colaborador co ON co.Contratacao_idContratacao = cn.idContratacao INNER JOIN cadastro ca ON ca.idCadastro = co.Cadastro_idCadastro INNER JOIN cargo cg ON cg.idCargo = cn.Cargo_idCargo WHERE cg.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?) AND cn.dDemissao IS NULL ORDER BY cn.dAso ASC LIMIT 5";
                                $stm = $conexao->prepare($sql);
                                $stm->bindParam(1, $_SESSION['idusuarios']);
                                $stm->execute();
                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                foreach($rs AS $r):
                                    $dAso 	= date('Y-m-d', strtotime('+1 year', strtotime($r->dAso)));
                                    $data 	= strtotime((date('Y-m-d', strtotime('+1 year', strtotime($r->dAso)))));
                                    $data1 	= strtotime(date('Y-m-d'));
                                    $diferenca = $data - $data1;
                                    $dias = (int)floor( $diferenca / (60 * 60 * 24));
                                ?>
                                <tr>
                                    <td><?php echo utf8_decode($r->CodColaborador)."-".utf8_decode($r->Nome); ?> </td>    
                                    <td><?php echo Muda_Data($dAso); ?></td>
                                    <td>*<?php echo $dias; ?></td>
                                </tr>
                                <?php
                                endforeach;   
                                ?>
                            </tbody>
                        </table>
                        <small>Próximos 5 ASOS a vencer. * Dias restantes.</small>
                    </div>
                </div>
            </div>
            <div class="col-xs-6 aso">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Últimas Alterações</h3>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-responsive text-center" id="aso">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Mudança</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT h.*, c.Nome, co.idColaborador, co.CodColaborador FROM historico h INNER JOIN colaborador co ON co.idColaborador = h.Colaborador_idColaborador INNER JOIN cadastro c ON c.idCadastro = co.Cadastro_idCadastro ORDER BY Data DESC LIMIT 5";
                                $stm = $conexao->prepare($sql);
                                $stm->execute();
                                $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                foreach($rs as $r):
                                ?>
                                <tr>
                                    <td><?php echo utf8_decode($r->CodColaborador)."-".utf8_decode($r->Nome); ?> </td>
                                    <td><?php echo utf8_decode($r->Historico); ?> </td>
                                    <td><?php echo Muda_Data($r->Data); ?> </td>
                                </tr>
                                <?php
                                endforeach;         
                                ?>
                            </tbody>
                        </table>
                        <small>5 últimas mudanças.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php
    require_once("control/arquivo/footer/Footer.php");
}
?>
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
<script type="text/javascript">
    $(document).ready(function() {
        $('#colaboradores').DataTable();
    } );
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-12 col-md-12 col-lg-12 text-center"><h1>Lista de Funcionários</h1></div>
            <div class="col-xs-12">
                <table class="table table-striped table-bordered table-responsive text-center" name="colaboradores" id="colaboradores">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Empresa</th>
                            <th>Unidade</th>
                            <th>Cargo</th>
                            <th>Detalhe</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT col.*, con.*, cad.Nome, cad.CPF, (SELECT cad.Nome as Unidade FROM unidade un INNER JOIN cadastro cad ON cad.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidade = con.Unidade_idUnidade) AS Unidade, (SELECT cad.Nome AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS Empresa, (SELECT cad.CNPJ AS Empresa FROM empresa em INNER JOIN cadastro cad ON cad.idCadastro = em.Cadastro_idCadastro WHERE em.idEmpresa = un.Empresa_idEmpresa) AS CNPJ  FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = con.Unidade_idUnidade WHERE un.idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?);";
                        $stm = $conexao->prepare($sql);
                        $stm->bindParam(1, $_SESSION['idusuarios']);
                        $stm->execute();

                        while($rs = $stm->fetch(PDO::FETCH_OBJ)):
                        $SQL = "SELECT * FROM cargo WHERE idCargo = ?";
                        $stmt = $conexao->prepare($SQL);
                        $stmt->bindParam(1, $rs->Cargo_idCargo);
                        $stmt->execute();
                        $Cargo = $stmt->fetch(PDO::FETCH_OBJ);
                        
                        if($rs->dDemissao == null){
                        ?>
                        <tr>
                        <?php
                        }else{
                        ?>
                        <tr style="background: #E87E6F">
                        <?php
                        }
                        ?>
                            <td><?php echo utf8_decode($rs->Nome); ?></td>
                            <td><?php echo utf8_decode(CPF_Padrao(str_pad($rs->CPF,11,0, STR_PAD_LEFT))); ?></td>
                            <td><?php echo  CNPJ_Padrao($rs->CNPJ); ?></td>
                            <td><?php echo utf8_decode($rs->Unidade); ?></td>
                            <td><?php echo utf8_decode($Cargo->Cargo); ?></td>
                            <td>
                                <div class="col-sm-3">
                                    <a href="<?php echo BASE; ?>pesquisas/DetalheColaborador.php?cod=<?php echo $rs->idColaborador; ?>"><i class="fa fa-folder-open" aria-hidden="true"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php
                        endwhile;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>
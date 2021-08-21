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
$sql = "SELECT col.idColaborador, cad.Nome, cad.CPF FROM colaborador col INNER JOIN cadastro cad ON cad.idCadastro = col.Cadastro_idCadastro INNER JOIN contratacao con ON con.idContratacao = col.Contratacao_idContratacao WHERE con.Unidade_idUnidade IN (SELECT Unidade_idUnidade FROM unidadeuser WHERE Usuario_idUsuario = ?) ORDER BY cad.Nome";
$stm = $conexao->prepare($sql);
$stm->bindParam(1, $_SESSION['idusuarios']);
$stm->execute();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-offset-3 col-xs-9"><h2>Ficha Funcionário</h2></div>
            <div class="col-xs-12">
                <form action="RelFichaFuncionario.php" method="get" enctype="multipart/form-data" rel="form" class="form-inline" data-toggle="validator">
                    <div class="col-xs-10">
                        <div class="form-group">
                            <label for="colaborador">Colaborador:</label>
                            <select class="selectpicker" name="colaborador" title="Selecione um Colaborador." data-live-search="true" required data-width="fit" data-error="Selecione um Colaborador.">
                                <?php
                                while($row = $stm->fetch(PDO::FETCH_OBJ)):
                                ?>
                                <option value="<?php echo $row->idColaborador ?>" data-tokens="<?php echo utf8_decode($row->Nome).' '.CPF_Padrao(str_pad($row->CPF,11,0, STR_PAD_LEFT)); ?>" data-subtext="<?php echo CPF_Padrao(str_pad($row->CPF,11,0, STR_PAD_LEFT)); ?>"><?php echo utf8_decode($row->Nome) ; ?></option>
                                <?php endwhile;?>    
                            </select>

                        </div>
                        <button class="btn btn-primary" type="submit">Enviar</button>   
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>
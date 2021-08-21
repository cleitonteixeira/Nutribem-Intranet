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
    endif;
$Troca = array("/","\\","|");
$conexao = conexao::getInstance();
$sql = "SELECT cad.Nome, un.idUnidade FROM unidade un INNER JOIN cadastro cad on cad.idCadastro = un.Cadastro_idCadastro ORDER BY cad.Nome ASC";
$stm = $conexao->prepare($sql);
$stm->execute();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-offset-3 col-xs-9"><h2>Cargos</h2></div>
            <div class="col-xs-12">
                <form action="RelCargos.php" method="get" enctype="multipart/form-data" rel="form" class="form-inline" data-toggle="validator">
                    <div class="col-xs-10">
                        <div class="form-group">
                            <label for="unidade">Unidade:</label>
                            <select class="selectpicker" name="unidade" title="Selecione uma Unidade." data-live-search="true" required data-width="fit">
                                <?php
                                while($row = $stm->fetch(PDO::FETCH_OBJ)):
                                ?>
                                <option value="<?php echo $row->idUnidade ?>"><?php echo utf8_decode($row->Nome) ?></option>
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
require_once("../control/arquivo/footer/Footer.php");
?>
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
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/UsuarioUnidadeDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormCargo" name="FormCargo">
                
                <?php
                    $conexao = conexao::getInstance();
                    $sql = 'SELECT ca.Nome FROM unidadefaturamento un INNER JOIN cadastro ca ON ca.idCadastro = un.Cadastro_idCadastro WHERE un.idUnidadeFaturamento  = ?';
                    $stm = $conexao->prepare($sql);
                    $stm->bindParam(1, $_GET['un']);
                    $stm->execute();
                    if($stm->rowCount() != 1):
                    ?>
                    <script>
                        alert("Unidade Não Existe!");
                        location.href = '../';
                    </script>
                    <?php
                    endif;
                    $rs = $stm->fetch(PDO::FETCH_OBJ);
                ?>
                <h3 class="text-center">Selecione os Usuários que terão acesso aos dados da unidade: <strong><?php echo utf8_decode($rs->Nome); ?></strong></h3>
                <?php
                    $sql = 'SELECT idusuarios, Nome, Login FROM usuarios;';
                    $stm = $conexao->prepare($sql);
                    $stm->execute();
                    $x = 0;
                    $z = 4;
                    $w = 0;
                    $cont = $stm->rowCount();
                    $y = (int)ceil($cont/4);
                    $v = $cont%4 ;
                    while($row = $stm->fetch(PDO::FETCH_OBJ)):
                        if($x == 0):
                            $w++;
                ?>
                <div class="row">
                        <?php endif; ?>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <div class="user-unidade">
                                <label class="checkbox-inline"><input name="usuario[]" type="checkbox" value="<?php echo $row->idusuarios; ?>"><small>Nome: </small><?php echo utf8_decode($row->Nome); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php
                    $x++;
                    if($w == $y):
                        $z = $v;
                    endif;
                    if($x == $z):
                        $x = 0;
                ?>
                </div>
                <?php endif;
                endwhile; ?>
                <div class="row">
                    <div class="col-xs-4">
                        <input type="hidden" name="unidade" value="<?php echo $_GET['un']; ?>">
                        <input type="hidden" name="Controle" value="Atualizar">
                        <button class="btn btn-primary" type="submit">Salvar <i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
</div>
<?php
endif;
require_once("../control/arquivo/footer/Footer.php");
?>
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
<script language='JavaScript'>
    function SomenteNumero(e){
        var tecla=(window.event)?event.keyCode:e.which;   
        if((tecla>47 && tecla<58)) return true;
        else{
            if (tecla==8 || tecla==0) return true;
            else  return false;
        }
    }
</script>
<script type="text/javascript">
    $(function(){
        $("#salario").maskMoney();
    })
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-offset-3 col-xs-9"><h2>Cadastro Cargos</h2></div>
            <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/CargoDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormCargo" name="FormCargo">
                <div class="col-xs-12">
                    <div class="form-group">
                         <label class="col-sm-2 control-label" for="unidade">Unidade:</label>
                        <div class="col-sm-5">
                            <select class="selectpicker" title="Selecione uma Unidade." name="unidade" id="unidade" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma Unidade." required>
                                <?php
                                $conexao = conexao::getInstance();
                                $sql = " SELECT * FROM unidade;";
                                $stm = $conexao->prepare($sql);
                                $stm->execute();
                                if($stm->rowCount() == 0):
                                ?>
                                <script>
                                    alert("Não consta nenhuma UNIDADE cadastrada, por favor cadastre uma!");
                                    location.href = 'Unidade.php';
                                </script>
                                <?php
                                else:
                                    $conexao = conexao::getInstance();
                                    $sql = 'SELECT ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro';
                                    $stm = $conexao->prepare($sql);
                                    $stm->execute();
                                    $rs = $stm->fetchAll(PDO::FETCH_OBJ);
                                    foreach($rs as $r):
                                    ?>
                                    <optgroup label="<?php echo CNPJ_Padrao($r->CNPJ); ?>" >
                                        <?php
                                        $sql = 'SELECT un.idUnidade, cd.Nome, ca.CNPJ FROM empresa em INNER JOIN cadastro ca ON ca.idCadastro = em.Cadastro_idCadastro INNER JOIN unidade un ON un.Empresa_idEmpresa = em.idEmpresa INNER JOIN cadastro cd ON cd.idCadastro = un.Cadastro_idCadastro WHERE ca.CNPJ = ? ORDER BY cd.Nome';
                                        $stm = $conexao->prepare($sql);
                                        $stm->bindParam(1, $r->CNPJ);
                                        $stm->execute();
                                        while($row = $stm->fetch(PDO::FETCH_OBJ)):
                                        ?>
                                        <option value="<?php echo $row->idUnidade; ?>"><?php echo utf8_decode($row->Nome); ?></option>
                                        <?php endwhile; ?>

                                    </optgroup>
                                    <?php 
                                    endforeach;
                                endif;
                                ?>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="codigo">Código:</label>
                        <div class="col-sm-2" title="Código do Cargo!">
                            <?php
                            $conexao = conexao::getInstance();
                            $codigo = 1001;
                            $sql = "SELECT CodCargo FROM cargo WHERE CodCargo = ?";
                            $stm = $conexao->prepare($sql);
                            $stm->bindParam(1, $codigo);
                            $stm->execute();
                            if($stm->rowCount() == 1):
                                $rs = $stm->fetch(PDO::FETCH_OBJ);
                                //print_r($rs);
                                while ($rs->CodCargo == $codigo):
                                    $codigo += 1;
                                    $stm = $conexao->prepare($sql);
                                    $stm->bindParam(1, $codigo);
                                    $stm->execute();
                                    $rs = $stm->fetch(PDO::FETCH_OBJ);
                                    if($stm->rowCount() == 0):
                                        break;
                                    endif;
                                endwhile;
                            endif;
                            ?>
                            <div class="input-group">
                                <span class="input-group-addon" id="cod" ><i class="fa fa-info" aria-hidden="true"></i></span>
                                <input type="text" readonly name="codigo" id="codigo" value="<?php echo $codigo; ?>" class="form-control" required="required" >
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="cargo">Cargo:</label>
                        <div class="col-sm-4">
                            <input type="text" name="cargo" id="cargo" class="form-control" required="required" data-error="Digie o Cargo!">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="funcao">Função:</label>
                        <div class="col-sm-4">
                            <input type="text" name="funcao" id="funcao" class="form-control" required="required" data-error="Digie a Função!">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="cbo">CBO:</label>
                        <div class="col-sm-4">
                            <input type="text" name="cbo" id="cbo" class="form-control" onkeypress="return SomenteNumero(event)" required="required" maxlength="6" data-minlength="6">
                            <div class="help-block">Mínimo 6 caracteres.</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-2" for="salario">Salário:</label>
                        <div class="col-sm-2">
                            <div class="input-group">
                                <span class="input-group-addon" id="real">R$</span>
                                <input type="text" class="form-control" id="salario" required name="salario" placeholder="0.00" aria-describedby="real" value="0.00" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-8 col-sm-3">
                            <input type="hidden" value="Cadastro" name="Cargo">
                            <button type="submit" class="btn btn-success">Salvar</button>
                            <button type="reset" class="btn btn-danger">Cancelar</button>
                        </div>
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
<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
function buscaSuperior( $control ){
    $conexao = conexao::getInstance();
    $sql = "SELECT idusuarios FROM usuarios WHERE Superior = ?;";
    if ($control->rowCount() > 0){
        $superior = $control->fetchAll(PDO::FETCH_OBJ);
        foreach($superior as $x){
            array_unique($_SESSION['idChefia']);
            array_push($_SESSION['idChefia'], $x->idusuarios);
        }
        foreach($superior as $s){
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(1, $s->idusuarios);
            $stmt->execute();
            $control = $stmt;
            buscaSuperior( $control );
        }
    }
}
$a = array(1,3,14,16,4,5);
if(in_array($_SESSION['idusuarios'], $a)){
    $conexao = conexao::getInstance();
    $sql = "SELECT idusuarios FROM usuarios WHERE idusuarios != ?;";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $_SESSION['idusuarios']);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_OBJ);
    $chefia = $_SESSION['idusuarios'];
    foreach( $resultado as $rest ){
        $chefia .= ", ". $rest->idusuarios;
    }
    $chefia;
}else{
    $conexao = conexao::getInstance();
    $sql = "SELECT idusuarios FROM usuarios WHERE Superior = ?;";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(1, $_SESSION['idusuarios']);
    $stmt->execute();
    $_SESSION['idChefia'] = array();
    $controlador = $stmt;
    buscaSuperior( $controlador );
    sort($_SESSION['idChefia']);
    $resultado = $_SESSION['idChefia'];
    unset($_SESSION['idChefia']);
    $chefia = $_SESSION['idusuarios'];
    foreach( $resultado as $rest ){
        $chefia .= ", ". $rest;
    }
    $chefia;
}
?>
<script>
$(document).ready(function(){
  $("#pesquisa").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#col_table tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12 conteudo">
            <div class="col-xs-12 col-md-12 col-lg-12 text-center"><h1>Lista de Funcionários</h1></div>
            
            <div class="col-xs-4 col-md-4 col-lg-4"></div>
            <div class="col-xs-2 col-md-2 col-lg-2">
                <form action="Colaborador.php" method="post"><input name="fCol" type="hidden" value="Todos"><button type="submit" class="btn btn-success">Todos</button></form>
            </div>
            <div class="col-xs-2 col-md-2 col-lg-2">
                <form action="Colaborador.php" method="post"><input name="fCol" type="hidden" value="Ativos"><button type="submit" class="btn btn-primary">Ativos</button></form>
            </div>
            <div class="col-xs-2 col-md-2 col-lg-2">
                <form action="Colaborador.php" method="post"><input name="fCol" type="hidden" value="Desligados"><button type="submit" class="btn btn-danger">Inativos</button></form>
            </div>
            <div class="col-xs-12 col-md-12 col-lg-12"> </div>
            <table class="table table-striped table-bordered table-responsive text-center" name="colaboradores" id="colaboradores">
                <thead>
                    <tr>
                        <th colspan="6"><input class="form-control" id="pesquisa" type="text" placeholder="Search.."></th>
                    </tr>
                    <tr>
                        <th>Nome</th>
                        <th>Empresa</th>
                        <th>Unidade</th>
                        <th>Cargo</th>
                        <th>Detalhe</th>
                    </tr>
                </thead>
                <?php
                if(isset($_POST['fCol']) && $_POST['fCol'] == "Todos"){
                ?>
                <tbody id="col_table">
                    <?php
                    $sql = "SELECT co.idColaborador, cn.dDemissao, cco.Nome AS Colaborador, car.Funcao, cun.Nome AS Unidade, cem.Nome AS Empresa, cem.CNPJ FROM chefia ch INNER JOIN colaborador co ON co.idColaborador = ch.Colaborador_idColaborador INNER JOIN cadastro cco ON cco.idCadastro = co.Cadastro_idCadastro INNER JOIN contratacao cn ON cn.idContratacao = co.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = cn.Unidade_idUnidade INNER JOIN cadastro cun ON cun.idCadastro = un.Cadastro_idCadastro INNER JOIN cargo car ON car.idCargo = cn.Cargo_idCargo INNER JOIN empresa em ON em.idEmpresa = un.Empresa_idEmpresa INNER JOIN cadastro cem ON cem.idCadastro = em.Cadastro_idCadastro WHERE ch.Usuario_idUsuario IN (".$chefia.") ORDER BY cco.Nome;";
                    $stm = $conexao->prepare($sql);
                    $stm->execute();
                    while($rs = $stm->fetch(PDO::FETCH_OBJ)){
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
                        <td><?php echo utf8_decode($rs->Colaborador); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Empresa)."-".CNPJ_Padrao($rs->CNPJ); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Unidade); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Funcao); ?></td>
                        <td>
                            <div class="col-sm-3">
                                <a href="<?php echo BASE; ?>pesquisas/DetalheColaborador.php?cod=<?php echo $rs->idColaborador; ?>"><i class="fa fa-folder-open" aria-hidden="true"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <?php
                }elseif(isset($_POST['fCol']) && $_POST['fCol'] == "Ativos" ){
                ?>
                <tbody id="col_table">
                    <?php
                    $sql = "SELECT co.idColaborador, cn.dDemissao, cco.Nome AS Colaborador, car.Funcao, cun.Nome AS Unidade, cem.Nome AS Empresa, cem.CNPJ FROM chefia ch INNER JOIN colaborador co ON co.idColaborador = ch.Colaborador_idColaborador INNER JOIN cadastro cco ON cco.idCadastro = co.Cadastro_idCadastro INNER JOIN contratacao cn ON cn.idContratacao = co.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = cn.Unidade_idUnidade INNER JOIN cadastro cun ON cun.idCadastro = un.Cadastro_idCadastro INNER JOIN cargo car ON car.idCargo = cn.Cargo_idCargo INNER JOIN empresa em ON em.idEmpresa = un.Empresa_idEmpresa INNER JOIN cadastro cem ON cem.idCadastro = em.Cadastro_idCadastro WHERE ch.Usuario_idUsuario IN (".$chefia.") AND cn.dDemissao IS NULL ORDER BY cco.Nome;";
                    $stm = $conexao->prepare($sql);
                    $stm->execute();

                    while($rs = $stm->fetch(PDO::FETCH_OBJ)){
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
                        <td><?php echo utf8_decode($rs->Colaborador); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Empresa)."-".CNPJ_Padrao($rs->CNPJ); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Unidade); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Funcao); ?></td>
                        <td>
                            <div class="col-sm-3">
                                <a href="<?php echo BASE; ?>pesquisas/DetalheColaborador.php?cod=<?php echo $rs->idColaborador; ?>"><i class="fa fa-folder-open" aria-hidden="true"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <?php
                }elseif(isset($_POST['fCol']) && $_POST['fCol'] == "Desligados"){
                ?>
                
                <tbody id="col_table">
                    <?php
                    $sql = "SELECT co.idColaborador, cn.dDemissao, cco.Nome AS Colaborador, car.Funcao, cun.Nome AS Unidade, cem.Nome AS Empresa, cem.CNPJ FROM chefia ch INNER JOIN colaborador co ON co.idColaborador = ch.Colaborador_idColaborador INNER JOIN cadastro cco ON cco.idCadastro = co.Cadastro_idCadastro INNER JOIN contratacao cn ON cn.idContratacao = co.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = cn.Unidade_idUnidade INNER JOIN cadastro cun ON cun.idCadastro = un.Cadastro_idCadastro INNER JOIN cargo car ON car.idCargo = cn.Cargo_idCargo INNER JOIN empresa em ON em.idEmpresa = un.Empresa_idEmpresa INNER JOIN cadastro cem ON cem.idCadastro = em.Cadastro_idCadastro WHERE ch.Usuario_idUsuario IN (".$chefia.") AND cn.dDemissao IS NOT NULL ORDER BY cco.Nome;";
                    $stm = $conexao->prepare($sql);
                    $stm->execute();

                    while($rs = $stm->fetch(PDO::FETCH_OBJ)){
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
                        <td><?php echo utf8_decode($rs->Colaborador); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Empresa)."-".CNPJ_Padrao($rs->CNPJ); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Unidade); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Funcao); ?></td>
                        <td>
                            <div class="col-sm-3">
                                <a href="<?php echo BASE; ?>pesquisas/DetalheColaborador.php?cod=<?php echo $rs->idColaborador; ?>"><i class="fa fa-folder-open" aria-hidden="true"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <?php
                }elseif(!isset($_POST['fCol'])){
                ?>
                <tbody id="col_table">
                    <?php
                    $sql = "SELECT co.idColaborador, cn.dDemissao, cco.Nome AS Colaborador, car.Funcao, cun.Nome AS Unidade, cem.Nome AS Empresa, cem.CNPJ FROM chefia ch INNER JOIN colaborador co ON co.idColaborador = ch.Colaborador_idColaborador INNER JOIN cadastro cco ON cco.idCadastro = co.Cadastro_idCadastro INNER JOIN contratacao cn ON cn.idContratacao = co.Contratacao_idContratacao INNER JOIN unidade un ON un.idUnidade = cn.Unidade_idUnidade INNER JOIN cadastro cun ON cun.idCadastro = un.Cadastro_idCadastro INNER JOIN cargo car ON car.idCargo = cn.Cargo_idCargo INNER JOIN empresa em ON em.idEmpresa = un.Empresa_idEmpresa INNER JOIN cadastro cem ON cem.idCadastro = em.Cadastro_idCadastro WHERE ch.Usuario_idUsuario IN (".$chefia.") AND cn.dDemissao IS NULL ORDER BY cco.Nome;";
                    $stm = $conexao->prepare($sql);
                    $stm->execute();

                    while($rs = $stm->fetch(PDO::FETCH_OBJ)){
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
                        <td><?php echo utf8_decode($rs->Colaborador); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Empresa)."-".CNPJ_Padrao($rs->CNPJ); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Unidade); ?></td>
                        <td class="inf_empresa"><?php echo utf8_decode($rs->Funcao); ?></td>
                        <td>
                            <div class="col-sm-3">
                                <a href="<?php echo BASE; ?>pesquisas/DetalheColaborador.php?cod=<?php echo $rs->idColaborador; ?>"><i class="fa fa-folder-open" aria-hidden="true"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>
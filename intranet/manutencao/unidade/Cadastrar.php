<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
    if($_SESSION['Acesso'] == 4 || $_SESSION['idusuarios'] == 1){
?>
<!-- Content -->
<div class="container-fluid">
  <div class="row conteudo">
    <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 text-center">
      <h1 class="text-center">CADASTRAR UNIDADE</h1>
      <form name="Form" role="form" action="<?=BASE;?>control/banco/UnidadeDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal conteudo" data-toggle="validator">
        <div class="col-xs-10 col-sm-10 col-lg-10 col-md-10">
          <div class="form-group">
            <label class="control-label col-sm-3" for="nome">Nome:</label>
            <div class="col-sm-9">
              <input type="text" name="nome" id="nome" required="required" class="form-control" placeholder="Ex.: Nutribem">
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-10 col-sm-10 col-lg-10 col-md-10">
          <div class="form-group">
            <label class="control-label col-sm-3" for="responsavel">Responsável:</label>
            <div class="col-sm-9">
              <select class="form-control selectpicker" name="responsavel" id="responsavel" title="SELECIONE UMA RESPONSÁVEL PELA UNIDADE!" required="required">
                <?php
                $sql = "SELECT u.idusuarios, u.nome AS Responsavel, u.Superior, s.Nome AS Superior FROM usuarios u INNER JOIN usuarios s ON s.idusuarios = u.Superior";
                $stmt = $conexao->prepare($sql);
                $stmt->execute();
                $dados = $stmt->fetchAll(PDO::FETCH_OBJ);
                foreach ($dados as $d) {
                ?>
                <option value="<?=$d->idusuarios;?>" data-subtext="<?='Supervisor(a):'.utf8_decode($d->Superior);?>"><?=utf8_decode($d->Responsavel);?></option>
                <?php
                }
                ?>
              </select>
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-5" for="endereco">Endereço:</label>
            <div class="col-sm-7">
              <input type="text" name="endereco" id="endereco" required="required" class="form-control" placeholder="Ex.: Rua Alamanda">
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-2" for="numero">Número:</label>
            <div class="col-sm-6">
              <input type="text" name="numero" id="numero" required="required" class="form-control" placeholder="Ex.: 710">
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12"></div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-5" for="bairro">Bairro:</label>
            <div class="col-sm-7">
              <input type="text" name="bairro" id="bairro" required="required" class="form-control" placeholder="Ex.: Jardim Serrano">
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-2" for="cep">CEP:</label>
            <div class="col-sm-6">
              <input type="text" name="cep" id="cep" required="required" class="form-control" placeholder="Ex.: 38.606-188">
              <div class="help-block with-errors"></div>
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-lg-12 col-md-12"></div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-5" for="cidade">Cidade:</label>
            <div class="col-sm-7">
                <input type="text" name="cidade" id="cidade" required="required" class="form-control" placeholder="Ex.: Paracatu">
            </div>
          </div>
        </div>
        <div class="col-xs-6 col-sm-6 col-lg-6 col-md-6">
          <div class="form-group">
            <label class="control-label col-sm-2" for="uf">Estado:</label>
            <div class="col-sm-6">
              <select name="uf" id="uf" class="selectpicker form-control" data-size="5" data-live-search="true" required="required" title="UF">
                <option data-tokens="Acre AC" value="AC">Acre - AC</option>
                <option data-tokens="Alagoas AL" value="AL">Alagoas - AL</option>
                <option data-tokens="Amapá AP" value="AP">Amapá - AP</option>
                <option data-tokens="Amazonas AM" value="AM">Amazonas - AM</option>
                <option data-tokens="Bahia BA" value="BA">Bahia - BA</option>
                <option data-tokens="Ceará CE" value="CE">Ceará - CE</option>
                <option data-tokens="Distrito Federal DF" value="DF">Distrito Federal - DF</option>
                <option data-tokens="Espirito Santo ES" value="ES">Espirito Santo - ES</option>
                <option data-tokens="Goiás GO" value="GO">Goiás - GO</option>
                <option data-tokens="Maranhão MA" value="MA">Maranhão - MA</option>
                <option data-tokens="Mato Grosso MT" value="MT">Mato Grosso - MT</option>
                <option data-tokens="Mato Grosso do Sul MS" value="MS">Mato Grosso do Sul - MS</option>
                <option data-tokens="Minas Gerais MG" value="MG">Minas Gerais - MG</option>
                <option data-tokens="Pará PA" value="PA">Pará - PA</option>
                <option data-tokens="Paraíba PB" value="PB">Paraíba - PB</option>
                <option data-tokens="Paraná PR" value="PR">Paraná - PR</option>
                <option data-tokens="Pernabuco PE" value="PE">Pernabuco - PE</option>
                <option data-tokens="Piauí PI" value="PI">Piauí - PI</option>
                <option data-tokens="Rio de Janeiro RJ" value="RJ">Rio de Janeiro - RJ</option>
                <option data-tokens="Rio Grande do Norte RN" value="RN">Rio Grande do Norte - RN</option>
                <option data-tokens="Rio Grande do Sul RS" value="RS">Rio Grande do Sul - RS</option>
                <option data-tokens="Rondônia RS" value="RO">Rondônia - RS</option>
                <option data-tokens="Roraima RR" value="RR">Roraima - RR</option>
                <option data-tokens="Santa Catarina SC" value="SC">Santa Catarina - SC</option>
                <option data-tokens="São Paulo SP" value="SP">São Paulo - SP</option>
                <option data-tokens="Sergipe SE" value="SE">Sergipe - SE</option>
                <option data-tokens="Tocantis TO" value="TO">Tocantis - TO</option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-xs-5 col-sm-5 col-lg-5 col-md-5">
          <div class="form-group">
            <input type="hidden" name="Unidade" value="Cadastrar">
            <button class="btn btn-success text-left" type="submit">Salvar <i class="fas fa-save"></i></button>
          </div>
        </div>
      </form>
    </div>
	</div>
</div>
<?php
  }else{
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/manutencao/");
  }
}
?>
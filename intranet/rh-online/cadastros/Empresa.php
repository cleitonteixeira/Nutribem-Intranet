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
    <script type="text/javascript">$(document).ready(function(){	$("#cnpj").mask("99.999.999/9999-99");});</script>
    <!-- Content -->
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-offset-3 col-xs-9"><h1>Cadastro Empresa</h1></div>
            <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/EmpresaDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator">
                <div class="col-xs-12">   
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="razao">Razão Social:</label>
                        <div class="col-sm-8">
                            <input type="text" name="razao" id="razao" class="form-control" required="required" data-error="Digie a Razão Social.">
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-sm-2"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="cnpj">CNPJ:</label>
                        <div class="col-sm-4">
                            <input type="text" name="cnpj" id="cnpj" class="cnpj form-control" maxlength="18" data-error="Digie um CNPJ." required="required">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    <div class="col-xs-offset-3 col-xs-9"><h4>Endereço</h4></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="logradouro">Logradouro:</label>
                        <div class="col-sm-8">
                            <input type="text" name="logradouro" id="logradouro" class="form-control" maxlength="40" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-sm-2"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="numero">Número:</label>  
                        <div class="col-sm-3">
                            <input type="text" name="numero" id="numero" class="form-control" required >
                            <div class="help-block with-errors"></div>
                        </div>
                        <label class="col-sm-2 control-label" for="cep">CEP:</label>  
                        <div class="col-sm-3">
                            <input type="text" name="cep" id="cep" class="form-control" required>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="cidade">Cidade:</label>  
                        <div class="col-sm-2">
                            <input type="text" name="cidade" id="cidade" class="form-control" required>
                        </div>
                        <label class="col-sm-2 control-label" for="bairro">Bairro:</label>  
                        <div class="col-sm-2">
                            <input type="text" name="bairro" id="bairro" class="form-control" required>
                        </div>
                        <label class="col-sm-1 control-label" for="uf">UF:</label>  
                        <div class="col-sm-3">
                           <select class="selectpicker" title="Selecione uma UF" name="uf" id="uf" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma UF." required>
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
                                <option data-tokens="São Paulo" value="SP">São Paulo - SP</option>
                                <option data-tokens="Sergipe SE" value="SE">Sergipe - SE</option>
                                <option data-tokens="Tocantis TO" value="TO">Tocantis - TO</option>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <div class="col-sm-offset-8 col-sm-3">
                            <input type="hidden" value="Cadastro" name="Empresa">
                            <button type="submit" class="btn btn-success">Salvar</button>
                            <button type="reset" class="btn btn-danger">Cancelar</button>
                        </div>
                    </div>
                
                </div>
            </form>
        </div>
    </div>
</div>
     <script type="text/javascript">
        $(document).ready(function () {
            $('.cnpj').cpfcnpj({
                mask: false,
                validate: 'cnpj',
                event: 'focusout',
                ifValid: function (input) { input.removeClass("has-error has-danger error"); },
                ifInvalid: function (input) { input.addClass("has-error has-danger error"); }
            });
        });
    </script>

<?php
  endif;
  require_once("../control/arquivo/footer/Footer.php");
?>
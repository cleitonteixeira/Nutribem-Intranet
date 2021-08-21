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
    <script type="text/javascript">
        /* Máscaras ER */
        function mascara(o,f){
            v_obj=o
            v_fun=f
            setTimeout("execmascara()",1)
        }
        function execmascara(){
            v_obj.value=v_fun(v_obj.value)
        }
        function mtel(v){
            v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito
            v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos
            v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos
            return v;
        }
        function id( el ){
            return document.getElementById( el );
        }
        window.onload = function(){
            id('telefone').onkeypress = function(){
                mascara( this, mtel );
            }
            id('celular').onkeypress = function(){
                mascara( this, mtel );
            }
        }
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 conteudo">
                <div class="col-xs-offset-3 col-xs-9"><h1>Cadastro Unidade</h1></div>
                <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/UnidadeDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator">
                    <div class="col-xs-12">   

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="nome">Nome:</label>
                            <div class="col-sm-8">
                                <input type="text" name="nome" id="nome" class="form-control" required="required" data-match-error="Digie o nome!">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="empresa">Empresa:</label>  
                            <div class="col-sm-5">
                               <select class="selectpicker" title="Selecione uma Empresa." name="Empresa" id="Empresa" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma Empresa." required>
                                <?php
                                    $conexao = conexao::getInstance();
                                    $sql = " SELECT emp.idEmpresa, cad.Nome, cad.CNPJ FROM empresa emp INNER JOIN cadastro cad ON cad.idCadastro = emp.Cadastro_idCadastro;";
                                    $stm = $conexao->prepare($sql);
                                    $stm->execute();
                                    if($stm->rowCount() == 0):
                                   ?>
                                <script>
                                    alert("Nenhuma Empresa Cadastrada!");
                                    location.href = 'Empresa.php';
                                </script>
                                   <?php
                                    else:
                                        while($row= $stm->fetch(PDO::FETCH_OBJ)):
                                   
                                   ?>
                                    <option data-tokens="<?php echo utf8_decode($row->Nome)." ".$row->CNPJ ?>" value="<?php echo $row->idEmpresa ?>"><?php echo utf8_decode($row->Nome)." - "; echo  CNPJ_Padrao($row->CNPJ); ?></option>
                                    <?php
                                        endwhile;
                                    endif;
                                    ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-5"></div>
                        </div>

                        <div class="col-xs-offset-3 col-xs-9"><h4>Contato</h4></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="celular">Celular:</label>
                            <div class="col-sm-5">
                                <input type="tel" name="celular" id="celular" class="form-control" data-minlength="15" maxlength="15">
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-5"></div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="telefone">Telefone:</label>  
                            <div class="col-sm-5">
                                <input type="tel" name="telefone" id="telefone" class="form-control" maxlength="14">
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-5"></div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">Email</label>
                            <div class="col-sm-5">
                                <input id="email" name="email" class="form-control" type="email" data-error="Por favor, informe um e-mail correto." required>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="col-sm-5"></div>
                        </div>
                        
                        <div class="col-xs-offset-3 col-xs-9"><h4>Endereço</h4></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="logradouro">Logradouro:</label>
                        <div class="col-sm-8">
                            <input type="text" name="logradouro" id="logradouro" class="form-control" maxlength="30" required>
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
                        <div class="col-sm-3">
                            <input type="text" name="cidade" id="cidade" class="form-control" required>
                        </div>
                        <label class="col-sm-2 control-label" for="bairro">Bairro:</label>  
                        <div class="col-sm-2">
                            <input type="text" name="bairro" id="bairro" class="form-control" required>
                        </div>
                        <label class="col-sm-1 control-label" for="uf">UF:</label>  
                        <div class="col-sm-2">
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
                                <input type="hidden" value="Cadastro" name="Unidade">
                                <button type="submit" class="btn btn-success">Salvar</button>
                                <button type="reset" class="btn btn-danger">Cancelar</button>
                            </div>
                        </div>

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
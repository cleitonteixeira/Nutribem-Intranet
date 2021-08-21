<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
    $Troca = array("/","\\","|");
?>
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
		id('TelefoneFinanceiro').onkeypress = function(){
			mascara( this, mtel );
		}
		id('TelefoneComercial').onkeypress = function(){
			mascara( this, mtel );
		}
	}
	function SomenteNumero(e){
		var tecla=(window.event)?event.keyCode:e.which;   
		if((tecla>47 && tecla<58)) return true;
		else{
			if (tecla==8 || tecla==0) return true;
			else  return false;
		}
	}
</script>
<script type="text/javascript">$(document).ready(function(){	$("#cnpj").mask("99.999.999/9999-99");});</script>
<script type="text/javascript">$(document).ready(function(){	$("#cep").mask("99.999-999");});</script>
<script type="text/javascript">$(document).ready(function(){	$("#cep").mask("99.999-999");});</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
            <?php
            $sql = "SELECT c.idContratante, cd.idCadastro, c.IE, cd.Nome AS Cliente, cd.CNPJ AS CNPJ, ed.*, cb.idECobranca AS idCobranca, cb.Endereco AS eCobranca, cb.Bairro AS bCobranca, cb.CEP AS ceCobranca, cb.Cidade AS cCobranca, cb.Numero AS nCobranca, cb.UF AS uCobranca FROM contratante c INNER JOIN cadastro cd ON cd.idCadastro = c.Cadastro_idCadastro INNER JOIN endereco ed ON ed.idEndereco = c.Endereco_idEndereco INNER JOIN ecobranca cb ON cb.idECobranca = c.Cobranca_idCobranca WHERE c.idContratante = ?;";
            $stm = $conexao->prepare($sql);
            $stm->bindParam(1, $_GET['cod']);
            $stm->execute();
            $rs = $stm->fetch(PDO::FETCH_OBJ);
            ?>
            <h1 class="text-center"><span class="label label-info">DADOS CLIENTE</span></h1>
            <div>
                <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/AtualizaClienteDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal text-center" data-toggle="validator" id="FormCliente" name="FormCliente">
                    <div class="col-xs-12 col-md-12 col-lg-12">
                        <hr />
                        <p><small>Dados de Básicos</small></p>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="razao">Razão Social:</label>
                                <div class="col-sm-9">
                                    <input readonly type="text" name="razao" id="razao" required class="form-control" value="<?php echo utf8_decode($rs->Cliente); ?>"  />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="cpnj">CNPJ:</label>
                                <div class="col-sm-6">
                                    <input readonly required type="text" name="cnpj" id="cnpj" class="form-control" value="<?php echo utf8_decode(CNPJ_Padrao(str_pad($rs->CNPJ, 14,0,STR_PAD_LEFT))); ?>"  />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="ie">IE:</label>
                                <div class="col-sm-6">
                                    <?php
                                    if(empty($rs->IE)){
                                        $ie = "Sem Dados";
                                    }else{
                                        $ie = $rs->IE;
                                    }
                                    ?>
                                    <input required type="text" name="ie" id="ie" class="form-control" value="<?php echo $ie; ?>"  />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-12">
                        <hr />
                        <p><small>Dados de Contato</small></p>
                        <?php
                        $sql = "SELECT * FROM ccontratante WHERE Contratante_idContratante = ?;";
                        $stm = $conexao->prepare($sql);
                        $stm->bindParam(1, $rs->idContratante);
                        $stm->execute();
                        $rx = $stm->fetchAll(PDO::FETCH_OBJ);
                        foreach($rx as $c){
                        ?>
                        <h4>Responsável <?php echo $c->Tipo?></h4>
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="col-sm-4 control-label" for="Responsavel<?php echo $c->Tipo?>">Responsável:</label>
                                <div class="col-sm-8">
                                    <input type="text" name="Responsavel<?php echo $c->Tipo?>" id="Responsavel<?php echo $c->Tipo?>" required class="form-control" value="<?php echo utf8_decode($c->Responsavel); ?>" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="Email<?php echo $c->Tipo?>">Email:</label>
                                <div class="col-sm-9">
                                    <input type="email" name="Email<?php echo $c->Tipo?>" id="Email<?php echo $c->Tipo?>" required class="form-control" value="<?php echo utf8_decode($c->Email); ?>" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="Telefone<?php echo $c->Tipo?>">Telefone:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="Telefone<?php echo $c->Tipo?>" id="Telefone<?php echo $c->Tipo?>" required class="form-control" onkeypress="return SomenteNumero(event)" value="<?php echo strlen($c->Telefone) == 11 ? Cel_Padrao($c->Telefone) : Tel_Padrao($c->Telefone); ?>" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="id<?php echo $c->Tipo?>" id="id<?php echo $c->Tipo?>" value="<?php echo $c->idCContratante; ?>"/>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-12 col-md-12 col-lg-12">
                        <hr />
                        <p><small>Dados de Endereço</small></p>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="logradouro">Logradouro:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="logradouro" id="logradouro" required class="form-control" value="<?php echo utf8_decode($rs->Endereco); ?>"  />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="cep">CEP:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="cep" id="cep" required class="form-control" placeholder="Ex.: 99.999-999" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode(CEP_Padrao($rs->CEP)); ?>" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="uf">UF:</label>
                                <div class="col-sm-3">
                                    <select class="selectpicker form-control" title="Selecione uma UF" name="uf" id="uf" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma UF." required>
                                        <option <?php echo $rs->UF=='AC'?'selected':'';?> data-tokens="Acre AC" value="AC">Acre - AC</option>
                                        <option <?php echo $rs->UF=='AL'?'selected':'';?> data-tokens="Alagoas AL" value="AL">Alagoas - AL</option>
                                        <option <?php echo $rs->UF=='AP'?'selected':'';?> data-tokens="Amapá AP" value="AP">Amapá - AP</option>
                                        <option <?php echo $rs->UF=='AM'?'selected':'';?> data-tokens="Amazonas AM" value="AM">Amazonas - AM</option>
                                        <option <?php echo $rs->UF=='BA'?'selected':'';?> data-tokens="Bahia BA" value="BA">Bahia - BA</option>
                                        <option <?php echo $rs->UF=='CE'?'selected':'';?> data-tokens="Ceará CE" value="CE">Ceará - CE</option>
                                        <option <?php echo $rs->UF=='DF'?'selected':'';?> data-tokens="Distrito Federal DF" value="DF">Distrito Federal - DF</option>
                                        <option <?php echo $rs->UF=='ES'?'selected':'';?> data-tokens="Espirito Santo ES" value="ES">Espirito Santo - ES</option>
                                        <option <?php echo $rs->UF=='GO'?'selected':'';?> data-tokens="Goiás GO" value="GO">Goiás - GO</option>
                                        <option <?php echo $rs->UF=='MA'?'selected':'';?> data-tokens="Maranhão MA" value="MA">Maranhão - MA</option>
                                        <option <?php echo $rs->UF=='MT'?'selected':'';?> data-tokens="Mato Grosso MT" value="MT">Mato Grosso - MT</option>
                                        <option <?php echo $rs->UF=='MS'?'selected':'';?> data-tokens="Mato Grosso do Sul MS" value="MS">Mato Grosso do Sul - MS</option>
                                        <option <?php echo $rs->UF=='MG'?'selected':'';?> data-tokens="Minas Gerais MG" value="MG">Minas Gerais - MG</option>
                                        <option <?php echo $rs->UF=='PA'?'selected':'';?> data-tokens="Pará PA" value="PA">Pará - PA</option>
                                        <option <?php echo $rs->UF=='PB'?'selected':'';?> data-tokens="Paraíba PB" value="PB">Paraíba - PB</option>
                                        <option <?php echo $rs->UF=='PR'?'selected':'';?> data-tokens="Paraná PR" value="PR">Paraná - PR</option>
                                        <option <?php echo $rs->UF=='PE'?'selected':'';?> data-tokens="Pernabuco PE" value="PE">Pernabuco - PE</option>
                                        <option <?php echo $rs->UF=='PI'?'selected':'';?> data-tokens="Piauí PI" value="PI">Piauí - PI</option>
                                        <option <?php echo $rs->UF=='RJ'?'selected':'';?> data-tokens="Rio de Janeiro RJ" value="RJ">Rio de Janeiro - RJ</option>
                                        <option <?php echo $rs->UF=='RN'?'selected':'';?> data-tokens="Rio Grande do Norte RN" value="RN">Rio Grande do Norte - RN</option>
                                        <option <?php echo $rs->UF=='RS'?'selected':'';?> data-tokens="Rio Grande do Sul RS" value="RS">Rio Grande do Sul - RS</option>
                                        <option <?php echo $rs->UF=='RO'?'selected':'';?> data-tokens="Rondônia RO" value="RO">Rondônia - RO</option>
                                        <option <?php echo $rs->UF=='RR'?'selected':'';?> data-tokens="Roraima RR" value="RR">Roraima - RR</option>
                                        <option <?php echo $rs->UF=='SC'?'selected':'';?> data-tokens="Santa Catarina SC" value="SC">Santa Catarina - SC</option>
                                        <option <?php echo $rs->UF=='SP'?'selected':'';?> data-tokens="São Paulo SP" value="SP">São Paulo - SP</option>
                                        <option <?php echo $rs->UF=='SE'?'selected':'';?> data-tokens="Sergipe SE" value="SE">Sergipe - SE</option>
                                        <option <?php echo $rs->UF=='TO'?'selected':'';?> data-tokens="Tocantis TO" value="TO">Tocantis - TO</option>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="numero">Número:</label>
                                <div class="col-sm-4">
                                    <input type="text" name="numero" id="numero" required class="form-control" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode($rs->Numero); ?>"  />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="cidade">Cidade:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="cidade" id="cidade" required class="form-control" value="<?php echo utf8_decode($rs->Cidade); ?>" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="bairro">Bairro:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="bairro" id="bairro" required class="form-control" value="<?php echo utf8_decode(stripslashes($rs->Bairro)); ?>" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <hr />
                        <p><small>Endereço de Cobrança</small></p>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="logradouroCobranca">Logradouro:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="logradouroCobranca" id="logradouroCobranca" required class="form-control" value="<?php echo utf8_decode($rs->eCobranca); ?>"  />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="cepCobranca">CEP:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="cepCobranca" id="cepCobranca" required class="form-control" placeholder="Ex.: 99.999-999" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode(CEP_Padrao($rs->ceCobranca)); ?>" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label" for="ufCobranca">UF:</label>
                                <div class="col-sm-3">
                                    <select class="selectpicker form-control" title="Selecione uma UF" name="ufCobranca" id="ufCobranca" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma UF." required>
                                        <option <?php echo $rs->uCobranca=='AC'?'selected':'';?> data-tokens="Acre AC" value="AC">Acre - AC</option>
                                        <option <?php echo $rs->uCobranca=='AL'?'selected':'';?> data-tokens="Alagoas AL" value="AL">Alagoas - AL</option>
                                        <option <?php echo $rs->uCobranca=='AP'?'selected':'';?> data-tokens="Amapá AP" value="AP">Amapá - AP</option>
                                        <option <?php echo $rs->uCobranca=='AM'?'selected':'';?> data-tokens="Amazonas AM" value="AM">Amazonas - AM</option>
                                        <option <?php echo $rs->uCobranca=='BA'?'selected':'';?> data-tokens="Bahia BA" value="BA">Bahia - BA</option>
                                        <option <?php echo $rs->uCobranca=='CE'?'selected':'';?> data-tokens="Ceará CE" value="CE">Ceará - CE</option>
                                        <option <?php echo $rs->uCobranca=='DF'?'selected':'';?> data-tokens="Distrito Federal DF" value="DF">Distrito Federal - DF</option>
                                        <option <?php echo $rs->uCobranca=='ES'?'selected':'';?> data-tokens="Espirito Santo ES" value="ES">Espirito Santo - ES</option>
                                        <option <?php echo $rs->uCobranca=='GO'?'selected':'';?> data-tokens="Goiás GO" value="GO">Goiás - GO</option>
                                        <option <?php echo $rs->uCobranca=='MA'?'selected':'';?> data-tokens="Maranhão MA" value="MA">Maranhão - MA</option>
                                        <option <?php echo $rs->uCobranca=='MT'?'selected':'';?> data-tokens="Mato Grosso MT" value="MT">Mato Grosso - MT</option>
                                        <option <?php echo $rs->UF=='MS'?'selected':'';?> data-tokens="Mato Grosso do Sul MS" value="MS">Mato Grosso do Sul - MS</option>
                                        <option <?php echo $rs->uCobranca=='MG'?'selected':'';?> data-tokens="Minas Gerais MG" value="MG">Minas Gerais - MG</option>
                                        <option <?php echo $rs->uCobranca=='PA'?'selected':'';?> data-tokens="Pará PA" value="PA">Pará - PA</option>
                                        <option <?php echo $rs->uCobranca=='PB'?'selected':'';?> data-tokens="Paraíba PB" value="PB">Paraíba - PB</option>
                                        <option <?php echo $rs->uCobranca=='PR'?'selected':'';?> data-tokens="Paraná PR" value="PR">Paraná - PR</option>
                                        <option <?php echo $rs->uCobranca=='PE'?'selected':'';?> data-tokens="Pernabuco PE" value="PE">Pernabuco - PE</option>
                                        <option <?php echo $rs->uCobranca=='PI'?'selected':'';?> data-tokens="Piauí PI" value="PI">Piauí - PI</option>
                                        <option <?php echo $rs->uCobranca=='RJ'?'selected':'';?> data-tokens="Rio de Janeiro RJ" value="RJ">Rio de Janeiro - RJ</option>
                                        <option <?php echo $rs->uCobranca=='RN'?'selected':'';?> data-tokens="Rio Grande do Norte RN" value="RN">Rio Grande do Norte - RN</option>
                                        <option <?php echo $rs->uCobranca=='RS'?'selected':'';?> data-tokens="Rio Grande do Sul RS" value="RS">Rio Grande do Sul - RS</option>
                                        <option <?php echo $rs->uCobranca=='RO'?'selected':'';?> data-tokens="Rondônia RO" value="RO">Rondônia - RO</option>
                                        <option <?php echo $rs->uCobranca=='RR'?'selected':'';?> data-tokens="Roraima RR" value="RR">Roraima - RR</option>
                                        <option <?php echo $rs->uCobranca=='SC'?'selected':'';?> data-tokens="Santa Catarina SC" value="SC">Santa Catarina - SC</option>
                                        <option <?php echo $rs->uCobranca=='SP'?'selected':'';?> data-tokens="São Paulo SP" value="SP">São Paulo - SP</option>
                                        <option <?php echo $rs->uCobranca=='SE'?'selected':'';?> data-tokens="Sergipe SE" value="SE">Sergipe - SE</option>
                                        <option <?php echo $rs->uCobranca=='TO'?'selected':'';?> data-tokens="Tocantis TO" value="TO">Tocantis - TO</option>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="numeroCobranca">Número:</label>
                                <div class="col-sm-4">
                                    <input type="text" name="numeroCobranca" id="numeroCobranca" required class="form-control" onkeypress="return SomenteNumero(event)" value="<?php echo utf8_decode($rs->nCobranca); ?>"  />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="cidadeCobranca">Cidade:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="cidadeCobranca" id="cidadeCobranca" required class="form-control" value="<?php echo utf8_decode($rs->cCobranca); ?>" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="bairroCobranca">Bairro:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="bairroCobranca" id="bairroCobranca" required class="form-control" value="<?php echo utf8_decode(stripslashes($rs->bCobranca)); ?>" />
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-offset-2 col-md-offset-2 col-lg-offset-2 col-xs-4 col-md-4 col-lg-4 ">
                        <input type="hidden" name="idCobranca" id="idCobranca" value="<?php echo $rs->idCobranca; ?>" />
                        <input type="hidden" name="idEndereco" id="idEndereco" value="<?php echo $rs->idEndereco; ?>" />
                        <input type="hidden" name="idCadastro" id="idCadastro" value="<?php echo $rs->idCadastro; ?>" />
                        <input type="hidden" name="idContratante" id="idContratante" value="<?php echo $rs->idContratante; ?>" />
                        <input type="hidden" name="Cliente" id="Cliente" value="Atualiza" />
                        
                        <button class="btn btn-success" type="submit">Salvar</button>
                    </div>
                </form>
            </div>  
            <div class="col-md-12 col-xs-12 col-lg-12">     
            </div>  
        </div>
    </div>
</div>
<?php
    require_once("../control/arquivo/footer/Footer.php");
}
?>
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
        $(document).ready(function(){
            $("select[name='empresa']").change(function(e){
                var empresa = $('#empresa').val();//pegando o value do option selecionado
                //alert(empresa);//apenas para debugar a variável

                $.getJSON('CompletaSelect.inc.php?empresa='+empresa, function (dados){
                    //alert(dados);
                    if (dados.length > 0){ 	
                        var option = '<option value="">Selecione!</option>';
                        $.each(dados, function(i, obj){
                            option += '<option value="'+obj.idUnidade+'">'+obj.Nome.toUpperCase()+'</option>';
                        })
                        $('#unidade').html(option).show();
                    }else{
                        Reset();
                    }
                })
            })
            <!-- Resetar Selects -->
            function Reset(){
                $('#unidade').empty();

            }
            
            $("select[name='unidade']").change(function(e){
                var unidade = $('#unidade').val();//pegando o value do option selecionado
                //alert(unidade);//apenas para debugar a variável
                $.getJSON('CompletaSelect.inc.php?unidade='+unidade, function (dados){
                    //alert(dados);
                    if (dados.length > 0){ 	
                        var option = '<option value="">Selecione!</option>';
                        $.each(dados, function(i, obj){
                            option += '<option value="'+obj.idCargo+'">'+obj.CodCargo+' - '+obj.Funcao.toUpperCase()+' - CBO:'+obj.CBO+'</option>';
                        })
                        $('#cargo').html(option).show();
                    }else{
                        Reset2();
                    }
                })
            })
            <!-- Resetar Selects -->
            function Reset2(){
                $('#cargo').empty();
            }
            
            
            $("select[name='cargo']").change(function(e){
                var cargo = $('#cargo').val();//pegando o value do option selecionado
                //alert(cargo);//apenas para debugar a variável

                $.getJSON('CompletaSelect.inc.php?Cargo='+cargo, function (dados){
                    //alert(dados);
                    if (dados.length == 1){ 	
                        var Salario = '';
                        var Funcao = '';
                        $.each(dados, function(i, obj){
                            Salario = obj.Salario;
                            Funcao = obj.Funcao;
                            //alert("aq");
                        })
                        $('#Salario').val(Salario).show();
                        $('#Funcao').val(Funcao).show();
                    }else{
                        //alert("aqwq");
                        Reset1();
                    }
                })
            })
            <!-- Resetar Selects -->
            function Reset1(){
                $('#Salario').val("").show();
                $('#Funcao').val("").show();
                /*$('#Salario').empty();
                    $('#Funcao').empty();*/
            }
            
            
            $("select[name='unidade']").change(function(e){
                var unidade = $('#unidade').val();//pegando o value do option selecionado
                //alert(unidade);//apenas para debugar a variável
                $.getJSON('CompletaSelect.inc.php?cod='+unidade, function (dados){
                    //alert(dados);
                    if (dados.length > 0){ 	
                        var option = '';
                        $.each(dados, function(i, obj){
                            option = obj.Cod;
                        })
                        $('#CodColaborador').val(option).show();
                    }else{
                        Reset3();
                    }
                })
            })
            <!-- Resetar Selects -->
            function Reset3(){
                $('#CodColaborador').empty();
            }
        });
    </script>
    <script type="text/javascript">
        $(function(){
            $("#salario").maskMoney();
        })
    </script>
    <script type="text/javascript">$(document).ready(function(){	$("#cpf").mask("999.999.999-99");});</script>
    <script language="javascript" type="text/javascript">

        var ftap="3298765432";
        var total=0;
        var i;
        var resto=0;
        var numPIS=0;
        var strResto="";

        function ChecaPIS(pis)
        {

            total=0;
            resto=0;
            numPIS=0;
            strResto="";

            numPIS=pis;

            if (numPIS=="" || numPIS==null)
            {
                return false;
            }

            for(i=0;i<=9;i++)
            {
                resultado = (numPIS.slice(i,i+1))*(ftap.slice(i,i+1));
                total=total+resultado;
            }

            resto = (total % 11)

            if (resto != 0)
            {
                resto=11-resto;
            }

            if (resto==10 || resto==11)
            {
                strResto=resto+"";
                resto = strResto.slice(1,2);
            }

            if (resto!=(numPIS.slice(10,11)))
            {
                return false;
            }

            return true;
        }

        function ValidaPis()
        {
            var pis = document.getElementById("pis").value;
            //alert(pis);
            if (!ChecaPIS(pis))
            {
                alert("PIS INVALIDO");
            } else {
                alert("PIS VALIDO");
            }
        }


    </script>
   
    <script type="text/javascript">
        //Total máximo de campos que você permitirá criar em seu site:
        var totalCampos = 30;

        //Não altere os valores abaixo, pois são variáveis controle;
        var iLoop = 1;
        var iCount = 0;
        var linhaAtual;

        function AddCampos() {
            var hidden1 = document.getElementById("hidden1");
            var hidden2 = document.getElementById("hidden2");

            //Executar apenas se houver possibilidade de inserção de novos campos:
            if (iCount < totalCampos) {

                //Limpar hidden1, para atualizar a lista dos campos que ainda estão vazios:
                hidden2.value = "";

                //Atualizando a lista dos campos que estão ocultos.
                //Essa lista ficará armazenada temporiariamente em hidden2;
                for (iLoop = 1; iLoop <= totalCampos; iLoop++) {
                    if (document.getElementById("linha"+iLoop).style.display == "none") {
                        if (hidden2.value == "") {
                            hidden2.value = "linha"+iLoop;
                        }else{
                            hidden2.value += ",linha"+iLoop;
                        }
                    }
                }
                //Quebrando a lista que foi armazenada em hidden2 em array:

                linhasOcultas = hidden2.value.split(",");

                if (linhasOcultas.length > 0) {
                    //Tornar visível o primeiro elemento de linhasOcultas:
                    document.getElementById(linhasOcultas[0]).style.display = "block"; iCount++;

                    //Acrescentando o índice zero a hidden1:
                    if (hidden1.value == "") {
                        hidden1.value = linhasOcultas[0];
                    }else{
                        hidden1.value += ","+linhasOcultas[0];
                    }

                    /*Retirar a opção acima da lista de itens ocultos: <-------- OPCIONAL!!!
                if (hidden2.value.indexOf(","+linhasOcultas[0]) != -1) {
                        hidden2.value = hidden2.value.replace(linhasOcultas[0]+",","");
                }else if (hidden2.indexOf(linhasOcultas[0]+",") == 0) {
                        hidden2.value = hidden2.value.replace(linhasOcultas[0]+",","");
                }else{
                        hidden2.value = "";
                }
                */
                }
            }
        }

        function RemoverCampos(id) {
            //Criando ponteiro para hidden1:        
            var hidden1 = document.getElementById("hidden1");
            //Pegar o valor do campo que será excluído:
            var campoValor = document.getElementById("dNome["+id+"]").value;
            //Se o campo não tiver nenhum valor, atribuir a string: vazio:
            if (campoValor == "") {
                campoValor = "vazio";
            }

            if(confirm("O campo que contém o valor:\n» "+campoValor+"\nserá excluído!\n\nDeseja prosseguir?")){
                document.getElementById("linha"+id).style.display = "none"; iCount--;

                //Removendo o valor de hidden1:
                if (hidden1.value.indexOf(",linha"+id) != -1) {
                    hidden1.value = hidden1.value.replace(",linha"+id,"");
                }else if (hidden1.value.indexOf("linha"+id+",") == 0) {
                    hidden1.value = hidden1.value.replace("linha"+id+",","");
                }else{
                    hidden1.value = "";
                }
            }
        }
    </script>
    <div class="row">
        <div class="col-md-12 conteudo">
            <div class="col-xs-offset-3 col-xs-9"><h2>Cadastro Colaborador</h2></div>
            <form name="Form" role="form" action="<?php echo BASE; ?>control/banco/ColaboradorDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator" id="FormColaborador" name="FormColaborador">
                <div class="col-xs-12">

                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#Pessoais">Dados Pessoais</a></li>
                        <li><a data-toggle="tab" href="#Contratacao">Dados da Contratação</a></li>
                        <li><a data-toggle="tab" href="#Depedentes">Depedentes</a></li>
                        <li><a data-toggle="tab" href="#Bancarios">Dados Bancários</a></li>
                        <li><a data-toggle="tab" href="#Endereco">Dados de Endereço</a></li>
                        <li><a data-toggle="tab" href="#Contato">Dados de Contato</a></li>
                        <li><a data-toggle="tab" href="#Chefia">Gestor</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="Pessoais" class="tab-pane fade in active">
                            <div class="col-xs-12">
                                <div class="col-xs-offset-3 col-xs-9"><h4><u>Dados Pessoais</u></h4></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="nome">Nome:</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="nome" id="nome" class="form-control" required="required" data-error="Digie o nome!">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="dNascimento">Data de Nascimento:</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="dNascimento" id="dNascimento" class="form-control" required="required">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <label class="control-label col-sm-2" for="sexo">Sexo:</label>
                                    <div class="col-sm-5">
                                        <label class="radio-inline"><input type="radio" name="sexo" value="Feminino" required >Feminino</label>
                                        <label class="radio-inline"><input type="radio" name="sexo" value="Masculino" required >Masculino</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="cor">Cor:</label>  
                                    <div class="col-sm-2">
                                        <select class="selectpicker" name="cor" id="cor" data-live-search="true" title="Selecione uma Cor." required>
                                            <option data-tokens="Branco" value="Branco">Branco</option>
                                            <option data-tokens="Amarelo" value="Amarelo">Amarelo</option>
                                            <option data-tokens="Pardo" value="Pardo">Pardo</option>
                                            <option data-tokens="Preto" value="Preto">Preto</option>
                                            <option data-tokens="Não Informado" value="Não Informado">Não Informado</option>
                                        </select>
                                    </div>
                                    <label class="control-label col-sm-2" for="naturalidade">Naturalidade:</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" name="naturalidade" id="naturalidade" required>
                                    </div>
                                    <label class="control-label col-sm-2" for="pais">País:</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" name="pais" id="pais" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="RG">RG:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="RG" id="RG" class="form-control" onkeypress="return SomenteNumero(event)" placeholder="Somente Números." required="required" maxlength="14">
                                    </div>
                                    <label class="col-sm-1 control-label" for="emissor">Emissor:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="emissor" id="emissor" class="form-control" required="required" maxlength="7">
                                    </div>
                                    <label class="col-sm-2 control-label" for="dEmissao">Data de Emissão:</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="dEmissao" id="dEmissao" class="form-control" required="required">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="cpf">CPF:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="cpf" id="cpf" class="form-control" placeholder="Somente Números." required="required" maxlength="14">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="militar">Doc. Militar:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="militar" id="militar" class="form-control" placeholder="Somente Números." maxlength="14">
                                    </div>
                                    <label class="col-sm-2 control-label" for="cMilitar">Categoria:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="cMilitar" id="cMilitar" class="form-control" maxlength="4">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="titulo">Título de Eleitor:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="titulo" id="titulo" class="form-control" required="required" maxlength="14" onkeypress="return SomenteNumero(event)">
                                    </div>
                                    <label class="col-sm-1 control-label" for="Zona">Zona:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="Zona" id="Zona" class="form-control" required="required" maxlength="7" onkeypress="return SomenteNumero(event)">
                                    </div>
                                    <label class="col-sm-2 control-label" for="secao">Seção:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="secao" id="secao" class="form-control" required="required" onkeypress="return SomenteNumero(event)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="cnh">CNH:</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="cnh" id="cnh" class="form-control" placeholder="Somente Números." maxlength="11" onkeypress="return SomenteNumero(event)">
                                    </div>
                                    <label class="col-sm-1 control-label" for="categoria">Categoria:</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="categoria" id="categoria" class="form-control" maxlength="11">
                                    </div>
                                    <label class="col-sm-2 control-label" for="validade">Validade:</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="validade" id="validade" class="form-control" maxlength="11">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="gInstrucao">Grau de Instrução:</label>
                                    <div class="col-sm-4">
                                        <select class="selectpicker" name="gInstrucao" id="gInstrucao" data-live-search="true" title="Selecione um Grau de Instrução." required data-width="fit" data-size="5" >
                                            <option data-tokens="Analfabeto" value="Analfabeto">Analfabeto</option>
                                            <option data-tokens="Ensino Fundamental Incompleto" value="Ensino Fundamental Incompleto">Ensino Fundamental Incompleto</option>
                                            <option data-tokens="Ensino Fundamental Completo" value="Ensino Fundamental Completo">Ensino Fundamental Completo</option>
                                            <option data-tokens="Ensino Médio Incompleto" value="Ensino Médio Incompleto">Ensino Médio Incompleto</option>
                                            <option data-tokens="Ensino Médio Completo" value="Ensino Médio Completo">Ensino Médio Completo</option>
                                            <option data-tokens="Ensino Superior Incompleto" value="Ensino Superior Incompleto">Ensino Superior Incompleto</option>
                                            <option data-tokens="Ensino Superior Completo" value="Ensino Superior Completo">Ensino Superior Completo</option>
                                            <option data-tokens="Pós Graduação/Especialização" value="Pós Graduação/Especialização">Pós Graduação/Especialização</option>
                                            <option data-tokens="Mestrado" value="Mestrado">Mestrado</option>
                                            <option data-tokens="Doutorado" value="Doutorado">Doutorado</option>
                                            <option data-tokens="Pós-Doutorado" value="Pós-Doutorado">Pós-Doutorado</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="eCivil">Estado Civil:</label>
                                    <div class="col-sm-2">
                                        <select class="selectpicker" name="eCivil" id="eCivil" data-live-search="true" title="Selecione um Estado Civil." required data-width="fit" data-size="5" >
                                            <option data-tokens="Solteiro(a)" value="Solteiro(a)">Solteiro(a)</option>
                                            <option data-tokens="Casado(a)" value="Casado(a)">Casado(a)</option>
                                            <option data-tokens="Separado(a)" value="Separado(a)">Separado(a)</option>
                                            <option data-tokens="Divorciado(a)" value="Divorciado(a)">Divorciado(a)</option>
                                            <option data-tokens="Viúvo(a)" value="Viúvo(a)">Viúvo(a)</option>
                                        </select>
                                    </div>
                                    <label class="col-sm-3 control-label" for="conjugue">Cônjugue <small>(Se casado)</small>:</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="conjugue" id="conjugue" class="form-control" maxlength="35">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="mConjugue">Data de Nascimento <small>(Cônjugue)</small>:</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="dConjugue" id="dConjugue" class="form-control">
                                    </div>
                                    <label class="col-sm-3 control-label" for="cCasamento">Certidão de Casamento:</label>
                                    <div class="col-sm-4">
                                        <input type="text" placeholder="Somente Números." name="cCasamento" id="cCasamento" class="form-control" maxlength="32" onkeypress="return SomenteNumero(event)">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="mConjugue">Nome da Mãe <small>(Cônjugue)</small>:</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="mConjugue" id="mConjugue" class="form-control" maxlength="35">
                                    </div>
                                    <label class="col-sm-2 control-label" for="cpfConjugue">CPF <small>(Cônjugue)</small>:</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="cpfConjugue" id="cpfConjugue" class="form-control" placeholder="Somente Números." maxlength="14">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="nMae">Nome da Mãe <small>(Colaborador)</small>:</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="nMae" id="nMae" class="form-control" maxlength="40" required>
                                    </div>
                                    <label class="col-sm-2 control-label" for="nPai">Nome do Pai <small>(Colaborador)</small>:</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="nPai" id="nPai" class="form-control" maxlength="40" required>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div id="Contratacao" class="tab-pane fade">
                            <div class="col-xs-12">
                                <div class="col-xs-offset-3 col-xs-9"><h4><u>Dados da Contratante</u></h4></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="empresa">Empresa:</label>  
                                    <div class="col-sm-5">
                                        <select class="selectpicker" title="Selecione uma Empresa." name="empresa" id="empresa" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma Empresa." required>
                                            <?php
                                            $conexao = conexao::getInstance();
                                            $sql = " SELECT emp.idEmpresa, cad.Nome, cad.CNPJ FROM empresa emp INNER JOIN cadastro cad ON cad.idCadastro = emp.Cadastro_idCadastro;";
                                            $stm = $conexao->prepare($sql);
                                            $stm->execute();
                                            if($stm->rowCount() == 0):
                                            ?>
                                            <script>
                                                alert("Não consta nenhuma EMPRESA cadastrada, por favor cadastre uma!");
                                                location.href = 'Empresa.php';
                                            </script>
                                            <?php
                                            else:
                                            while($row= $stm->fetch(PDO::FETCH_OBJ)):

                                            ?>
                                            <option data-tokens="<?php echo utf8_decode($row->Nome)." ".$row->CNPJ ?>" value="<?php echo $row->idEmpresa ?>"><?php echo utf8_decode(strtoupper($row->Nome))." - "; echo  CNPJ_Padrao($row->CNPJ); ?></option>
                                            <?php
                                            endwhile;
                                            endif;
                                            ?>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="col-sm-5"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="unidade">Unidade:</label>  
                                    <div class="col-sm-5">
                                        <select class=" form-control" name="unidade" id="unidade" title="Selecione uma UNIDADE." required>

                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="col-sm-5"></div>
                                </div>

                                <div class="col-xs-offset-3 col-xs-9"><h4><u>Dados da Contratação</u></h4></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="CodColaborador">Código Colaborador:</label>
                                    <div class="col-sm-3">
                                        <div class="input-group" alt="Código do Colaborador" title="Código do Colaborador">
                                            <span class="input-group-addon"><i class="fa fa-info-circle"></i></span>
                                            <input id="CodColaborador" placeholder="Carrega automaticamente!" type="text" class="form-control" name="CodColaborador" readonly value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="cargo">Cargo:</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" title="Selecione um Cargo." name="cargo" id="cargo" required data-live-search="true">

                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-sm-4 control-label" for="Salario">Salário:</label>
                                        <div class="col-sm-8">
                                            <div class="input-group" alt="Código do Colaborador" title="Código do Colaborador">
                                                <span class="input-group-addon">R$ </span>
                                                <input type="text" class="form-control" value="" readonly id="Salario" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="col-sm-4 control-label" for="Funcao">Função:</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" value="" readonly id="Funcao" >
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="dAdmissao">Data de Admissão:</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="dAdmissao" id="dAdmissao" class="form-control" required>
                                    </div>
                                    <label class="col-sm-2 control-label" for="dAso">Data ASO:</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="dAso" id="dAso" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="ctps">CTPS:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="ctps" id="ctps" class="form-control" required onkeypress="return SomenteNumero(event)">
                                    </div>
                                    <label class="col-sm-2 control-label" for="sCtps">Série:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="sCtps" id="sCtps" class="form-control" required onkeypress="return SomenteNumero(event)">
                                    </div>
                                    <label class="col-sm-2 control-label" for="uCtps">UF CTPS:</label>  
                                    <div class="col-sm-2">
                                        <select class="selectpicker" title="Selecione uma UF" name="uCtps" id="uCtps" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione uma UF." required>
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
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="dCtps">Data de Expedição CTPS:</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="dCtps" id="dCtps" class="form-control" required>
                                    </div>
                                    <label class="col-sm-2 control-label" for="pis">Nº PIS:</label>
                                    <div class="col-sm-5">
                                        <input type="text" name="pis" id="pis" class="form-control" required onkeypress="SomenteNumero(event)" onblur="ValidaPis()">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="dIntegracao">Data de Integração:</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="dIntegracao" id="dIntegracao" class="form-control" required>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div id="Depedentes" class="tab-pane fade">
                            <div class="col-xs-12">
                                <div class="col-xs-offset-3 col-xs-9"><h4><u>Depedentes</u></h4></div>

                                <script type="text/javascript">
                                    //Escrevendo o código-fonte HTML e ocultando os campos criados:
                                    for (iLoop = 1; iLoop <= totalCampos; iLoop++) {
                                        document.write("<div id='linha"+iLoop+"' style='display:none'><div class='form-group'><label class='control-label col-sm-2' for='dNome"+iLoop+"'>Depedente:</label><div class='col-sm-5'><input type='text' class='form-control' name='dNome"+iLoop+"' id='dNome["+iLoop+"]' /></div><label class='control-label col-sm-2' for='Parentesco"+iLoop+"'>Parentesco:</label><div class='col-sm-3'><select class='selectpicker' title='Selecione o grau de Parentesco.' name='Parentesco"+iLoop+"' id='Parentesco"+iLoop+"' data-live-search='true' data-width='fit' data-size='5' data-error='Selecione o grau de Parentesco.'><option data-tokens='Mãe' value='Mãe'>Mãe</option><option data-tokens='Pai' value='Pai'>Pai</option><option data-tokens='Filho(a)' value='Filho(a)'>Filho(a)</option><option data-tokens='Padastro/Madastra' value='Padastro/Madastra'>Padastro/Madastra</option><option data-tokens='Irmã(o)' value='Irmã(o)'>Irmã(o)</option><option data-tokens='Outros' value=Outros'>Outros</option></select></div></div><div class='form-group'><label class='control-label col-sm-2' for='fRegistro"+iLoop+"'>Termo/Registro:</label><div class='col-sm-2'><input type='text' class='form-control' name='fRegistro"+iLoop+"' id='fRegistro["+iLoop+"]' /></div><label class='control-label col-sm-2' for='fLivro"+iLoop+"'>Livro:</label><div class='col-sm-2'><input type='text' class='form-control' name='fLivro"+iLoop+"' id='fLivro"+iLoop+"' /></div><label class='control-label col-sm-2' for='fFolha"+iLoop+"'>Folha:</label><div class='col-sm-2'><input type='text' class='form-control' name='fFolha"+iLoop+"' id='fFolha"+iLoop+"' /></div></div><div class='form-group'><label class='control-label col-sm-2' for='dnv"+iLoop+"'>DNV:</label><div class='col-sm-4'><input type='text' class='form-control' name='dnv"+iLoop+"' id='dnv["+iLoop+"]' /></div><label class='control-label col-sm-2' for='dFilho"+iLoop+"'>Data de Nascimento:</label><div class='col-sm-3'><input type='date' class='form-control' name='dFilho"+iLoop+"' id='dFilho"+iLoop+"' /></div></div><div class='form-group'><label class='control-label col-sm-2' for='fMae"+iLoop+"'>Nome da Mãe:</label><div class='col-sm-4'><input type='text' class='form-control' name='fMae"+iLoop+"' id='fMae["+iLoop+"]' /></div><label class='control-label col-sm-2' for='dCpf"+iLoop+"'>CPF:</label><div class='col-sm-4'><input type='text' class='form-control' name='dCpf"+iLoop+"' id='dCpf"+iLoop+"' /></div><div class='col-sm-offset-11 col-sm-1'><button type='button' class='btn btn-danger btn-remover' title='Remover Campos' onclick='RemoverCampos(\""+iLoop+"\")'>-</button></div></div></div>");
                                    }
                                </script>
                                <div class="col-xs-offset-2 col-xs-10">
                                    <button type="button" title="Adicionar Campos" class="btn btn-default" onclick="AddCampos()">+</button>
                                    <input type="hidden" name="hidden1" id="hidden1">
                                    <input type="hidden" name="hidden2" id="hidden2">
                                </div>
                            </div>
                        </div>
                        <div id="Bancarios" class="tab-pane fade">
                            <div class="col-xs-12"> 
                                <div class="col-xs-offset-3 col-xs-9"><h4><u>Conta Salário</u></h4></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="sBanco">Banco:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="sBanco" id="sBanco" class="form-control" maxlength="15" required>
                                    </div>
                                    <label class="col-sm-2 control-label" for="sConta">Conta:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="sConta" id="sConta" class="form-control" maxlength="15" required>
                                    </div>
                                    <label class="col-sm-2 control-label" for="sAgencia">Agência:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="sAgencia" id="sAgencia" class="form-control" maxlength="15" required>
                                    </div>
                                    <div class="col-sm-8"></div>
                                </div>
                                <div class="col-xs-offset-3 col-xs-9"><h4><u>Conta Pessoal</u></h4></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="banco">Banco:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="banco" id="banco" class="form-control" maxlength="15">
                                    </div>
                                    <label class="col-sm-2 control-label" for="conta">Conta:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="conta" id="conta" class="form-control" maxlength="15">
                                    </div>
                                    <label class="col-sm-2 control-label" for="agencia">Agência:</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="agencia" id="agencia" class="form-control" maxlength="15">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="tipo">Tipo:</label>
                                    <div class="col-sm-4">
                                        <select class="selectpicker" title="Selecione o Tipo da Conta." name="tipo" id="tipo" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione o Tipo da Conta.">
                                            <option data-tokens="Conta Corrente" value="Conta Corrente">Conta Corrente</option>
                                            <option data-tokens="Conta Poupança" value="Conta Poupança">Conta Poupança</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="Endereco" class="tab-pane fade">
                            <div class="col-xs-12">
                                <div class="col-xs-offset-3 col-xs-9"><h4><u>Dados de Endereço</u></h4></div>
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
                                    <label class="col-sm-1 control-label" for="bairro">Bairro:</label>  
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
                            </div>
                        </div>
                        <div id="Contato" class="tab-pane fade">
                            <div class="col-xs-12">
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
                                    <label for="email" class="col-sm-2 control-label">E-mail:</label>
                                    <div class="col-sm-5">
                                        <input id="email" name="email" class="form-control" type="email" data-error="Por favor, informe um e-mail correto.">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="col-sm-5"></div>
                                </div>
                            </div>
                        </div>

                        <div id="Chefia" class="tab-pane fade">
                            <div class="col-xs-12">
                                <div class="col-xs-offset-3 col-xs-9"><h4>Gestor</h4></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="chefia">Selecione o Gestor Imediato:</label>  
                                    <div class="col-sm-5">
                                        <select class="selectpicker" title="Selecione o Gestor Imediato." name="chefia" id="chefia" data-live-search="true" data-width="fit" data-size="5" data-error="Selecione o Gestor Imediato." required>
                                            <?php
                                            $conexao = conexao::getInstance();
                                            $sql = "SELECT idusuarios, Nome, Email FROM usuarios WHERE chefia = 'S' ORDER BY Nome ASC;";
                                            $stm = $conexao->prepare($sql);
                                            $stm->execute();
                                            while($row= $stm->fetch(PDO::FETCH_OBJ)):

                                            ?>
                                            <option data-tokens="<?php echo utf8_decode($row->Nome)." ".$row->Email ?>" value="<?php echo $row->idusuarios ?>"><?php echo utf8_decode($row->Nome); ?></option>
                                            <?php
                                            endwhile;
                                            ?>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-8 col-sm-3">
                                <input type="hidden" value="Cadastro" name="Colaborador">
                                <button type="submit" id="submit" class="btn btn-success">Salvar</button>
                                <button type="reset" class="btn btn-danger">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.cpf').cpfcnpj({
            mask: false,
            validate: 'cpf',
            event: 'focusout',
            ifValid: function (input) { input.removeClass("error");},
            ifInvalid: function (input) { input.addClass("error");}
        });
    });
</script>
<?php
  endif;
require_once("../control/arquivo/footer/Footer.php");
?>
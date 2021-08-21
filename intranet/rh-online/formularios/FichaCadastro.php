<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
	session_destroy();
	header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
	require_once("../control/Pacote.php");
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
                document.getElementById(linhasOcultas[0]).style.display = "block"; iCount++;
                if (hidden1.value == "") {
                    hidden1.value = linhasOcultas[0];
                }else{
                    hidden1.value += ","+linhasOcultas[0];
                }
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
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<div class="text-center"><h1>FICHA DE CADASTRO</h1></div>
			<form action="FichaCadastroEnviar.php" method="post" enctype="multipart/form-data" rel="form" class="form-horizontal text-center" data-toggle="validator" >
			    <div class="col-md-12 col-xs-12 col-lg-12">
			    	<hr/>
			    	<p><small>Dados Pessoais</small></p>
			    	<hr/>
				</div>
				<div class="col-md-12 col-xs-12 col-lg-12">
				    <div class="form-group">
				    	<label for="Nome" class="control-label col-sm-2">Nome:</label>
				    	<div class="col-sm-8">
				    		<input type="text" name="Nome" id="Nome" class="form-control" required="required">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="Naturalidade" class="control-label col-sm-4">Naturalidade:</label>
				    	<div class="col-sm-8">
				    		<input type="text" name="Naturalidade" id="Naturalidade" class="form-control" required="required">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="dNascimento" class="control-label col-sm-4">Data de Nascimento:</label>
				    	<div class="col-sm-4">
				    		<input type="date" name="dNascimento" id="dNascimento" class="form-control" required="required">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="Sexo" class="control-label col-sm-4">Sexo:</label>
				    	<div class="col-sm-8">
				    		<label class="radio-inline"><input type="radio" name="Sexo" id="Sexo" value="F" required="required">Feminino</label>
				    		<label class="radio-inline"><input type="radio" name="Sexo" id="Sexo" value="M" required="required">Masculino</label>
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="Cor" class="control-label col-sm-2">Cor:</label>
				    	<div class="col-sm-6">
				    		<select class="selectpicker form-control" name="Cor" id="Cor" data-live-search="true" title="Selecione uma Cor." required="required">
	                            <option data-tokens="Branco" value="Branco">Branco</option>
	                            <option data-tokens="Amarelo" value="Amarelo">Amarelo</option>
	                            <option data-tokens="Pardo" value="Pardo">Pardo</option>
	                            <option data-tokens="Preto" value="Preto">Preto</option>
	                            <option data-tokens="Não Informado" value="Não Informado">Não Informado</option>
	                        </select>
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="CPF" class="control-label col-sm-6">CPF:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="CPF" id="CPF" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-2 col-xs-2 col-lg-2">
				    <div class="form-group">
				    	<label for="RG" class="control-label col-sm-3">RG:</label>
				    	<div class="col-sm-9">
				    		<input type="text" name="RG" id="RG" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="dRG" class="control-label col-sm-4">Data de Emissão RG:</label>
				    	<div class="col-sm-4">
				    		<input type="date" name="dRG" id="dRG" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Titulo" class="control-label col-sm-6">Título de Eleitor:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="Titulo" id="Titulo" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-3 col-xs-3 col-lg-3">
				    <div class="form-group">
				    	<label for="Secao" class="control-label col-sm-3">Seção:</label>
				    	<div class="col-sm-9">
				    		<input type="text" name="Secao" id="Secao" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Zona" class="control-label col-sm-2">Zona:</label>
				    	<div class="col-sm-7">
				    		<input type="text" name="Zona" id="Zona" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="Habilitacao" class="control-label col-sm-4">Habilitação:</label>
				    	<div class="col-sm-8">
				    		<input type="text" name="Habilitacao" id="Habilitacao" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="Categoria" class="control-label col-sm-2">Categoria:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="Categoria" id="Categoria" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="gInstrucao" class="control-label col-sm-4">Grau de Instrução:</label>
                        <div class="col-sm-8">
                            <select class="selectpicker form-control" name="gInstrucao" id="gInstrucao" data-live-search="true" title="Selecione um Grau de Instrução." required="required" data-size="5" >
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
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="eCivil" class="control-label col-sm-3">Estado Civil:</label>
				    	<div class="col-sm-5">
				    		<select class="selectpicker form-control" name="eCivil" id="eCivil" data-live-search="true" title="Selecione um Estado Civil." required="required" data-size="5" >
	                            <option data-tokens="Solteiro(a)" value="Solteiro(a)">Solteiro(a)</option>
	                            <option data-tokens="Casado(a)" value="Casado(a)">Casado(a)</option>
	                            <option data-tokens="Separado(a)" value="Separado(a)">Separado(a)</option>
	                            <option data-tokens="Divorciado(a)" value="Divorciado(a)">Divorciado(a)</option>
	                            <option data-tokens="Viúvo(a)" value="Viúvo(a)">Viúvo(a)</option>
	                        </select>
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="Conjuge" class="control-label col-sm-4">Cônjuge:</label>
				    	<div class="col-sm-8">
				    		<input type="text" name="Conjuge" id="Conjuge" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="cCPF" class="control-label col-sm-3">CPF: <small>(Cônjuge)</small></label>
				    	<div class="col-sm-5">
				    		<input type="text" name="cCPF" id="cCPF" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="cMae" class="control-label col-sm-4">Mãe: <small>(Cônjuge)</small></label>
				    	<div class="col-sm-8">
				    		<input type="text" name="cMae" id="cMae" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="cNascimento" class="control-label col-sm-4">Nascimento: <small>(Cônjuge)</small></label>
				    	<div class="col-sm-4">
				    		<input type="date" name="cNascimento" id="cNascimento" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-12 col-xs-12 col-lg-12">
				    <div class="form-group">
				    	<label for="cCasamento" class="control-label col-sm-2">Certidão de Casamento:</label>
				    	<div class="col-sm-4">
				    		<input type="date" name="cCasamento" id="cCasamento" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-12 col-xs-12 col-lg-12">
			    	<hr/>
			    	<p><small>Dados de Endereço</small></p>
			    	<hr/>
			    </div>
			    <div class="col-md-8 col-xs-8 col-lg-8">
				    <div class="form-group">
				    	<label for="Endereco" class="control-label col-sm-3">Endereço:</label>
				    	<div class="col-sm-8">
				    		<input type="text" name="Endereco" id="Endereco" class="form-control" required="required">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Numero" class="control-label col-sm-2">Número:</label>
				    	<div class="col-sm-4">
				    		<input type="text" name="Numero" id="Numero" class="form-control" required="required">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Cidade" class="control-label col-sm-6">Cidade:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="Cidade" id="Cidade" class="form-control" required="required">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Bairro" class="control-label col-sm-3">Bairro:</label>
				    	<div class="col-sm-9">
				    		<input type="text" name="Bairro" id="Bairro" class="form-control" required="">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="UF" class="control-label col-sm-2">UF:</label>
				    	<div class="col-sm-4">
				    		<select class="selectpicker form-control" title="UF" name="UF" id="UF" data-live-search="true" data-size="5" data-error="Selecione a UF." required="required">
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
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="CEP" class="control-label col-sm-6">CEP:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="CEP" id="CEP" class="form-control" required="required">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-12 col-xs-12 col-lg-12">
			    	<hr/>
			    	<p><small>Dados da Contratação</small></p>
			    	<hr/>
			    </div>
				<div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group conteudo">
				    	<label for="Unidade" class="control-label col-sm-4">Unidade:</label>
				    	<div class="col-sm-8">
				    		<input type="text" name="Unidade" id="Unidade" required="required" class="form-control">
				    	</div>
				    </div>
				</div>
				<div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group conteudo">
				    	<label for="Funcao" class="control-label col-sm-2">Função:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="Funcao" id="Funcao" required="required" class="form-control">
				    	</div>
				    </div>
				</div>
				<div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="CTPS" class="control-label col-sm-6">CTPS:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="CTPS" id="CTPS" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-3 col-xs-3 col-lg-3">
				    <div class="form-group">
				    	<label for="Serie" class="control-label col-sm-3">Série:</label>
				    	<div class="col-sm-9">
				    		<input type="text" name="Serie" id="Serie" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="uCTPS" class="control-label col-sm-4">UF CTPS:</label>
				    	<div class="col-sm-5">
				    		<select class="selectpicker form-control" title="UF" name="uCTPS" id="uCTPS" data-live-search="true" data-size="5" data-error="Selecione a UF." required="required">
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
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="dCTPS" class="control-label col-sm-6">Data CTPS:</label>
				    	<div class="col-sm-6">
							<input type="date" name="dCTPS" id="dCTPS" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-3 col-xs-3 col-lg-3">
				    <div class="form-group">
				    	<label for="Aso" class="control-label col-sm-4">Data Aso:</label>
				    	<div class="col-sm-8">
				    		<input type="date" name="Aso" id="Aso" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Admissao" class="control-label col-sm-3">Admissão:</label>
				    	<div class="col-sm-6">
				    		<input type="date" name="Admissao" id="Admissao" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-6 col-xs-6 col-lg-6">
				    <div class="form-group">
				    	<label for="PIS" class="control-label col-sm-4">PIS:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="PIS" id="PIS" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-12 col-xs-12 col-lg-12">
			    	<hr/>
			    	<p><small>Dados Bancários</small></p>
			    	<hr/>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Banco" class="control-label col-sm-6">Banco:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="Banco" id="Banco" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-3 col-xs-3 col-lg-3">
				    <div class="form-group">
				    	<label for="Agencia" class="control-label col-sm-3">Agência:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="Agencia" id="Agencia" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Conta" class="control-label col-sm-2">Conta:</label>
				    	<div class="col-sm-7">
				    		<input type="text" name="Conta" id="Conta" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="TipoConta" class="control-label col-sm-6">Tipo Conta:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="TipoConta" id="TipoConta" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-12 col-xs-12 col-lg-12">
			    	<hr/>
			    	<p><small>Dados de EPI</small></p>
			    	<hr/>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Calca" class="control-label col-sm-6">Calça:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="Calca" id="Calca" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-3 col-xs-3 col-lg-3">
				    <div class="form-group">
				    	<label for="Camiseta" class="control-label col-sm-3">Camiseta:</label>
				    	<div class="col-sm-9">
				    		<input type="text" name="Camiseta" id="Camiseta" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-4 col-xs-4 col-lg-4">
				    <div class="form-group">
				    	<label for="Bota" class="control-label col-sm-3">Botina/Bota:</label>
				    	<div class="col-sm-6">
				    		<input type="text" name="Bota" id="Bota" required="required" class="form-control">
				    	</div>
				    </div>
			    </div>
			    <div class="col-md-12 col-xs-12 col-lg-12">
			    	<hr/>
			    	<p><small>Dados de Filhos</small></p>
			    	<hr/>
			    </div>
			    <div class="col-md-12 col-xs-12 col-lg-12">
                  	<script type="text/javascript">
                        //Escrevendo o código-fonte HTML e ocultando os campos criados:
                        for (iLoop = 1; iLoop <= totalCampos; iLoop++) {
                            document.write("<div id='linha"+iLoop+"' style='display:none'><div class='form-group'><label class='control-label col-sm-2' for='dNome"+iLoop+"'>Depedente:</label><div class='col-sm-5'><input type='text' class='form-control' name='dNome"+iLoop+"' id='dNome["+iLoop+"]' /></div><label class='control-label col-sm-2' for='Parentesco"+iLoop+"'>Parentesco:</label><div class='col-sm-3'><select class='selectpicker' title='Selecione o grau de Parentesco.' name='Parentesco"+iLoop+"' id='Parentesco"+iLoop+"' data-live-search='true' data-width='fit' data-size='5' data-error='Selecione o grau de Parentesco.'><option data-tokens='Mãe' value='Mãe'>Mãe</option><option data-tokens='Pai' value='Pai'>Pai</option><option data-tokens='Filho(a)' value='Filho(a)'>Filho(a)</option><option data-tokens='Padastro/Madastra' value='Padastro/Madastra'>Padastro/Madastra</option><option data-tokens='Irmã(o)' value='Irmã(o)'>Irmã(o)</option><option data-tokens='Outros' value=Outros'>Outros</option></select></div></div><div class='form-group'><label class='control-label col-sm-2' for='fRegistro"+iLoop+"'>Termo/Registro:</label><div class='col-sm-2'><input type='text' class='form-control' name='fRegistro"+iLoop+"' id='fRegistro["+iLoop+"]' /></div><label class='control-label col-sm-2' for='fLivro"+iLoop+"'>Livro:</label><div class='col-sm-2'><input type='text' class='form-control' name='fLivro"+iLoop+"' id='fLivro"+iLoop+"' /></div><label class='control-label col-sm-2' for='fFolha"+iLoop+"'>Folha:</label><div class='col-sm-2'><input type='text' class='form-control' name='fFolha"+iLoop+"' id='fFolha"+iLoop+"' /></div></div><div class='form-group'><label class='control-label col-sm-2' for='dnv"+iLoop+"'>DNV:</label><div class='col-sm-4'><input type='text' class='form-control' name='dnv"+iLoop+"' id='dnv["+iLoop+"]' /></div><label class='control-label col-sm-2' for='dFilho"+iLoop+"'>Data de Nascimento:</label><div class='col-sm-3'><input type='date' class='form-control' name='dFilho"+iLoop+"' id='dFilho"+iLoop+"' /></div></div><div class='form-group'><label class='control-label col-sm-2' for='fMae"+iLoop+"'>Nome da Mãe:</label><div class='col-sm-4'><input type='text' class='form-control' name='fMae"+iLoop+"' id='fMae["+iLoop+"]' /></div><label class='control-label col-sm-2' for='dCpf"+iLoop+"'>CPF:</label><div class='col-sm-4'><input type='text' class='form-control' name='dCpf"+iLoop+"' id='dCpf"+iLoop+"' /></div><div class='col-sm-offset-11 col-sm-1'><button type='button' class='btn btn-danger btn-remover' title='Remover Campos' onclick='RemoverCampos(\""+iLoop+"\")'>-</button></div></div></div>");
                        }
                    </script>
                    <div class="col-xs-offset-2 col-xs-10 text-left">
                        <button type="button" title="Adicionar Campos" class="btn btn-success" onclick="AddCampos()">+</button>
                        <input type="hidden" name="hidden1" id="hidden1">
                        <input type="hidden" name="hidden2" id="hidden2">
                    </div>
                </div>
                <div class="col-md-12 col-xs-12 col-lg-12">
		    		<hr/>
		    	</div>
				<div class="col-xs-offset-1 col-xs-4"><button type="submit" class="btn btn-success envio">Enviar Ficha</button></div>
			</form>
		</div>
	</div>
</div>
<?php
	require_once("../control/arquivo/footer/Footer.php");
}
?>
<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])){
    session_destroy();
    header("Location: http://www.nutribemrefeicoescoletivas.com.br/intranet/");
}else{
    require_once("../control/Pacote.php");
    $conexao = conexao::getInstance();
    
    
    $option = '';
    $sql = "SELECT * FROM categoria";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    while($r = $stmt->fetch(PDO::FETCH_OBJ)){
        $option .= "<option data-tokens='".utf8_decode($r->Nome)." ".$r->idCategoria."' value='".$r->idCategoria."' >".str_pad($r->idCategoria, 3, 0, STR_PAD_LEFT)." - ".utf8_decode($r->Nome)."</option>";
    }
?>
<script type="text/javascript">
	//Total máximo de campos que você permitirá criar em seu site:
	var totalCampos = 20;

	//Não altere os valores abaixo, pois são variáveis controle;
	var iLoop = 2;
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
			for (iLoop = 2; iLoop <= totalCampos; iLoop++) {
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
		var campoValor = document.getElementById("Arq"+id+"").value;
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
            document.getElementById("Arq"+id).value = "";
            document.getElementById("categoria"+id).selectedIndex = 0;
		}
	}
</script>


<!-- Content -->
<div class="container-fluid">
    <div class="row conteudo">
        <div class="col-md-12 col-xs-12 col-lg-12 text-center">
            <h1 class="text-center"><u>UPLOAD DE ARQUIVOS</u></h1>
            <form name="Form" role="form" action="<?=BASE;?>control/banco/ArquivosDAO.php" method="post" enctype="multipart/form-data" class="form-horizontal" data-toggle="validator">            
                <div class="col-md-12 col-xs-12 col-lg-12"> </div>
                <div class="col-md-12 col-xs-12 col-lg-12">
                    <div class="col-xs-12 col-md-12 col-lg-12"> </div>
                    <div class="row">
                        <div class='col-xs-12 col-md-12 col-lg-12'>
                            <div class='col-xs-6 col-sm-6 col-md-6'>
                                <div class='form-group'>
                                    <label class='control-label col-sm-4' for='Arq1'>Arquivo:</label>
                                    <div class='col-sm-8'>
                                        <input type='file' class='form-control' id='Arq1' name='Arq1' required/>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class='col-xs-4 col-sm-4 col-md-4'>
                                <div class='form-group'>
                                    <label class='control-label col-sm-3' for='categoria1'>Categoria:</label>
                                    <div class='col-sm-8'>
                                        <select class="selectpicker form-control" title="Selecione uma Categoria!" name="categoria1" id="categoria1" data-live-search="true" data-width="100%" data-size="5" data-actions-box="true" required>
                                            <?=$option;?>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            //Escrevendo o código-fonte HTML e ocultando os campos criados:
                            for (iLoop = 2; iLoop <= totalCampos; iLoop++) {
                                document.write("<div class='col-xs-12 col-md-12 col-lg-12' id='linha"+iLoop+"' style='display:none'><div class='col-xs-6 col-sm-6 col-md-6'><div class='form-group'><label class='control-label col-sm-4' for='Arq"+iLoop+"'>Arquivo:</label><div class='col-sm-8'><input type='file' name='Arq"+iLoop+"' id='Arq"+iLoop+"' class='form-control'></div></div></div><div class='col-xs-4 col-sm-4 col-md-4'><div class='form-group'><label class='control-label col-sm-3' for='categoria"+iLoop+"'>Categoria:</label><div class='col-sm-8'><select class='selectpicker form-control' title='Selecione uma Categoria!' name='categoria"+iLoop+"' id='categoria"+iLoop+"' data-live-search='true' data-width='100%' data-size='5' data-actions-box='true' ><?=$option;?></select></div></div></div><div class='col-xs-2 col-sm-2 col-md-2'><button type='button' class='btn btn-danger' title='Remover Campos' onclick='RemoverCampos(\""+iLoop+"\")'>-</button></div></div>");
                            }
                        </script>
                        <div class="col-lg-12 col-xs-12 col-md-12">
                            <div class="col-xs-offset-2 col-lg-2 col-lg-offset-2 col-xs-2 col-md-offset-2 col-md-2">
                                <button type="button" title="Adicionar Campos" class="btn btn-default" onclick="AddCampos()">+</button>
                                <input type="hidden" name="hidden1" id="hidden1">
                                <input type="hidden" name="hidden2" id="hidden2">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 col-xs-12 col-lg-12">
                    <button class="btn btn-success" type="submit">Salvar</button>
                    <input type="hidden" name="Arquivos" id="Arquivos" value="Cadastrar" />
                </div>
            </form>
        </div>
	</div>
</div>
<?php
}
?>
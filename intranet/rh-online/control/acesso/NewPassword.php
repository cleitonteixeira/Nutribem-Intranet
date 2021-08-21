<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
session_destroy();
require_once("../arquivo/funcao/Outras.php");
require_once("../banco/conexao.php");
require_once("../arquivo/header/Header.php");
require_once("../arquivo/Login.php");
else:
require_once("../Pacote.php");
?>
<script>
	// Check javascript has loaded
	$(document).ready(function(){

		$("#pSenha").change(function(e){
			var cod = $('#pSenha').val();//pegando o value do option selecionado
			var delay=1000;
			//alert(cod);//apenas para debugar a variável
			$('#retorno').html("Validando...").show();
			$.post('CheckPassword.inc.php', {p: cod}, function (dados){
				//alert(dados);
				data = JSON.parse(dados);
				if (data.length == 1){ 	
					var Retorno = '';
					$.each(data, function(i, obj){
						Retorno = obj.Retorno;
					})

					if(Retorno == 1){
						setTimeout(function(){ 
							$('#retorno').html("Válida!").show();
						}, 1000);
						setTimeout(function(){ 
							$('#nSenha').attr('required', 'required');
							$('#nSenha').removeAttr('disabled', 'disabled');
							$('#cSenha').attr('required', 'required');
							$('#cSenha').removeAttr('disabled', 'disabled');
							$('#sSenha').css({
								display: 'inline'
							});
						}, 2000);
					}else{
						setTimeout(function(){ 
							$('#retorno').html("Inválida!").show();
						}, 1000);
						$("#sSenha").css({
							display: 'none'
						});
						$('#nSenha').removeAttr('required', 'required');
						$('#nSenha').attr('disabled', 'disabled');
						$('#cSenha').removeAttr('required', 'required');
						$('#cSenha').attr('disabled', 'disabled');
					}

				}else{
					Reset();
				}
			})
		})
		<!-- Resetar Selects -->
		function Reset(){
			$('#retorno').html("").show();
		}
		// Click event of the showPassword button
		$('#showPassword').on('click', function(){

			// Get the password field
			var passwordField = $('#nSenha');

			// Get the current type of the password field will be password or text
			var passwordFieldType = passwordField.attr('type');

			// Check to see if the type is a password field
			if(passwordFieldType == 'password')
			{
				// Change the password field to text
				passwordField.attr('type', 'text');

				// Change the Text on the show password button to Hide
				$(this).removeClass('fa fa-eye');
				$(this).addClass('fa fa-eye-slash');
			} else {
				// If the password field type is not a password field then set it to password
				passwordField.attr('type', 'password');

				// Change the value of the show password button to Show
				$(this).removeClass('fa fa-eye-slash');
				$(this).addClass('fa fa-eye');
			}
		})
	});
</script>
<div class="container-fluid">
	<div class="row">
		<div class="conteudo">
			<div class="col-lg-12 col-xs-12 col-md-12 text-center"><h2>Atualização de Senha</h2></div>
			<div class="col-lg-12 col-xs-12 col-md-12 text-justify">
				<form action="<?php echo BASE;?>control/banco/SenhaDAO.php" method="post" enctype="multipart/form-data" autocomplete="off" data-toggle="validator" rel="form" class="form-horizontal">
					<div class="form-group">
						<label class="control-label col-sm-3" for="pSenha">Senha Antiga:</label>
						<div class="col-sm-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
								<input required type="password" name="pSenha" id="pSenha" class="form-control" />
							</div>
						</div>
						<div class="col-sm-3"></div>
					</div>
					<div class="form-group">
						<div class="col-xs-offset-3 col-xs-4"><span id="retorno"></span></div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3" for="nSenha">Nova Senha:</label>
						<div class="col-sm-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
								<input placeholder="Nova Senha" type="password" name="nSenha" id="nSenha" class="form-control" data-minlength="6" disabled />
								<span class="input-group-addon"><i title="Mostar Senha" id="showPassword" class="fa fa-eye" aria-hidden="true"></i></span>
							</div>
							<div class="help-block">Mínimo de 6 caracteres</div>
						</div>
						<div class="col-sm-3"></div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3" for="cSenha">Repita a Senha:</label>
						<div class="col-sm-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock" aria-hidden="true"></i></span>
								<input placeholder="Confirmar" type="password" id="cSenha" class="form-control" data-match="#nSenha" data-match-error="As senhas não estão iguais." disabled/>
							</div>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-sm-3"></div>
					</div>
					<div class="col-xs-offset-7 col-xs-4">
						<input type="hidden" value="newSenha" name="Senha" />
						<button class="btn btn-success" type="submit" id="sSenha" style="display: none;">Alterar</button>
						<button class="btn btn-warning" type="reset">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
endif;
require_once("../arquivo/footer/Footer.php");
?>
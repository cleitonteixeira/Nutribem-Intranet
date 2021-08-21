<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['idusuarios'])):
    session_destroy();
    require_once("../control/arquivo/funcao/Outras.php");
    header("Location: ".BASE);
else:
    require_once("../control/Pacote.php");
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
	id('telefone').onkeypress = function(){
	  mascara( this, mtel );
	}
  }
  $(document).on("input", "#msg", function() {
	var limite = 350;
	var informativo = "caracteres restantes.";
	var caracteresDigitados = $(this).val().length;
	var caracteresRestantes = limite - caracteresDigitados;

	if (caracteresRestantes <= 0) {
		var comentario = $("textarea[name=msg]").val();
		$("textarea[name=msg]").val(comentario.substr(0, limite));
		$("#contador").html("0 " + informativo).show();
	} else {
            $("#contador").html(caracteresRestantes + " " + informativo).show();
        }
    });
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12 col-lg-12 conteudo">
			<div class="col-md-12 col-xs-12 col-lg-12 text-center"><h1>Criticas/Sugestões</h1></div>
            <form class="form-horizontal " data-toggle="validator" rel="form" enctype="multipart/form-data">
				<div class="col-xs-2 col-md-2 col-lg-2"></div>
				<div class="col-xs-8 col-md-8 col-lg-8">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="e-mail">E-mail:</label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon">@</span>
								<input class="form-control" type="email" name="e-mail" id="e-mail" data-error="Digite um e-mail válido!" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$">
							</div>
							<div class="help-block with-errors"></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="nome">Nome:</label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-user" aria-hidden="true"></i></span>
								<input class="form-control" type="text" name="nome" id="nome">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="telefone">Telefone:</label>
						<div class="col-sm-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-phone" aria-hidden="true"></i></span>
								<input class="form-control" type="text" name="telefone" id="telefone" maxlength="15">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="msg">Mensagem:</label>
						<div class="col-sm-8">
							<textarea class="form-control" name="msg" rows="5" id="msg" maxlength="350" minlength="50" placeholder="Digite sua mensagem." required></textarea>
							<p><small><span id="contador" name="contador"></span></small></p>
							<div class="help-block with-errors"></div>
						</div>
					</div>
					<div class="col-xs-offset-2"><button class="btn btn-primary" type="submit">Enviar</button></div>
				</div>
				<div class="col-xs-2 col-md-2 col-lg-2"></div>
			</form>
        </div>
    </div>
</div>
<?php
endif;
?>
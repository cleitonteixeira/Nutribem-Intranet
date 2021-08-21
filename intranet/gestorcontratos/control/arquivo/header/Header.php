<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>NUTRIBEM - Gestor de Contratos</title>
    <meta name="description" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" href="<?php echo BASE; ?>img/Icone.png" type="image/x-icon" />
    <!-- Place favicon.ico in the root directory -->
    <script src="<?php echo BASE;?>js/jquery.js"></script>
    <script src="<?php echo BASE;?>js/bootstrap-datepicker.min.js"></script>
    <script src="<?php echo BASE;?>js/jquery.maskMoney.js"></script>
    <script src="<?php echo BASE;?>js/jquery.maskedinput.min.js"></script>
    <script src="<?php echo BASE;?>js/bootstrap-select.min.js"></script>
    <script src="<?php echo BASE;?>js/bootstrap.min.js"></script>
    <script src="<?php echo BASE;?>js/app.js"></script>
    <script src="<?php echo BASE;?>js/jquery.dataTables.min.js"></script>
    <script src="<?php echo BASE;?>js/validator.js"></script>
    <script src="<?php echo BASE;?>js/validator.min.js"></script>
    <script src="<?php echo BASE;?>js/jquery.cpfcnpj.min.js"></script>
    <script src="<?php echo BASE;?>js/jquery.complexify.js"></script>
    <script src="<?php echo BASE;?>js/bootstrap-filestyle.min.js"></script>
    <!-- Fim Arquivos JS -->
    <!-- Início Arquivos CSS -->
    <link rel="stylesheet" href="<?php echo BASE;?>css/bootstrap-datepicker.min.css"/>
    <link rel="stylesheet" href="<?php echo BASE;?>css/bootstrapValidator.css"/>
    <link rel="stylesheet" href="<?php echo BASE;?>css/bootstrapValidator.min.css"/>
    <link rel="stylesheet" href="<?php echo BASE;?>css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo BASE;?>css/bootstrap.min.css">
    <?php if(isset($_SESSION['idusuarios']) || isset($_SESSION['contrato'])):?>
        <link rel="stylesheet" href="<?php echo BASE;?>css/app.css">
        
      <?php else:?>
        <link rel="stylesheet" href="<?php echo BASE;?>css/Login.css">
      <?php endif;?>
      <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE;?>css/dataTables.bootstrap.min.css" />
    <link rel="stylesheet" href="<?php echo BASE;?>css/bootstrap-select.min.css">
  </head>
  <body>
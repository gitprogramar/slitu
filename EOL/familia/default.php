<?php
	if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }	
    $baseUrl = $protocol . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    ?>
<!DOCTYPE html>
<html xml:lang="es-ES" lang="es-ES" >
   <head>
      <title>Diploma- VIII Enapol</title>
      <meta name="keywords" content="VIII enapol, jornadas eol, imprimir, titulo, título, diploma">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <base href="<?= $baseUrl?>">
      <meta property="og:image" content="images/full-image.jpg">
      <meta http-equiv="content-type" content="text/html; charset=utf-8">
      <meta name="author" content="Programar">
      <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
      <link rel="stylesheet" href="css/bootstrap-theme.min.css" type="text/css" />
      <link rel="stylesheet" href="css/animate.css" type="text/css" />
      <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css" />
      <link rel="stylesheet" href="css/common.css?v=01" type="text/css" />	   
   </head>
   <body>
      <header>
         <div class="header-container">
            <div class="header-image img-responsive"></div>
         </div>
      </header>
      <main class="container">
         <h1>Ingrese sus datos para descargar el diploma</h1>
         <article class="main-content">
            <div class="container">
				<form id="form1" class="ajaxForm">
				   <div class="row form-group" style="">
					  <div class="col-md-6 col-md-offset-3">
							<label>NOMBRE</label>
							<input type="text" id="name" name="name" placeholder="Ingrese su nombre" class="form-control"/>
						</div>
				   </div>
				   <div class="row form-group" style="">
					  <div class="col-md-6 col-md-offset-3">
							<label>APELLIDO</label>
							<input type="text" id="lastname" name="lastname" placeholder="Ingrese su apellido" class="form-control"/>
							<input type="hidden" id="file" name="file" value="Certificados VIII ENAPOL">
							<input type="hidden" id="database" name="database" value="data">
							<input type="hidden" id="xyname" name="xyname" value="128,88">
							<input type="hidden" id="xyposition" name="xyposition" value="128,100">
						</div>
				   </div>
				   <div class="control control-group-button col-sm-12">
						<div class="control">
							<button type="button" class="btn btn-default ajaxFormBtn">DESCARGAR</button>
						</div>
					</div>
				</form>
            </div>
         </article>
         <div id="bottom-main"></div>
      </main>
      <footer>
         <div class="container" id="footer-content">
            <p>Escuela de la Orientación Lacaniana | eol@eol.org.ar | Tel: (54-11) 4773-5440 / 4774-9408 interno 1 EOL
               Ancón 5201 (C1425BZC) | Ciudad Autónoma de Buenos Aires - Argentina
            </p>
         </div>
      </footer>
      <div id="loading" style="display: none;"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
         <span class="sr-only">Cargando...</span>
      </div>
   </body>  
   <script src="js/jquery.min.js" type="text/javascript"></script>
   <script src="js/jquery.validate.min.js" type="text/javascript"></script>
   <script src="js/bootstrap.min.js" type="text/javascript"></script>
   <script src="js/bootstrap-notify.min.js" type="text/javascript"></script>
   <script src="js/common.js" type="text/javascript"></script>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Sistema de Votación</title>
	
	<!-- JQuery -->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <!-- Google Fonts Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" />
    <!-- MDB -->
    <link rel="stylesheet" href="mdb5/css/mdb.min.css" />
	<!-- Scripts y hojas de estilo del Sistema de Votación -->
    <script type="text/javascript" src="js/site.js"></script>
	<link rel="stylesheet" href="css/site.css" />
	<?php $form_path = 'voto'; ?>
	<script type="text/javascript">
		var currentForm = '<?php echo $form_path; ?>';
	</script>
	<script type="text/javascript" src="forms/<?php echo $form_path ?>/js/vals.js"></script>
	<script type="text/javascript" src="forms/<?php echo $form_path ?>/js/script.js"></script>
  </head>
  <body>
    <?php include('forms/'. $form_path . '/form.html'); ?>
  

  </body>
  <!-- MDB -->
  <script type="text/javascript" src="mdb5/js/mdb.umd.min.js"></script>
  	
</html>

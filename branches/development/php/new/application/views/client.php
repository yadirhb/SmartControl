<?php
$method = $this->input->post('method');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Welcome to smart_control Web Services</title>
  <style type="text/css">
    body {
      background-color: #fff;
      margin: 40px;
      font-family: Lucida Grande, Verdana, Sans-serif;
      font-size: 14px;
      color: #4F5155;
    }

    a {
      color: #003399;
      background-color: transparent;
      font-weight: normal;
    }

    h1 {
      color: #444;
      background-color: transparent;
      border-bottom: 1px solid #D0D0D0;
      font-size: 16px;
      font-weight: bold;
      margin: 24px 0 2px 0;
      padding: 5px 0 6px 0;
    }

    code {
      font-family: Monaco, Verdana, Sans-serif;
      font-size: 12px;
      background-color: #f9f9f9;
      border: 1px solid #D0D0D0;
      color: #002166;
      display: block;
      margin: 14px 0 14px 0;
      padding: 12px 10px 12px 10px;
    }
    fieldset{
      margin: 10px 0;
      padding: 10px;
    }
  </style>
    <style type="text/css">
        .menu-general {
            position: relative;
        }

        ul.nav {
            list-style: none;
            display: block;
            position: relative;
            -webkit-background-size: 50% 100%;
            -moz-background-size: 50% 100%;
            -o-background-size: 50% 100%;
            padding: 0;
        }

        li {
            margin: 5px;
            float: left;
        }

        ul.nav li a {
            -webkit-transition: all 0.3s ease-out;
            -moz-transition: all 0.3s ease-out;
            -o-transition: all 0.3s ease-out;
            background: #ebebeb;
            color: #174867;
            padding: 7px 15px 7px 15px;
            -webkit-border-top-right-radius: 10px;
            -moz-border-top-right-radius: 10px;
            -o-border-top-right-radius: 10px;
            -webkit-border-bottom-right-radius: 10px;
            -moz-border-bottom-right-radius: 10px;
            -o-border-bottom-right-radius: 10px;
            -webkit-border-top-left-radius: 10px;
            -moz-border-top-left-radius: 10px;
            -o-border-top-left-radius: 10px;
            -webkit-border-bottom-left-radius: 10px;
            -moz-border-bottom-left-radius: 10px;
            -o-border-bottom-left-radius: 10px;
            text-decoration: none;
            -webkit-box-shadow: 2px 2px 4px #0e169b;
            -moz-box-shadow: 2px 2px 4px #0e169b;
            -o-box-shadow: 2px 2px 4px #0e169b;
            border-radius: 5px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            -o-border-radius: 5px;
        }

        ul.nav li a:hover, ul.nav li a.active {
            background: #faefaf;
            color: #0e169b;
            border-radius: 0;
            -webkit-border-radius: 0;
            -moz-border-radius: 0;
            -o-border-radius: 0;
            padding: 7px 25px 7px 25px;
        }
    </style>
    <link rel="stylesheet" href="<?php print base_url(); ?>/bootstrap/css/bootstrap.css" media="all">
    <link rel="stylesheet" href="<?php print base_url(); ?>/bootstrap/css/bootstrap-theme.min.css" media="all">
</head>
<body class="container">
<h1>Welcome to smart_control Web Services!</h1>
<div class="menu-general">
    <ul class="nav">
        <li><a href="<?php print base_url(); ?>api.php/backend">Backend</a></li>

        <li><a href="<?php print base_url(); ?>api.php/user_admin">Admin users</a></li>
        <li><a class="active" href="<?php print base_url(); ?>api.php/client">Web Test Client</a></li>
    </ul>
</div>
<fieldset>
  <legend>Métodos del servicio</legend>
<form action="<?php print base_url(); ?>api.php/client" method="post" accept-charset="utf-8">
<div class="form-group">
  <?php echo form_label('Seleccione el Método a probar', 'method', array('class' => '')); ?>
  <div class="">
    <?php echo form_dropdown('method', $options, $method); ?>
  </div>
</div>
<input type="submit" name="get_fields_method" value="TESTEAR METODO"/>
</form>
</fieldset>
<fieldset>
  <legend>Datos del servicio</legend>
  <b>METHOD: </b><?php print $service_data['method']; ?><br />
  <b>URL: </b><?php print $service_data['url']; ?><br />
</fieldset>
<fieldset>
  <legend>Entrada del método</legend>
  <form action="<?php print base_url(); ?>api.php/client" method="post" accept-charset="utf-8">
  <?php
  if(is_array($form_fields)){
    foreach($form_fields as $field){
      $value = $this->input->post($field);
      if($field == 'prueba'){
        $select = '<select name="prueba">
        <option>botometro</option>
        <option>pajimetro</option>
        <option>lanzamiento_movil</option>
        <option>no_hagas_nada</option>
        <option>ojimetro</option>
        <option>piropos</option>
        <option>preguntas</option>
        </select>';
        echo $select;
      }
      else
        echo $field.' <input type="textfield" name="'.$field.'" value="'.$value.'" /><br />';
    }
  }
  ?>
    <input type="submit" name="get_result_method" value="VER RESULTADOS"/>
  </form>
</fieldset>
<fieldset>
  <legend>Salida del método</legend>
  <pre><?php if(!empty($result_test['responseBody'])) print $result_test['responseBody']; ?></pre>
</fieldset>
</body>
</html>
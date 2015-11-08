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
    fieldset {
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

        <li><a class="active" href="<?php print base_url(); ?>api.php/user_admin">Admin users</a></li>
        <li><a href="<?php print base_url(); ?>api.php/client">Web Test Client</a></li>
    </ul>
</div>
| <a href="<?php print base_url(); ?>api.php/user_admin">Listar usuarios</a> |
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Foto</th>
      <th>Nombre</th>
      <th>Correo</th>
      <th>Operaciones</th>
    </tr>
  </thead>
  <tbody>
    <?php
      if(count($users) == 0) :
        ?><tr><td colspan="4">No hay usuarios registrados</td></tr><?php
      else :
        foreach($users as $u) : ?>
          <tr>
            <td><img src="<?php print $u->data['photo']; ?>" width="85" height="85"/></td>
            <td><?php print $u->name; ?></td>
            <td><?php print $u->mail; ?></td>
            <td>
              <ul>
                  <li>
                      <a href="#" onclick="confirmar_delete('<?php print $u->uid; ?>');">Eliminar</a>
                  </li>
              </ul>
            </td>
          </tr>
      <?php
        endforeach;
      endif;
    ?>
  </tbody>
</table>
<script>
    function confirmar_delete(uid){
        var answer = confirm('Está seguro de eliminar el usuario');
        if(answer)
            location.href = '<?php print base_url(); ?>api.php/user_admin/eliminar/' + uid;
    }
</script>
</body>
</html>
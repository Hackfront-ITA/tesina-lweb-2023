<?php
require_once('config.php');

$perm_visitatore = true;
$perm_cliente = true;
$perm_gestore = true;
$perm_admin = true;

require_once(RC_ROOT . '/lib/start.php');
require_once(RC_ROOT . '/lib/xml.php');

$id_prodotto = $_POST['id_prodotto'];
$doc_prodotti = load_xml('prodotti');
$result = xpath($doc_prodotti, 'prodotti', "/ns:prodotti/ns:prodotto[@id=$id_prodotto]");
$prodotto = $result[0];

$nome = $prodotto->getElementsByTagName('nome')[0]->textContent;
$marca = $prodotto->getElementsByTagName('marca')[0]->textContent;
$descrizione = $prodotto->getElementsByTagName('descrizione')[0]->textContent;
$costo = $prodotto->getElementsByTagName('costo')[0]->textContent;
$categoria = $prodotto->getElementsByTagName('categoria')[0]->textContent;
$quantita = $prodotto->getElementsByTagName('quantita')[0]->textContent;
?>

<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
  <title>Home page &ndash; R&amp;C store</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rampart+One&amp;display=swap" />

  <link rel="stylesheet" type="text/css" href="<?php echo(RC_SUBDIR); ?>/res/css/common.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo(RC_SUBDIR); ?>/res/css/header.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo(RC_SUBDIR); ?>/res/css/footer.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo(RC_SUBDIR); ?>/res/css/index.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo(RC_SUBDIR); ?>/res/css/prodotto.css" />
</head>

<body>

  <?php require(RC_ROOT . '/lib/header.php'); ?>

  <div id="contenuto">
    <img id="img-prod" src="res/img/prodotti/<?php echo($id_prodotto); ?>.png" alt="shop_<?php echo($p_id); ?>.png" ></img>
    <div id="descrizione">
        <p><i> <?php echo($marca); ?> </i></p>
        <p><b> <?php echo($nome); ?> </b></p>
        <p> <?php echo($descrizione); ?> </p>
    </div>
  </div>

  <?php require(RC_ROOT . '/lib/footer.php'); ?>

</body>

</html>
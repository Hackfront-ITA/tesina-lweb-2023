<?php
require_once('config.php');

$perm_visitatore = true;
$perm_cliente = true;
$perm_gestore = true;
$perm_admin = true;

require_once(RC_ROOT . '/lib/start.php');
require_once(RC_ROOT . '/lib/xml.php');
require_once(RC_ROOT . '/lib/offerte.php');
require_once(RC_ROOT . '/lib/rating.php');

$id_valido = isset($_GET['id']) && !is_nan($_GET['id']);
if ($id_valido) {
  $id_prodotto = $_GET['id'];

  $doc_prodotti = load_xml('prodotti');
  $result = xpath($doc_prodotti, 'prodotti', "/ns:prodotti/ns:prodotto[@id=$id_prodotto]");
  if ($result->length !== 1) {
    $id_valido = false;
  } else {
    $prodotto = $result[0];

    $nome = $prodotto->getElementsByTagName('nome')[0]->textContent;
    $marca = $prodotto->getElementsByTagName('marca')[0]->textContent;
    $descrizione = $prodotto->getElementsByTagName('descrizione')[0]->textContent;
    $costo_orig = $prodotto->getElementsByTagName('costo')[0]->textContent;
    $categoria = $prodotto->getElementsByTagName('categoria')[0]->textContent;
    $quantita = $prodotto->getElementsByTagName('quantita')[0]->textContent;

    $doc_offerte = load_xml('offerte');
    $off_app = offerte_applicabili($doc_offerte, $prodotto);
    $sconto = calcola_sconto($off_app);
    $bonus = calcola_bonus($off_app);
    $costo_finale = round($costo_orig * (1 - $sconto), 2);

    $disponibile = $quantita > 0;

    $doc_recensioni = load_xml('recensioni');
    $recensioni = xpath($doc_recensioni, 'recensioni', "/ns:recensioni/ns:recensione[@idProdotto='$id_prodotto']");

    $doc_domande = load_xml('domande');
    $domande = xpath($doc_domande, 'domande', "/ns:domande/ns:domanda[@idProdotto='$id_prodotto']");

    $doc_utenti = load_xml('utenti');
  }
}
?>
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it">
<head>
  <title><?php echo($nome); ?> &ndash; R&amp;C store</title>

  <link rel="stylesheet" type="text/css" href="<?php echo(RC_SUBDIR); ?>/res/css/common.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo(RC_SUBDIR); ?>/res/css/header.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo(RC_SUBDIR); ?>/res/css/footer.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo(RC_SUBDIR); ?>/res/css/prodotto.css" />
</head>

<body>
  <?php require(RC_ROOT . '/lib/header.php'); ?>

  <div id="contenuto">
<?php if (!$id_valido) { ?>
    <p>Prodotto non trovato.</p>
<?php } else { ?>
    <div id="pag-prodotto">
      <div id="prod-top">
        <div id="prod-top-sx">
          <img src="<?php echo(RC_SUBDIR); ?>/res/img/prodotti/<?php echo($id_prodotto); ?>.png" alt="Immagine prodotto <?php echo($id_prodotto); ?>" ></img>
        </div>
        <div id="prod-top-mid">
          <p id="marca"><?php echo($marca); ?></p>
          <p id="nome"><?php echo($nome); ?></p>
          <p id="descrizione"><?php echo($descrizione); ?></p>
        </div>
        <div id="prod-top-dx" class="riquadro">
          <div id="prod-top-dx-info">
            <div id="col-1">
<?php if ($disponibile) { ?>
              <p>&#x1F44D; Quantità disponibile: <?php echo($quantita); ?></p>
<?php } else { ?>
              <p>&#x274C; Prodotto terminato!</p>
<?php } ?>
<?php if ($sconto > 0.00) { ?>
              <p>&#x1F4B2; <?php echo($sconto * 100); ?>% di sconto</p>
<?php } ?>
<?php if ($bonus > 0) { ?>
              <p>&#x1F4B2; +<?php echo($bonus); ?> crediti</p>
<?php } ?>
            </div>
            <div id="col-2">
<?php if ($costo_orig - $costo_finale >= 0.01) { ?>
              <span id="prezzo-originale" class="barrato"><?php echo(number_format($costo_orig, 2)); ?> &euro;</span>
<?php } ?>
              <span id='prezzo-finale'><?php echo(number_format($costo_finale, 2)); ?> &euro;</span>
            </div>
          </div>
            <form id="prod-top-dx-action" action="<?php echo(RC_SUBDIR); ?>/cliente/carrello.php" method="post">
              <input type="hidden" name="id_prodotto" value="<?php echo($id_prodotto); ?>" />
              <div id="input-qta">
                <input type="number" name="quantita" class="input-box" value="1" min="1" step="1" max="<?php echo($quantita); ?>" <?php if (!$disponibile) echo ('disabled'); ?>/>
              </div>
              <div id="btn-aggiungi">
                <button type="submit" name="azione" value="aggiungi" class="button ml-8" <?php if (!$disponibile) echo ('disabled'); ?>>Aggiungi al carrello</button>
              </div>
            </form>
        </div>
      </div>
      <button id="tab-rec" class="tab-attiva" onclick="mostraRecensioni()"> Recensioni</button><button id="tab-dr" class="tab-inattiva" onclick="mostraDR()"> Domande e Risposte</button>
      <div id="recensioni">
        <h3>Recensioni</h3>
<?php
  foreach ($recensioni as $recensione) {
    $id_recensione = $recensione->getAttribute('id');
    $id_utente = $recensione->getElementsByTagName('idUtente')[0]->textContent;
    $contenuto = $recensione->getElementsByTagName('contenuto')[0]->textContent;

    $result = xpath($doc_utenti, 'utenti', "/ns:utenti/ns:utente[@id='$id_utente']");
    $utente = $result[0];
    $nome_ut = $utente->getElementsByTagName('nome')[0]->textContent;
    $cognome_ut = $utente->getElementsByTagName('cognome')[0]->textContent;

    $ratings = $recensione->getElementsByTagName('ratings')[0]->childNodes;
    $rating_medio = calcola_rating_medio($ratings);
?>
        <div class="flex-col mb-32 mt-16">
          <div class="fb-20">
            Supporto <?php echo($rating_medio['supporto']); ?>, utilit&agrave; <?php echo($rating_medio['utilita']); ?>
            <p>da <?php echo($nome_ut . ' ' . $cognome_ut); ?></p>
            <div class="riquadro pa-8 mt-8 mr-32">
              <p id="supporto_<?php echo($id_recensione); ?>">Supporto:
                <a class="stellina" href="#" onclick="setSupporto(<?php echo($id_recensione); ?>, 1)">&#x2606</a>
                <a class="stellina" href="#" onclick="setSupporto(<?php echo($id_recensione); ?>, 2)">&#x2606</a>
                <a class="stellina" href="#" onclick="setSupporto(<?php echo($id_recensione); ?>, 3)">&#x2606</a>
                <a class="stellina" href="#" onclick="setSupporto(<?php echo($id_recensione); ?>, 4)">&#x2606</a>
                <a class="stellina" href="#" onclick="setSupporto(<?php echo($id_recensione); ?>, 5)">&#x2606</a>
              </p>
              <form id="rating_<?php echo($id_recensione); ?>" method="post">
                <input type="hidden" name="id_recensione" value="<?php echo($id_recensione); ?>" />
                <input type="hidden" name="supporto" value="0" />
                <input type="hidden" name="utilita" value="0" />
                <button type="submit" name="azione" value="rating_recensione" class="button-2 destra mr-4">Invia</button>
              </form>
              <p id="utilita_<?php echo($id_recensione); ?>">Utilit&agrave;:
                <a class="stellina" href="#1" onclick="setUtilita(<?php echo($id_recensione); ?>, 1)">&#x2606</a>
                <a class="stellina" href="#2" onclick="setUtilita(<?php echo($id_recensione); ?>, 2)">&#x2606</a>
                <a class="stellina" href="#3" onclick="setUtilita(<?php echo($id_recensione); ?>, 3)">&#x2606</a>
              </p>
            </div>
          </div>
          <div class="fb-80">
            <p class="giustificato"><?php echo($contenuto); ?></p>
          </div>
        </div>
<?php
  }
?>
      </div>
      <div id="dr" class="nascosto">
        <h3>Domande e risposte</h3>
<?php
  foreach ($domande as $domanda) {
    $contenuto = $domanda->getElementsByTagName('contenuto')[0]->textContent;
?>
        <h4><?php echo($contenuto); ?></h4>
        <p>Ciao.</p>
<?php
  }
?>
      </div>
    </div>
<?php } ?>
  </div>

  <script type="text/javascript">
    function mostraRecensioni() {
      var tabRec = document.getElementById('tab-rec');
      var tabDR = document.getElementById('tab-dr');
      var divRec = document.getElementById('recensioni');
      var divDR = document.getElementById('dr');

      tabDR.classList.add('tab-inattiva');
      tabDR.classList.remove('tab-attiva');

      tabRec.classList.add('tab-attiva');
      tabRec.classList.remove('tab-inattiva');

      divRec.classList.remove('nascosto');
      divDR.classList.add('nascosto');
    }

    function mostraDR() {
      var tabRec = document.getElementById('tab-rec');
      var tabDR = document.getElementById('tab-dr');
      var divRec = document.getElementById('recensioni');
      var divDR = document.getElementById('dr');

      tabRec.classList.add('tab-inattiva');
      tabRec.classList.remove('tab-attiva');

      tabDR.classList.add('tab-attiva');
      tabDR.classList.remove('tab-inattiva');

      divRec.classList.add('nascosto');
      divDR.classList.remove('nascosto');
    }

    function setSupporto(id, valore) {
      document.forms['rating_' + id].elements.supporto.value = valore;

      var stelline = document.querySelectorAll('#supporto_' + id + ' > a');
      for (var i = 0; i < stelline.length; i++) {
        if (i < valore) {
          stelline[i].textContent = '\u2605';
        } else {
          stelline[i].textContent = '\u2606';
        }
      }
    }

    function setUtilita(id, valore) {
      document.forms['rating_' + id].elements.utilita.value = valore;

      var stelline = document.querySelectorAll('#utilita_' + id + ' > a');
      for (var i = 0; i < stelline.length; i++) {
        if (i < valore) {
          stelline[i].textContent = '\u2605';
        } else {
          stelline[i].textContent = '\u2606';
        }
      }
    }
  </script>

  <?php require(RC_ROOT . '/lib/footer.php'); ?>
</body>

</html>

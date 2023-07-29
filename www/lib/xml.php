<?php
require_once('../config.php');

function load_xml($table) {
  $xmlFile = RC_ROOT . '/data/' . $table . '.xml';

  $xmlData = file($xmlFile);
  if (!$xmlData) {
    return null;
  }

  $xmlString = '';
  foreach($xmlData as $line) {
    $xmlString .= trim($line);
  }
  $doc = new DOMDocument();
  $doc->loadXML($xmlString);

  return $doc;
}

function estrazione_utente($id) {
  $doc = load_xml('utenti');
  
  $xpath = new DOMXPath($doc);
  $xpath->registerNameSpace('ut', 'http://www.lweb.uni/tesina-rcstore/utenti/');
  $query = "/ut:utenti/ut:utente[@id = $id]";
  $result = $xpath->evaluate($query);
  if ($result->length !== 1) {
    echo ("Utente non presente\n");
    return false;
  }

  return $result[0];
}

function save_xml($doc, $table) {
  $xmlFile = RC_ROOT . '/data/' . $table . '.xml';

  $doc->save($xmlFile);
}

function domlist_to_array($domlist) {
  $arr = [];
  for ($i = 0; $i < $domlist->length; $i++) {
    $arr[$i] = $domlist->item($i);
  }
  return $arr;
}


function sort_by_element_dec($elements, $key) {
  usort($elements, function($aElement, $bElement) use ($key) {
    $aValue = $aElement->getElementsByTagName($key)[0]->textContent;
    $bValue = $bElement->getElementsByTagName($key)[0]->textContent;

    $aValue = floatval($aValue);
    $bValue = floatval($bValue);

    return $aValue <=> $bValue;
  });

  return $elements;
}

function sort_by_element_txt($elements, $key) {
  usort($elements, function($aElement, $bElement) use ($key) {
    $aValue = $aElement->getElementsByTagName($key)[0]->textContent;
    $bValue = $bElement->getElementsByTagName($key)[0]->textContent;

    return strnatcmp($aValue, $bValue);
  });

  return $elements;
}
?>

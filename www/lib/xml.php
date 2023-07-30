<?php
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

function save_xml($doc, $table) {
  $xmlFile = RC_ROOT . '/data/' . $table . '.xml';

  $doc->formatOutput = true;
  $doc->preserveWhiteSpace = false;

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

function get_next_id($elements) {
  $next_id = 0;
  for ($i = 0; $i < $elements->length; $i++) {
    $cur_id = $elements[$i]->getAttribute('id');
    if ($next_id < $cur_id) {
      $next_id = $cur_id;
    }
  }
  $next_id++;

  return $next_id;
}

function xpath($doc, $table, $query) {
  $xp = new DOMXPath($doc);
  $xp->registerNameSpace('ns', 'http://www.lweb.uni/tesina-rcstore/' . $table . '/');
  $result = $xp->evaluate($query);
  return $result;
}
?>

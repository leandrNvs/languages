<?php
ini_set('display_errors', 1);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $action = $_GET['action'];

  if($action == 'add') {

    $words = array_map(
      'trim',
      explode("\n", file_get_contents('already.txt'))
    );

    $word = $_GET['word'];

    if(!in_array($word, $words)) {
      file_put_contents('already.txt', $word."\n", FILE_APPEND);

      $data = json_decode(file_get_contents('php://input')); 

      file_put_contents('files/words.txt', implode("\n", $data)."\n", FILE_APPEND);    

      die('phrases added');
    }

    die('Already exists');
  }

  if($action == 'file') {
    $file = $_FILES['file'];

    $data = explode("\n", file_get_contents($file['tmp_name']));
    $data = array_filter($data);

    $data = array_map(function($key, $value) {
      $value = array_filter(
        explode("\t", $value)
      );

      $phrase = explode(" [sound:", $value[0]);
      $phrase[1] = str_replace(']', "", $phrase[1]);

      $mean   = $value[1];
      $what   = $value[2];

      return <<<ITEM
        <dt data-audio="{$phrase[1]}">
          <span class="phrase">{$phrase[0]}</span>
          <button type="button" data-pos="$key">Play</button>
        </td>
        <dd>
          $what
          <hr />
          $mean
        </dd>
      ITEM;
    }, array_keys($data), $data);

    $html = file_get_contents('template.html');
    $html = str_replace('{{list}}', implode("\n", $data), $html);

    file_put_contents('html/phrases.html', $html);

    die('files created');
  }

}
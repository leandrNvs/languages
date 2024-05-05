<?php

$act = $_GET['act'];

if(!file_exists('files/')) {
  mkdir('files');
}

switch($act):
  case 'upload':
      $lines = file($_FILES['subtitles']['tmp_name']);

      $lines = array_map(function($i) {
        $i = trim($i);
        $i = strip_tags($i);
        $i = preg_replace('/^[0-9]+((.*?)[0-9]+)?$/', '', $i);

        return $i;
      }, $lines);

      $text = implode(" ", $lines);

      file_put_contents('files/tmp.txt', $text);

      die($text);
    break;

  case 'load':
      if(file_exists('files/tmp.txt')) {
        die(file_get_contents('files/tmp.txt'));
      }
    break;

  case 'save':
      $text = file_get_contents('php://input');

      file_put_contents('files/tmp.txt', $text);

      die('Saved');
    break;

  case 'saveas';
      $data = file_get_contents('php://input');
      $data = json_decode($data, true);

      $title = 'files/' . $data['title'] . '.txt';

      file_put_contents($title, $data['text']);

      die('Saved');
    break;
endswitch;
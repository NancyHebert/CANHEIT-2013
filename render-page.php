<?php

  ini_set('error_reporting', E_ALL);
  ini_set('display_errors', 1);

# set defaults

  $cache_dir = 'json-cache/';
  $template_dir = 'templates';

# load requirements

  require_once 'lib/.vendor/autoload.php';
  $loader = new Twig_Loader_Filesystem($template_dir);
  $twig = new Twig_Environment($loader);

# parse the URI

  $p = $_SERVER['REQUEST_URI'];
  $json_file = "";
  $template_file = "";
  
  switch ($p) {
    case (preg_match("/^\/(your-stay\/accommodations)\/([0-9]{1,6})\.html$/", $p, $matches) ? true : false) :
      $json_file = $matches[1].'/'.$matches[2].'.json';
      $template_file = $matches[1].'/accommodation.twig';
      break;
    case (preg_match("/^\/(your-stay\/accommodations)\/$/", $p, $matches) ? true : false) :
      $json_file = $matches[1].'/index.json';
      $template_file = $matches[1].'/index.twig';
      break;
    default:
      return_404();
  }

# load the json, throw a 404 on error

  $json = file_get_contents($cache_dir . $json_file);
  
  if (false === $json) {
    return_404();
  }
  
  $json = utf8_encode($json);

# parse the json, throw a 404 on error

  $data = json_decode($json, true);
  
  if (false == is_array($data)) {
    return_404();
  }

# load the template

  if (!is_file($template_dir . '/' . $template_file)) {
    return_404();
  }

  echo $twig->render($template_file, $data);

# helper functions

  function return_404() {
    header("HTTP/1.0 404 Not Found");
    require '404.html';
    exit;
  }

?>
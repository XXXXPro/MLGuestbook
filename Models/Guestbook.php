<?php

namespace PCatalog\Models;

use MLFW\ExceptionConfig;
use MLFW\ExceptionWrongData;

class Guestbook extends \MLFW\Models\Entity {
  public function __construct() {
    parent::__construct();
    $this->status=2;
  }

  public static function load(array $conditions=[]):array {
    $result = [];
    if (!empty($conditions['premod'])) $dirname = __DIR__.'/../data/guestbook/premod/';
    else $dirname = __DIR__.'/../data/guestbook/';
    $files = array_reverse(glob($dirname.'*.json'));
    if (!empty($conditions['limit'])) $files=array_slice($files,0,$conditions['limit']);
    foreach ($files as $file) {
      $data = unserialize(file_get_contents($file));
      if ($data) $result[]=$data;
    }
    return $result;
  }

  public function save() {
    if ($this->status==2) $dirname = __DIR__.'/../data/guestbook/premod/';
    else $dirname = __DIR__.'/../data/guestbook/';
    if (!\is_writable($dirname)) throw new ExceptionConfig('Data directory is not writable!'); // TODO: add logging
    $seconds = \time()-\strtotime('today');
    $filename = date('Y-m-d_');
    $try=0;    
    $fullname = $dirname.$filename;
    $fh = @fopen($fullname.$seconds.'.json','x');
    while (!$fh) {
      $try++;
      $seconds++;
      $fh = @fopen($fullname.$seconds.'.txt','x');
      if ($try>255) throw new ExceptionWrongData();
    }    
    if ($fh) {
      if (empty($this->id)) $this->id = $try==0 ? $filename : $filename.'_'.$try;
      fputs($fh,serialize($this));
      fclose($fh);
    } 
  }

  public function validate():array {
    
    return [];
  }
}
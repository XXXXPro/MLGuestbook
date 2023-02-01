<?php

/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork basic layout class (defaults to text/plain )
 *  ================================ **/

namespace MLFW\layouts;

class Basic extends \MLFW\Template {
  private $mime = 'text/plain';
  private $charset = 'utf-8';
  private $last_modified =  null;
  private $headers = [];

  public function __construct() {
    header_register_callback([$this,'headersOutput']);
  }

  public function setMime($mime):void {
    $this->mime = $mime;
  }

  public function setCharset($charset):void {
    $this->charset = $charset;
  }

  public function setHeader($name,$value):void {
    $this->headers[$name]=$value;
  }

  public function addHeader($name,$value):void {
    if (!empty($this->headers[$name])) $this->headers.=', '.$value;
    else $this->headers[$name]=$value;
  }

  public function headersOutput():void {
    if (empty($this->headers['Content-Type'])) $this->headers['Content-Type']=$this->mime.'; charset='.$this->charset;
    // if (empty($this->headers['Last-Modified'])) $this->headers['Last-Modified']=empty($this->modified) ? date('') : ; // TODO: add correct format output
    if (empty($this->headers['Content-Length'])) $this->headers['Content-Length']=ob_get_length();
    foreach ($this->headers as $name=>$value) header($name.': '.$value);
  }

  public function getTemplate(): string {
    $result = '';
    foreach ($this->subitems as $obj) $result.=$obj;
    return $result;
  }
}
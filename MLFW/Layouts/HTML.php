<?php

/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork HTML layout class
 *  ================================ **/

namespace MLFW\Layouts;

use MLFW\Debug;

use function \MLFW\app;

class HTML extends Basic {
  protected $title;
  protected $meta = [];
  protected $links = [];
  protected $style = '';
  protected $body_script_code = '';
  protected $head_script_code = '';


  function __construct(\MLFW\Models\Entity $obj=null) {
    parent::__construct();
    if ($obj!==null) $this->setData($obj);
    if (empty($this->title)) $this->title = app()->config('site_title','');
    $this->setMime('text/html');
  }

  public function setTitle(string $title):void {
    $this->title = $title;
  }

  public function setDescription(string $description):void {
    $this->meta['description']=$description;
  }

  public function setData(\MLFW\Models\Entity $obj):void {
    if (!empty($obj->seo_title)) $this->title=$obj->seo_title;
    elseif (!empty($obj->title)) $this->title-$obj->title;
    if (!empty($obj->seo_descr)) $this->title=$obj->seo_descr;
    elseif (!empty($obj->descr)) $this->title=$obj->descr;
  }

  public function addMeta(string $name, string $content):void {
    $this->meta[$name]=$content;
  }

  public function addLink(string $rel, string $href, string $extra=null):void {
    $this->links[]=['rel'=>$rel,'href'=>$href,'extra'=>$extra];
  }

  public function addPreload(string $type, string $href):void {
    $extra = 'as="'.$this->attrEscape($type).'"';
    if ($type==='font') $extra.=' crossorigin';
    if ($type==='style') $extra.=' onload="this.rel=\'stylesheet\'"';
    $this->addLink('preload',$href,);
  }

  public function addInlineScript(string $script_code) {
    $this->body_script_code.=$script_code.PHP_EOL;
  }

  public function addInlineHeadScript(string $script_code) {
    $this->head_script_code.=$script_code.PHP_EOL;
   }  

  public function addRSS(string $url):void {
    $this->addLink('alternate',$url,'type="application/rss+xml"');
  }

  public function addAtom(string $url):void {
    $this->addLink('alternate',$url,'type="application/rss+atom"');
  }

  public function getMetaTags():string {
    $result='';
    foreach ($this->meta as $name=>$content) {
      $result.='<meta name="'.$this->attrEscape($name).'" content="'.$this->attrEscape($content).'"/>'.PHP_EOL;
    }
    return $result;
  }

  public function getLinkTags():string {
    $result='';
    foreach ($this->links as $tag) {
      $result.='<link rel="'.$this->attrEscape($tag['rel']).'" href="'.$this->attrEscape($tag['href']).'"';
      if (!empty($tag['extra'])) $result.=' '.$tag['extra'];
      $result.='"/>'.PHP_EOL;
    }
    return $result;    
  }

  public function getHead():string {
    if (empty($this->meta['viewport'])) $this->meta['viewport']='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover';
    // TODO: add CSS minification
    $style=''; 
    if (!empty($this->style)) $style='<style>'.PHP_EOL.$this->style.'</style>';
    $script='';
    if (!empty($this->head_script_code)) $script='<script>'.PHP_EOL.$this->head_script_code.'</script>';
    return <<<EOL
<!DOCTYPE html>
<html>
  <head>
    <meta charset="{$this->attrEscape($this->charset)}">
    <title>{$this->escape($this->title)}</title>
    {$style}
    {$this->getLinkTags()}
    {$this->getMetaTags()}
    {$script}
  </head>
  <body>
EOL;    
  }

  public function getBody():string {
    $result='';
    foreach ($this->subitems as $subitem) {
      $result.=(string)$subitem.PHP_EOL;
    }
    return $result;
  }

  public function getFooter():string {
    $script='';
    if (!empty($this->body_script_code)) $script='<script>'.PHP_EOL.$this->body_script_code.'</script>'.PHP_EOL;
    return $script.'</body></html>';
  }

  public function getTemplate(): string {
    // TODO: Add HTML minification
    return $this->getHead().$this->getBody().$this->getFooter();
  }
}
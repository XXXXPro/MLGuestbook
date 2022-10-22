<?php
/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork router stub class for single-page apps. 
 *  Always return Index as controller name class
 *  ================================ **/

namespace MLFW\Router;

class Stub implements \MLFW\IRouter {
  public function getAction($url): string {
    return 'Actions\\Index';
  }

  public function route($name,$params):string {
    return './';
  }
}
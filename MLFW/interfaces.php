<?php

/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork interfaces and exceptions
 *  ================================ **/

namespace MLFW;

interface IRouter {
  public function getAction($url):string;
  public function route($name,$params):string;
};

interface IAction {
  public function  exec($params):Layouts\Basic;
}
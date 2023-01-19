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
  public function __construct($params);
  public function getAction($url):array;
  public function route($name,$params):string;
};

interface IAction {
  public function exec($params):Layouts\Basic;
}

interface IEventHandler {
  public static function processEvent(string $name, object $event):void;
}

interface INotifier {
  public function notify(string $receiver, string|object $data, $extra=null);
}

class ExceptionConfig extends \Exception {}
class ExceptionSecurity extends \Exception {}
class Exception404 extends \Exception {}
class Exception410 extends \Exception {}
class Exception403 extends \Exception {}
class ExceptionBanned extends \Exception {}
class ExceptionWrongData extends \Exception {}
class EventStopPropagation extends \Exception {}
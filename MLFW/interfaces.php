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

interface IEventProcessor {
  public function trigger(string $name, object $event_data):void;  
}

interface IEventHandler {
  public static function handleEvent(string $name, object $event):void;
}

interface INotifier {
  public function send(string $receiver, string $data, array $files=[], mixed $extra=null):int;
}

const NOTIFIER_OK = 0;
const NOTIFIER_NO_API_KEY = -255;
const NOTIFIER_BLOCKED = -254;

interface IAuth {
  public function __construct(array $params=[]);
  public function impersonate(string $login, string $scope, bool $permanent=true):bool;
  public function checkAccess(string $action):bool;
  public function getUserLogin():string;
  public function getUserID():int;
  public function getUser():object;
  public function checkPassword(string $login, string $password):bool;
  public function isBanned():bool;
  public function isGuest():bool;
  public function logout():void;
}

const USER_GUEST_LOGIN = 'Guest';
const USER_GUEST_ID = 0;

class ExceptionConfig extends \Exception {}
class ExceptionSecurity extends \Exception {}
class ExceptionHTTPCode extends \Exception {}
class Exception403 extends ExceptionHTTPCode {}
class Exception404 extends ExceptionHTTPCode {}
class Exception405 extends ExceptionHTTPCode {}
class Exception410 extends ExceptionHTTPCode {}
class ExceptionBanned extends ExceptionHTTPCode {}
class ExceptionWrongData extends \Exception {}
class EventStopPropagation extends \Exception {}
class ExceptionClassNotFound extends \Exception {}
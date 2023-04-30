<?php
/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork basic application class
 *  ================================ **/

namespace MLFW;

require __DIR__."/interfaces.php";

class Application {
  private $_params;
  /** @var \PDO */
  public $db;
  /** @var \MLFW\IRouter */
  public $router;
  /** @var \MLFW\IEventProcessor */
  public $events;
  /** @var \MLFW\IAuth */
  public $auth;

  function __construct($params) {
    $this->_params = $params;
    $default_values = [
      'router'=>'MLFW\\Routers\\Stub',
      'router_settings'=>null,
      'events'=>'MLFW\\Events\\Basic',
      'events_settings'=>null,
      'auth'=>'MLFW\\Auth\\Stub',
      'auth_settings'=>null,
      'ob_handler'=>null,
      'error_reporting'=>0,
      'display_errors'=>0,
      'debug'=>0,
      'charset'=>'utf-8',
      'session_name'=>'MLFW_sid'
    ];
    foreach ($default_values as $key=>$def_value) if (!isset($this->_params[$key])) $this->_params[$key]=$def_value;    
  }

  function init() {
    if (!\function_exists('app')) require __DIR__."/functions.php";
    // starting output buffering
    \ob_start($this->_params['ob_handler']);
    // enabling errors display
    \ini_set("error_reporting",(string)$this->_params['error_reporting']);
    \ini_set("display_errors",(string)$this->_params['display_errors']);
    // initializing database if needed
    $this->initDB();
    // creating router class
    $this->router = new $this->_params['router']($this->_params['router_settings']);
    // creating 
    $this->events = new $this->_params['events']($this->_params['events_settings']);
    // user should be initialized when other components are ready
    $this->initUser();
  }

  function initDB() {
    if (!empty($this->_params['databases'])) { 
      $err_msg = '';
      foreach ($this->_params['databases'] as $dbdata) {
        try {
          $this->db = new \PDO($dbdata['dsn'],$dbdata['user'],$dbdata['password'],$dbdata['options']);
          break; // if connected successfully, no need to try other databases
        }
        catch (\PDOException $e) {
          $err_msg.= $e->getMessage();
        }
      }
      if (!\is_object($this->db)) throw new \PDOException("Unable to connect to any of databases! ".$err_msg);
    }
  }

  function initUser() {
    $this->auth = new $this->_params['auth']($this->_params['auth_settings']);
    if ($this->auth->isBanned()) throw new ExceptionBanned('You are not allowed to visit this site!');
  }

  /** Starts PHP session. If session is already started, does nothing. 
   * If sessions are disabled in PHP configuration, throws ExceptionConfig. 
   * For security reasons parameter cookie_httponly is always set.
   *  @param array $params — Params to be passed to session_start function.  
   */
  function session($params=[]):void {
    $status = \session_status();
    if ($status === \PHP_SESSION_DISABLED) throw new ExceptionConfig('Sessions are disabled in PHP settings');
    elseif ($status === \PHP_SESSION_NONE) { 
      $sess_name = $this->config('session_name','MLFW_sid');
      \session_name($sess_name);
      if (empty($params['cookie_httponly'])) $params['cookie_httponly']=1;
      if (empty($params['cookie_samesite'])) $params['cookie_samesite']='Lax';
      if (empty($params['cookie_secure']) && !empty($_SERVER['HTTPS']))  $params['cookie_secure']=true; // if request via https, set "secure" attribute for cookie
      \session_start($params);
    }
  }

  /** Returns parameter from application configuration. If parameter is not set, returns specified default value.
   * @param string $param Parameter name
   * @param mixed $default_value Default value to return if parameter is not defined.
   */
  function config(string $param, mixed $default_value=null):mixed {
    return $this->_params[$param] ?? $default_value;
  }

/*  function class_fqn(string $classname, string $interface):string {
    if (strpos($classname,'\\')===false) { // if no namespace specified

    }
    else $classnames = [$classname];
    if (empty(class_implements($classname)[$interface])) throw new ExceptionClassNotFound(htmlspecialchars("Class $classname not found or does not implement interface $interface."));
    return $classname;

  }*/

  function main():void {
    try {
      $this->init();

      $base_url = \dirname($_SERVER['PHP_SELF']);
      if ($base_url!=='/' && $base_url!=='\\') $url = \str_replace($base_url,'',$_SERVER['REQUEST_URI']);
      else $url = $_SERVER['REQUEST_URI'];
      
      list($controller_class,$controller_params) = $this->router->getAction($url);
      if (!\class_exists($controller_class)) throw new Exception404("Class $controller_class not found!");
      $controller = new $controller_class($controller_params);
      $result = $controller->exec(null);
      print $result;
    }
    catch (ExceptionConfig $e) {
      $this->showError(500,'Configuration error: '.$e->getMessage(),$e);
    }
    catch (ExceptionSecurity $e) {    
      $this->showError(500,'Security error: '.$e->getMessage(),$e);
    }
    catch (Exception404 $e) {
      $this->showError(404,$e->getMessage(),$e);
    }
    catch (Exception405 $e) {
      $allow = $e->methods;
      header('Allow: '.$allow);
      $this->showError(405,$e->getMessage(),$e);
    }
    catch (Redirect $e) {
      http_response_code($e->http_code);
      header('Location: '.$e->location);
    }
    catch (\PDOException $e) {
      $this->showError(503,$e->getMessage(),$e);
    }    
    catch (\Exception $e) {
      $this->showError(500,'General error: '.$e->getMessage(),$e);
    }
  }

  function showError(int $code,string $text,\Exception $e=null) {
    http_response_code($code);    
    if (class_exists('Layouts\\ErrorPage')) {
      $errpage = new Layouts\ErrorPage();
      $errpage->putText($text);
      print $errpage;
    }
    else {
      header('Content-Type: text/plain; charset=utf-8');
      print $text;
      if ($this->_params['debug'] && !Debug::isEmpty()) print PHP_EOL."Debug info:".Debug::output();
    }
  }
}
<?php
/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork event processing class
 *  ================================ **/

namespace MLFW\Events;

use Exception;
use MLFW\IEventHandler, MLFW\IEventProcessor;

use function MLFW\_dbg;

class Basic implements \MLFW\IEventProcessor {
  private $params;
  private $default_namespace='';

  public function __construct($params) {
    $this->default_namespace = $params['default_namespace'] ?? '';
    $this->params = $params;
  }

  protected function getHandlers(string $event_name) {
    $events_dir = $this->params['events_dir'] ?? __DIR__.'/../../events'; 
    if (!preg_match('|^\w+$|',$event_name)) throw new \MLFW\ExceptionSecurity(\sprintf("Wrong event name: %s!",$event_name));
    $filelist = glob($events_dir.'/'.$event_name.'/*.txt');
    $handlers = [];
    foreach ($filelist as $file) {
      $handlers+=file($file,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    }
    return $handlers;
  }

  /** Passes event to all registered handlers. 
   * 
   */
  public function trigger(string $name, object $event_data):void {
    $handlers = $this->getHandlers($name);
    $handlers = str_replace('\\\\','\\',$handlers); // to avoid programer mistakes, caused by \\ in common text files
    foreach ($handlers as $handler) {
      try {
        if (strpos($handler,'\\')===false && $this->default_namespace!=='') $result=$this->default_namespace.'\\'.$handler;        
        if (class_exists($handler) && !empty(class_implements($handler)['MLFW\IEventHandler'])) $handler::handleEvent($name,$event_data); 
        else _dbg("Event %s, handler class %s not found or not implementing IEventHandler interface, skipping!");
      }
      catch (\MLFW\EventStopPropagation $e) { // if event handler wants to stop futher event propagation, it should throw this Exception
        break;
      }
      catch (\MLFW\ExceptionHTTPCode $e) { // if handler throw any of status exception (Exception404, Exception403 and so on), rethrow it to process in main application class
        throw $e;
      }
      catch (\MLFW\ExceptionSecurity $e) {
        // TODO: add logging
        _dbg(\sprintf("Security exception: %s for event %s in handler %s",$e->getMessage(),$name,$handler));
      }
      catch (\Exception $e) {
        _dbg(\sprintf("Exception: %s for event %s in handler %s",$e->getMessage(),$name,$handler));
      }
    }
  }
}
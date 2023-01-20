<?php
/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork event processing stub class — just do nothing
 *  ================================ **/

namespace MLFW\Events;

use Exception;
use MLFW\IEventHandler, MLFW\IEventProcessor;

use function MLFW\_dbg;

class Stub implements IEventProcessor {
  public function __construct($params) {
  }
 
  /** Really this class do nothing */
  public function trigger(string $name, object $event_data): void {
  }
    
}
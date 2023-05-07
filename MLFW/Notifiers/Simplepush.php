<?php
/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork Telegram notification class
 * 
 *  Send notification via Simplepush
 *  Bot key must be specified in config entry API_Simplepush_key
 *  ================================ **/

namespace MLFW\Notifiers;

use function \MLFW\app;

class Simplepush implements \MLFW\INotifier {
  const SIMPLEPUSH_URL = 'https://api.simplepush.io/send';

  public function send(string $receiver, string $data, array $files=[], mixed $extra=null):int {
    $api_key = app()->config('API_Simplepush_key',null);
    if (empty($api_key)) {
      // TODO: log warning
      return \MLFW\NOTIFIER_NO_API_KEY;
    }
    $req = new \MLFW\Helpers\Requests();
    if (empty($extra)) $extra=[];
    $data = \strip_tags($data);
    $params = ['key'=>$api_key,'msg'=>$data]+$extra;
    $req->post(Simplepush::SIMPLEPUSH_URL,$params);
    $http_status = $req->getStatus();
    if ($http_status===200) return \MLFW\NOTIFIER_OK;
    // elseif ($http_status===403) return \MLFW\NOTIFIER_BLOCKED;
    else return $http_status;
  }
}
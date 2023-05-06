<?php

namespace PCatalog\Actions;

use Exception;
use PCatalog\Models\Guestbook as ModelsGuestbook;
use \MLFW\Helpers;
use stdClass;

use function \MLFW\app, \MLFW\_dbg;

class Guestbook implements \MLFW\IAction {
  public function exec($params=null):\MLFW\Layouts\Basic {
    $l =  new \PCatalog\Layouts\Guestbook;
    $l->addLink('stylesheet','./www-dev/s/surface.css');

    if (\MLFW\Helpers\HTTP::isPost()) {
      $new_item = new ModelsGuestbook;
      $new_item->text =  filter_input(INPUT_POST,'text',FILTER_DEFAULT);
      $new_item->owner = filter_input(INPUT_POST,'owner',FILTER_SANITIZE_SPECIAL_CHARS);
      $new_item->extra = new stdClass;
      $new_item->extra->email = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
      $new_item->extra->ip = filter_input(INPUT_SERVER,'REMOTE_ADDR',FILTER_VALIDATE_IP,FILTER_FLAG_IPV4|FILTER_FLAG_IPV6);
      try {
        $new_item->save();
        $l->putText('Ваше сообщение поставлено на премодерацию');
        app()->events->trigger("newpost",$new_item);
      }
      catch (Exception $e) {
        $l->putText('Ошибка сохранения: '.$e->getMessage());
      }
      throw new \MLFW\Redirect("./",303);
    }
    $l->form = new \PCatalog\Templates\GuestbookForm;
    $messages = \PCatalog\Models\Guestbook::load();
    foreach ($messages as $message) {
      $message->text = \MLFW\Helpers\HTMLCleaner::clean($message->text,['a'=>'href','img'=>['src','alt']]);
    }
    $l->wrapAll($messages,'\\PCatalog\\Templates\\GuestbookEntry');

    return $l;
  } 
}
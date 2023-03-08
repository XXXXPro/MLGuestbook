<?php

/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork HTTP helper class
 * 
 *  Provides typical operations for HTTP requests,
 *  like detecting method of request
 *  or converting If-Modified-Since header to DateTime
 *  ================================ **/

namespace MLFW\Helpers;

class HTTP {
  public static function requestMethod():string {
    return $_SERVER['REQUEST_METHOD'];
  }

  public static function ifModifiedSince():DateTime {
    if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) return new \DateTime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
    else return null;
  }
}
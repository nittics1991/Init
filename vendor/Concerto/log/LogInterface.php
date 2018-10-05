<?php
/**
*   ログインターフェース
*
*   @version 170126
*/
namespace Concerto\log;

interface LogInterface
{
    /**
    *   出力
    *
    *   @param
    */
    public function write($messages);
}

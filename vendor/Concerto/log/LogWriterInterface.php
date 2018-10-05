<?php
/**
*   ログライターインターフェース
*
*   @version 150419
*/
namespace Concerto\log;

interface LogWriterInterface
{
    /**
    *   フォーマット
    *
    *   @param
    */
    public function setFormat($format);
    
    /**
    *   出力
    *
    *   @param
    */
    public function write($messages);
}

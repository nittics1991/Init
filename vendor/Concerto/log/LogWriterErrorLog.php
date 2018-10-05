<?php
/**
*   ログライターerror_log function
*
*   @version 160822
*/
namespace Concerto\log;

use \InvalidArgumentException;
use \RuntimeException;
use Concerto\log\LogWriterInterface;

class LogWriterErrorLog implements LogWriterInterface
{
    /**
    *   保存先
    *
    *   @var string
    */
    private $stream = 'err.log';
    
    /**
    *   フォーマット
    *
    *   @var string
    */
    private $format = "%s";
    
    /**
    *   コンストラクタ
    *
    *   @param array 設定値
    */
    public function __construct($config, $name = 'default')
    {
        if (is_array($config['log'][$name])) {
            foreach ($config['log'][$name] as $key => $val) {
                if (isset($this->$key) && (!empty($val))) {
                    $this->$key = $val;
                }
            }
        }
    }
    
    /**
    *   フォーマット
    *
    *   @param string 書式(printfと同じ)
    */
    public function setFormat($format)
    {
        $this->format = $format;
    }
    
    /**
    *   出力
    *
    *   @param string or array メッセージ or メッセージ配列(vsprintf引数)
    *   @throws InvalidArgumentException, RuntimeException
    */
    public function write($messages)
    {
        $args = (!is_array($messages))? [$messages]:$messages;
        
        if ($this->getFormatElementCount() != count($args)) {
            throw new InvalidArgumentException("write error");
        }
        
        if (!error_log(vsprintf($this->format, $args), 3, $this->stream)) {
            throw new RuntimeException("write error");
        }
    }
    
    /**
    *   フォーマット要素数
    *
    *   @return integer 要素数
    */
    public function getFormatElementCount()
    {
        return (
            mb_strlen($this->format)
            - mb_strlen(
                mb_ereg_replace('%[b,c,d,e,E,f,F,g,G,o,s,u,x,X]', '', $this->format)
            )
        ) / 2;
    }
}

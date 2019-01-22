<?php
namespace Middlewares;
use Naka507\Koa\Middleware;
use Naka507\Koa\Context;
class Assets implements Middleware
{
    public $path;
    public static $mimes = array (
        'css' => 'text/css',
        'xml' => 'text/xml',
        'gif' => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript',
        'eot' => 'application/octet-stream',
        'txt' => 'text/plain',
        'png' => 'image/png'
    );
    public function __construct( $path = "./themes")
    {
        if ( !is_dir($path) ) {
            throw new \RuntimeException("static path error");
        }
        global $configs;
        $this->path = $path . DS . $configs['site']['theme'] ;
    }

    public function __invoke(Context $ctx, $next)
    {
        $url_info = parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        if ( !$url_info ) {
            $ctx->status = 404;
            $ctx->body = '<h1>400 Bad Request</h1>';
            return;
        }
        
        $path = isset($url_info['path']) ? $url_info['path'] : '/';
        $path_info      = pathinfo($path);

        $file_extension = isset($path_info['extension']) ? $path_info['extension'] : '';
        if ($file_extension !== '') {
            $file_path = $this->path .  $path;
            if (is_file($file_path)) {
                $file_path = realpath($file_path);
                $info = stat($file_path);
                $modified_time = $info ? date('D, d M Y H:i:s', $info['mtime']) . ' ' . date_default_timezone_get() : '';
                if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $info) {

                    if ($modified_time === $_SERVER['HTTP_IF_MODIFIED_SINCE']) {
                        $ctx->status = 304;
                        $ctx->body = '<h1>400 Bad Request</h1>';
                        return;
                    }
                }

                $file_size = filesize($file_path);
                $file_info = pathinfo($file_path);
                $extension = isset($file_info['extension']) ? $file_info['extension'] : '';
                $file_name = isset($file_info['filename']) ? $file_info['filename'] : '';
                $ctx->status = 200;

                if ($modified_time) {
                    $ctx->lastModified = $modified_time;
                }
                if (isset(static::$mimes[$extension])) {
                    $ctx->type = static::$mimes[$extension];
                }  

                $ctx->body = file_get_contents($file_path);
                return;
            }
        }

        yield $next;
    }

}

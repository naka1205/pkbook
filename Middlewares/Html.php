<?php
namespace Middlewares;
use Naka507\Koa\Middleware;
use Naka507\Koa\Context;
class Html implements Middleware
{
    public $path;
    public static $mimes = array (
        'html'  => 'text/html',
        'htm'   => 'text/html',
        'shtml' => 'text/html',
        'txt'   => 'text/plain',
        'css'   => 'text/css',
        'xml'   => 'text/xml',
        'png'   => 'image/png',
        'gif'   => 'image/gif',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'bmp'   => 'image/x-ms-bmp',
        'rss'   => 'application/rss+xml',
        'js'    => 'application/x-javascript',
        'eot'   => 'application/octet-stream',
        'ttf'   => 'application/x-font-ttf',
        'woff2' => 'font/woff2',
        'json'  => 'application/json'
    );
    public function __construct()
    {
        if ( !defined ( 'PUBLIC_PATH' ) || !is_dir(PUBLIC_PATH) ) {
            throw new \RuntimeException("assets path error");
        }
        $this->path = PUBLIC_PATH;
    }

    public function __invoke(Context $ctx, $next)
    {
        yield $next; 
        $url_info = parse_url('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

        
        if ( !$url_info ) {
            $ctx->status = 400;
            $ctx->body = '<h1>Bad Request</h1>';
            return;
        }
        
        $path = isset($url_info['path']) ? $url_info['path'] : '/';
        $path_info      = pathinfo($path);
        $file_extension = isset($path_info['extension']) ? $path_info['extension'] : '';

        if ( $file_extension === '' ) {
            $ctx->status = 400;
            $ctx->body = '<h1>Bad Request</h1>';
            return;
        }

        $file_path = $this->path .  $path;
        if (is_file($file_path)) {
            $file_path = realpath($file_path);
            $info = stat($file_path);
            $modified_time = $info ? date('D, d M Y H:i:s', $info['mtime']) . ' ' . date_default_timezone_get() : '';
            if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $info) {

                if ($modified_time === $_SERVER['HTTP_IF_MODIFIED_SINCE']) {
                    $ctx->status = 304;
                    $ctx->body = '<h1>Not Modified</h1>';
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
                $ctx->body = file_get_contents($file_path);
                return;
            }  
            
        }

        $html = PUBLIC_PATH  . DS . '404.html';
        $body_404 = is_file($html) ? file_get_contents($html) : '<h1>Not Found</h1>';

        $ctx->status = 404;
        $ctx->body = $body_404;
        return;
    }

}

<?php
namespace Middlewares;
use Naka507\Koa\Middleware;
use Naka507\Koa\Context;
use Extend\Config;
use Extend\Template;
class Show implements Middleware
{
    public $view;
    public function __construct()
    {
        $config = [
            'view_suffix'   =>	'html',
            'view_path'	    =>	THEME_PATH,
	        'cache_path'	=>	TEMP_PATH . DS . 'themes'
        ];
        $this->view = new Template($config);
    }

    public function __invoke(Context $ctx, $next)
    {   
        $ctx->add('show',function($template) use ($ctx) {
            $site = Config::get('site');
            $this->view->assign('state',$ctx->state);
            $ctx->body = $this->view->fetch($site['theme'] . '/' . $template);
        });
        yield $next;
    }

}

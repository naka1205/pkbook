<?php
namespace Middlewares;
use Naka507\Koa\Middleware;
use Naka507\Koa\Context;
use Extend\Template;
class Show implements Middleware
{
    public $view;
    public function __construct($opt)
    {
        $this->view = new Template($opt);
    }

    public function __invoke(Context $ctx, $next)
    {   
        $ctx->add('show',function($template) use ($ctx) {
            global $configs;
            $this->view->assign('state',$ctx->state);
            $ctx->body = $this->view->fetch($configs['site']['theme'] . '/' . $template);
        });
        yield $next;
    }

}

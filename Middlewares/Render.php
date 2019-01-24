<?php
namespace Middlewares;
use Naka507\Koa\Middleware;
use Naka507\Koa\Context;
use Extend\Template;
class Render implements Middleware
{
    public $view;
    public function __construct($opt)
    {
        $this->view = new Template($opt);
    }

    public function __invoke(Context $ctx, $next)
    {   
        $ctx->add('render',function($template) use ($ctx) {
            foreach ($ctx->state as $key => $value) {
                $this->view->assign($key,$value);
            }
            $ctx->body = $this->view->fetch($template);
        });
        yield $next;
    }

}

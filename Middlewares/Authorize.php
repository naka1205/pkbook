<?php
namespace Middlewares;
use Naka507\Koa\Middleware;
use Naka507\Koa\Context;
class Authorize implements Middleware
{
    public $type;

    public function __construct()
    {

    }

    public function __invoke(Context $ctx, $next)
    {
        yield $next;


    }

}

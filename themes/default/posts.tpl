<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <title>Sidebar | Amaze UI Example</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="format-detection" content="telephone=no">
  <meta name="renderer" content="webkit">
  <meta http-equiv="Cache-Control" content="no-siteapp" />
  <link rel="alternate icon" type="image/png" href="/assets/i/favicon.png">
  <link rel="stylesheet" href="/assets/css/amazeui.min.css"/>
  <link rel="stylesheet" href="/assets/css/posts.css"/>
</head>
<body>
<header class="am-g my-head">
  <div class="am-u-sm-12 am-article">
    <h1 class="am-article-title">{posts->title}</h1>
    <p class="am-article-meta">{posts->date}</p>
  </div>
</header>
<hr class="am-article-divider"/>
<div class="am-g am-g-fixed">
  <div class="am-u-md-9 am-u-md-push-3">
    <div class="am-g">
      <div class="am-u-sm-11 am-u-sm-centered">
        <div class="am-cf am-article">
          {posts->html}
        </div>
        <hr/>
        <ul class="am-comments-list">
          <li class="am-comment">
            <a href="#link-to-user-home">
              <img src="http://s.amazeui.org/media/i/demos/bw-2014-06-19.jpg?imageView/1/w/96/h/96/q/80" alt="" class="am-comment-avatar" width="48" height="48">
            </a>
            <div class="am-comment-main">
              <header class="am-comment-hd">
                <div class="am-comment-meta">
                  <a href="#link-to-user" class="am-comment-author">某人</a> 评论于 <time datetime="2013-07-27T04:54:29-07:00" title="2013年7月27日 下午7:54 格林尼治标准时间+0800">2014-7-12 15:30</time>
                </div>
              </header>
              <div class="am-comment-bd">
                <p>《永远的蝴蝶》一文，还吸收散文特长，多采用第一人称，淡化情节，体现一种思想寄托和艺术追求。</p>
              </div>
            </div>
          </li>
          <li class="am-comment">
            <a href="#link-to-user-home">
              <img src="http://s.amazeui.org/media/i/demos/bw-2014-06-19.jpg?imageView/1/w/96/h/96/q/80" alt="" class="am-comment-avatar" width="48" height="48">
            </a>
            <div class="am-comment-main">
              <header class="am-comment-hd">
                <div class="am-comment-meta">
                  <a href="#link-to-user" class="am-comment-author">路人甲</a> 评论于 <time datetime="2013-07-27T04:54:29-07:00" title="2013年7月27日 下午7:54 格林尼治标准时间+0800">2014-7-13 0:03</time>
                </div>
              </header>
              <div class="am-comment-bd">
                <p>感觉仿佛是自身的遭遇一样，催人泪下</p>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="am-u-md-3 am-u-md-pull-9 my-sidebar">
    <div class="am-offcanvas" id="sidebar">
      <div class="am-offcanvas-bar">
        <ul class="am-nav">
          <li><a href="#">永远的蝴蝶</a></li>
          <li class="am-nav-header">目录</li>
          <li><a href="#">原文</a></li>
          <li><a href="#">作者简介</a></li>
          <li><a href="#">文章赏析</a></li>
          <li><a href="#">读者评论</a></li>
        </ul>
      </div>
    </div>
  </div>
  <a href="#sidebar" class="am-btn am-btn-sm am-btn-success am-icon-bars am-show-sm-only my-button" data-am-offcanvas><span class="am-sr-only">侧栏导航</span></a>
</div>

<footer class="my-footer">
  <p>sidebar template<br><small>© Copyright XXX. by the AmazeUI Team.</small></p>
</footer>

<!--[if lt IE 9]>
<script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.staticfile.org/modernizr/2.8.3/modernizr.js"></script>
<script src="/assets/js/amazeui.ie8polyfill.min.js"></script>
<![endif]-->

<!--[if (gte IE 9)|!(IE)]><!-->
<script src="/assets/js/jquery.min.js"></script>
<!--<![endif]-->
<script src="/assets/js/amazeui.min.js"></script>
<script>
var _id = '{posts->_id}';
var id = '{posts->id}';
</script>
</body>
</html>

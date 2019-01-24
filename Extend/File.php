<?php
namespace Extend;
use Exception;
class File
{
    protected $options = [
        'expire'        => 0,
        'prefix'        => '',
        'cache_path'          => '',
        'cache_subdir'  => false,
        'data_compress' => false,
    ];

    public $tag;

    private $_file;      //操作对象
    private static $_instance = NULL; //链接对象

    /**
     * 构造函数
     * @param array $options
     */
    private function __construct($options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if (substr($this->options['cache_path'], -1) != DS) {
            $this->options['cache_path'] .= DS;
        }
        $this->filePath();
    }

    //检测目录
    private function filePath()
    {
        // 创建项目缓存目录
        if (!is_dir($this->options['cache_path'])) {
            if (mkdir($this->options['cache_path'], 0755, true)) {
                return true;
            }
        }
        return false;
    }    

    //获取实例
    public static function init($config)
    {
            //判断是否已存在示例对象
            if( !(static::$_instance instanceof static) ) {
                    static::$_instance = new static($config);
            }
            return static::$_instance;
    }

    //取得变量的存储文件名
    public function getCacheKey($name)
    {
        $name = md5($name);
        if ($this->options['cache_subdir']) {
            // 使用子目录
            $name = substr($name, 0, 2) . DS . substr($name, 2);
        }
        if ($this->options['prefix']) {
            $name = $this->options['prefix'] . DS . $name;
        }
        $filename = $this->options['cache_path'] . $name . '.php';
        $dir      = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $filename;
    }

    //判断缓存是否存在
    public function has($name)
    {
        return $this->get($name) ? true : false;
    }

    //读取缓存
    public function get($name, $default = false)
    {
        $filename = $this->getCacheKey($name);
        if (!is_file($filename)) {
            return $default;
        }
        $content = file_get_contents($filename);
        if (false !== $content) {
            $expire = (int) substr($content, 8, 12);
            if (0 != $expire && $_SERVER['REQUEST_TIME'] > filemtime($filename) + $expire) {
                return $default;
            }
            $content = substr($content, 32);
            if ($this->options['data_compress'] && function_exists('gzcompress')) {
                //启用数据压缩
                $content = gzuncompress($content);
            }
            $content = unserialize($content);
            return $content;
        } else {
            return $default;
        }
    }

    //写入缓存
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($expire instanceof DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        $filename = $this->getCacheKey($name);

        $data = serialize($value);
        if ($this->options['data_compress'] && function_exists('gzcompress')) {
            //数据压缩
            $data = gzcompress($data, 3);
        }
        $data   = "<?php\n//" . sprintf('%012d', $expire) . "\n exit();?>\n" . $data;
        $result = file_put_contents($filename, $data);
        if ($result) {
            clearstatcache();
            return true;
        } else {
            return false;
        }
    }


    //删除缓存
    public function remove($name)
    {
        $filename = $this->getCacheKey($name);
        return $this->unlink($filename);
    }

    //清除缓存
    public function clear()
    {

        $files = (array) glob($this->options['cache_path'] . ($this->options['prefix'] ? $this->options['prefix'] . DS : '') . '*');
        foreach ($files as $path) {
            if (is_dir($path)) {
                array_map('unlink', glob($path . '/*.php'));
                rmdir($path);
            } else {
                unlink($path);
            }
        }
        return true;
    }

    //判断文件是否存在后，删除
    private function unlink($path)
    {
        return is_file($path) && unlink($path);
    }    
}

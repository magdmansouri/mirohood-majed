<?php
// includes/CacheHelper.php — Simple file cache for gallery data

class CacheHelper {

    private static $cacheDir = null;

    private static function cacheDir() {
        if (self::$cacheDir === null) {
            self::$cacheDir = BASE_PATH . '/cache';
            if (!is_dir(self::$cacheDir)) {
                mkdir(self::$cacheDir, 0755, true);
            }
        }
        return self::$cacheDir;
    }

    private static function cacheFile($key) {
        return self::cacheDir() . '/' . preg_replace('/[^a-z0-9_-]/i', '_', $key) . '.json';
    }

    public static function get($key, $ttl = 300) {
        $file = self::cacheFile($key);
        if (!file_exists($file)) {
            return false;
        }
        if (filemtime($file) + $ttl < time()) {
            unlink($file);
            return false;
        }
        $data = json_decode(file_get_contents($file), true);
        return $data;
    }

    public static function set($key, $data) {
        $file = self::cacheFile($key);
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }

    public static function delete($key) {
        $file = self::cacheFile($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public static function clear() {
        foreach (glob(self::cacheDir() . '/*.json') as $file) {
            unlink($file);
        }
    }
}

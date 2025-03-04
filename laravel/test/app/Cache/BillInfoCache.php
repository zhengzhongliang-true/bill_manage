<?php

namespace App\Cache;

use App\Models\BillInfoModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Predis\Client;

class BillInfoCache
{
    protected $connection = 'mysql';
    protected $table = 'mysql.Bill_Info';
    protected $primekey = 'id';
    public $timestamps = false;
    public static function add(Request $request)
    {
        $redis = new Client(array('host' => '127.0.0.1', 'port' => 6379));
        $key = $request->input('key', '');
        $value = $request->input('value', '');
        $ttl = $request->input('ttl', 0);
        if ($ttl) {
            $setRs = $redis->setex($key, $ttl, $value);
        } else {
            // exit();
            $setRs = $redis->set($key, $value);
        }
        return array(
            '设置结果:' . $setRs,
            '缓存值:' . $redis->get($key),
            '缓存剩余时间:' . $redis->ttl($key)
        );
    }
    /*
    * 通过把数据修改并读入Redis缓存的方式实现设置账单收藏的功能
    */
    public static function setBillFavorite($id, $listBill, $cmd)
    {
        $rediskey = 'success';
        $redis = new Client(array('host' => '127.0.0.1', 'port' => 6379));
        foreach ($listBill as $value) {
            if ($value['id'] == $id) {
                if ($cmd == 'setFa') {
                    $value['favorite'] = 1;
                    $returnMe = $redis->setex($rediskey, 60, $value['favorite']);
                } else if ($cmd == 'unsetFa') {
                    $value['favorite'] = 0;
                    $returnMe = $redis->setex($rediskey, 60, $value['favorite']);
                }
                return array(
                    $value,
                    array(
                        '缓存结果' => $returnMe,
                        '缓存值'   => json_decode($redis->get($rediskey)),
                        '缓存剩余时间' => $redis->ttl($rediskey),
                    )
                );
            }
        }
        return null;
    }
    /*
    * 通过把数据从数据库中读入Redis缓存并进行条件查询
    */
    public static function queryFavoriteBill($first_date, $second_date, $category)
    {
        $rediskey = 'success';
        $redis = new Client(array('host' => '127.0.0.1', 'port' => 6379));
        $All_FaBill = BillInfoModel::getAllFaBill();
        $result = BillInfoModel::queryInfo($first_date, $second_date, $category, $All_FaBill);
        $returnMe = $redis->setex($rediskey, 60, '1');
        return array(
            $result,
            array(
                '缓存结果' => $returnMe,
                '缓存值'   => $redis->get($rediskey),
                '缓存剩余时间' => $redis->ttl($rediskey),
            )
        );
    }
}

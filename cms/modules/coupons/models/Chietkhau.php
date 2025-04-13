<?php

namespace cms\modules\coupons\models;

use Yii;

/**
 * This is the model class for table "tb_chietkhau".
 *
 * @property int $id
 * @property string|null $product_id
 * @property string|null $price
 * @property string|null $source
 * @property string|null $coupon_short_url
 * @property string|null $create_date
 * @property int|null $create_time
 */
class Chietkhau extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tb_chietkhau';
    }


    public static $arrConfig = [
        1 => [
            'url' => 'http://chietkhauali.com/agency/search?token=1bEwa7dLBpcvve7',
            'web' => '&web=',
            'product_id' => '&num_iid=',
        ],
        2 => [
            'url' => 'http://partner.alibo.vn/customer_commission_api/?access_token=7f85ffaa82efce415e66ae4befd619fe',
            'web' => '&platform=',
            'product_id' => '&product_id=',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'create_time'], 'integer'],
            [['source','coupon_short_url'], 'string'],
            [['create_date'], 'safe'],
            [['product_id', 'price'], 'string', 'max' => 255],
        ];
    }

    /**
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'price' => 'Price',
            'source' => 'Source',
            'coupon_short_url' => 'Coupon Short Url',
            'create_date' => 'Create Date',
            'create_time' => 'Create Time',
        ];
    }


    public static function curlCoupon($product_id, $web = 1, $type = 1)
    {
        $configApi = self::$arrConfig[$type];
        $url = $configApi['url'].$configApi['web'].$web.$configApi['product_id'].$product_id;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($result, true);

        return $data;
    }

    public static function getLinkCoupon($suplier,$type = 1)
    {
        $arrProductID = [];
        $arrCoupon = [];
        if (!empty($suplier)) {
            foreach ($suplier as $k => $item) {
                $web = 1;

                if($type == 1){
                    if($item['sourceName'] == '1688')
                        $web = 2;

                }elseif ($type == 2){
                    $web = 0;
                    if($item['sourceName'] == '1688')
                        $web = 1;
                }

                $arrProductID[$item['shopProductID']] = $web;
            }

            $cache = \Yii::$app->cache;
            foreach ($arrProductID as $pid => $web) {
                //change link
                $key_cache = 'chiet_khau_product_' . $pid . $web.$type;
                $data = $cache->get($key_cache);
                if ($data === false) {
                    $res = self::curlCoupon($pid, $web,$type);
                    $coupon_short_url = '';
                    $price = '';

                    if($type == 1 && isset($res['data']['coupon_short_url']) && !empty($res['data']['coupon_short_url'])){
                        $coupon_short_url = $res['data']['coupon_short_url'];
                        $price = isset($res['data']['chietkhau']) ? $res['data']['chietkhau'] : '';
                    }else if(isset($res['commission_url']) && !empty($res['commission_url'])){
                        $coupon_short_url = $res['commission_url'];
                    }

                    if (!empty($coupon_short_url)) {
                        $arrCoupon[$pid] = $coupon_short_url;

                        $cache->set($key_cache, $arrCoupon[$pid], \Yii::$app->params['CACHE_TIME']['HOUR']);
                    }
                } else {
                    $arrCoupon[$pid] = $data;
                }
            }
        }

        return $arrCoupon;
    }
}

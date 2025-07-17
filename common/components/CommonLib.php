<?php

namespace common\components;

use cms\models\TbCategory;
use cms\models\TbMenu;
use cms\models\TbSettings;
use common\helpers\Upload;
use common\models\Check;
use common\models\Cny;
use common\models\Deposit;
use common\models\TbAccountTransaction;
use common\models\TbCustomers;
use common\models\TbKg;
use common\models\TbOrders;
use common\models\TbOrdersDetail;
use common\models\TbOrderSupplier;
use common\models\TbService;
use common\models\TbShipping;
use common\models\TbTransfercode;
use common\models\User;
use phpseclib3\Crypt\Rijndael;
use yii\db\Query;
use yii\helpers\Url;


class CommonLib
{
    const TOKEN = 'qJB0rGtIn5UB1xG03efyCp';
    const TRANSLITERATE_LOOSE = 'Any-Latin; Latin-ASCII; [\u0080-\uffff] remove';


    public static function normalizeUrl($url)
    {

        if (strpos($url, '//') === 0) {
            // Nếu URL bắt đầu bằng "//", thêm "https:"
            $url = 'https:' . $url;
        }

        return $url;
    }

    public static function downloadTaobaoImage($url)
    {

        $url = self::normalizeUrl($url);
        // Tên file từ đường dẫn gốc
        $fileName = basename(parse_url($url, PHP_URL_PATH));

        // Đường dẫn vật lý lưu ảnh
        $saveDir = \Yii::getAlias('@mediaImages');
        if (!file_exists($saveDir)) {
            mkdir($saveDir, 0777, true);
        }

        $savePath = $saveDir . DIRECTORY_SEPARATOR . $fileName;

        // Nếu ảnh đã tồn tại thì không tải lại
        if (!file_exists($savePath)) {
            $imageContent = file_get_contents($url);
            if ($imageContent === false) {
                return false; // lỗi khi tải ảnh
            }

            file_put_contents($savePath, $imageContent);
        }

        // Trả về link ảnh (ví dụ: /file/media/images/tenfile.jpg)file/media/images
        $imageUrl = \Yii::$app->params['FileDomain'] . '/media/images/' . $fileName;

        return $imageUrl;
    }



    public static function curlCoupon($product_id, $sourceName = '1688')
    {
        $api = 'http://partner.alibo.vn/customer_commission_api/?access_token=7f85ffaa82efce415e66ae4befd619fe';
        switch ($sourceName) {
            case "1688":
                $web = 1;
                break;
            case "pinduoduo":
                $web = 2;
                break;
            case "taobao":
            case "tmall":
            default:
                $web = 0;
                break;
        }

        //change link
        $key_cache = 'chiet_khau_product' . $product_id . $web;
        $cache = \Yii::$app->cache;
        $coupon_short_url = $cache->get($key_cache);

        if (!$coupon_short_url) {
            $url = $api . '&platform=' . $web . '&product_id=' . $product_id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            $res = json_decode($result, true);

            if (isset($res['commission_url'])) {
                $coupon_short_url = $res['commission_url'];
                $cache->set($key_cache, $coupon_short_url, 180);
            }
        }

        return $coupon_short_url;
    }


    public static function getCouponLink($product_id, $sourceName = 'taobao')
    {
        // Define the base API URL
        $apiUrl = 'http://partner.alibo.vn/customer_commission_api/?access_token=7f85ffaa82efce415e66ae4befd619fe';

        switch ($sourceName) {
            case '1688':
                $platform = 1;
                break;
            case 'pinduoduo':
                $platform = 2;
                break;
            case 'taobao':
            default:
                $platform = 0;
                break;
        }

        // Construct the final URL
        $url = $apiUrl . '&platform=' . $platform . '&product_id=' . $product_id;

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Optional, depending on SSL settings

        // Execute cURL request
        $result = curl_exec($ch);

        // Close cURL session
        curl_close($ch);

        // Decode the JSON response
        $response = json_decode($result, true);

        // Check if commission_url exists in the response
        if (isset($response['commission_url'])) {
            return $response['commission_url']; // Return the commission URL
        }

        // If no commission URL found, return a default message
        return '';
    }


    public static $transliterator = self::TRANSLITERATE_LOOSE;


    public static function getSettingByName($name)
    {
        $key = 'setting-config-' . md5(json_encode($name));
        $cache = \Yii::$app->cache;
        $data = $cache->get($key);

        if ($data === false) {
            $result = TbSettings::find()->where(['name' => $name])->select(['name', 'value'])->asArray()->all();
            if (count($result)) {
                foreach ($result as $val) {
                    $data[$val['name']] = $val['value'];
                }
            }

            $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }

        return $data;
    }

    public static function convertUrl($url)
    {
        if (empty($url)) return '';

        $parsed_url = parse_url($url);
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $query = isset($parsed_url['query']) ? $parsed_url['query'] : '';

        switch ($host) {
            case 'm.intl.taobao.com';
                $host = 'item.taobao.com';
                $path = '/item.htm';
                break;
            case 'm.1688.com';
                $host = 'detail.1688.com';
                break;
        }

        parse_str($query, $exploded);

        $domain = "$scheme$user$pass$host$port$path";
        if (!empty($exploded['id'])) {
            $domain .= '?id=' . $exploded['id'];
        }

        return $domain;
    }


    public static function checkOriginAd()
    {
        $allowOrigin = [\Yii::$app->params['adminUrl']];
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if ($origin == '') {
            return true;
        } elseif (in_array($origin, $allowOrigin)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 3600');
            return true;
        }
        return false;
    }

    public static function checkOrigin()
    {
        $allowOrigin = [\Yii::$app->params['baseUrl']];
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if ($origin == '') {
            return true;
        } elseif (in_array($origin, $allowOrigin)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 3600');
            return true;
        }
        return false;
    }

    public static function getCNY($cny_setting, $cny_customer, $cny_order = '')
    {
        $cny_customer = is_numeric($cny_customer) ? $cny_customer : $cny_setting;
        return is_numeric($cny_order) ? $cny_order : $cny_customer;
    }

    public static function updateUserIdentify()
    {
        $cache = \Yii::$app->cache;
        $identify = self::generateUserId();
        $key = 'Key-users-' . $identify;
        $auth_id = \Yii::$app->user->id;
        $uInfo = ['identify' => $identify, 'auth_id' => $auth_id];
        $cache->set($key, $uInfo, \Yii::$app->params['CACHE_TIME']['HOUR']);
    }

    public static function getUserIdentify()
    {
        $cache = \Yii::$app->cache;
        $identify = CommonLib::generateUserId();
        $key = 'Key-users-' . $identify;
        return $cache->get($key);
    }

    public static function generateUserId()
    {
        $remote = new RemoteAddress();
        $ip_address = $remote->getIpAddress();
        $userId = md5($_SERVER['HTTP_USER_AGENT'] . $ip_address); //generate username with browser and ip address
        return $userId;
    }

    public static function seo_friendly_url($string)
    {
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '', $string);
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), '-', $string);
        return strtolower(trim($string, '-'));
    }

    public static function cut_string($str, $limit, $end = '..', $encode = 'UTF-8')
    {
        $str = trim($str);
        if (mb_strlen($str, $encode) > $limit) {
            $limit -= 2;
            $str_prefix = mb_substr($str, 0, $limit, $encode);
            $str_suffix = mb_substr($str, $limit, null, $encode);
            if (mb_strpos($str_suffix, ' ', 0, $encode) == 0) {
                return $str_prefix . $end;
            }
            // Nu sau k t cui b ct khng l du cch
            $lastSpacepos = mb_strrpos($str_prefix, ' ', 0, $encode);
            //echo $lastSpacepos; die;
            if (!$lastSpacepos) return $str_prefix . $end;
            return mb_substr($str_prefix, 0, $lastSpacepos, $encode) . $end;
        }
        return $str;
    }

    public static function verifyCaptcha($response)
    {
        if (empty($response)) return false;

        $secretKey = \Yii::$app->params['CAPTCHA_SECRET_KEY'];
        $ip = $_SERVER['REMOTE_ADDR'];

        $post = ['secret' => $secretKey, 'response' => $response, 'remoteip' => $ip];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $data = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($data, true);
        if (!isset($data['success']) || !$data['success']) {
            return false;
        }

        return true;
    }

    /*translate*/
    public static function Translate($trans, $data)
    {
        //translate
        if (!empty($data['shop_name'])) {
            $data['shop_name'] = $trans->translate(FROM, TO, $data['shop_name']);
        }
        if (!empty($data['title'])) {
            $data['title'] = $trans->translate(FROM, TO, $data['title']);
        }
        if (!empty($data['shop_address'])) {
            $data['shop_address'] = $trans->translate(FROM, TO, $data['shop_address']);
        }
        if (!empty($data['size']) && strlen($data['size']) > 1) {
            $data['size'] = $trans->translate(FROM, TO, $data['size']);
        }
        if (!empty($data['color']) && strlen($data['color']) > 1) {
            $data['color'] = $trans->translate(FROM, TO, $data['color']);
        }
        return $data;
    }

    public static function allowedDomains()
    {
        return ['*', // star allows all domains
            //'https://orderhangtaobao.com',
        ];
    }

    // Does string contain special characters?
    public static function has_special_chars($string)
    {
        return preg_match('/[^a-zA-Z;\d]/', $string, $rs); //them ki tu ;

    }

    /**
     * @return boolean if intl extension is loaded
     */
    protected static function hasIntl()
    {
        return extension_loaded('intl');
    }

    protected static function transliterate($string)
    {
        if (static::hasIntl()) {
            return transliterator_transliterate(static::$transliterator, $string);
        } else {
            return str_replace(array_keys(static::$transliteration), static::$transliteration, $string);
        }
    }

    public static $transliteration = ['' => 'A', '' => 'A', '' => 'A', '' => 'A', '' => 'A', '' => 'A', '' => 'AE', '' => 'C', '' => 'E', '' => 'E', '' => 'E', '' => 'E', '' => 'I', '' => 'I', '' => 'I', '' => 'I', '' => 'D', '' => 'N', '' => 'O', '' => 'O', '' => 'O', '' => 'O', '' => 'O', '' => 'O', '' => 'O', '' => 'U', '' => 'U', '' => 'U', '' => 'U', '' => 'U', '' => 'Y', '' => 'TH', '' => 'ss', '' => 'a', '' => 'a', '' => 'a', '' => 'a', '' => 'a', '' => 'a', '' => 'ae', '' => 'c', '' => 'e', '' => 'e', '' => 'e', '' => 'e', '' => 'i', '' => 'i', '' => 'i', '' => 'i', '' => 'd', '' => 'n', '' => 'o', '' => 'o', '' => 'o', '' => 'o', '' => 'o', '' => 'o', '' => 'o', '' => 'u', '' => 'u', '' => 'u', '' => 'u', '' => 'u', '' => 'y', '' => 'th', '' => 'y',];

    public static function slug($string, $replacement = '-', $lowercase = true)
    {
        $string = self::_utf8($string);
        $string = static::transliterate($string);
        $string = preg_replace('/[^a-zA-Z0-9=\s-]+/u', '', $string);
        $string = preg_replace('/[=\s-]+/u', $replacement, $string);
        $string = trim($string, $replacement);
        return $lowercase ? strtolower($string) : $string;
    }


    const keyEncrypt = 'Khong!$%&^Noi';
    const methodEncrypt = 'AES-256-CBC';

    public static function decryptRijndael($encryptedBase64)
    {
        if (is_null($encryptedBase64)) return null;

        $key = md5(self::TOKEN);
        $iv = md5(md5(self::TOKEN));

        $ciphertext = base64_decode($encryptedBase64);

        $cipher = new Rijndael('cbc');
        $cipher->setBlockLength(256);
        $cipher->setKey($key);
        $cipher->setIV($iv);
        $cipher->disablePadding();

        $decrypted = $cipher->decrypt($ciphertext);

        return rtrim($decrypted, "\0");
    }


    public static function encryptIt($data)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::methodEncrypt));
        $ciphertext = openssl_encrypt($data, self::methodEncrypt, self::keyEncrypt, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $ciphertext);

    }

    public static function decryptIt($encrypted_data)
    {
        $encrypted_data = base64_decode($encrypted_data);
        $iv_length = openssl_cipher_iv_length(self::methodEncrypt);
        $iv = substr($encrypted_data, 0, $iv_length);
        $ciphertext = substr($encrypted_data, $iv_length);

        return openssl_decrypt($ciphertext, self::methodEncrypt, self::keyEncrypt, OPENSSL_RAW_DATA, $iv);

    }

    public static function getCity($id = '')
    {
        $list = [
            0 => '- All -',
            1 => 'Kho VN',
            2 => 'Kho TQ',
        ];

        return isset($list[$id]) ? $list[$id] : $list;
    }

    public static function getListRole($id = '')
    {
        $list = [
            1 => 'Admin',
            2 => 'NV Kho TQ',
            3 => 'NV Kho VN',
            4 => 'NV Đặt hàng',
            5 => 'NV Kế toán',
            10 => 'NV Kinh doanh',
            6 => 'NV Khiếu nại',
            7 => 'CSKH tiếng trung'
        ];

        return isset($list[$id]) ? $list[$id] : $list;
    }

    /*cap nhat lai cach tinh*/
    public static function updateOrder($modelOrder)
    {
        $order = [];
        $data = TbOrders::getOrderDetail($modelOrder->orderID);
        if ($data) {
            foreach ($data as $item) {
                $order[$item['supplierID']][$item['id']] = $item;
            }
        }
        $totalShopPriceTQ = 0;
        $totalQuantity = 0;
        $totalDiscount = 0;
        $totalWeight = 0;
        $totalShipTQ = 0;
        $totalIncurred = 0;
        $totalPhiDV = 0;
        $kgFee = 0;
        if ($order) {
            foreach ($order as $supplierID => $shop) {
                if ($shop) {
                    $shopPriceVn = 0;
                    $shopPriceTQ = 0;
                    $shopQuantity = 0;
                    //duyet shop cap nhat lai du lieu neu co thay doi
                    foreach ($shop as $oDetail) {
                        $oDetail = TbOrdersDetail::findOne($oDetail['id']); //id = id detail
                        //ko tim thay hoac sp het hang khong cong vao gio hang
                        if ($oDetail['status'] == 3) {
                            continue;
                        }
                        $oDetail->unitPriceVn = ($oDetail->unitPrice * $modelOrder->cny);
                        $oDetail->totalPriceVn = $oDetail->unitPriceVn * $oDetail->quantity; //tong tien viet
                        $oDetail->save(false);
                        $shopQuantity += $oDetail['quantity'];
                        $shopPriceVn += $oDetail['totalPriceVn'];
                        $shopPriceTQ += $oDetail['totalPrice'];
                    }
                    //cap nhat bang order supplier
                    $orderSup = TbOrderSupplier::findOne(['orderID' => $modelOrder->orderID, 'supplierID' => $supplierID]);
                    if ($orderSup) {
                        $orderSup->shipmentVn = $orderSup->shipmentFee * $modelOrder->cny; //tinh tien ship cua shop
                        $orderSup->shopPriceTQ = $shopPriceTQ; //tong tien shop TQ
                        $orderSup->shopPriceKg = $orderSup->weight * $modelOrder->weightCharge; //tong tien can nang
                        $orderSup->quantity = $shopQuantity;
                        $orderSup->shopPrice = $shopPriceVn; //tien hang cua shop
                        //tien phi dich vu cua shop = tong tien shop + tien ship
                        $orderSup->discountDeals = $modelOrder->discountDeals;
                        $orderSup->orderFee = self::getFeeSV($shopPriceVn, $modelOrder->discountDeals); //+ $orderSup->shipmentVn
                        //tong tien shop
                        $orderSup->shopPriceTotal = $orderSup->kgFee + $orderSup->shopPrice + $orderSup->orderFee + $orderSup->shopPriceKg + $orderSup->shipmentVn + $orderSup->incurredFee;
                        //tinh tien triet khau cua shop = tien hang + tien ship - thuc te thanh toan
                        $totalActual = $orderSup->shopPriceTQ + $orderSup->shipmentFee;
                        $orderSup->discount = round($totalActual - $orderSup->actualPayment, 2);
                        if ($shopPriceVn == 0) {
                            $orderSup->status = 3; //tong tien shop = 0 => shop het hang

                        }
                        $orderSup->save(false);
                        //cong tong cac shop
                        $totalPhiDV += $orderSup->orderFee;
                        $totalQuantity += $orderSup->quantity; //cong tong so luong tung shop
                        $totalShopPriceTQ += $orderSup->shopPriceTQ;
                        $kgFee += $orderSup->kgFee;
                        $totalWeight += $orderSup->weight;
                        $totalShipTQ += $orderSup->shipmentFee; //tin ship TQ
                        $totalDiscount += $orderSup->discount; //tong chiet khau cac shop
                        $totalIncurred += $orderSup->incurredFee; //tong phi phat sinh shop
                        //shop het hang
                        if ($orderSup->status == 3) {
                            $totalPhiDV = ($totalPhiDV > $orderSup->orderFee ? $totalPhiDV - $orderSup->orderFee : 0);
                            $totalQuantity = ($totalQuantity > $orderSup->quantity ? $totalQuantity - $orderSup->quantity : 0);
                            $totalShopPriceTQ = ($totalShopPriceTQ > $orderSup->shopPriceTQ ? $totalShopPriceTQ - $orderSup->shopPriceTQ : 0);
                            $totalWeight = ($totalWeight > $orderSup->weight ? $totalWeight - $orderSup->weight : 0);
                            $totalShipTQ = ($totalShipTQ > $orderSup->shipmentFee ? $totalShipTQ - $orderSup->shipmentFee : 0);
                            $totalDiscount = ($totalDiscount > $orderSup->discount ? $totalDiscount - $orderSup->discount : 0);
                            $totalIncurred = ($totalIncurred > $orderSup->incurredFee ? $totalIncurred - $orderSup->incurredFee : 0);
                            $kgFee = ($kgFee > $orderSup->kgFee ? $kgFee - $orderSup->kgFee : 0);
                        }
                        $modelOrder = self::getOrderStatus($modelOrder, $orderSup);
                    }
                }
            }
        }
        $modelOrder->totalShip = $totalShipTQ;
        $modelOrder->totalOrderTQ = $totalShopPriceTQ;
        $modelOrder->totalWeight = $totalWeight;
        $modelOrder->totalShipVn = $modelOrder->totalShip * $modelOrder->cny;
        $modelOrder->totalOrder = $modelOrder->totalOrderTQ * $modelOrder->cny;
        $modelOrder->totalIncurred = $totalIncurred; //tong phi phat sinh
        $modelOrder->totalQuantity = $totalQuantity; // tong san pham
        //tong phi dich vu = (tien hang + phi ship)/%dv
        $modelOrder->orderFee = $totalPhiDV;
        $modelOrder->totalDiscount = $totalDiscount; //tong tien chiet khau TQ
        $modelOrder->totalDiscountVn = $totalDiscount * $modelOrder->cny; //tong tien chiet khau TQ
        //tinh phi giam gia can nang
        $modelOrder->totalWeightPrice = $modelOrder->totalWeight * $modelOrder->weightCharge;
        //  var_dump($modelOrder->totalWeight);var_dump($modelOrder->weightCharge);die('dgdf');
        $modelOrder->totalWeight = (double)$modelOrder->totalWeight;
        //tinh kiem dong go
        if ($modelOrder->isBox == 1) {
            $boxFee = ($modelOrder->totalWeight * 3000) + 60000;
        } else {
            $boxFee = 0;
        }
        $modelOrder->phidonggo = $boxFee;
        //phi kiem dem
        if ($modelOrder->isCheck == 1) {
            $phikiemhang = self::getFeeCheck($modelOrder->totalQuantity) * $modelOrder->totalQuantity;
        } else {
            $phikiemhang = 0;
        }
        //var_dump($phikiemhang);die;
        $modelOrder->phikiemhang = $phikiemhang; // 5771760 |5741760
        /*var_dump($boxFee);
        var_dump($kgFee);
        var_dump($modelOrder->orderFee);
        var_dump($modelOrder->totalShipVn);
        var_dump($modelOrder->totalWeightPrice);
        var_dump($modelOrder->totalIncurred);die;*/
        //tong tien don hang
        $modelOrder->totalPayment = $modelOrder->phikiemhang + $boxFee + $kgFee + $modelOrder->totalOrder + $modelOrder->orderFee + $modelOrder->totalShipVn + $modelOrder->totalWeightPrice + $modelOrder->totalIncurred; //tong tien don hang
        //cap nhat lai so tien con thieu
        $modelOrder->debtAmount = ($modelOrder->totalPayment > $modelOrder->totalPaid ? $modelOrder->totalPayment - $modelOrder->totalPaid : 0);
        //            var_dump($modelOrder->debtAmount);die;
        //chiet khau % dich vu don hang cho kinh doanh
        $modelOrder = self::getBusinessFee($modelOrder);
        //tinh tien chiet khau cho nv
        //discountRate = % chiet khau
        //discountKg = chiet khau can nang
        //ck dv = (%ck) * (thuong luong + dv)
        $modelOrder->discountBusiness = ($modelOrder->totalDiscountVn + $totalPhiDV) * $modelOrder->discountRate / 100 + ($modelOrder->discountKg * $modelOrder->totalWeight);
        $modelOrder->save(false);
        return $modelOrder;
    }

    //tinh phi dich vu
    public static function getFeeSV($total, $percent = 0)
    {
        if ($percent > 0) {
            return round($total * $percent / 100);
        }
        return 0;
    }

    public static function roles()
    {
        return [
            1 => 'Admin',
            2 => 'Nhân Viên Kế Toán',
            3 => 'Nhân viên thanh toán',
            4 => 'Nhân viên kho',
            10 => 'Nhân viên kinh doanh'
        ];
    }

    /* Dch v bo him*/
    public static function getInsuranceFees($totalPrice)
    {
        return ($totalPrice * 5) / 100;
    }

    /*dich vu kiem dem*/
    public static function getChecksum($quantity)
    {
        if ($quantity >= 1 && $quantity <= 4) $return = 5000;
        elseif ($quantity >= 5 && $quantity <= 15) $return = 3500;
        elseif ($quantity >= 16 && $quantity <= 100) $return = 2000;
        elseif ($quantity >= 101 && $quantity <= 500) $return = 1500;
        else $return = 1000;
        return $return;
    }

    /*phi DCH V MUA HNG(phi dat hang)*/
    public static function getFeeDV($price, $provinID)
    {
        if (is_null($price)) return $price;
        if ($price <= 0) return 0;
        $model = TbService::find()->select('percent')->where(['<=', 'from', $price])->andWhere(['>', 'to', $price]);
        if ($provinID) {
            $model->andWhere(['provinID' => (int)$provinID]);
        }
        $data = $model->asArray()->one();
        if ($data) {
            return $data['percent'];
        }
        return 0;
    }

    /*phi ty gia*/
    public static function getCNY_TABLE($totalPrice)
    {
        if ((double)$totalPrice <= 0) return null;
        $query = Cny::find()->select('cny')->where(['<=', 'from', $totalPrice])->andWhere(['>', 'to', $totalPrice]);
        $data = $query->asArray()->one();
        if ($data) {
            return $data['cny'];
        }
        return 0;
    }

    /*phi kg*/
    public static function getFeeKg($kg, $provinID = '')
    {
        if ((double)$kg <= 0) return null;
        $query = TbKg::find()->select('price')->where(['<=', 'from', $kg])->andWhere(['>', 'to', $kg]);
        if (!empty($provinID)) {
            $query->andWhere(['provinID' => $provinID]);
        }
        $data = $query->asArray()->one();
        if ($data) {
            return $data['price'];
        }
        return 0;
    }

    //phi kiem dem
    public static function getFeeCheck($quantity)
    {
        if ($quantity <= 0) return 0;
        $data = Check::find()->select('price')->where(['<=', 'from', $quantity])->andWhere(['>', 'to', $quantity])->asArray()->one();
        if ($data) {
            return $data['price'];
        }
        return 0;
    }

    /*trang thai giao dich*/
    public static function getStatus($status = 0)
    {
        $arrStatus = [0 => 'Chờ duyệt', 1 => 'Đang xử lý', 2 => 'Đã hoàn thành', 3 => 'Đã hủy'];

        return $status ? $arrStatus[$status] : $arrStatus;
    }

    public static function listCustomer()
    {
        $user = TbCustomers::find()->select(['id', 'username'])->where(['status' => 1])->asArray()->all();
        $data = [];
        if ($user) {
            foreach ($user as $item) {
                $data[$item['id']] = $item['username'];
            }
        }
        return $data;
    }

    public static function listUserByUsername($role = 0, $role_not = [])
    {
        $query = User::find()->select(['id', 'username', 'fullname', 'role'])->where(['status' => 1]);
        if ($role) {
            $query = $query->andWhere(['role' => $role]);
        }
        if ($role_not) {
            $query->andWhere(['not in', 'role', $role_not]);
        }
        $user = $query->asArray()->all();
        $data = [];
        if ($user) {
            foreach ($user as $item) {
                $data[$item['id']] = $item['username'];
            }
        }
        return $data;
    }

    public static function listUser($role = 0, $role_not = [])
    {
        $query = User::find()->select(['id', 'username', 'fullname', 'role'])->where(['status' => 1]);
        if ($role) {
            $query = $query->andWhere(['role' => $role]);
        }
        if ($role_not) {
            $query->andWhere(['not in', 'role', $role_not]);
        }
        $user = $query->asArray()->all();
        $data = [];
        if ($user) {
            foreach ($user as $item) {
                $data[$item['id']] = $item['fullname'];
            }
        }
        return $data;
    }

    public static function statusProduct($status = 0)
    {
        $result = [
            1 => 'Bình thường',
            2 => 'Còn hàng',
            3 => 'Hết hàng'
        ];
        if (is_null($status))
            $status = 1;

        if ($status) {
            return $result[$status];
        }

        return $result;
    }

    public static function getBarcodeDropdown($shipStatus = 0)
    {

        $result = [
            0 => 'Shop xưởng giao',
            2 => 'Đang vận chuyển',
            3 => 'Kho VN',
            5 => 'Đã trả hàng',
        ];

        return ($shipStatus) ? $result[$shipStatus] : $result;

    }

    public static function statusText($status = 0)
    {
        $result = [
//            7 => 'Chờ báo giá',
            1 => 'Chờ đặt cọc',
            11 => 'Đã đặt cọc',
            2 => 'Đang đặt hàng',
            3 => 'Đã đặt hàng',
            4 => 'Shop xưởng giao',
            5 => 'Đã hủy',
            6 => 'Đã trả hàng',
            8 => 'Đang vận chuyển',
            9 => 'Kho VN nhận',
        ];

        return ($status) ? $result[$status] : $result;
    }

    /*trang thai hang*/
    public static function statusOnWay($status = 0)
    {
        $result = [
            0 => 'Không xác định',
            1 => 'Đang trên đường',
        ];

        return ($status) ? $result[$status] : $result;
    }

    //tinh trang hang
    public static function statusOrder($status = 0)
    {
        $result = [
            0 => 'Đang giao dịch',
            1 => 'Đã thanh toán',
            2 => 'Trả hàng',
            3 => 'Khiếu nại',
            4 => 'Hết hàng',
        ];

        return ($status) ? $result[$status] : $result;
    }

    /*trang thai ship cua shop*/
    public static function getShippingStatusByShop($shipStatus = 0)
    {
        $shipStatus = (int)$shipStatus;
        switch ($shipStatus) {
            case 0:
                return '<span class="label label-warning">Shop xưởng giao</span>';
                break;
            case 2:
                return '<span class="label label-info">Đang vận chuyển</span>';
                break;
            case 3:
                return '<span class="label label-primary">Kho VN</span>';
                break;
            case 4:
                return '<span class="label label-danger">Đang trên đường</span>';
                break;
            case 5:
                return '<span class="label label-success">Đã trả hàng</span>';
                break;
        }

        return null;
    }

    //phuong thuc thanh toan
    public static function paymentType($status = -1)
    {
        switch ($status) {
            case 1:
                return 'Hàng ký gửi';
                break;
            case 2:
                return 'Hàng Order';
                break;

            default:
                $result = [
                    1 => 'Hàng ký gửi',
                    2 => 'Hàng Order',
                ];
        }


        return $result;
    }

    //hinh thuc giao hang
    public static function paymentStatus($status = -1)
    {
        switch ($status) {
            case 0:
            case null:
                return '';
                break;
            case 1:
                return 'Nhận tại kho';
                break;
            case 2:
                return 'Giao hàng tận nơi';
                break;
            case 3:
                return 'Chuyển phát nhanh';
                break;
            case 4:
                return 'Gửi vận tải';
                break;
            default:
                $result = [
                    1 => 'Nhận tại kho',
                    2 => 'Giao hàng tận nơi',
                    3 => 'Chuyển phát nhanh',
                    4 => 'Gửi vận tải',
                ];
        }


        return $result;
    }

    //tinh trang ship cua don dropdown
    public static function statusShippingText($status = null)
    {

        $result = [
            0 => 'Chưa ship',
            2 => 'Đang vận chuyển',
            3 => 'Kho VN',
//          4 => 'Shop xưởng giao',
            5 => 'Đã trả hàng',
        ];

        return isset($result[$status]) ? $result[$status] : $result;
    }

    public static function getStatusShipper($shipStatus = 0)
    {
        $shipStatus = (int)$shipStatus;
        switch ($shipStatus) {
            case 0:
                return '<span class="label label-warning">Chưa ship</span>';
                break;
            case 2:
                return '<span class="label label-info">Đang vận chuyển</span>';
                break;
            case 3:
                return '<span class="label label-primary">Kho VN</span>';
                break;
//            case 4:
//                return '<span class="label label-danger">Shop xưởng giao</span>';
//                break;
            case 5:
                return '<span class="label label-finish">Đã trả hàng</span>';
                break;
        }

        return null;
    }

    /*hien thi label trang thai van chuyen cua don hang*/
    public static function getStatusShipping($status = 0)
    {
        switch ($status) {
            case 0:
                return '<span class="label label-warning">Chưa ship</span>';
                break;
            /*case 1:
                return '<span class="label label-success">Đã ship</span>';
                break;*/
            case 2:
                return '<span class="label label-info">Đang vận chuyển</span>';
                break;
            case 3:
                return '<span class="label label-primary">Kho VN nhận</span>';
                break;
            case 4:
                return '<span class="label label-danger">Shop xưởng giao</span>';
                break;
        }

        return null;
    }

    //icon th giao dich
    public static function getStatusAcounting($status = 0)
    {
        switch ($status) {
            case 0:
                return '<span class="label label-warning">Chờ duyệt</span>';
                break;
            case 2:
                return '<span class="label label-success">Hoàn thành</span>';
                break;
            case 1:
                return '<span class="label label-danger">Đang xử lý</span>';
                break;
            case 3:
                return '<span class="btn bg-navy margin" >Đã Hủy</span>';
                break;
        }
    }


    public static function rechargeType($type = 0)
    {
        $transaction = [
            1 => 'Nạp tiền',
            2 => 'Rút tiền',
            3 => 'Thanh toán đơn hàng',
            4 => 'Đặt cọc đơn hàng',
            5 => 'Tất toán đơn hàng',
            6 => 'Hoàn lại tiền',
            8 => 'Đặt cọc yêu cầu giao',
        ];


        return isset($transaction[$type]) ? $transaction[$type] : $transaction;
    }

    public static function getImage($src, $w = 100, $h = 100, $resize = true)
    {
        $width = 'width="' . $w . '" height="' . $h . '"';
        return '<img src="' . \Yii::$app->params['FileDomain'] . $src . '" ' . ($resize ? $width : '') . ' />';
    }

    public static function getAllCate()
    {
        $key = TbCategory::tableName() . '-All';
        $cache = \Yii::$app->cache;
        $result = $cache->get($key);
        if ($result === false) {
            $arrCates = CategoryModel::getAllCategory(TbCategory::className());
            if (count($arrCates)) {
                foreach ($arrCates as $val) {
                    $arrCate[$val['parent_id']][] = $val;
                    $arrCateAllKey[$val['slug']] = $val;
                    $arrCateAllId[$val['category_id']] = $val;
                    $arrParent[$val['parent_id']][] = $val['category_id'];
                }
                $result['all_slug'] = isset($arrCateAllKey) ? $arrCateAllKey : '';
                $result['all_id'] = isset($arrCateAllId) ? $arrCateAllId : '';
                $result['parent_id'] = isset($arrParent) ? $arrParent : '';
                $result['data'] = isset($arrCate) ? $arrCate : '';
                $result['first_id'] = $arrCates[0]['category_id'];
            }
            $cache->set($key, $result, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }
        return $result;
    }

    public static function getAllSettings()
    {
        $key = 'setting-cache';
        $cache = \Yii::$app->cache;
        if (($data = $cache->get($key)) === false) {
            $result = TbSettings::find()->select(['name', 'value'])->asArray()->all();
            if (count($result)) {
                foreach ($result as $val) {
                    $data[$val['name']] = $val['value'];
                }
            }
            $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }
        /*$result = TbSettings::find()->select(['name', 'value'])->asArray()->all();
        if (count($result)) {
            foreach ($result as $val) {
                $data[$val['name']] = $val['value'];
            }
        }*/
        return $data;
    }

    public static function getAllMenu()
    {
        $key = 'TB_Menus:ALL';
        $cache = \Yii::$app->cache;
        $data = $cache->get($key);
        if ($data === false) {
            $arrCates = TbMenu::getMenu();
            if ($arrCates) {
                foreach ($arrCates as $val) {
                    $arrCate[$val['parent_id']][] = $val;
                    $arrCateAllKey[$val['slug']] = $val;
                    $arrCateAllId[$val['category_id']] = $val;
                    $arrRouter[$val['parent_id']][] = $val['redirect'];
                    $control[$val['parent_id']][] = $val['control'];
                    $arrCateData[$val['cate_id']] = $val;
                }
                $data['all_slug'] = isset($arrCateAllKey) ? $arrCateAllKey : '';
                $data['all_id'] = isset($arrCateAllId) ? $arrCateAllId : '';
                $data['cate_id'] = isset($arrCateData) ? $arrCateData : '';
                $data['data'] = isset($arrCate) ? $arrCate : '';
                $data['redirect'] = isset($arrRouter) ? $arrRouter : '';
                $data['control'] = isset($control) ? $control : '';
                $data['first_id'] = $arrCates[0]['category_id'];
            }
            $cache->set($key, $data, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }
        return $data;
    }

    public static function _utf8($str)
    {
        if (!$str) return false;
        $utf8 = array('a' => '|||||||||||||||||||||||||||||||||', 'd' => '|', 'e' => '|||||||||||||||||||||', 'i' => '|||||||||', 'o' => '|||||||||||||||||||||||||||||||||', 'u' => '|||||||||||||||||||||', 'y' => '|||||||||',);
        foreach ($utf8 as $ascii => $uni) $str = preg_replace("/($uni)/i", $ascii, $str);
        $patern = '[\W+]';
        return preg_replace($patern, ' ', strtolower($str));
    }

    public static function pr($str)
    {
        echo "<pre>";
        print_r($str);
        echo "</pre>";
        //die;

    }

    public static function downloadImageUrl($url, $slug)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        $file = fopen($url, "rb");
        if ($file) {
            $thumb = '';
            $directory = \Yii::getAlias('@upload_dir') . DIRECTORY_SEPARATOR . Upload::$UPLOADS_DIR . DIRECTORY_SEPARATOR . "downloads/";
            $valid_exts = array("jpg", "jpeg", "gif", "png"); // default image only extensions
            $tmp = explode('.', basename($url));
            $ext = end($tmp);
            $is_ok = preg_match("/(" . implode($valid_exts, "|") . ")/", $ext, $rs);
            if ($is_ok) {
                $filename = $slug . '.' . $rs[0];
                $newfile = fopen($directory . $filename, "wb"); // creating new file on local server
                if ($newfile) {
                    while (!feof($file)) {
                        // Write the url file to the directory.
                        fwrite($newfile, fread($file, 1024 * 8), 1024 * 8); // write the file to the new directory at a rate of 8kb/sec. until we reach the end.

                    }
                    $thumbWebFile = '/' . Upload::$UPLOADS_DIR . '/downloads/' . $filename;
                    if (file_exists($directory . $filename)) {
                        $thumb = $thumbWebFile;
                    }
                }
            }
            return $thumb;
        }
        return false;
    }

    public static function removeLink($html)
    {
        return preg_replace("/href=\"(.*?)\"/s", "href=\"\"", $html);
    }

    public static function start($count = 0, $char = '-')
    {
        $str = '';
        if ($count) {
            for ($i = 0; $i < $count; $i++) $str .= $char;
        }
        return $str;
    }

    public static function redirectError()
    {
        return \Yii::$app->response->redirect(Url::to('site/error'), 302);
    }

    public static function DropDownList($form_id, $data, $parent, $title = '-- Mục gốc --')
    {
        $html = ' <select class="form-control" id="Tbnews-category_id" name="' . $form_id . '">
                       <option value="" class="smooth">' . $title . '</option>';
        foreach ($data as $node) :
            $style = 'style = ";padding-left: ' . ($node->depth > 0 ? $node->depth * 20 : '5') . 'px"';
            $html .= '<option ' . $style . '  value="' . $node->category_id . '" ' . ($parent == $node->category_id ? 'selected' : '') . ' >';
            $html .= self::start($node->depth * 3) . ' ' . $node->title . '</option>';
        endforeach;
        $html .= '</select>';

        return $html;
    }

    public static function getRandomInt($number = 5)
    {
        return substr(uniqid(rand(1, 10000), true), 0, $number);
    }

    public static function getRandNumber($amount = 1)
    {
        $previousValues = array();
        for ($i = 0; $i < $amount; $i++) {
            $rand = rand(0, 200);
            while (in_array($rand, $previousValues)) {
                $rand = rand(0, 200);
            }
            $previousValues[] = $rand;
            $unique = $rand;
            return $unique;
        }
    }

    public static function getChartQuarter($year, $field = '', $model = '', $status = 6)
    {
        $arrYear = [$year - 1, $year];
        $data = [];
        foreach ($arrYear as $cyear) {
            $arr = [];
            for ($i = 1; $i <= 4; $i++) {
                $quy = self::getQuarter($i, $cyear, $field, $model, $status);
                $arr[$i - 1] = ['y' => $cyear . ' Q' . $i, 'value' => $quy];
            }
            $data = array_merge($data, $arr);
        }
        return $data;
    }

    public static function getChart($model, $field, $status = 6)
    {
        $query = (new \yii\db\Query())->from(TbOrders::tableName() . ' o');
        switch ($status) {
            case 6:
                $query->where(['o.status' => $status]); //tim don hang da hoan thanh hoac da tra hang de thong ke

                break;
            case 1:
                $query->where(['!=', 'o.status', $status])->andWhere(['>', 'o.debtAmount', 0]);
                break;
        }
        if ($model->businessID) {
            $query->andWhere(['or', ['o.businessID' => $model->businessID], ['o.orderStaff' => $model->businessID]]);
        }
        /*if($model->orderStaff){
             $query->andFilterWhere(['o.orderStaff'=>$model->orderStaff]);
        }
        if($model->businessID){
             $query->andFilterWhere(['o.businessID'=>$model->businessID]);
        }*/
        $startDate = $endDate = '';
        if (!empty($model->startDate)) {
            $startDate = str_replace('/', '-', $model->startDate);
        }
        if (!empty($model->endDate)) {
            $endDate = str_replace('/', '-', $model->endDate);
        }
        $finterDate = 'o.paymentDate';
        if (!empty($startDate) && !empty($endDate)) {
            $startDate = date('Y-m-d H:i:s', strtotime($startDate));
            $endDate = date('Y-m-d H:i:s', strtotime($endDate) + 86400 - 1);
            $query->andFilterWhere(['>=', $finterDate, $startDate])->andFilterWhere(['<=', $finterDate, $endDate]);
        } elseif (!empty($startDate) && empty($endDate)) {
            $startDate = date('Y-m-d H:i:s', strtotime($startDate));
            $endDate = date('Y-m-d H:i:s', strtotime($startDate) + 86400 - 1);
            $query->andFilterWhere(['>=', $finterDate, $startDate])->andFilterWhere(['<=', $finterDate, $endDate]);
        }
        if ($field == 'actualPayment') {
            $query->innerJoin(TbOrderSupplier::tableName() . ' s', 'o.orderID = s.orderID');
            return $query->sum('s.' . $field);
        }
        return $query->sum('o.' . $field);
    }

    public static function getQuarter($quarter = 1, $year, $field = '', $model = '', $status = 6)
    {
        $sum = 0;
        switch ($quarter) {
            case 1:
                $startDate = $year . '-01-01';
                $endDate = $year . '-04-01';
                break;
            case 2:
                $startDate = $year . '-04-01';
                $endDate = $year . '-07-01';
                break;
            case 3:
                $startDate = $year . '-07-01';
                $endDate = $year . '-10-01';
                break;
            case 4:
                $startDate = $year . '-10-01';
                $endDate = $year + 1 . '-01-01';
                break;
        }
        $query = (new \yii\db\Query())->from(TbOrders::tableName() . ' o');
        switch ($status) {
            case 6:
                $query->where(['o.status' => $status]); //tim don hang da hoan thanh hoac da tra hang de thong ke

                break;
            case 1:
                $query->where(['!=', 'o.status', $status])->andWhere(['>', 'o.debtAmount', 0]);
                break;
        }
        if (!empty($startDate) && !empty($endDate)) {
            $query->andWhere(['>=', 'o.paymentDate', $startDate])->andWhere(['<', 'o.paymentDate', $endDate]);
        }
        if ($model->businessID) {
            $query->andWhere(['or', ['o.businessID' => $model->businessID], ['o.orderStaff' => $model->businessID]]);
        }
        /* if($model->orderStaff){
             $query->andFilterWhere(['o.orderStaff'=>$model->orderStaff]);
        }
        if($model->businessID){
             $query->andFilterWhere(['o.businessID'=>$model->businessID]);
        }*/
        if ($field == 'actualPayment') {
            $query->innerJoin(TbOrderSupplier::tableName() . ' s', 'o.orderID = s.orderID');
            $sum = $query->sum('s.' . $field);
            $sum = round($sum, 2);
        } else {
            $sum = (int)$query->sum('o.' . $field);
        }
        return $sum;
    }

    public static function isOderNumber($orderID)
    {
        return TbOrderSupplier::find()->select('id')->where(['orderID' => $orderID])->andWhere(['or', ['shopProductID' => null], ['billLadinID' => null]])->count();
    }

    public static function isNotTQ($orderID)
    {
        return TbOrderSupplier::find()->select('id')->where(['orderID' => $orderID])->andWhere(['<', 'shippingStatus', 2])
            //                ->andWhere(['or',['<>','shippingStatus',2],['<>','shippingStatus',3]])
            ->count();
    }

    public static function isNotVN($orderID)
    {
        return TbOrderSupplier::find()->select('id')->where(['orderID' => $orderID])->andWhere(['<', 'shippingStatus', 3])
            //                ->andWhere(['or',['<>','shippingStatus',2],['<>','shippingStatus',3]])
            ->count();
    }

    public static function getOrderWeight($orderID)
    {
        return TbOrderSupplier::find()->select(['weight'])->where(['orderID' => $orderID, 'weight' => ''])->count();
    }

    public static function hasIt($q)
    {
        if (is_null($q)) return null;
        $qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(self::TOKEN), base64_decode($q), MCRYPT_MODE_CBC, md5(md5(self::TOKEN))), "");
        return ($qDecoded);
    }

    public static function AES_encrypt($string, $AES_Key, $AES_IV)
    {
        $key = base64_decode($AES_Key);
        $iv = base64_decode($AES_IV);
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, self::addpadding($string), MCRYPT_MODE_CBC, $iv));
    }

    public static function AES_decrypt($string, $AES_Key, $AES_IV)
    {
        $key = base64_decode($AES_Key);
        $iv = base64_decode($AES_IV);
        $string = base64_decode($string);
        return self::strippadding(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, $iv));
    }

    public static function addpadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    public static function strippadding($string)
    {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

    public static function getIconStatus($status)
    {
        switch ($status) {
            case 1: //da thanh toan
                return '<span data-toggle="tooltip" title="Đã thanh toán" class="pdstatus btn btn-success btn-xs">Đã thanh toán</span>';
                break;
            case 2: //da bao gia
                return '<span data-toggle="tooltip" title="Đã báo giá" class="pdstatus btn btn-danger btn-xs">Đã báo giá</span>';
                break;
            case 3: //het hang
                return '<span data-toggle="tooltip" title="Hết hàng" class="pdstatus btn bg-black btn-xs">Hết hàng</span>';
                break;
            case 4: //da tra hang
                return '<span data-toggle="tooltip" title="Đã trả hàng" class="pdstatus btn bg-purple btn-xs">Đã trả hàng</span>';
                break;
        }

        return '';
    }

    public static function checkOrderAlert($orderID, $createDate)
    {
        $now = time(); // or your date as well
        $datediff = $now - strtotime($createDate);
        $dayAllow = round($datediff / (60 * 60 * 24));
        $allShop = TbOrderSupplier::find()->select(['billLadinID', 'shopProductID', 'shippingStatus'])->where(['orderID' => $orderID])->all();
        $isWarning = false;
        $msg = '';
        if ($allShop) {
            foreach ($allShop as $item) {
                //not mavd
                if (!is_null($item->shopProductID) && is_null($item->billLadinID) && ($dayAllow > 2)) {
                    $isWarning = true;
                    $msg = '<div class="red-color"><i>Đã quá 2 ngày chưa có mã vận đơn</i></div>';
                    break;
                } else if (!is_null($item->shopProductID) && (!is_null($item->billLadinID)) && ($dayAllow > 5) && $item->shippingStatus != 3) {
                    $isWarning = true;
                    $msg = '<div class="red-color"><i>Đã quá 5 ngày kho Việt Nam chưa xác nhận</i></div>';
                    break;
                }
            }

            $modelOrder = TbOrders::findOne(['orderID' => $orderID]);
            //cap nhat trang thai don hang co canh bao active = 1
            if ($isWarning) {
                $modelOrder->active = 1;
                $modelOrder->save();

                return $msg;
            } else {
                $modelOrder->active = 0;
                $modelOrder->save();
            }
        }

        return '';
    }

    public static function getOrderStatus($currentOrder, $orderSupplier)
    {
        // Nguonhang
        // if ($currentOrder->status == 11 && !empty($orderSupplier->shopProductID) && !in_array($currentOrder->status,[2,3,4,5,6,8,9])) {
        //     $currentOrder->status = 2;
        // }
        //
        //da coc va co ma order number => dang dat hang
        //other
        if ($currentOrder->status == 11 && !in_array($currentOrder->status, [2, 3, 4, 5, 6, 8, 9])) {
            $currentOrder->status = 2;
        }
        //TH da co ma order number =>  da dat hang
        if ($currentOrder->status == 2 && !empty($orderSupplier->shopProductID) && !in_array($currentOrder->status, [3, 4, 5, 6, 8, 9])) {
            $currentOrder->status = 3;
            if ((strtotime($currentOrder->shipDate) <= 0 || empty($currentOrder->shipDate))) {
                $currentOrder->shipDate = date('Y-m-d H:i:s'); //ngay dat hang

            }
        }
        // Nguonhang
        /*if ($currentOrder->status == 11 && !empty($orderSupplier->shopProductID) && !in_array($currentOrder->status,[2,3,4,5,6,8,9])) {
            $currentOrder->status = 2;
        }*/
        //
        //shop xuong giao  //kiem tra shop co ma van don
        $modelTransfer = TbTransfercode::find()->select('shipStatus')->where(['orderID' => $currentOrder->orderID])->asArray()->all();
        $isKhoTQ = false;
        $isKhoVN = false;
        if ($modelTransfer) {
            foreach ($modelTransfer as $val) {
                if ($val['shipStatus'] == 2) {
                    //kho TQ
                    $isKhoTQ = true;
                }
                if ($val['shipStatus'] == 3) {
                    //kho vn
                    $isKhoVN = true;
                }
            }
        }
        // var_dump($currentOrder->deliveryDate);die;
        if ($currentOrder->status == 3 && $modelTransfer && !in_array($currentOrder->status, [4, 5, 6, 8, 9])) {
            $currentOrder->status = 4;
            if ((strtotime($currentOrder->deliveryDate) <= 0 || empty($currentOrder->deliveryDate))) {
                $currentOrder->deliveryDate = date('Y-m-d H:i:s'); //shop giao

            }
        }
        //dang van chuyen khi ve kho TQ
        if ($currentOrder->status == 4 && $modelTransfer && $isKhoTQ && !in_array($currentOrder->status, [5, 6, 8, 9])) {
            $currentOrder->status = 8;
            if ((strtotime($currentOrder->shippingDate) <= 0 || empty($currentOrder->shippingDate))) {
                $currentOrder->shippingDate = date('Y-m-d H:i:s'); //kho tq

            }
        }
        //kho vn nhan
        if ($modelTransfer && $isKhoVN && !in_array($currentOrder->status, [5, 6, 9])) {
            $currentOrder->status = 9;
            if ((strtotime($currentOrder->vnDate) <= 0 || empty($currentOrder->vnDate))) {
                $currentOrder->vnDate = date('Y-m-d H:i:s'); //kho vn

            }
        }
        //het hang
        if ($orderSupplier->status == 3) {
            $currentOrder->status = 5;
            if ((strtotime($currentOrder->finshDate) <= 0 || empty($currentOrder->finshDate))) {
                $currentOrder->finshDate = date('Y-m-d H:i:s');
            }
        }
        return $currentOrder;
    }

    //add new
    public static function getAvatar($fileUrl, $width = 150, $height = 'auto')
    {
        if (empty($fileUrl)) {
            $fileUrl = \Yii::$app->params['FileDomain'] . '/media/avatar/default_user_icon.png';
        }
        if (!empty($fileUrl) && !preg_match('/http:.*|https:.*/', $fileUrl)) {
            $fileUrl = \Yii::$app->params['FileDomain'] . $fileUrl;
        }
        return '<img class="img-circle" src="' . $fileUrl . '" width = "' . $width . 'px" height="' . $height . 'px" />';
        //        return $fileUrl;

    }

    public static function getPercentDeposit($price, $customerID = '', $deposit = '')
    {
        if (!empty($deposit)) return $deposit;
        $customer = TbCustomers::findOne($customerID);
        if (!$customer || $price <= 0) return 0;
        if (isset($customer->deposit) && (int)$customer->deposit > 0) {
            return $customer->deposit;
        }
        /*if (in_array($customer->username, ['honganh2301', 'thanhnhan13', 'cabasports'])) {
            return 50;
        }*/
        $kgsFee = Deposit::find()->select('percent')->where(['<=', 'from', $price])->andWhere(['>', 'to', $price])->asArray()->one();
        if ($kgsFee) {
            return $kgsFee['percent'];
        }
        return 0;
    }

    /*update orderID vao bang transfercode*/
    public static function updateOrderIDToTranfercode()
    {
        $data = TbTransfercode::findBySql('SELECT a.*,b.orderID,b.identify,b.businessID FROM tb_transfercode a INNER JOIN tb_orders b ON a.identify = b.identify')->asArray()->all();
        if ($data) {
            foreach ($data as $item) {
                //update tranfercode set orderid
                $tbTranfer = TbTransfercode::findOne(['id' => $item['id']]);
                if ($tbTranfer) {
                    $tbTranfer->orderID = $item['orderID'];
                    $tbTranfer->businessID = $item['businessID'];
                    $tbTranfer->save(false);
                }
            }
            return true;
        }
        return false;
    }

    //update to shipping
    public static function updateOrderIDToShipping()
    {
        $sql = 'SELECT `a`.*, `b`.`orderID`, b.`transferID` FROM `tb_shipping` `a`
                        LEFT JOIN `tb_transfercode` `b` ON a.tranID = b.id
                        LEFT JOIN `tb_orders` `o` ON b.orderID = o.orderID
                        WHERE `a`.`status`=1 AND b.`orderID` IS NULL';
        $data = TbTransfercode::findBySql($sql)->asArray()->all();
        if ($data) {
            foreach ($data as $item) {
                //update tranfercode set orderid
                $tbShipping = TbShipping::findOne(['id' => $item['id']]);
                if ($tbShipping) {
                    $tbShipping->userID = \Yii::$app->user->id;
                    $tbShipping->shopID = null;
                    $tbShipping->tranID = null;
                    $tbShipping->status = 0;
                    $tbShipping->city = (\Yii::$app->user->identity->role == WAREHOUSETQ ? 2 : 1);
                    $tbShipping->save(false);
                }
            }
            return true;
        }
        return false;
    }

    /*
     ** Author:HAIHS
     ** Content: update histor, thanh toan tra hang
     ** CreateDate: 126-05-2018 14:24
     * $listBarcode danh sach ma tra hang
    */
    public static function PaymentShop($currentOrder, $currentShop, $bank, $listBarcode)
    {
        /*xu ly kiem thanh toan shop*/
        //TH da dat coc
        //tien coc tinh theo shop
        if ($currentShop->totalPaid == 0 || empty($currentShop->totalPaid)) {
            $currentShop->totalPaid = round($currentShop->shopPrice * $currentOrder->perCent / 100);//gan lai tong coc ban dau
        }

        //tinh tien thieu cua shop
        $tienThieu = $currentShop->totalPaid < $currentShop->shopPriceTotal ? $currentShop->shopPriceTotal - $currentShop->totalPaid : 0;
        //kiem tra vi dien tu
        //$bank = TbAccountBanking::findOne(['customerID' => $currentOrder->customerID]);
        if (!$bank || ($tienThieu && $bank && ($tienThieu > $bank->totalResidual)) || $bank->totalResidual <= 0) {
            /*
             * TH chua co tai khoan hoac so tien trong tai khoan hien tai khong du thanh toan
             * */
            return false;
        }

        if ($tienThieu) {
            if ($bank->totalResidual) {
                $bank->totalResidual -= $tienThieu; //lay tien trong tai khoan tru di tien con thieu
                $bank->totalPayment += $tienThieu; //cap nhat tong tien da thanh toan vai vi
            }
            if ($bank->save(false)) {
                if ($currentOrder->debtAmount >= $tienThieu) { //neu dang con no
                    $currentOrder->debtAmount -= $tienThieu; //tong tien con no - so tien coc con thieu
                } else if ($currentOrder->totalPayment >= $currentOrder->totalPaid) {
                    $currentOrder->debtAmount = $currentOrder->totalPayment - $currentOrder->totalPaid;
                }
                //sau khi tra hang tru di so tien coc cho shop thi cong lai tong so tien da coc
                $currentOrder->totalPaid += $tienThieu; //cong so tien coc
                $currentOrder->save(false);
//                $currentShop->status = 4;
//                $currentShop->paymentDate = date('Y-m-d H:i:s');
                //cap nhat trang thai shop tra hang
                if ($currentShop->save(false)) {
                    //kiem tra ma da ve kho vn hoac da tra hang thi cap nhat ngay tra
                    //kiem tra xem da tra bao nhieu ma
                    $totalMvd = \common\models\TbTransfercode::find()->select('transferID')->where(['orderID' => $currentOrder->orderID])->count();
                    if ($listBarcode) {
                        //update trang thai tra hang cho cac ma
                        TbTransfercode::updateAll(['status' => 5, 'payDate' => date('Y-m-d H:i:s')], ['transferID' => $listBarcode, 'orderID' => $currentOrder->orderID]);
                    }
                    //neu so ma cua don = so ma da ban thi finish don hang
                    if ($totalMvd == count($listBarcode)) {
                        $currentOrder->status = 6; //trang thai da tra hang
                        $currentOrder->paymentDate = date('Y-m-d H:i:s');    //cap nhat ngay thanh toan
                    }
                    //cap nhat lai don hang
                    CommonLib::updateOrder($currentOrder);
                }

                /*history*/
                $mdlTransaction = new TbAccountTransaction();
                $mdlTransaction->type = 5; //trang thai tra hang cho shop
                $mdlTransaction->status = 2; //trang thai giao dich thanh cong
                $mdlTransaction->customerID = $currentOrder->customerID;
                $mdlTransaction->userID = \Yii::$app->user->id;//nhan vien giao dich
                $note = 'Thanh toán số tiền còn thiếu cho đơn hàng: <a class="link_order" target="_blank" href="' . Url::toRoute(['orders/view', 'id' => $currentOrder->orderID, '#' => 'shop-' . $currentShop->id]) . '"><b>' . $currentOrder->identify . ' </b></a>';
                $note .= '<br/>Các mã vđ đã trả: ' . implode(';', $listBarcode);
                $mdlTransaction->sapo = $note;
                $mdlTransaction->value = $tienThieu;//so tien thanh toan
                $mdlTransaction->accountID = $bank->id;//ma tai khoan
                $mdlTransaction->balance = $bank->totalResidual;//so du sau khi giao dich
                $mdlTransaction->create_date = date('Y-m-d H:i:s');
                $mdlTransaction->save(false);

                return true;
            }
        }

        return false;
    }
    //update lai su lieu cac shop va don hang
    //updateOrderData
    public static function updateDataOrders($currentOrder)
    {
        $totalWeight = 0;
        $totalWeightPrice = 0;
        $totalShip = 0;
        $totalShipVn = 0;
        $totalIncurred = 0;
        $orderSupplierAll = TbOrderSupplier::find()->where(['orderID' => $currentOrder->orderID])->all();
        if ($orderSupplierAll) {
            foreach ($orderSupplierAll as $value) {
                $phi_kg = self::getFeeKg($value->weight, $currentOrder->provinID);
                $totalPriceKg = ($value->weight > 0) ? $value->weight * $phi_kg : 0; //tinh tien kg
                $totalWeight += $value->weight;
                $totalWeightPrice += $totalPriceKg;
                $totalShip += $value->shipmentFee;
                $totalShipVn += $value->shipmentVn;
                $totalIncurred += $value->incurredFee; //tong phi phat sinh shop

            }
        }
        $currentOrder->totalIncurred = $currentOrder->incurredFee + $totalIncurred; //tong phi phat sinh cac shop + phi phat sinh don hang neu co
        $currentOrder->totalWeight = $totalWeight;
        $currentOrder->totalWeightPrice = $totalWeightPrice;
        $currentOrder->totalShip = $totalShip; //tong ship TQ
        $currentOrder->totalShipVn = $totalShipVn; //tong ship VN
        $currentOrder->save(false);
        return CommonLib::updateOrder($currentOrder); //tinh lai all

    }

    /*
     ** Author:HAIHS
     ** Content: update barcode
     ** CreateDate: 112-04-2018 16:25
    */
    public static function shippingProcess($currentOrder, $shop, $listMvd)
    {
        $status = false;
        /*update new*/
        if (isset($listMvd) && !empty($listMvd)) {
            //insert moi
            foreach ($listMvd as $barCode) {
                if (empty($barCode)) continue;
                $barCode = trim($barCode);
                $barCode = str_replace('+', ' ', $barCode);
                $barCode = str_replace('"', '', $barCode);
                $barCode = str_replace('/', ' ', $barCode);
                $barCode = str_replace(array('"', '/', '>', '?', '@', '#', '&', '*', ')', '(', "'", '`', '^', '~', '%', '!', '$', 'amp;', '}', '{', '|', '<'), '', $barCode);
                $barCode = str_replace(':', '\:', $barCode);
                $shippingCode = trim(urlencode($barCode));
                // $length = strlen($shippingCode);
                //if ($length > 20 || $length < 5) {
                // continue;
                // }
                $status = true;
                //not exist => insert
                //'orderID' => $currentOrder->orderID, 'shopID' => $shop->id,['like', 'name', $_GET['q'] . '%', false] , 'orderID' => $currentOrder->orderID
                /*$number = TbTransfercode::find()->where(['like','transferID', $shippingCode.'%',false])->count();
                if($number > 0){
                  //  $number ++;
                    $transferID = $shippingCode.'-'.$number;
                }else{
                    $transferID = $shippingCode;
                }*/
                if (!$modelTransfer = TbTransfercode::findOne(['transferID' => $shippingCode, 'orderID' => $currentOrder->orderID])) {
                    $modelTransfer = new TbTransfercode();
                    $modelTransfer->shopID = $shop->id;
                    $modelTransfer->businessID = $currentOrder->businessID;
                    $modelTransfer->orderID = $currentOrder->orderID;
                    $modelTransfer->identify = $currentOrder->identify;
                    $modelTransfer->transferID = $shippingCode;
                    $modelTransfer->shipStatus = 0; //0: chua ship,2: kho TQ;3: kho VN
                    $modelTransfer->createDate = date('Y-m-d H:i:s');
                    $modelTransfer->save(false);
                }
                //TH ma chua ve kho VN or kho TQ
                if (!$modelTransfer->shipStatus) {
                    /*
                     * kiem tra ma tren he thon kho xem da duoc kho TQ or kho VN xac nhan nhung dan la kien vo chu
                     * */
                    if ($tbshipping = TbShipping::findOne(['status' => 0, 'shippingCode' => $shippingCode])) {
                        //TH neu kho VN ban
                        $shipStatus = 0;
                        if ($tbshipping->city == 1) {
                            $shipStatus = 3; //Tran thai ma da ve kho vn

                        } elseif ($tbshipping->city == 2) {
                            $shipStatus = 2; //Tran thai ma da ve kho TQ

                        }
                        if ($shipStatus) {
                            $modelTransfer->shipStatus = $shipStatus;
                            $modelTransfer->save(false); //cap nhat trang thai ma da ve kho TQ or vn
                            $tbshipping->status = 1; //update lai trang thai kho ban ma thanh cong
                            $tbshipping->shopID = $modelTransfer->shopID;
                            $tbshipping->tranID = $modelTransfer->id;
                            $tbshipping->editDate = date('Y-m-d H:i:s');
                            $tbshipping->save(false);
                            $shop->shippingStatus = $shipStatus; //Trang thai kho

                        }
                    }
                }
            }
            if ($status && $shop->shippingStatus === 0) {
                $shop->shippingStatus = 4; //Trang thai dang tren duong

            } else {
                $shop->shippingStatus = 0;
            }
        }
        return $shop;
    }

    public static function getPercentDVofOrder($totalMoney, $percentKH, $percentOrder, $provinID = 0)
    {
        if (!empty($percentOrder) || is_numeric($percentOrder)) {
            $percentOrder = CommonLib::toInt($percentOrder);
        } else {
            $percentOrder = null;
        }
        if (!empty($percentKH) || is_numeric($percentKH)) {
            $percentKH = CommonLib::toInt($percentKH);
        } else {
            $percentKH = null;
        }
        $percentOrder = is_numeric($percentOrder) ? $percentOrder : $percentKH;
        return is_numeric($percentOrder) ? $percentOrder : self::getFeeDV($totalMoney, $provinID);
    }

    /**
     * Content: get kg fee of order
     * author: Administrator
     * createdDate: 2018-04-23 11:59
     * $totalWeight: tong kg
     * $ckKH: chiet khau tien kg cho khach hang
     * $ckOrder: chiet khau tien kg cho don hang
     */
    public static function getKgofOrder($totalKg, $ckKH, $ckOrder, $provinID = 0)
    {
        if (!empty($ckOrder) || is_numeric($ckOrder)) {
            $ckOrder = CommonLib::toInt($ckOrder);
        } else {
            $ckOrder = null;
        }
        if (!empty($ckKH) || is_numeric($ckKH)) {
            $ckKH = CommonLib::toInt($ckKH);
        } else {
            $ckKH = null;
        }
        $ckOrder = is_numeric($ckOrder) ? $ckOrder : $ckKH;
        return is_numeric($ckOrder) ? $ckOrder : self::getFeeKg($totalKg, $provinID);
    }
    /*chiet khau cho kd*/
    /*14/04/2018*/
    public static function getBusinessFee($order)
    {
        $business = $order->business;
        $discountRate = 0;
        $discountKg = 0;
        if (isset($business) && $business) {
            $discountRate = $business->discountRate;
            $discountKg = $business->discountKg;
        }
        //discountRate : % chiet khau cho kd
        $order->discountRate = is_numeric($order->discountRate) ? $order->discountRate : $discountRate;
        //uu dai chiet khau kg cho kinh doanh
        $order->discountKg = is_numeric($order->discountKg) ? $order->discountKg : $discountKg;
        return $order;
    }

    /*
     ** Author:HAIHS
     ** Content: lay trang thai kho ban ma
     ** CreateDate: 125-05-2018 17:38
    */
    public static function getWarehoure($status = 0)
    {
        switch ($status) {
            case  1:
                return '<button class="btn  btn-success btn-sm">Thành công</button>';
                break;
            case 0:
                return '<button class="btn btn-danger btn-sm">Kiện vô chủ</button>';
                break;
        }

        return null;
    }

    /*
     ** Author:HAIHS
     ** Content: Trng thai phieu xuat kho
     ** CreateDate: 125-05-2018 18:09
    */
    public static function getStatusExportWarehouse($status = 0)
    {
        switch ($status) {
            case 0:
                return '<span class="btn label label-warning">Chờ xuất</span>';
                break;
            case 1:
                return '<span class="btn label label-success">Hoàn thành</span>';
                break;
            case 2:
                return '<span class="btn label label-default">Yêu cầu ship</span>';
                break;
            case 3:
                return '<span class="btn label btn-info">Chờ ship</span>';
                break;
        }

        return null;
    }

    public static function toInt($str)
    {
        return (double)str_replace(',', '', $str);
    }

    public static function getDateByOrderStatus($model)
    {
        $date = '';
        switch ($model->status) {
            // case 1: //ngay coc
            //    if (!empty($model->orderDate)) {
            //        $date = date('d/m/Y H:i', strtotime($model->orderDate));
            //    }
            //    break;

            case 11: //ngay coc
                if (!empty($model->setDate)) {
                    $date = date('d/m/Y', strtotime($model->setDate));
                }
                break;
            case 3: //ngay mua hang hay co ma ordernumber
                if (!empty($model->shipDate)) {
                    $date = date('d/m/Y', strtotime($model->shipDate));
                }
                break;
            case 4: //shop xuong giao hay co ma van don
                if (!empty($model->deliveryDate)) {
                    $date = date('d/m/Y', strtotime($model->deliveryDate));
                }
                break;
            case 8: //dang van chuyen hay kho tq ban
                if (!empty($model->shippingDate)) {
                    $date = date('d/m/Y', strtotime($model->shippingDate));
                }
                break;
            case 9: //kho vn nhan
                if (!empty($model->vnDate)) {
                    $date = date('d/m/Y', strtotime($model->vnDate));
                }
                break;
            case 6: //ngay tra hang
                if (!empty($model->paymentDate)) {
                    $date = date('d/m/Y', strtotime($model->paymentDate));
                }
                break;
            case 5: //ngay het hang
                if (!empty($model->finshDate)) {
                    $date = date('d/m/Y', strtotime($model->finshDate));
                }
                break;
        }
        return $date;
    }

    public static function encodeURIComponent($string)
    {
        $result = "";
        for ($i = 0; $i < strlen($string); $i++) {
            $result .= self::encodeURIComponentbycharacter(urlencode($string[$i]));
        }
        return $result;
    }

    public static function encodeURIComponentbycharacter($char)
    {
        if ($char == "+") {
            return "%20";
        }
        if ($char == "%21") {
            return "!";
        }
        if ($char == "%27") {
            return '"';
        }
        if ($char == "%28") {
            return "(";
        }
        if ($char == "%29") {
            return ")";
        }
        if ($char == "%2A") {
            return "*";
        }
        if ($char == "%7E") {
            return "~";
        }
        if ($char == "%80") {
            return "%E2%82%AC";
        }
        if ($char == "%81") {
            return "%C2%81";
        }
        if ($char == "%82") {
            return "%E2%80%9A";
        }
        if ($char == "%83") {
            return "%C6%92";
        }
        if ($char == "%84") {
            return "%E2%80%9E";
        }
        if ($char == "%85") {
            return "%E2%80%A6";
        }
        if ($char == "%86") {
            return "%E2%80%A0";
        }
        if ($char == "%87") {
            return "%E2%80%A1";
        }
        if ($char == "%88") {
            return "%CB%86";
        }
        if ($char == "%89") {
            return "%E2%80%B0";
        }
        if ($char == "%8A") {
            return "%C5%A0";
        }
        if ($char == "%8B") {
            return "%E2%80%B9";
        }
        if ($char == "%8C") {
            return "%C5%92";
        }
        if ($char == "%8D") {
            return "%C2%8D";
        }
        if ($char == "%8E") {
            return "%C5%BD";
        }
        if ($char == "%8F") {
            return "%C2%8F";
        }
        if ($char == "%90") {
            return "%C2%90";
        }
        if ($char == "%91") {
            return "%E2%80%98";
        }
        if ($char == "%92") {
            return "%E2%80%99";
        }
        if ($char == "%93") {
            return "%E2%80%9C";
        }
        if ($char == "%94") {
            return "%E2%80%9D";
        }
        if ($char == "%95") {
            return "%E2%80%A2";
        }
        if ($char == "%96") {
            return "%E2%80%93";
        }
        if ($char == "%97") {
            return "%E2%80%94";
        }
        if ($char == "%98") {
            return "%CB%9C";
        }
        if ($char == "%99") {
            return "%E2%84%A2";
        }
        if ($char == "%9A") {
            return "%C5%A1";
        }
        if ($char == "%9B") {
            return "%E2%80%BA";
        }
        if ($char == "%9C") {
            return "%C5%93";
        }
        if ($char == "%9D") {
            return "%C2%9D";
        }
        if ($char == "%9E") {
            return "%C5%BE";
        }
        if ($char == "%9F") {
            return "%C5%B8";
        }
        if ($char == "%A0") {
            return "%C2%A0";
        }
        if ($char == "%A1") {
            return "%C2%A1";
        }
        if ($char == "%A2") {
            return "%C2%A2";
        }
        if ($char == "%A3") {
            return "%C2%A3";
        }
        if ($char == "%A4") {
            return "%C2%A4";
        }
        if ($char == "%A5") {
            return "%C2%A5";
        }
        if ($char == "%A6") {
            return "%C2%A6";
        }
        if ($char == "%A7") {
            return "%C2%A7";
        }
        if ($char == "%A8") {
            return "%C2%A8";
        }
        if ($char == "%A9") {
            return "%C2%A9";
        }
        if ($char == "%AA") {
            return "%C2%AA";
        }
        if ($char == "%AB") {
            return "%C2%AB";
        }
        if ($char == "%AC") {
            return "%C2%AC";
        }
        if ($char == "%AD") {
            return "%C2%AD";
        }
        if ($char == "%AE") {
            return "%C2%AE";
        }
        if ($char == "%AF") {
            return "%C2%AF";
        }
        if ($char == "%B0") {
            return "%C2%B0";
        }
        if ($char == "%B1") {
            return "%C2%B1";
        }
        if ($char == "%B2") {
            return "%C2%B2";
        }
        if ($char == "%B3") {
            return "%C2%B3";
        }
        if ($char == "%B4") {
            return "%C2%B4";
        }
        if ($char == "%B5") {
            return "%C2%B5";
        }
        if ($char == "%B6") {
            return "%C2%B6";
        }
        if ($char == "%B7") {
            return "%C2%B7";
        }
        if ($char == "%B8") {
            return "%C2%B8";
        }
        if ($char == "%B9") {
            return "%C2%B9";
        }
        if ($char == "%BA") {
            return "%C2%BA";
        }
        if ($char == "%BB") {
            return "%C2%BB";
        }
        if ($char == "%BC") {
            return "%C2%BC";
        }
        if ($char == "%BD") {
            return "%C2%BD";
        }
        if ($char == "%BE") {
            return "%C2%BE";
        }
        if ($char == "%BF") {
            return "%C2%BF";
        }
        if ($char == "%C0") {
            return "%C3%80";
        }
        if ($char == "%C1") {
            return "%C3%81";
        }
        if ($char == "%C2") {
            return "%C3%82";
        }
        if ($char == "%C3") {
            return "%C3%83";
        }
        if ($char == "%C4") {
            return "%C3%84";
        }
        if ($char == "%C5") {
            return "%C3%85";
        }
        if ($char == "%C6") {
            return "%C3%86";
        }
        if ($char == "%C7") {
            return "%C3%87";
        }
        if ($char == "%C8") {
            return "%C3%88";
        }
        if ($char == "%C9") {
            return "%C3%89";
        }
        if ($char == "%CA") {
            return "%C3%8A";
        }
        if ($char == "%CB") {
            return "%C3%8B";
        }
        if ($char == "%CC") {
            return "%C3%8C";
        }
        if ($char == "%CD") {
            return "%C3%8D";
        }
        if ($char == "%CE") {
            return "%C3%8E";
        }
        if ($char == "%CF") {
            return "%C3%8F";
        }
        if ($char == "%D0") {
            return "%C3%90";
        }
        if ($char == "%D1") {
            return "%C3%91";
        }
        if ($char == "%D2") {
            return "%C3%92";
        }
        if ($char == "%D3") {
            return "%C3%93";
        }
        if ($char == "%D4") {
            return "%C3%94";
        }
        if ($char == "%D5") {
            return "%C3%95";
        }
        if ($char == "%D6") {
            return "%C3%96";
        }
        if ($char == "%D7") {
            return "%C3%97";
        }
        if ($char == "%D8") {
            return "%C3%98";
        }
        if ($char == "%D9") {
            return "%C3%99";
        }
        if ($char == "%DA") {
            return "%C3%9A";
        }
        if ($char == "%DB") {
            return "%C3%9B";
        }
        if ($char == "%DC") {
            return "%C3%9C";
        }
        if ($char == "%DD") {
            return "%C3%9D";
        }
        if ($char == "%DE") {
            return "%C3%9E";
        }
        if ($char == "%DF") {
            return "%C3%9F";
        }
        if ($char == "%E0") {
            return "%C3%A0";
        }
        if ($char == "%E1") {
            return "%C3%A1";
        }
        if ($char == "%E2") {
            return "%C3%A2";
        }
        if ($char == "%E3") {
            return "%C3%A3";
        }
        if ($char == "%E4") {
            return "%C3%A4";
        }
        if ($char == "%E5") {
            return "%C3%A5";
        }
        if ($char == "%E6") {
            return "%C3%A6";
        }
        if ($char == "%E7") {
            return "%C3%A7";
        }
        if ($char == "%E8") {
            return "%C3%A8";
        }
        if ($char == "%E9") {
            return "%C3%A9";
        }
        if ($char == "%EA") {
            return "%C3%AA";
        }
        if ($char == "%EB") {
            return "%C3%AB";
        }
        if ($char == "%EC") {
            return "%C3%AC";
        }
        if ($char == "%ED") {
            return "%C3%AD";
        }
        if ($char == "%EE") {
            return "%C3%AE";
        }
        if ($char == "%EF") {
            return "%C3%AF";
        }
        if ($char == "%F0") {
            return "%C3%B0";
        }
        if ($char == "%F1") {
            return "%C3%B1";
        }
        if ($char == "%F2") {
            return "%C3%B2";
        }
        if ($char == "%F3") {
            return "%C3%B3";
        }
        if ($char == "%F4") {
            return "%C3%B4";
        }
        if ($char == "%F5") {
            return "%C3%B5";
        }
        if ($char == "%F6") {
            return "%C3%B6";
        }
        if ($char == "%F7") {
            return "%C3%B7";
        }
        if ($char == "%F8") {
            return "%C3%B8";
        }
        if ($char == "%F9") {
            return "%C3%B9";
        }
        if ($char == "%FA") {
            return "%C3%BA";
        }
        if ($char == "%FB") {
            return "%C3%BB";
        }
        if ($char == "%FC") {
            return "%C3%BC";
        }
        if ($char == "%FD") {
            return "%C3%BD";
        }
        if ($char == "%FE") {
            return "%C3%BE";
        }
        if ($char == "%FF") {
            return "%C3%BF";
        }
        return $char;
    }

    public static function getallAdmin()
    {
        $key = User::tableName() . '-All';
        $cache = \Yii::$app->cache;
        $result = $cache->get($key);
        if ($result === false) {
            $allUsers = User::find()->asArray()->all();
            if ($allUsers) {
                foreach ($allUsers as $val) {
                    $arrAllId[$val['id']] = $val;
                }
                $result = isset($arrAllId) ? $arrAllId : '';
            }
            $cache->set($key, $result, \Yii::$app->params['CACHE_TIME']['HOUR']);
        }
        return $result;
    }

    public static function getIconBySource($src)
    {
        $img = '';
        switch ($src) {
            case '1688':
                $img = '<img title="1688" style="max-width: 50px;" src="/images/1688.png" />';
                break;
            case 'tmall':
                $img = '<img title="Tmall" style="max-width: 80px;"  src="/images/tmall.png" />';
                break;
            case 'taobao':
                $img = '<img title="Taobao" style="max-width: 80px;"  src="/images/taobao.png" />';
                break;
        }
        return $img;
    }

    public static function checkOrder($userID, $number = 10)
    {
        $identify = 'D' . $userID . '-' . $number;
        if (TbOrders::findOne(['identify' => $identify])) {
            $number++;
            return self::checkOrder($userID, $number);
        }
        return $identify;
    }

    public static function bagStatus($status = 0)
    {
        $arr = [1 => 'Ch x l', 2 => ' ng bao',];
        return isset($arr[$status]) ? $arr[$status] : $arr;
    }

    public static function secondsToTime($time)
    {
        // Calculate difference between current
        // time and given timestamp in seconds
        $diff = time() - $time;
        if ($diff < 1) {
            return '1 second ago';
        }
        $time_rules = array(12 * 30 * 24 * 60 * 60 => 'year', 30 * 24 * 60 * 60 => 'month', 24 * 60 * 60 => 'day', 60 * 60 => 'hour', 60 => 'minute', 1 => 'second');
        foreach ($time_rules as $secs => $str) {
            $div = $diff / $secs;
            if ($div >= 1) {
                $t = round($div);
                return $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ago';
            }
        }
    }

    public static function has_specchar($x, $excludes = array())
    {
        if (is_array($excludes) && !empty($excludes)) {
            foreach ($excludes as $exclude) {
                $x = str_replace($exclude, '', $x);
            }
        }
        if (preg_match('/^[A-Za-z0-9()\/-]+$/', $x)) {
            return true;
        }
        return false;
    }
    //trang thai thanh toan ho
    //trang thai thanh toan ho
    public static function getStatusPaymentTransport($status = '', $type = 1)
    {
        switch ($type) {
            case 1:
                switch ($status) {
                    case 0:
                        return '<div class="btn-group btn-group-sm"><span class="btn label label-danger">Chưa thanh toán</span></div>';
                        break;
                    case 2:
                        return '<div class="btn-group btn-group-sm"><span class="btn label label-wait">Chờ duyệt</span></div>';
                        break;
                    case 3:
                        return '<div class="btn-group btn-group-sm"><span class="btn label label-finish">Đã thanh toán</span></div>';
                        break;
                    case 4:
                        return '<div class="btn-group btn-group-sm"><span class="btn label label-default">Đã hủy</span></div>';
                        break;

                }
                break;

            case 2:

                return [
                    0 => 'Chưa thanh toán',
                    3 => 'Đã thanh toán',
                    2 => 'Chờ duyệt',
                    4 => 'Đã dủy'
                ];

                break;

        }


        return null;
    }
}
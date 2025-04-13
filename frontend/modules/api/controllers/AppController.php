<?php
namespace frontend\modules\api\controllers;


use common\components\CommonLib;

use common\models\LoginMember;

use common\models\TbCustomers;

use common\models\TbOrdersSession;

use frontend\modules\api\resources\UserResource;

use Yii;

use yii\filters\auth\CompositeAuth;

use yii\filters\auth\HttpBasicAuth;

use yii\filters\auth\HttpBearerAuth;

use yii\filters\auth\QueryParamAuth;

use yii\filters\Cors;

use yii\filters\VerbFilter;

use yii\rest\Controller;

use yii\web\Response;


class AppController extends Controller
{

    const DOMAIN = 'dathangtrung.vn';

    const VERSION = '1.0.0';
    const API_TAOBAO = 'https://api-dota.gobiz.dev/api/M26/search';
    const HOST_KEY = 'ZGF0aGFuZ3RydW5nLnZu';
    const ALLOW_EXTENSIONS = ['nghlcghghkbcelfjbhgbkkbebnamdkcd', 'okkkpohnnjcpmdmbikolalomknfadkpn'];
    // Development environment constants
    const DEV_EXEC = true;

    const TOKEN_CACHE_PREFIX = 'extension_token_';
    const TOKEN_EXPIRATION_SUFFIX = '_expiration';
    const CACHE_DURATION = 3600;  // Thời gian sống cache là 10 giây



    public $modelClass = UserResource::class;

    public function actions()
    {
        $action = parent::actions();

        unset($action['index']);
        unset($action['create']);
        unset($action['update']);
        unset($action['delete']);
    }


    public function beforeAction($action)
    {
        parent::beforeAction($action);

        // Remove headers
        Yii::$app->response->headers->remove('X-Powered-By');
        Yii::$app->response->headers->remove('X-Debug-Tag');
        Yii::$app->response->headers->remove('Server');
        Yii::$app->response->headers->remove('Set-Cookie');
        Yii::$app->response->headers->add('X-Powered-By', self::DOMAIN);
        Yii::$app->response->headers->add('X-Debug-Tag', self::DOMAIN);

        // Set response headers
        Yii::$app->response->headers->set('Access-Control-Expose-Headers', 'Authorization-Token, Anonymous-Token, X-Version,source, hostkey,chrome-Id, Content-Type, Authorization');
        Yii::$app->response->headers->set('Cache-Control', 'public, max-age=' . self::CACHE_DURATION);
        Yii::$app->response->headers->set('X-Version', self::VERSION);
        Yii::$app->response->headers->set('ETag', md5(self::VERSION));

        return true;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Cấu hình CORS
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],  // Cho phép tất cả các domain
                'Access-Control-Request-Method' => ['GET', 'POST'],
                'Access-Control-Allow-Headers' => ['Authorization', 'Content-Type', 'source', 'domain-key', 'chrome-id'],  // Thêm các header tùy chỉnh vào đây
                'Access-Control-Max-Age' => 3600,
            ],
        ];

        // Cấu hình Authenticator
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'except' => ['config', 'checking', 'get-token', 'refresh', 'search', 'init'],
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];

        return $behaviors;
    }


    public function actionRefresh()
    {
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if (\Yii::$app->request->getIsPost()) {

                $resDefault = ['code' => 404, 'error' => 'Bad Request', 'config' => null];
                // Get domain key from headers
                $domainKey = Yii::$app->request->getHeaders()->get('domain-key');
                // Validate extension
                $extensionId = Yii::$app->request->getHeaders()->get('chrome-id');

                if (YII_ENV === 'prod' && !in_array($extensionId, self::ALLOW_EXTENSIONS)) {
                    return $resDefault;
                }

                $source = Yii::$app->request->getHeaders()->get('source');

                $oid = (int)\Yii::$app->request->post('oid');

                $marketplace = \Yii::$app->request->post('marketplace');
                $btoa = \Yii::$app->request->post('btoa');
                $link = '';

                if ($oid && $marketplace) {
                    $link = CommonLib::getCouponLink($oid, $marketplace);
                }

                if (!preg_match('/(1688|taobao|tmall)/i', $source)) {
                    return $resDefault;
                }

                // Get settings
                $settings = CommonLib::getSettingByName(['hotline', 'CNY']);

                // Build config
                $config = array(
                    'hotline' => isset($settings['hotline']) ? $settings['hotline'] : '',
                    'nhtqExchangeRate' => isset($settings['CNY']) ? (int)$settings['CNY'] : 0,
                    'baseUrl' => Yii::$app->params['baseUrl'],
                    'apiDomain' => Yii::$app->params['baseUrl'],
                    'siteName' => Yii::$app->params['SITE_NAME'],
                    'key' => $this->generateToken(),
                    'checklink' => $link,
                    'hostkey' => $domainKey,
                    'token' => $btoa,
                    'exec' => YII_ENV === 'prod' ? (self::HOST_KEY == $domainKey) : self::DEV_EXEC,
                    'version' => self::VERSION,
                    'timestamp' => time()
                );

                Yii::$app->response->headers->set('Anonymous-Token', $config['key']['token']);

                // Handle authentication
                $uerLogin = Yii::$app->session->get('uerLogin');
                if (!empty($uerLogin) && Yii::$app->user->isGuest) {
                    Yii::$app->user->login($uerLogin, LoginMember::EXPIRE_TIME);
                }

                if (!Yii::$app->user->isGuest) {
                    $authToken = Yii::$app->user->identity->access_token;
                    Yii::$app->response->headers->set('Authorization-Token', $authToken);
                }

                return array(
                    'config' => json_encode($config),
                    'version' => self::VERSION
                );
            }
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return array('error' => 'Internal server error');
        }
    }


    public function actionSearch()
    {
        try {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            // Get and validate input
            $oid = Yii::$app->request->post('oid');
            $marketplace = Yii::$app->request->post('marketplace', 'taobao');

            if (empty($oid)) {
                return ['error' => 'ID is required'];
            }

            // Validate marketplace
            $allowedMarketplaces = ['taobao', '1688', 'tmall'];
            if (!in_array($marketplace, $allowedMarketplaces)) {
                return ['error' => 'Invalid marketplace', 'data' => null];
            }

            // Validate extension - Only in production environment
            $extensionId = Yii::$app->request->getHeaders()->get('chrome-Id');
            if (YII_ENV === 'prod' && !in_array($extensionId, self::ALLOW_EXTENSIONS)) {
                return ['error' => 'Bad Request', 'code' => 400, 'data' => null];
            }

            // Check token expiration
            $tokenExpired = \Yii::$app->request->post('token');
            if (!$this->checkTokenExpiration($tokenExpired)) {
                return ['error' => 'Bad Request', 'code' => 400, 'data' => null];
            }

            // cache data
            $cacheKey = "dota_api_{$marketplace}_{$oid}";
            // Try to get from cache first
            $data = Yii::$app->cache->get($cacheKey);
            if ($data !== false) {
                return [
                    'success' => true,
                    'data' => $data,
                ];
            }

            // Danh sách User-Agent ngẫu nhiên
            $userAgents = [
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0',
                'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0'
            ];

            // Danh sách referer ngẫu nhiên
            $referers = [
                'https://www.taobao.com/',
                'https://www.taobao.com/search?q=product',
                'https://www.taobao.com/markets/all',
                'https://www.taobao.com/markets/taobao',
                'https://www.taobao.com/markets/tmall',
                'https://detail.tmall.com/',
                'https://detail.tmall.com/item.htm',
                'https://item.taobao.com/',
                'https://item.taobao.com/item.htm',
                'https://detail.1688.com/',
                'https://detail.1688.com/offer/',
                'https://www.taobao.com/markets/1688',
                'https://www.taobao.com/markets/1688/search',
                'https://www.taobao.com/markets/1688/offer'
            ];

            // Chọn ngẫu nhiên User-Agent và referer
            $userAgent = $userAgents[array_rand($userAgents)];
            $referer = $referers[array_rand($referers)];

            // Initialize cURL
            $ch = curl_init();

            // Set cURL options
            $url = self::API_TAOBAO."/{$oid}?marketplace={$marketplace}";
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_HTTPGET, true);

            // Set headers to mimic browser
            $headers = [
                'accept: */*',
                'accept-language: en-US,en;q=0.9,vi;q=0.8',
                'cache-control: no-cache',
                'dnt: 1',
                'pragma: no-cache',
                'priority: u=1, i',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: none',
                'sec-fetch-storage-access: active',
                'user-agent: ' . $userAgent,
                'x-tenant: sabomall',
                'referer: ' . $referer,
                'origin: https://www.taobao.com',
                'x-requested-with: XMLHttpRequest',
                'sec-ch-ua: "Not.A/Brand";v="8", "Chromium";v="134"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Thêm delay ngẫu nhiên giữa 2-5 giây để giả lập hành vi người dùng
            usleep(rand(2000000, 5000000));

            // Execute cURL request
            $response = curl_exec($ch);

            // Get HTTP status code and response headers
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $responseHeaders = curl_getinfo($ch);

            // Check for cURL errors
            if (curl_errno($ch)) {
                throw new \Exception('Curl error: ' . curl_error($ch));
            }

            // Close cURL
            curl_close($ch);

            // Parse response
            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response');
            }

            // Check if response contains error
            if (isset($data['error']) && $data['error'] === true) {
                $errorMessage = isset($data['message']) ? $data['message'] : 'Unknown error';
                $errorCode = isset($data['code']) ? $data['code'] : 500;

                // Cache error response for 5 minutes
                Yii::$app->cache->set($cacheKey, [
                    'error' => true,
                    'message' => $errorMessage,
                    'code' => $errorCode
                ], 300);

                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'code' => $errorCode,
                    'headers' => $responseHeaders,
                    'response' => $response
                ];
            }

            // Check HTTP status code
            if ($httpCode !== 200) {
                $errorMessage = isset($data['message']) ? $data['message'] : 'Unknown error';
                $errorCode = isset($data['code']) ? $data['code'] : $httpCode;

                // Cache error response for 5 minutes
                Yii::$app->cache->set($cacheKey, [
                    'error' => true,
                    'message' => $errorMessage,
                    'code' => $errorCode
                ], 300);

                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'code' => $errorCode,
                    'headers' => $responseHeaders,
                    'response' => $response
                ];
            }

            $data = $this->formatData($data);

            // Cache duration: 1 hour for normal responses, 5 minutes for error responses
            $cacheDuration = isset($data['error']) ? 300 : 24 * 60 * 60;
            Yii::$app->cache->set($cacheKey, $data, $cacheDuration);

            return [
                'success' => true,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => 500
            ];
        }
    }

    /**
     * Tạo token mới và lưu vào cache
     * @return array
     */
    public function generateToken()
    {
        $currentTime = time();
        $cacheKey = self::TOKEN_CACHE_PREFIX . md5(self::DOMAIN);
        $expirationTime = $currentTime + self::CACHE_DURATION;

        // Kiểm tra xem token hiện tại có còn hiệu lực không
        $existingExpiration = Yii::$app->cache->get($cacheKey . self::TOKEN_EXPIRATION_SUFFIX);

        if ($existingExpiration && $currentTime < $existingExpiration) {
            // Nếu token còn hiệu lực, trả về token hiện tại
            $token = Yii::$app->cache->get($cacheKey);
            return [
                'token' => $token,
                'exp' => $existingExpiration
            ];
        }

        // Tạo token mới
        $token = Yii::$app->security->generateRandomString(32);
        // Lưu token và thời gian hết hạn vào cache
        Yii::$app->cache->set($cacheKey, $token, self::CACHE_DURATION);
        Yii::$app->cache->set($cacheKey . self::TOKEN_EXPIRATION_SUFFIX, $expirationTime, self::CACHE_DURATION);

        return [
            'token' => $token,
            'exp' => $expirationTime
        ];
    }

    /**
     * Kiểm tra token có hết hạn hay không
     * @param string|null $token Token cần kiểm tra
     * @return bool Trả về true nếu token hợp lệ, false nếu token hết hạn hoặc không khớp
     */
    function checkTokenExpiration($token = null)
    {
        if (!$token) {
            return false;
        }

        $cacheKey = self::TOKEN_CACHE_PREFIX . md5(self::DOMAIN);
        $expirationTime = Yii::$app->cache->get($cacheKey . self::TOKEN_EXPIRATION_SUFFIX);
        $currentTime = time();

        if (!$expirationTime || $currentTime > $expirationTime) {
            return false;  // Token hết hạn hoặc không tồn tại
        }

        // Kiểm tra token có khớp với token trong cache không
        $cachedToken = Yii::$app->cache->get($cacheKey);
        return ($cachedToken === $token);  // Trả về true nếu khớp, false nếu không
    }

    /*end start */

    public function formatData($data)
    {
        // Format lại dữ liệu đầu ra
        $formatted = [
            'productId' => isset($data['oid']) ? $data['oid'] : null,
            'shopId' => isset($data['merchant']['id']) ? $data['merchant']['id'] : null,
            'shopName' => isset($data['merchant']['name']) ? $data['merchant']['name'] : null,
            'url' => isset($data['url']) ? $data['url'] : '',
            'name' => isset($data['name']) ? $data['name'] : '',
            'currency' => isset($data['currency']) ? $data['currency'] : 'CNY',
            'skus' => []
        ];

        // Format SKU
        if (!empty($data['skus']) && is_array($data['skus'])) {
            foreach ($data['skus'] as $sku) {
                $variant = [];

                if (!empty($sku['variantProperties'])) {
                    foreach ($sku['variantProperties'] as $vp) {
                        if (isset($vp['name']) && isset($vp['value'])) {
                            $variant[$vp['name']] = $vp['value'];
                        }
                    }
                }

                $formatted['skus'][] = [
                    'image' => isset($sku['image']) ? $sku['image'] : null,
                    'msrp' => isset($sku['msrp']) ? $sku['msrp'] : null,
                    'salePrice' => isset($sku['salePrice']) ? $sku['salePrice'] : null,
                    'variant' => $variant
                ];
            }
        }

        return $formatted;
    }


    /*end*/


    public function checkToken()
    {
        $statusCode = 0;

        if (isset($_SERVER['HTTP_ACCEPT_MAIN'])) {
            $encodedToken = $_SERVER['HTTP_ACCEPT_MAIN'];
            $decodedToken = base64_decode($encodedToken);


            if (md5(self::DOMAIN) === md5($decodedToken)) {
                $statusCode = 1;
            }
        }

        // Thiết lập giá trị trong header của phản hồi
        header('X-Code: ' . $statusCode);
    }


    public function actionConfig()
    {

        $this->checkToken();

        Yii::$app->response->statusCode = 200;
        $setting = CommonLib::getSettingByName(['hotline', 'CNY']);


        $data = [
            'hotline' => $setting['hotline'],
            "exchange_rate" => (int)$setting['CNY'],
        ];

        $uerLogin = Yii::$app->session->get('uerLogin');
        if (!empty($uerLogin) && Yii::$app->user->isGuest) {
            Yii::$app->user->login($uerLogin, LoginMember::EXPIRE_TIME);
        } else {
            $user = Yii::$app->user->identity;
            Yii::$app->session->set('uerLogin', $user);
        }


        if (!Yii::$app->user->isGuest) {
            $token = Yii::$app->user->identity->access_token;
            header('X-Token: ' . $token);
        }

        return $data;
    }


    public function actionChecking()
    {

        try {

            if (\Yii::$app->request->getIsPost()) {
                $item_id = (int)\Yii::$app->request->post('item_id');
                $website = \Yii::$app->request->post('website');
                $link = CommonLib::curlCoupon($item_id, $website);
                Yii::$app->response->statusCode = 200;

                return [
                    'link' => $link
                ];
            }
        } catch (\Exception $e) {
        }

        Yii::$app->response->statusCode = 442;

        return [
            'error' => 1,
        ];
    }


    public function actionAddCart()

    {

        try {


            $message = 'error';


            if (\Yii::$app->request->getIsPost()) {

                $totalSuccess = 0;

                $data = \Yii::$app->request->post();

                $arrData = [];

                $total_quantity = 0;


                if (isset($data['list_sku']) && !empty($data['list_sku'])) {

                    $data['shop_id'] = md5($data['shop_id']);

                    foreach ($data['list_sku'] as $v) {

                        $quantity = !empty($v['quantity']) ? (int)$v['quantity'] : 0;


                        $price = !empty($v['price']) ? doubleval($v['price']) : 0;

                        $size = !empty($v['size']) ? $v['size'] : '';

                        $color = !empty($v['color']) ? $v['color'] : '';

                        $image = !empty($v['image']) ? $v['image'] : '';

                        $name = !empty($v['name']) ? $v['name'] : '';
                        $data['title'] = !empty($data['title']) ? $data['title'] : $name;

                        if (!empty($v['name'])) {
                            $properties = explode(';', $v['name']);
                            $size = (empty($size) && !empty($properties[0])) ? $properties[0] : $size;
                            $color = empty($color) && !empty($properties[1]) ? $properties[1] : $color;
                            //check co 3 thuoc tinh
                            if (count($properties) > 2) {
                                $size = $size . ';' . $properties[2];
                            }
                        }


                        if (empty($image)) {
                            $image = !empty($data['image']) ? $data['image'] : '';
                        }

                        $note = !empty($data['note']) ? $data['note'] : '';


                        if ($price <= 0 && !empty(isset($v['price_ranges']))) {

                            $price_ranges = reset($v['price_ranges']);

                            $price = isset($price_ranges['price']) ? doubleval($price_ranges['price']) : 0;

                        }


                        $total_quantity += $quantity;

                        //set sku

                        $key = md5($data['shop_id'] . $data['id'] . $size . $color);


                        if (isset($arrData[$key])) {

                            $quantity = $arrData[$key]['quantity'] + $quantity;

                            $arrData[$key]['quantity'] = $quantity;

                        } else {

                            if (empty($size) && $v['name']) {

                                $properties = explode(';', $v['name']);

                                $size = !empty($properties[0]) ? $properties[0] : '';

                            }


                            $tmp = $data;

                            $tmp['size'] = $size;

                            $tmp['color'] = $color;

                            $tmp['image'] = $image;

                            $tmp['quantity'] = $quantity;

                            $tmp['price'] = $price;

                            $tmp['note'] = $note;


                            $arrData[$key] = $tmp;

                        }

                    }

                }


                if (!empty($arrData)) {

                    $setting = CommonLib::getSettingByName(['hotline', 'CNY']);

                    $customerID = \Yii::$app->user->id;

                    $customer = TbCustomers::findOne($customerID);

                    $CNY = CommonLib::getCNY($setting['CNY'], $customer->cny);


                    foreach ($arrData as $pkey => $value) {

                        //count quantity by shop

                        // $totalQty = TbOrdersSession::find()->where(['shop_id'=> $value['shop_id'], 'customerID'=>$customerID])->sum('quantity');

                        // $totalQty += $total_quantity;

                        // $totalShop = $arrTotalShop[$value['shop_id']] = $totalQty;


                        $model = new TbOrdersSession();

                        $model->customerID = $customerID;

                        $model->isCheck = 1;

                        $model->shop_id = $value['shop_id'];

                        $model->shop_name = !empty($value['shop_name']) ? $value['shop_name'] : $value['shop_id'];

                        $model->shop_address = !empty($value['shop_address']) ? $value['shop_address'] : '';

                        $model->source_site = $value['website'];

                        $model->shopProductID = $value['id'];

                        $model->title = $value['title'];

                        $model->link = \common\components\CommonLib::convertUrl($value['url']);

                        $image = !empty($data['image']) ? $data['image'] : '';

                        $model->image = !empty($value['image']) ? $value['image'] : $image;

                        $model->quantity = $value['quantity'];

                        $model->noteProduct = $value['note'];

                        $model->size = (isset($value['size']) && !empty($value['size'])) ? trim($value['size']) : '';

                        $model->color = (isset($value['color']) && !empty($value['color'])) ? trim($value['color']) : '';

                        $priceInit = doubleval($value['price']);

                        $model->unitPrice = $priceInit; //tien tq

                        $model->md5 = $pkey;

                        $model->totalPrice = $model->unitPrice * $model->quantity; //tong tien TQ

                        $model->unitPriceVn = round($CNY * $model->unitPrice);

                        $model->totalPriceVn = round($model->unitPriceVn * $model->quantity); //tong tien TQ


                        if ($modelExits = TbOrdersSession::findOne(['md5' => $model->md5, 'customerID' => $model->customerID])) {

                            $modelExits->quantity += $model->quantity;

                            $modelExits->unitPrice = $model->unitPrice;

                            $modelExits->unitPriceVn = $model->unitPriceVn;

                            $modelExits->totalPrice = $modelExits->unitPrice * $modelExits->quantity; //tong tien TQ

                            $modelExits->totalPriceVn = $modelExits->unitPriceVn * $modelExits->quantity; //tong tien TQ

                            if (!empty($value['note'])) {

                                $modelExits->noteProduct = $modelExits->noteProduct . ', ' . $value['note'];

                            }

                            $modelExits->save(false);

                        } else {

                            $model->save(false);

                        }


                        $totalSuccess++;

                    }


                    Yii::$app->response->statusCode = 200;


                    return [
                        // 'totalSuccess' => $totalSuccess,
                        // 'totalProduct' => count($data),
                        'success' => true,
                        'error' => 0,

                    ];

                }

            }

        } catch (\Exception $e) {

            $message = '';//$e->getMessage();

        }


        Yii::$app->response->statusCode = 442;

        return [

            'error' => 1,

            'message' => $message

        ];

    }


    const LINK_TAOBAO = 'https://item.taobao.com/item.htm?';


}
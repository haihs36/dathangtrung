<?php

namespace frontend\modules\ecommerce\controllers;
use common\components\CommonLib;
use common\models\Custommer;
use common\models\TbOrders;
use common\models\TbOrdersDetail;
use common\models\TbOrdersSession;
use common\models\TbOrderSupplier;
use common\models\TbProduct;
use common\models\TbSupplier;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;

/**
 * Default controller for the `ecommerce` module
 */
class CartController extends \common\components\APPController
{
    public $layout = '@app/views/layouts/customer';
    /**
     * Renders the index view for the module
     * @return string
     */

    public function actionIndex($page = 1)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }

        $customer = \Yii::$app->user->identity;
        $customerID = $customer->getId();
        $setting = CommonLib::getSettingByName(['hotline','CNY']);
        $cny = doubleval(CommonLib::getCNY($setting['CNY'], $customer->cny));
        $ordersession  = TbOrdersSession::find()->where(['customerID' => $customerID])->groupBy('shop_id');
        $totalCount = $ordersession->count();
        $pagination = new Pagination(['defaultPageSize' => 20, 'totalCount' => $totalCount]);
        $ordersession   = $ordersession->offset($pagination->offset)->limit($pagination->limit)->orderBy('id DESC')->asArray()->all();

        $totalQuantityAll = 0;
        $totalFinalTQAmountAll = 0;
        $totalFinalAmountAll = 0;
        $arrShop_ids = [];
        $arrShopChecked = [];
        $orderCart = [];

        if ($ordersession) {
            $arrShop_ids = array_column($ordersession,'shop_id');
            $arrShop_ids = array_unique($arrShop_ids);
            //lay sp theo shop
            $productInShop = TbOrdersSession::find()->where(['shop_id' => $arrShop_ids,'customerID' => $customerID])->orderBy('id DESC')->asArray()->all();

            if(!empty($productInShop)){
                foreach ($productInShop as $item) {
                    $item['unitPriceVn'] = round($cny * doubleval($item['unitPrice']));
                    $item['totalPriceVn'] = round($item['unitPriceVn'] * (int)$item['quantity']); //tong tien TQ

                    if (isset($item['isCheck']) && $item['isCheck']) {
                        $totalQuantityAll += (int)$item['quantity'];
                        $totalFinalTQAmountAll += doubleval($item['totalPrice']);
                        $totalFinalAmountAll += $item['totalPriceVn'];
                    }

                    if (isset($item['isCheck']) && $item['isCheck'] && !in_array($item['shop_id'], $arrShopChecked)) {
                        $arrShopChecked[$item['shop_id']][] = $item['id'];
                    }

                    $orderCart[$item['shop_id']][$item['id']] = $item;
                }
            }
        }

        $totalShopAll = count($arrShop_ids);

        if (Yii::$app->request->isPost)  {
            $data = \Yii::$app->request->post();
            $token = isset($data['_csrf']) ? trim(strip_tags($data['_csrf'])) : '';

            $valideToken = CommonLib::isAccessTokenValid($token);
            if ($valideToken === false) {
                if (Yii::$app->request->isAjax) {
                    return ['success' => false, 'message' => 'Thời gian xử lý đã hết hạn. Vui lòng thử lại.','token' => 'timeout'];
                } else {
                    Yii::$app->session->setFlash('error', "Thời gian xử lý đã hết hạn. Vui lòng thử lại");
                    return $this->refresh();
                }
            }

            if (count($orderCart) > 0 && isset($data['shop_cart_item']) && count($data['shop_cart_item']) > 0) {
                //insert tb orders
                $number = TbOrders::find()->where(['customerID' => $customerID])->count();
                $number++;
                $identify = CommonLib::checkOrder($customerID, $number);
                $arrDeleteChecked = [];
                try {
                    $modelOrder = new TbOrders();
                    $modelOrder->identify = $identify;
                    $modelOrder->customerID = $customerID;
                    $modelOrder->isBox = isset($data['isBox']) ? $data['isBox'] : 0;
                    $modelOrder->isCheck = isset($data['isCheck']) ? $data['isCheck'] : 0;
                    $modelOrder->isBad = isset($data['isBad']) ? $data['isBad'] : 0;
                    $modelOrder->businessID = !empty($customer->userID) ? $customer->userID : null; //nhan vien kd ?
                    $modelOrder->orderStaff = isset($customer->staffID) ? $customer->staffID : null; //nhan vien dh ?
                    $modelOrder->orderDate = date('Y-m-d H:i:s');
                    $modelOrder->provinID = isset($data['provinceID']) ? $data['provinceID'] : 0;
                    $modelOrder->noteOrder = isset($data['ghi_chu']) ? strip_tags($data['ghi_chu']) : '';
                    $modelOrder->shipAddress = isset($data['shipAddress']) ? strip_tags($data['shipAddress']) : '';

                    if ($modelOrder->save(false)) {
                        $totalOrder = 0;//tong tien hang
                        $totalOrderTQ = 0;//tong tien hang tq
                        $totalQuantity = 0;
                        $orderID = $modelOrder->orderID;
                        $img = '';
                        //% phi giao dich ap dung cho don hang
                        foreach ($data['shop_cart_item'] as $shop_id => $value) {
                            if (isset($orderCart[$shop_id]) && !empty($orderCart[$shop_id])) {
                                /*save supplier*/
                                $quantity = 0;
                                $totalShopPrice = 0;//tong tien shop
                                $totalPriceTq = 0;//tong tien tq
                                $shop = current($orderCart[$shop_id]);
                                //save shop
                                $suplieID = $this->saveSuplier($shop);
                                if ($suplieID && isset($value['item']) && count($value['item']) > 0) {
                                    $tbOrderSupplier = new TbOrderSupplier();
                                    $tbOrderSupplier->orderID = $orderID;
                                    $tbOrderSupplier->supplierID = $suplieID;
                                    $tbOrderSupplier->noteInsite = '';
                                    $tbOrderSupplier->status = 2;//chuong trang thai sang dat coc luon
                                    //save tb_order_supplier
                                    if ($tbOrderSupplier->save(false)) {
                                        $orderSID = $tbOrderSupplier->id;
                                        $isProduct = false;
                                        foreach ($value['item'] as $k => $detail) {
                                            if (isset($orderCart[$shop_id][$k]) && isset($detail['product_check']) && $detail['product_check']) {
                                                $arrDeleteChecked[] = $k;
                                                $productInfo = $orderCart[$shop_id][$k];
                                                $img = $productInfo['image'];
                                                $productInfo['quantity'] = (isset($detail['qty']) && (int)$detail['qty'] > 0) ? (int)$detail['qty'] : 1;
                                                //insert tb product
                                                $productID = $this->saveProduct($productInfo, $suplieID);
                                                if ($productID) {
                                                    $isProduct = true;
                                                    //insert order detail
                                                    $orderDetail = new TbOrdersDetail();
                                                    $orderDetail->orderID = $orderID;
                                                    $orderDetail->productID = $productID;
                                                    $orderDetail->orderSupplierID = $orderSID;
                                                    $orderDetail->quantity = $productInfo['quantity'];
                                                    $orderDetail->noteProduct = isset($detail['ghi_chu']) ? $detail['ghi_chu'] : '';
                                                    $orderDetail->unitPrice = $productInfo['unitPrice'];
                                                    $orderDetail->unitPriceVn = $orderDetail->unitPrice * $cny;
                                                    $orderDetail->totalPrice = $orderDetail->unitPrice * $orderDetail->quantity;
                                                    $orderDetail->totalPriceVn = $orderDetail->unitPriceVn * $orderDetail->quantity;
                                                    $orderDetail->size = $productInfo['size'];
                                                    $orderDetail->color = $productInfo['color'];
                                                    $orderDetail->image = $productInfo['image'];
                                                    $orderDetail->save(false);

                                                    $totalPriceTq += $orderDetail->totalPrice;
                                                    $totalShopPrice += $orderDetail->totalPriceVn;
                                                    $quantity += $orderDetail->quantity;
                                                }
                                            }
                                        }

                                        if ($isProduct) {
                                           //update order supplier
                                            $tbOrderSupplier->shopPriceTQ = $totalPriceTq;
                                            $tbOrderSupplier->shopPriceKg = ($modelOrder->weightCharge > 0 && $tbOrderSupplier->weight) ? round($tbOrderSupplier->weight * $modelOrder->weightCharge) : 0;
                                            $tbOrderSupplier->quantity = $quantity;
                                            $tbOrderSupplier->shopPrice = $totalShopPrice;//tong tien hang of shop
                                            //tien dich vu theo shop
                                            $tbOrderSupplier->discountDeals = CommonLib::getPercentDVofOrder($totalShopPrice, $customer->discountRate, $modelOrder->discountDeals, $modelOrder->provinID);
                                            $tbOrderSupplier->orderFee = round(($tbOrderSupplier->shopPrice * $tbOrderSupplier->discountDeals) / 100);
                                            $tbOrderSupplier->shopPriceTotal = $totalShopPrice + $tbOrderSupplier->orderFee;//tong tien shop
                                            $tbOrderSupplier->update();
                                            $totalOrder += $totalShopPrice;//tinh tong tien cac shop
                                            $totalOrderTQ += $totalPriceTq;//tinh tong tien cac shop
                                            $totalQuantity += $quantity;
                                        }
                                    }
                                }
                            }
                        }
                        /*update order total price*/
                        if ($totalOrder && $orderID) {
                            $modelOrder->orderID = $orderID;
                            $modelOrder->image = $img;
                            $modelOrder->totalQuantity = $totalQuantity;
                            $modelOrder->cny = $cny;//ti gia mac dinh he thong
                            /*% phi dv cho don hang*/
                            $modelOrder->discountDeals = CommonLib::getPercentDVofOrder($totalOrder, $customer->discountRate, $modelOrder->discountDeals, $modelOrder->provinID);
                            $modelOrder->orderFee = round(($totalOrder * $modelOrder->discountDeals) / 100);//tong tien phi dich vu
                            //phi giam gia kg luc chua coc
                            $modelOrder->weightDiscount = CommonLib::getKgofOrder(null, $modelOrder->totalWeight, $customer->discountKg, $modelOrder->weightDiscount, $modelOrder->provinID);
                            $modelOrder->weightCharge = $modelOrder->weightDiscount;//tong tien giam gia
                            $modelOrder->totalOrder = $totalOrder; //tong tien hang
                            $modelOrder->totalOrderTQ = $totalOrderTQ; //tong tien hang tq
                            $boxFee = 0;
                            $phikiemhang = 0;
                            //tinh kiem dong go
                            if ((int)$modelOrder->isBox == 1) {
                                $boxFee = ($modelOrder->totalWeight * 3000) + 6000;
                            }
                            //phi kiem dem
                            if ((int)$modelOrder->isCheck == 1) {
                                $feeCount = CommonLib::getFeeCheck($modelOrder->totalQuantity);
                                $phikiemhang = $feeCount * $modelOrder->totalQuantity;
                            }

                            $modelOrder->phidonggo = $boxFee;
                            $modelOrder->phikiemhang = $phikiemhang;
                            $totalPayment = $modelOrder->phikiemhang + $boxFee  + $modelOrder->totalOrder + $modelOrder->orderFee + $modelOrder->totalShipVn + $modelOrder->totalWeightPrice + $modelOrder->totalIncurred;//tong tien don hang
                            $debtAmount = ($totalPayment > $modelOrder->totalPaid) ? $totalPayment - $modelOrder->totalPaid : 0;
                            $modelOrder->totalPayment = $totalPayment;//tong tien don hang
                            //tien con thieu
                            $modelOrder->debtAmount = $debtAmount;
                            $modelOrder->status = 1;//cho coc
                            $modelOrder->save(false);
                            //CommonLib::updateOrder($modelOrder);
                            //Cap nhat thong tin khach hang
                            $customer->provinID = $modelOrder->provinID;
                            $customer->billingAddress = !empty($modelOrder->shipAddress) ? $modelOrder->shipAddress : $customer->billingAddress;
                            $customer->save(false);

                            if ($arrDeleteChecked) {
                                TbOrdersSession::deleteAll(['customerID' => $customerID, 'id' => $arrDeleteChecked]);
                            }

                            \Yii::$app->session->get('num_cart');

                            if (Yii::$app->request->isAjax) {
                                return ['success' => true, 'identify' => $identify];
                                // return ['success' => true,'message'=>'Gửi đơn hàng thành công.'];
                            } else {
                                $this->flash('success', 'Gửi đơn hàng thành công.');
                            }
                            // return $this->redirect(['orders/index']);
                        } else {
                            TbOrderSupplier::deleteAll(['orderID' => $modelOrder->orderID]);
                            TbOrdersDetail::deleteAll(['orderID' => $modelOrder->orderID]);
                            $modelOrder->delete();

                            if (Yii::$app->request->isAjax) {
                                return ['success' => false, 'message' => 'Gửi đơn hàng thất bại. Vui lòng kiểm tra lại thông tin sản phẩm'];
                            } else {
                                Yii::$app->session->setFlash('error', "Gửi đơn hàng thất bại. Vui lòng thử lại");
                                return $this->refresh();
                            }
                        }
                        return $this->refresh();
                    }

                } catch (\ErrorException $e) {
                     // pr($e->getMessage());
                    TbOrderSupplier::deleteAll(['orderID' => $modelOrder->orderID]);
                    TbOrdersDetail::deleteAll(['orderID' => $modelOrder->orderID]);
                    $modelOrder->delete();
                    if (Yii::$app->request->isAjax) {
                        return ['success' => false, 'message' => 'Gửi đơn hàng thất bại.'];
                    } else {
                        Yii::$app->session->setFlash('error', "Gửi đơn hàng thất bại. Vui lòng thử lại");
                        return $this->refresh();
                    }
                }
            } else {
                if (Yii::$app->request->isAjax) {
                    return ['success' => false, 'message' => 'Gửi đơn hàng thất bại.'];
                }
            }
        }


        \frontend\widgets\SeoMeta::widget(['seo' => ['title'=>'Quản lý Giỏ hàng']]);

        return $this->render('index', [
            'page' => $page,
            'pagination' => $pagination,
            'token' => CommonLib::generateAccessToken(),
            'orderCart' => $orderCart,
            'customer' => $customer,
            'arrShopChecked' => $arrShopChecked,
            'totalShopAll' => $totalShopAll,
            'totalQuantityAll' => $totalQuantityAll,
            'totalFinalTQAmountAll' => $totalFinalTQAmountAll,
            'totalFinalAmountAll' => $totalFinalAmountAll,
        ]);
    }

   protected function saveProduct($data, $supplierID)
    {
          $productShop = isset($data['shopProductID']) ? $data['shopProductID'] : '';
          $data['title'] = !empty($data['title']) ? $data['title'] : $data['shop_name'].' - '.$productShop;

        if (isset($data['title']) && !empty($data['title'])) {
            $transaction = Yii::$app->db->beginTransaction(); // Bắt đầu transaction

            try {
                $model = new TbProduct();
                $model->supplierID = $supplierID;
                $model->shopProductID = $productShop;
                $model->shopID = isset($data['shop_id']) ? $data['shop_id'] : '';
                $model->sourceName = isset($data['source_site']) ? $data['source_site'] : '';
                $model->md5 = $data['md5'];
                $model->name = trim($data['title']);
                $model->unitPrice = isset($data['unitPrice']) ? $data['unitPrice'] : 0;
                $model->quantity = isset($data['quantity']) ? $data['quantity'] : 0;
                $model->image = isset($data['image']) ? $data['image'] : '';
                $model->link = isset($data['link']) ? $data['link'] : '';
                $model->size = isset($data['size']) ? $data['size'] : '';
                $model->color = isset($data['color']) ? $data['color'] : '';
                $model->time = time();

                if ($modelExits = TbProduct::findOne(['md5' => $model->md5, 'supplierID' => $supplierID])) {
                    $transaction->commit(); // Không cần lưu mới, commit luôn
                    return $modelExits->productID;
                } else {
                    if (!$model->save()) {
                        throw new \Exception('Lỗi khi lưu sản phẩm: ' . json_encode($model->getErrors()));
                    }

                    $transaction->commit(); // Commit transaction nếu thành công
                    return $model->productID;
                }
            } catch (\Exception $e) {
                $transaction->rollBack(); // Rollback nếu có lỗi
             
                return 0; // Trả về 0 nếu có lỗi
            }
        }

        return 0;
    }

    protected function saveSuplier($data)
    {
        if ((!isset($data['shop_id']) && !isset($data['shop_name'])) || (empty($data['shop_id']) && empty($data['shop_name']))) {
            return false;
        }

        $model = new TbSupplier();
        $model->shopID = isset($data['shop_id']) ? $data['shop_id'] : '';
        $model->shopName = isset($data['shop_name']) ? $data['shop_name'] : '';
        $model->sourceName = isset($data['source_site']) ? $data['source_site'] : '';
        $model->shopProductID = isset($data['shopProductID']) ? $data['shopProductID'] : '';
        $model->address = isset($data['shop_address']) ? $data['shop_address'] : '';
        $model->shopUrl = isset($data['link']) ? $data['link'] : '';

        if ($modelExist = TbSupplier::findOne(['shopID' => $model->shopID])) {
            return $modelExist->supplierID;
        } else {
            $model->save(false);
            return $model->supplierID;
        }
    }
}

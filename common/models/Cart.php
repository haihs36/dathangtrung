<?php
/**
 * Created by PhpStorm.
 * User: SUTI
 * Date: 5/12/2019
 * Time: 4:29 PM
 */

namespace common\models;

use yii\web\Session;

class Cart
{


    public function addCart($id, $data)
    {
        $session = \Yii::$app->session;

        if (!isset($session['cart'])) {
            $cart[$id] = $data;
        } else {
            $cart = $session['cart'];
            if (array_key_exists($id, $cart)) {
                $cart[$id]['quantity']++;
                $cart[$id]['totalPrice'] = $cart[$id]['quantity'] * $cart[$id]['unitPrice'];
                $cart[$id]['totalPriceVn'] = $cart[$id]['quantity'] * $cart[$id]['unitPriceVn'];
            }else{
                $cart[$id] = $data;
            }
        }

        $session['cart'] = $cart;
    }
}
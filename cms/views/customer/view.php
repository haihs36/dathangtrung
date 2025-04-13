<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TbCustomers */

$this->title                   = 'Thông tin khách hàng: ' . ' ' . $customer['username'];
$this->params['breadcrumbs'][] = ['label' => 'Danh sách khách hàng', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-user-secret" aria-hidden="true"></i> Thông tin khách hàng</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="panel">
            <ul class="nav nav-tabs">
                <li class="">
                    <a href="<?= Url::toRoute(['customer/update', 'id' => $customer['id']]) ?>">
                        <i aria-hidden="true" class="glyphicon glyphicon-wrench"></i> Chỉnh sửa thông tin
                    </a>
                </li>
                <li class="active">
                    <a href="#"><i class="glyphicon glyphicon-eye-open"></i> Thông tin khách hàng</a>
                </li>
            </ul>
            <div class="tab-content box-body">
                  <div>
                      <table class="table table-order-information">
                          <tbody>
                          <tr>
                              <td class="text-right">Nhân viên quản lý:</td>
                              <td class="text-bold">
                                  <?php if(isset($customer->user->username)){ ?>
                                      Tài khoản: <a target="_blank" href="<?= Url::toRoute(['user/view','id'=>$customer['userID']]) ?>"><?= isset($model->user->username) ? $model->user->username : null;?> </a>
                                  <?php } ?>
                              </td>
                          </tr>
                          <tr>
                              <td class="text-right">Tên đăng nhập:</td>
                              <td><b><?= $customer['username'] ?></b></td>
                          </tr>
                          <tr>
                              <td class="text-right">Họ và tên:</td>
                              <td class="text-bold"><?= $customer['fullname']  ?></td>
                          </tr>
                          <tr>
                              <td class="text-right">Số dư VĐT:</td>
                              <td class="text-bold vnd-unit"><?= number_format($customer['totalResidual']) ?> <em>đ</em></td>
                          </tr>
                          <tr>
                              <td class="text-right">Trạng thái tài khoản:</td>
                              <td class="text-bold"><?= ($customer['status'] == 1) ? 'Đã kích hoạt' : 'Khóa' ?></td>
                          </tr>
                          <tr>
                              <td class="text-right">Số điện thoại:</td>
                              <td class="text-bold"> <?= $customer['phone'] ?></td>
                          </tr>
                          <tr>
                              <td class="text-right">Email:</td>
                              <td class="text-bold"> <?= $customer['email'] ?></td>
                          </tr>
                          <tr>
                              <td class="text-right">Ngày đăng kí:</td>
                              <td class="text-bold"> <?= date('d-m-Y H:i:s', $customer['created_at']) ?></td>
                          </tr>
                          <tr>
                              <td class="text-right">Địa chỉ nhà:</td>
                              <td> <?= $customer['address'] ?></td>
                          </tr>
                          <tr>
                              <td class="text-right">Địa chỉ giao hàng:</td>
                              <td> <?= $customer['billingAddress'] ?></td>
                          </tr>
                          </tbody>
                      </table>
                  </div>
            </div>
        </div>
    </div>
</div>

<div class="clear">
    <table class="table table-bordered order-info">
        <tbody>
        <tr>
            <td class="pd0">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Thông tin đơn hàng</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-order-information">
                                    <tbody>
                                    <tr>
                                        <td>Mã đơn hàng:</td>
                                        <td> <?= $model->identify; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Ngày lên đơn:</td>
                                        <td> <?= date('d-m-Y H:i:s', strtotime($model->orderDate)); ?></td>
                                    </tr>

                                    <tr>
                                        <td>Nhân viên kinh doanh:</td>
                                        <td>
                                            <?php
                                                $business = $model->business;
                                                if($business) {
                                                    echo $business->first_name . ' ' . $business->last_name;
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Nhân viên thanh toán:</td>
                                        <td>
                                            <?php
                                                $staff = $model->staff;
                                                if($staff) {
                                                    echo $staff->first_name . ' ' . $staff->last_name;
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>∑ Số sản phẩm:</td>
                                        <td> <?= $model->totalQuantity; ?></td>
                                    </tr>
                                    <tr>
                                        <td>∑ Số kg:</td>
                                        <td> <?= $model->totalWeight; ?> kg</td>
                                    </tr>
                                    <tr>
                                        <td>Tỷ giá áp dụng cho đơn hàng:</td>
                                        <td>
                                            <span class="currency"><?php
                                                $customer = $model->customer;
                                                $cnys = isset($customer->cny) ? $customer->cny : '';
                                               echo \common\components\CommonLib::getCNY($this->setting['CNY'],$cnys,$model->cny);
                                                ?></span><em>vnđ</em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>% Phí giao dịch cho đơn hàng:</td>
                                        <td> <?= $model->discountDeals ?>
                                            %
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>∑ Tiền phí giao dịch (Phí đặt hàng):</td>
                                        <td>
                                            <span class="currency"><?= ($model->orderFee) ? $model->orderFee : 0; ?></span><em>vnđ</em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>∑ Phí cân nặng:</td>
                                        <td>
                                            <span class="currency"><?= ($model->totalWeightPrice) ? $model->totalWeightPrice : 0; ?></span><em>vnđ</em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>∑ Phí ship nội địa:</td>
                                        <td><span class="currency"><?= ($model->totalShipVn) ?></span><em>vnđ</em>
                                            <!--  <p class="tienhang">
                                                <span class="price"><? /*= number_format(round($model->totalShip)) */ ?> đ</span>
                                            </p>-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>∑ Tiền hàng:</td>
                                        <td><span class="currency"><?= ($model->totalOrder) ?></span><em>vnđ</em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>∑ Tiền đơn hàng:</td>
                                        <td><span class="currency"><?= ($model->totalPayment) ?></span><em>vnđ</em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <a target="_blank" title="Chi tiết đơn hàng" href="<?php echo Url::toRoute(['orders/view', 'id' => $model->orderID]) ?>">Chi
                                                tiết đơn hàng</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
            <td class="pr0 pt0">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Thông tin khách hàng</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 col-lg-3 " align="center">
                                <img alt="User Pic" src="/images/user_photos/default.gif" class="img-circle img-responsive">
                            </div>
                            <div class=" col-md-9 col-lg-9 ">
                                <table class="table table-user-information">
                                    <tbody>
                                    <tr>
                                        <td>Họ và tên:</td>
                                        <td> <?= $customer->fullname; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Số điện thoại</td>
                                        <td> <?= $customer->phone; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><a href="mailto:<?= $customer->email; ?>"><?= $customer->email; ?></a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Tỉnh thành phố</td>
                                        <td> <?= isset($customer->city->CityName) ? $customer->city->CityName : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Quận/Huyên</td>
                                        <td> <?= isset($customer->district->DistrictName) ? $customer->district->DistrictName : ''; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Địa chỉ nhận hàng</td>
                                        <td> <?= $customer->billingAddress; ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>
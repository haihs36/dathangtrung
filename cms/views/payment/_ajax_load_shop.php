<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

if (isset($order) && $order) {
    $business_disable = '';
    $role = Yii::$app->user->identity->role;
    $form = ActiveForm::begin([
        'id' => 'form',
        'action' => ['payment/load-shop'],
        'method' => 'post',
        'options' => [
            'class' => 'don-hang-gio-hang-add-form'
        ],
    ]);
    ?>
    <div class="clear overfow-x">
        <input type="hidden" name="uid" id="customerID" value="<?= isset($customer->id) ? $customer->id : 0 ?>">
        <input type="hidden" name="loid" id="loID" value="<?= isset($loID) ? $loID : 0 ?>">
        <div class="box-bodyc" id="tbl_list">
            <?php
            $totalPayment = 0; //tien thieu
            $total_coc = 0; //tien thieu
            $debtAmount = 0; //tien thieu
            $totalKg = 0;
            $totalOrder = 0;
            $stt = 0;
            $totalkgShop = 0;
            $totalShipmentFee = 0;
            $totalPaid = 0;
            $number_code = 0;
            $number_code_check = 0;
            foreach ($order as $orderID => $item) {
                $total_barcode = count($item);
                $currentOrder = reset($item);
                $shopID = $currentOrder['shopID'];
                $stt++;

                $disable_shop = '';
                ?>
                <div class="box shop-item clearfix collapsed-box" id="order-<?= $currentOrder['orderID'] ?>"
                        numcode="<?= $total_barcode ?>">
                    <div class="clear overfow-x">
                        <table class="table table-bordered mb0">
                            <tbody>
                            <tr>
                                <td colspan="5" class="border-top pd0 collapsed-b">
                                    <div class="box none-border none-shadow">

                                        <!--xu ly shop-->
                                        <div class="box-body pd0 form-horizontal" style="display: block;">
                                            <div class="rows">
                                                <table class="table table-bordered table-striped dataTable ">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center" width="10%">MĐH</th>
                                                        <th class="text-center" width="15%">Giá trị ĐH</th>
                                                        <th class="text-center" width="12%">Tổng cân nặng</th>
                                                        <th class="text-center" width="15%">Tiền cân nặng</th>
                                                        <th class="text-center" width="10%">Phí kiện gỗ</th>
                                                        <th class="text-center" width="10%">Phí kiểm đếm</th>
                                                        <th class="text-center" width="15%">Tổng tiền</th>
                                                        <th class="text-center" width="15%">Đã thanh toán</th>
                                                        <th class="text-center" width="10%">Còn thiếu</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a class="underline" target="_blank"
                                                                    href="<?= Url::toRoute(['orders/view', 'id' => $currentOrder['orderID']]) ?>">
                                                                <b><?= $currentOrder['identify'] ?></b> </a>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php $Gtdh = $currentOrder['totalOrder'] + $currentOrder['orderFee'] + $currentOrder['incurredFee'] + $currentOrder['totalShipVn']; ?>
                                                            <b class="vnd-unit th_gtdh"><?= number_format(round($Gtdh)) ?></b>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php //$phi_kg = \common\components\CommonLib::getKgofOrder($currentOrder['totalWeight'], $customer->discountKg, $currentOrder['weightDiscount']); ?>
                                                            <b class="totalkgFee th_total_kg"><?php echo $currentOrder['totalWeight'] ?></b>kg
                                                        </td>
                                                        <td class="text-center"><b
                                                                    class="vnd-unit weightPrice th_weight_price"><?= number_format(round($currentOrder['totalWeightPrice'])) ?></b>
                                                        </td>
                                                        <td class="vnd-unit text-bold text-center th_phidonggo"><?= number_format(round($currentOrder['phidonggo'])); ?></td>
                                                        <td class="vnd-unit text-bold text-center"><?= number_format(round($currentOrder['phikiemhang'])); ?></td>
                                                        <td class="text-center"><b
                                                                    class="vnd-unit text-bold totalPayment th_total_payment"><?php echo number_format(round($currentOrder['totalPayment'])) ?></b>
                                                        </td>
                                                        <td class="text-center"><b
                                                                    class="vnd-unit text-bold totalPaid th_totalPaid"><?= number_format(round($currentOrder['totalPaid'])); ?></b>
                                                        </td>
                                                        <td class="text-center"><b
                                                                    class="vnd-unit text-bold debtAmount th_debtAmount"><?= number_format(round($currentOrder['debtAmount'])); ?></b>
                                                        </td>

                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table class="orders-table table table-bordered table-striped dataTable mb0 tbl-<?= $currentOrder['orderID'] ?>">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center" width="7%">
                                                            <?php
                                                                $disable = '';
                                                                if($currentOrder['shipStatus'] == 5){
                                                                    $disable = 'disabled';//don da tra hang thi khoa
                                                                }
                                                            ?>
                                                            <input class="checkAll" <?= $disable ?>
                                                                    id="checkAll_<?= $currentOrder['orderID'] ?>"
                                                                    type="checkbox"/>
                                                            <label for="checkAll_<?= $currentOrder['orderID'] ?>"> Tất cả</label>
                                                        </th>
                                                        <th class="text-center" width="15%">MVĐ</th>
                                                        <th class="text-center" width="8%">Cân nặng</th>
                                                        <th class="text-center" width="5%">Dài</th>
                                                        <th class="text-center" width="5%">Rộng</th>
                                                        <th class="text-center" width="5%">Cao</th>
                                                        <th class="text-center" width="8%">Cân<br> quy đổi</th>
                                                        <th class="text-center" width="8%">Cân<br> tính tiền</th>
                                                        <th class="text-center">Số lượng</th>
                                                        <th class="text-center">Ghi chú</th>
<!--                                                        <th class="text-center" width="5%"></th>-->
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    //get ma van don da ve kho vn
                                                    $listMvd = $item;

                                                    $isChecked = false;
                                                    if ($listMvd) {
                                                        foreach ($listMvd as $k => $value) {
                                                            $k++;
                                                            $number_code++; $disable = '';
                                                            if ($value['status'] == 1) {
                                                                $number_code_check++;
                                                                $totalKg += $value['kgPay'];
                                                                $isChecked = true;
                                                             //   $disable = 'disabled'; //kiem tra xem don hang co ma nao duoc chon ko
                                                            }
                                                            if($value['shipStatus'] == 5 || $value['ostatus'] == 5){
                                                                $disable = 'disabled';//don da tra hang thi khoa
                                                            }

                                                            ?>
                                                            <tr class="items" data-cid="<?= $customer->id ?>" data-rel="<?= $value['transferID'] ?>" data-id="<?= $value['id'] ?>" data-orderid="<?= $currentOrder['orderID'] ?>" data-sid="<?= $shopID ?>">
                                                                <td class="text-center">
                                                                    <label class="checkbox checkbox-primary pull-right">
                                                                        <input <?= $disable ?> data-pid="<?= $value['id'] ?>" <?= ($value['status'] == 1) ? 'checked' : '' ?> rel="<?= $value['transferID'] ?>" class="barcode-check-item checkbox-<?= $value['id'] ?> "
                                                                                value="1" type="checkbox" />
                                                                        <label for="barcode-<?= $value['id'] ?>">&nbsp;</label>
                                                                    </label>
                                                                </td>
                                                                <td class="text-center">
                                                                    <?= $value['transferID'] ?>
                                                                  <label class="isCheck-<?= $value['id'] ?>">  <?php if((int)$value['quantity'] > 0){
                                                                            echo '<span class="btn bg-orange btn-xs ">Đã kiểm</span>';
                                                                      } ?></label>

                                                                </td>
                                                                <td class="text-center">
                                                                    <input <?= $disable ?> onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)" class="isNumber kg-<?= $value['id'] ?> w60 pd3 form-control"
                                                                            data-rel="<?= $value['id'] ?>"
                                                                            value="<?= $value['kg'] ?>"
                                                                            type="text" name="kg"   min="0" max="50000">
                                                                </td>
                                                                <td class="text-center">
                                                                    <input <?= $disable ?>
                                                                            onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)"
                                                                            class="isNumber long-<?= $value['id'] ?> w60 pd3 form-control"
                                                                            type="text"
                                                                            value="<?= $value['long'] ?>"   min="0" max="50000"/>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input <?= $disable ?>
                                                                            onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)"
                                                                            class="isNumber wide-<?= $value['id'] ?> w60 pd3 form-control"
                                                                            type="text"
                                                                            value="<?= $value['wide'] ?>"   min="0" max="50000" />
                                                                </td>
                                                                <td class="text-center"><input <?= $disable ?>
                                                                            onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)"
                                                                            class="isNumber high-<?= $value['id'] ?> w60 pd3 form-control"
                                                                            type="text"
                                                                            value="<?= $value['high'] ?>"   min="0" max="50000"/>
                                                                </td>
                                                                <td class="text-center"><input <?= $disable ?>
                                                                            disabled
                                                                            class="isNumber kgChange-<?= $value['id'] ?> w60 pd3 form-control"
                                                                            type="text"
                                                                            value="<?= $value['kgChange'] ?>"   min="0" max="50000"/>
                                                                </td>
                                                                <td class="text-center">

                                                                    <input disabled
                                                                            class="isNumber kgpay kgPay-<?= $value['id'] ?> w60 pd3 form-control"
                                                                            type="text" kgPay="<?= $value['kgPay'] ?>"
                                                                            value="<?= $value['kgPay'] ?>"/></td>
                                                                <td class="text-center">
                                                                    <input <?= $disable ?> onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)" class="isNumber quantity-<?= $value['id'] ?> w60 pd3 form-control"
                                                                           data-rel="<?= $value['id'] ?>"
                                                                           value="<?= (int)$value['quantity'] ?>"
                                                                           type="text" name="quantity"   min="0" max="50000">
                                                                </td>
                                                                <td class="text-center">
                                                                    <textarea <?= $disable ?> onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)" class="note-<?= $value['id'] ?> form-control"><?= trim($value['note']) ?></textarea>
                                                                </td>
                                                                <!--<td>
                                                                    <?php /*echo \common\components\CommonLib::getShippingStatusByShop($value['shipStatus']); */?>
                                                                </td>-->
<!--                                                                <td class="text-center">-->
<!--                                                                    <a-->
<!--                                                                                data-code="--><?php //echo $value['transferID'];  ?><!--"-->
<!--                                                                                data-oid="--><?//= $value['orderID']  ?><!--"-->
<!--                                                                                data-sid="--><?//= $value['shopID']  ?><!--"-->
<!--                                                                                class="btn btn-primary btn-save-code">Lưu</a>-->
<!--                                                                        &nbsp;-->
<!--                                                                    <a-->
<!--                                                                            class="btn btn-danger btn-delete-code"-->
<!--                                                                            data-id="--><?php //echo $value['id']; ?><!--"-->
<!--                                                                            data-oid="--><?//= $value['orderID'] ?><!--"-->
<!--                                                                            data-sid="--><?//= $value['shopID'] ?><!--">Xóa</a>-->
<!--                                                                </td>-->
                                                            </tr>

                                                        <?php }
                                                    } ?>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                if ($isChecked) {
                    //$totalKg += $currentOrder['totalWeight'];
                    //$totalPayment += $currentOrder['totalPayment'];
                    $debtAmount += $currentOrder['debtAmount'];
                    // $totalPaid += $currentOrder['totalPaid'];
                }
                ?>
            <?php } ?>

            <?php $totalPayment = round($totalPayment); ?>
            <div class="clear col-sm-6 pull-right">
                <div class="clear box" id="ResultCurrency" style="display: block">
                    <table class="table table-bordered table-striped dataTable text-center">
                        <thead>
                        <tr>
                            <th>Tổng số mã</th>
                            <th>Tổng số mã được chọn</th>
                            <th>Cân nặng được chọn</th>
                            <!--                            <th>Đã thanh toán</th>-->
                            <th>Tổng tiền thanh toán</th>
                            <!--                            <th>Còn thiếu</th>-->
                        </tr>                        
                        </thead>
                        <tbody>
                            <tr>
                                <td><?= $number_code ?></td>
                                <td><label class="numCodeCheck"><?= $number_code_check ?></label></td>
                                <td><label class="totalKgPay"><?= $totalKg ?></label>kg</td>
                                <td><label class="totalPay vnd-unit text-bold"><?= number_format($debtAmount) ?> </label>
                                </td>
                         </tr>
                        </tbody>
                         <tfoot>
                            <tr>
                                
                                <td></td>
                                 <td></td>
                                <td class="text-right text-bold">Phí phát sinh: </td>
                                <td>
                                    <input style="width: 150px" type="text" class="currency form-control" name="shipfee" id="shipfee" /> 
                                    <em class="red-color">đ</em>
                                </td>
                            </tr>
                            </tfoot>
                    </table>
                    <input id="totalKgPay" type="hidden" value="<?= $totalKg ?>"/>
                    <input id="totalPay" type="hidden" value="<?= $debtAmount ?>"/>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
<?php if(Yii::$app->controller->route == 'lo/update'){ ?>
    <div class="clear">
        <div class="clear text-right" id="btnOption">
            <input type="button" value="Lưu thông tin" class="btn btn-success btn-save-list">
            <button class="btn-pay-all btn btn-primary">
                <i class="fa fa-cart-arrow-down" aria-hidden="true"></i> Trả hàng
            </button>
        </div>
    </div>

<?php } ?>
<?php } ?>
<script>
    $(function () {
        $('input[type=checkbox]').iCheck({
            checkboxClass: 'icheckbox_minimal-blue', //icheckbox_square-blue
            radioClass: 'iradio_minimal-blue',
            increaseArea: '20%' /* optional */
        });

        $('input.checkAll').on('ifChanged', function (event) {
            $table = $(this).closest('.orders-table');
            var checkboxes = $table.find('input.barcode-check-item');
            if ($(this).is(':checked')) {
                checkboxes.iCheck('check');
            } else {
                checkboxes.iCheck('uncheck');
            }
        });

        $('input.barcode-check-item').on('ifChanged', function () {
            var pid = $(this).data('pid');
            Main.kgChange($(this),pid,2);
        });

        $('.btn-save-list').on('click', function () {
            var myArray = [];
            $('#tbl_list .items').each(function () {
                var id = $(this).data('id');
                var oid = $(this).data('orderid');
                var sid = $(this).data('sid');
                var kg = parseFloat($(this).find('.kg-' + id).val());
                var long = parseFloat($(this).find('.long-' + id).val());
                var wide = parseFloat($(this).find('.wide-' + id).val());
                var high = parseFloat($(this).find('.high-' + id).val());
                var note = $(this).find('.note-' + id).val();

                var  quantity = parseFloat($(this).find('.quantity-' + id).val());

                var  checkbox = 0;
                if ($(this).find('.checkbox-' + id).is(':checked')) {
                    checkbox = parseInt($(this).find('.checkbox-' + id+':checked').val());
                }
                var kgChange = parseFloat((long * wide * high) / 6000); //can nang quy doi
                    kgChange = kgChange.toFixed(2);
                var kgPay = (kgChange > kg ? kgChange : kg);

                myArray.push({
                    oid: oid,
                    sid: sid,
                    checkbox: checkbox,
                    id: id,
                    kg: kg,
                    long: long,
                    wide: wide,
                    high: high,
                    kgChange: kgChange,
                    kgPay: kgPay,
                    note: note,
                    qty:quantity
                });
            });
           // console.log(myArray, 'myArray'); return false;

            if (myArray.length <= 0) {
                $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                $('.modal-container').html('<div style="text-align: center"><i class="fa fa-warning" style="font-size:24px;color:red"></i> Vui lòng chọn lưu tối thiểu 1 mã vận đơn. </div>');
                setTimeout(function () {
                    $('#myModal').modal('hide');
                }, 1000);
                return false;
            }


            if (confirm('Bạn có chắc chắn lưu các thông tin này không?')) {
                $.ajax({
                    type: "POST",
                    url: '/transfercode/save',
                    data: {
                        'myArray': myArray
                    },
                    beforeSend: function () {
                        $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                        $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
                    },
                    dataType: 'json',
                    success: function (rs) {
                        if (rs.success) {
                            $('.modal-container').html('<div style="text-align: center"><i class="fa fa-check-circle" style="font-size:24px;color:red"></i> Cập nhật thành công.</div>');
                            setTimeout(function () {
                                $('#myModal').modal('hide');
                            }, 1000);

                            location.reload();
                        } 

                        else {
                            $('.modal-container').html(rs.message);
                        }
                    }
                });
            }

            return false;
        });

    });
</script>


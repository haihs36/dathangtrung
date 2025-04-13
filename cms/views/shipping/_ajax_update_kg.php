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

                $customer_id = !empty($currentOrder['customerID']) ? $currentOrder['customerID'] : $customer_id;

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
                                                        <th class="text-center" width="15%">Khách hàng</th>    
                                                        <th class="text-center" width="15%">Tổng cân nặng</th>
                                                        <th class="text-center" width="15%">Số lượng</th>
                                                        <th class="text-center" width="15%">Kho đích</th>
                                                        <th class="text-center" width="15%">Loại kiện hàng</th>
                                                        <th class="text-center" width="15%">Ghi chú</th>                                                        
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                            <a class="underline" target="_blank"
                                                                    href="<?= Url::toRoute(['orders/view', 'id' => $currentOrder['orderID']]) ?>">
                                                                <b><?= $currentOrder['identify'] ?></b> </a>

                                                        </td>
                                                        <td>
                                                            <?php
                                                                if(isset($customers[$currentOrder['customerID']])){
                                                                   echo $customers[$currentOrder['customerID']];
                                                                }else if(isset($customers[$customer_id])){
                                                                    echo $customers[$customer_id];
                                                                }
                                                            ?>
                                                        </td>
                                                        <td class="text-center">                                                          
                                                            <b class="totalkgFee th_total_kg"><?php echo !empty($currentOrder['orderID']) ? $currentOrder['totalWeight'] : $currentOrder['kgPay'] ?></b>kg
                                                        </td>
                                                        <td class="text-center">
                                                            <?php
                                                                $totalQuantity = '';
                                                                if(!empty($currentOrder['orderID'])){
                                                                    $txt = '_';
                                                                    if($currentOrder['quantity'])
                                                                        $txt = (int)$currentOrder['quantity'];

                                                                    $totalQuantity = $currentOrder['totalQuantity'] .' / '.$txt;
                                                                }else {
                                                                    $totalQuantity = ($currentOrder['cquantity'] > 0 ? $currentOrder['cquantity'] : '');
                                                                }
                                                              ?>
                                                            <b class="toalQuantity th_total_quantity"><?php echo $totalQuantity ?></b>
                                                        </td>
                                                        <td class="text-center">
                                                               <?php  if(!empty($currentOrder['name'])){
                                                                    echo '<b>'.$currentOrder['name'].'</b>';
                                                                }
                                                                if($currentOrder['isBox']){
                                                                    echo '<label class=" btn-block btn-danger btn-xs"> Đóng gỗ</label>';
                                                                }
                                                                if($currentOrder['isCheck']){
                                                                   echo '<label class=" btn-block btn-warning btn-xs"> Kiểm đếm</label>';
                                                                }
                                                                ?>
                                                        </td>
                                                        <td class="text-center">
                                                                <?php echo (!empty($currentOrder['orderID'])) ? 'Hàng order' : 'Hàng ký gửi' ?>
                                                        </td>
                                                        <td class="text-center"> <?php echo $currentOrder['noteOrder'] ?></td>
                                                       

                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table class="orders-table table table-bordered table-striped dataTable mb0 tbl-<?= $currentOrder['orderID'] ?>">
                                                    <thead>
                                                    <tr>
                                                            <?php
                                                                $disable = '';
                                                                if($currentOrder['shipStatus'] == 5){
                                                                    $disable = 'disabled';//don da tra hang thi khoa
                                                                }
                                                            ?>

                                                        <th class="text-center" width="15%">MVĐ</th>
                                                        <th class="text-center" width="8%">Cân thực tế</th>
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

                                                            if($value['shipStatus'] == 5 || $value['ostatus'] == 5){
                                                               $disable = 'disabled';//don da tra hang thi khoa
                                                            }

                                                            ?>
                                                            <tr class="items" data-cid="<?= $customer_id ?>" data-rel="<?= $value['transferID'] ?>" data-id="<?= $value['id'] ?>" data-orderid="<?= $currentOrder['orderID'] ?>" data-sid="<?= $shopID ?>">

                                                                <td class="text-center">
                                                                    <?= $value['transferID'] ?>
                                                                  <label class="isCheck-<?= $value['id'] ?>">  <?php if((int)$value['quantity'] > 0){
                                                                            echo '<span class="btn bg-orange btn-xs ">Đã kiểm</span>';
                                                                      } ?></label>

                                                                </td>
                                                                <td class="text-center">
                                                                    <input <?= $disable ?> onchange="Main.kgChange(this,<?php echo $value['id']; ?>,1)" class="isNumber kg-<?= $value['id'] ?> w60 pd3 form-control"
                                                                            data-rel="<?= $value['id'] ?>"
                                                                            value="<?= $value['kg'] ?>"
                                                                            type="text" name="kg"   min="0" max="50000">
                                                                </td>
                                                                <td class="text-center"><input <?= $disable ?>
                                                                            onchange="Main.kgChange(this,<?php echo $value['id']; ?>,1)"
                                                                            class="isNumber long-<?= $value['id'] ?> w60 pd3 form-control"
                                                                            type="text"
                                                                            value="<?= $value['long'] ?>"   min="0" max="50000"/>
                                                                </td>
                                                                <td class="text-center"><input <?= $disable ?>
                                                                            onchange="Main.kgChange(this,<?php echo $value['id']; ?>,1)"
                                                                            class="isNumber wide-<?= $value['id'] ?> w60 pd3 form-control"
                                                                            type="text"
                                                                            value="<?= $value['wide'] ?>"   min="0" max="50000" />
                                                                </td>
                                                                <td class="text-center"><input <?= $disable ?>
                                                                            onchange="Main.kgChange(this,<?php echo $value['id']; ?>,1)"
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
                                                                    <input <?= $disable ?> onchange="Main.kgChange(this,<?php echo $value['id']; ?>,1)" class="isNumber quantity-<?= $value['id'] ?> w60 pd3 form-control"
                                                                           data-rel="<?= $value['id'] ?>"
                                                                           value="<?= (int)$value['cquantity'] ?>"
                                                                           type="text" name="quantity"   min="0" max="50000">
                                                                </td>
                                                                <td class="text-center">
                                                                    <textarea <?= $disable ?> onchange="Main.kgChange(this,<?php echo $value['id']; ?>,1)" class="note-<?= $value['id'] ?> form-control"><?= trim($value['note']) ?></textarea>
                                                                </td>

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

        </div>
    </div>
    <?php ActiveForm::end(); ?>

<?php } ?>

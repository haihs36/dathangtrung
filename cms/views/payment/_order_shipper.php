<?php if ($data) { ?>
    <div class="box shop-item clearfix collapsed-box">
        <div class="clear">
            <table class="orders-table table table-bordered mb0">
                <tbody>
                <tr>
                    <td colspan="5" class="border-top pd0 collapsed-b">
                        <div class="box none-border none-shadow">

                            <!--xu ly shop-->
                            <div class="box-body pd0 form-horizontal" style="display: block;">
                                <div class="rows">
                                    <table class="table table-bordered table-striped dataTable mb0">
                                        <thead>
                                        <tr>
                                            <th class="text-center" width="5%">
                                                <input id="checkAll" class="checkAll" type="checkbox"/>
                                                <label for="checkAll"> All</label>
                                            </th>
                                            <th class="text-center" width="15%">MVĐ</th>
                                            <th class="text-center" width="8%">Cân nặng</th>
                                            <th class="text-center" width="5%">Dài</th>
                                            <th class="text-center" width="5%">Rộng</th>
                                            <th class="text-center" width="5%">Cao</th>
                                            <th class="text-center" width="8%">Cân<br> quy đổi</th>
                                            <th class="text-center" width="8%">Cân<br> tính tiền</th>
                                            <th class="text-center">Phí kg</th>
                                            <th class="text-center">Tiền kg</th>
                                            <th class="text-center">Ghi chú</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $num_code = 0;
                                            $num_checked = 0;
                                            $kgPay = 0;
                                            $totalMoney = 0;
                                            $totalKg = 0;
                                        foreach ($data as $value) {
                                            $num_code ++;
                                            if($value['status'] == 1){
                                                $num_checked ++;
                                                $totalKg += $value['kgPay'];
                                                $totalMoney += $value['totalPriceKg'];
                                            }
                                            ?>
                                            <tr class="items" data-cid="<?= $customer->id ?>" data-rel="<?= $value['transferID'] ?>" data-id="<?= $value['id'] ?>" data-orderid="0" data-sid="0">
                                                <td class="text-center">
                                                    <label class="checkbox checkbox-primary pull-right">
                                                        <input  data-pid="<?= $value['id'] ?>" <?= ($value['status'] == 1) ? 'checked' : '' ?> rel="<?= $value['transferID'] ?>"
                                                                class="barcode-check-item checkbox-<?= $value['id'] ?> " value="1" type="checkbox" />
                                                        <label for="barcode-<?= $value['id'] ?>">&nbsp;</label>
                                                    </label>
                                                </td>
                                                <td class="text-center"><?= $value['transferID'] ?></td>
                                                <td class="text-center">
                                                    <input  onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)" class="isNumber kg-<?= $value['id'] ?> w60 pd3 form-control"
                                                                           data-rel="<?= $value['id'] ?>"
                                                                           value="<?= $value['kg'] ?>"
                                                                           type="text" name="kg"   min="0" max="50000">

                                                </td>
                                                <td class="text-center">
                                                    <input
                                                            onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)"
                                                            class="isNumber long-<?= $value['id'] ?> w60 pd3 form-control"
                                                            type="text"
                                                            value="<?= $value['long'] ?>"   min="0" max="50000"/>
                                                </td>
                                                <td class="text-center">
                                                    <input
                                                            onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)"
                                                            class="isNumber wide-<?= $value['id'] ?> w60 pd3 form-control"
                                                            type="text"
                                                            value="<?= $value['wide'] ?>"   min="0" max="50000" />
                                                </td>
                                                <td class="text-center">
                                                    <input
                                                            onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)"
                                                            class="isNumber high-<?= $value['id'] ?> w60 pd3 form-control"
                                                            type="text"
                                                            value="<?= $value['high'] ?>"   min="0" max="50000"/>
                                                </td>
                                                <td class="text-center"><input
                                                            disabled
                                                            class="isNumber kgChange-<?= $value['id'] ?> w60 pd3 form-control"
                                                            type="text"
                                                            value="<?= $value['kgChange'] ?>"   min="0" max="50000"/>
                                                </td>
                                                <td class="text-center">
                                                    <input disabled
                                                           class="isNumber kgpay kgPay-<?= $value['id'] ?> w60 pd3 form-control"
                                                           type="text" kgPay="<?= $value['kgPay'] ?>"
                                                           value="<?= $value['kgPay'] ?>"/>
                                                </td>
                                                <td class="text-center">
                                                    <label id="kgfee-<?= $value['id'] ?>" class="vnd-unit"><?= number_format($value['kgFee']) ?></label>
                                                </td>
                                                <td class="text-center">
                                                    <label id="priceKg-<?= $value['id'] ?>" class="vnd-unit"><?= number_format($value['totalPriceKg']) ?></label>
                                                </td>
                                                <td class="text-center">
                                                    <textarea   onchange="Main.kgChange(this,<?php echo $value['id']; ?>,2)" class="note-<?= $value['id'] ?> form-control"><?= trim($value['note']) ?></textarea>
                                                </td>
                                            </tr>
                                        <?php } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="uid" id="customerID" value="<?= $customer->id ?>">
                        <input type="hidden" name="loid" id="loID" value="<?= $loID ?>">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clear col-sm-6 pull-right">
        <div class="clear box" id="ResultCurrency" style="display: block">
            <table class="table table-bordered table-striped dataTable">
                <thead>
                <tr>
                    <th>Tổng số mã</th>
                    <th width="200px">Tổng số mã được chọn</th>
                    <th>Cân nặng thực tế</th>
                    <th>Tổng tiền thanh toán</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?= $num_code ?></td>
                    <td><label class="numCodeCheck"><?= $num_checked ?></label></td>
                    <td><label class="totalKgPay"><?= $totalKg ?></label>kg</td>
                    <td><label class="totalPay-shippers vnd-unit text-bold"><?= number_format($totalMoney) ?></label> <em class="red-color">đ</em></td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                     <td class="text-right text-bold">Phí phát sinh: </td>
                    <td><input type="text" class="currency form-control" name="shipfee" id="shipfee" /></td>
                    <td> <em class="red-color">đ</em></td>
                    <td colspan="4" class="text-right">
                        <div class="clear">
                            <div class="clear text-right" id="btnOption" >
                                <input type="hidden" class="currency form-control" name="shipfee" id="shipfee" />
                                <button  class="btn-pay-shipper btn btn-primary">
                                    <i class="fa fa-cart-arrow-down" aria-hidden="true"></i> Trả hàng
                                </button>
                            </div>
                        </div>

                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <script>
        $(function () {
            $('input[type=checkbox]').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue',
                increaseArea: '20%'
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
        });
    </script>
<?php } ?>
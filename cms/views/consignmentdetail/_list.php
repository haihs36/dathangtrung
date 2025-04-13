<?php use yii\helpers\Html;
$isChecked = false;
$totalKg = 0;
$totalIncurredFee = 0;//tong phu thu
$totalKgFee = 0;//tong tien phi can nang < 1.5kg
?>
<table class="orders-table table table-bordered table-striped dataTable mb0" id="tbl_list">
    <thead>
    <tr>
        <th class="text-center" width="5%">STT</th>
        <th class="text-center" width="15%">MVĐ</th>
        <th class="text-center" width="5%">Cân nặng</th>
        <th class="text-center" width="5%">Dài</th>
        <th class="text-center" width="5%">Rộng</th>
        <th class="text-center" width="5%">Cao</th>
        <th class="text-center" width="8%">Cân quy đổi</th>
        <th class="text-center" width="8%">Cân tính tiền</th>
        <th class="text-center" width="8%">Phí phụ thu</th>
        <th class="text-center" width="8%">Phí < 1.5kg<br> = 4.000đ</th>
        <th class="text-center">Ghi chú</th>
        <th class="text-center" width="5%">Thao tác</th>
    </tr>
    </thead>
    <tbody>
    <?php
    //get ma van don da ve kho vn
    if ($data) {
        foreach ($data as $k => $value) {
            $k++;
            $totalKg += $value['kgPay'];
            $totalIncurredFee += $value['incurredFee'];
            $totalKgFee += $value['kgFee'];
            ?>
            <tr class="items" rel="<?= $value['id'] ?>">
                <td class="text-center"><?= $k ?></td>
                <td class="text-center"><?= $value['barcode'] ?></td>
                <td class="text-center"><input
                            onblur="Main.kgChange(this,<?php echo $value['id']; ?>)"
                            class=" isNumber kg-<?= $value['id'] ?> w60 pd3 form-control"
                            data-rel="<?= $value['id'] ?>" <?= ($value['id'] == 1) ? 'disabled' : '' ?>
                            value="<?= $value['kg'] ?>"
                            type="number" name="kg">
                </td>
                <td class="text-center"><input
                            onblur="Main.kgChange(this,<?php echo $value['id']; ?>)"
                            class=" isNumber long-<?= $value['id'] ?> w60 pd3 form-control"
                            type="number"
                            value="<?= $value['long'] ?>"/>
                </td>
                <td class="text-center"><input
                            onblur="Main.kgChange(this,<?php echo $value['id']; ?>)"
                            class=" isNumber wide-<?= $value['id'] ?> w60 pd3 form-control"
                            type="number"
                            value="<?= $value['wide'] ?>"/>
                </td>
                <td class="text-center"><input
                            onblur="Main.kgChange(this,<?php echo $value['id']; ?>)"
                            class=" isNumber high-<?= $value['id'] ?> w60 pd3 form-control"
                            type="number"
                            value="<?= $value['high'] ?>"/>
                </td>
                <td class="text-center"><input
                            disabled
                            class="kgChange isNumber kgChange-<?= $value['id'] ?> w60 pd3 form-control"
                            type="number"
                            value="<?= $value['kgChange'] ?>"/>
                </td>
                <td class="text-center">
                    <input disabled class=" kgPay-<?= $value['id'] ?> isNumber  w60 pd3 form-control" type="number"
                           value="<?= $value['kgPay'] ?>"/>


                </td>
                <td class="text-center">
                    <input type="text" class="isNumber currency w60 pd3 form-control incurredFee-<?= $value['id'] ?>"
                           value="<?= $value['incurredFee'] ?>"/>
                </td>
                <td class="text-center">
                    <input <?php echo (!empty($value['kgFee'])) ? 'checked': '' ?> id="checkbox_<?= $value['id'] ?>" class="checkbox-<?= $value['id'] ?>" value="4000"
                           type="checkbox"/>
                    <label for="checkbox_<?= $value['id'] ?>"> Chọn </label>
                </td>
                <td class="text-center">
                    <textarea class="note-<?= $value['id'] ?> form-control"><?= trim($value['note']) ?></textarea>
                </td>
                <td class="text-center">
                    <a class="btn btn-danger btn-delete" data-id="<?php echo $value['id']; ?>">Xóa</a>
                </td>
            </tr>

        <?php }
    } ?>
    </tbody>
</table>
<?php
$totalMoney = ($totalKg * $discountKgPrice) + $totalIncurredFee + $totalKgFee;
?>
<div class="clear col-sm-12">
    <div class="clear box" id="ResultCurrency" style="display: block">
        <div class="pull-rights">
            <table class="table table-bordered table-striped dataTable">
                <thead>
                <tr>
                    <th>Hình thức thanh toán</th>
                    <th>Tổng số mã</th>
                    <th>Cân nặng thực tế</th>
                    <th>Tổng tiền <b class="vnd-unit">(vnđ)</b></th>
                    <th colspan="2" width="20%">Thanh toán thực tế <b class="vnd-unit">(vnđ)</b></th>
                </tr>
                <tr>
                    <td>
                        <div class="form-check">
                            <label>
                                <input type="radio" name="rdo_pay" <?php echo (($consign->status == 0) ? 'checked':'') ?> value="0"> <span class="label-text">Chưa thanh toán</span>
                            </label>
                        </div>

                        <div class="form-check">
                            <label>
                                <input type="radio" name="rdo_pay" <?php echo (($consign->status == 2) ? 'checked':'') ?> value="2"> <span class="label-text">Trả tiền mặt</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <label>
                                <input type="radio" name="rdo_pay" <?php echo (($consign->status == 1) ? 'checked':'') ?> value="1"> <span class="label-text">Trả tiền qua ngân hàng</span>
                            </label>
                            <div class="clear" style="display: <?= ($consign->status == 1) ? 'block':'none' ?>" id="thumb_preview">
                                <div class="img" style="text-align: center; overflow: hidden; position: relative;"  data-toggle="tooltip" title="Upload ảnh">
                                    <a href="javascript:void(0)" class="upload-anh" data-id="thumb_img">
                                        <?php
                                            $img = '/images/image-no-image.png';
                                            if(!empty($consign->images)){
                                                $img = Yii::$app->params['FileDomain'].$consign->images;
                                            }
                                        ?>
                                        <img src="<?= $img ?>" width="100" height="100"><br>
                                        <span style="background:#3182c1; color: #fff; padding: 0px 8px">Tải</span>
                                    </a>
                                    <input  class="file_img-thumb_img" type="hidden" name="thumb_img" value="">
                                </div>
                            </div>
                        </div>

                    </td>
                    <td><?= count($data) ?></td>
                    <td><label class="totalKgPay"><?= $totalKg ?></label>kg/<b class="vnd-unit"><?= number_format($discountKgPrice) ?> vnđ</b></td>
                    <td>
                        <label class="currency totalPay currency vnd-unit text-bold"><?php echo number_format($totalMoney) ?> </label>
                    </td>
                    <td><label><input id="vitual_pay" class="currency form-control" type="text" value="<?php echo number_format($consign->actualPayment) ?>"> </label></td>

                    <td><input type="button" value="Lưu phiếu" class="btn btn-primary btn-save-list"></td>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<input id="identify" value="<?= $consign->id ?>" type="hidden">
<div style="display: none">
    <?= Html::beginForm(\yii\helpers\Url::to(['/consignment/upload']), 'post', ['enctype' => 'multipart/form-data']) ?>
    <?= Html::fileInput('', null, [
        'id' => 'photo-file',
        'class' => 'hidden',
        'multiple' => 'multiple',
    ])
    ?>
    <input type="file" name="Consignment[images]">
    <input type="hidden" name="identify">
    <?php Html::endForm() ?>
</div>

<script>
    $(function () {

        $('input[name=rdo_pay]').on('click',function () {
            if($(this).val() == 1){
                $('#thumb_preview').show();
            }else{
                $('#thumb_preview').hide();
            }
        });

        $('.btn-delete').on('click', function () {
            if (confirm('Bạn có chắc chắn xóa mã vận đơn này không?')) {
                $.ajax({
                    url: '/delete-consign-detail',
                    data: {id: $(this).data('id')},
                    type: 'post',
                    dataType: 'json',
                    success: function (rs) {
                        $('#myModal').modal('show');

                        if (rs.success) {
                            $sms = '<div style="text-align: center"><i class="fa fa-check-circle" style="font-size:24px;color:red"></i> Xóa dữ liệu thành công. </div>';
                        } else {
                            $sms = '<div style="text-align: center"><i class="fa fa-warning" style="font-size:24px;color:red"></i> Xóa dữ liệu thất bại. </div>';
                        }

                        $('.modal-container').html($sms);
                        setTimeout(function () {
                            $('#myModal').modal('hide');
                            location.reload();
                        }, 2000);
                    }
                });
            }

            return false;

        });

        $('.btn-save-list').on('click', function () {
            var myArray = [];
            $('#tbl_list .items').each(function () {
                var id = $(this).attr('rel');
                var kg = $(this).find('.kg-' + id).val();
                var long = parseFloat($(this).find('.long-' + id).val());
                var wide = parseFloat($(this).find('.wide-' + id).val());
                var high = parseFloat($(this).find('.high-' + id).val());
                var incurredFee = $(this).find('.incurredFee-' + id).val();
                    incurredFee = parseFloat(incurredFee.replace(',', ''));

                var  kgFee = 0;
                if ($(this).find('.checkbox-' + id).is(':checked')) {
                      kgFee = $(this).find('.checkbox-' + id+':checked').val();
                }

                var note = $(this).find('.note-' + id).val();
                var kgChange = parseFloat((long * wide * high) / 6000); //can nang quy doi
                    kgChange = kgChange.toFixed(2);
                var kgPay = (kgChange > kg ? kgChange : kg);

                myArray.push({
                    id: id,
                    kg: kg,
                    long: long,
                    wide: wide,
                    high: high,
                    kgChange: kgChange,
                    kgPay: kgPay,
                    kgFee: kgFee,
                    incurredFee: incurredFee,
                    note: note
                });
            });

            if (myArray.length <= 0) {
                $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                $('.modal-container').html('<div style="text-align: center"><i class="fa fa-warning" style="font-size:24px;color:red"></i> Vui lòng chọn lưu tối thiểu 1 mã vận đơn. </div>');
                setTimeout(function () {
                    $('#myModal').modal('hide');
                }, 3000);
                return false;
            }

            var  status = 0;
            if ($('input[name="rdo_pay"]').is(':checked')) {
                status = $('input[name="rdo_pay"]:checked').val();
            }

            var cusID = $('#customer_id').val();
            var consignID = $('#pxk').val();
            var vitual_pay = $('#vitual_pay').val();
                vitual_pay = parseFloat(vitual_pay.replace(',', ''));

            if (confirm('Bạn có chắc chắn lưu các mã đã nhập không?')) {
                $.ajax({
                    type: "POST",
                    url: '/save-consign-detail',
                    data: {
                        'myArray': myArray,status:status,vitual_pay:vitual_pay,consignID:consignID,cusID:cusID
                    },
                    beforeSend: function () {
                        $('#myModal').modal('show').find('.modal-title').text('Thông báo');
                        $('.modal-container').html('<div style="text-align: center"><img src="' + baseUrl + '/images/loader.gif"/><br>Đang xử lý </div>');
                    },
                    dataType: 'json',
                    success: function (rs) {
                        if (rs.success) {
                            $('.modal-container').html('<div style="text-align: center"><i class="fa fa-check-circle" style="font-size:24px;color:red"></i> Lưu phiếu thành công.</div>');
                            setTimeout(function () {
                                $('#myModal').modal('hide');
                            }, 2000);

                            location.reload();
                            return false;
                        } else {
                            $('.modal-container').html(rs.message);
                        }
                    }
                });
            }

            return false;
        });

        $('.currency').formatCurrency();

        $('input[type=checkbox]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' /* optional */
        });

        $('input.currency').on('input', function (e) {
            $(this).val(formatCurrency2(this.value.replace(/[,]/g, '')));
            $(this).val(formatCurrency2(this.value.replace(/[,]/g, '')));
        }).on('keypress', function (e) {
            if (!$.isNumeric(String.fromCharCode(e.which))) e.preventDefault();
        }).on('paste', function (e) {
            var cb = e.originalEvent.clipboardData || window.clipboardData;
            if (!$.isNumeric(cb.getData('text'))) e.preventDefault();
        });
    })
</script>
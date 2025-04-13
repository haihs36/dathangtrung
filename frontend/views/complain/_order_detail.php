<?php use yii\helpers\Html;
    if($data){
        $setting = Yii::$app->controller->setting;
        ?>

        <div id="edit-container--9" class="form-wrapper">
            <div class="khieunai-title"><span>Chọn sản phẩm cần khiếu nại</span></div>
            <table class="table table-bordered table-hover dataTable sticky-table" id="tbl_manager">
                <thead>
                <tr>
                    <th></th>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th style="width: 10%">Đơn giá</th>
                    <th style="width: 5%">Số lượng</th>
                    <th>Tải ảnh</th>
                    <th>Ghi chú</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    if($data){
                        $totalMoneyVn = $totalMoneyTq = $totalOrderFee = $totalFreeCount = $totalShipmentFee = 0;
                        foreach ($data as $k=> $value){
                            $moneyVn = $setting['CNY']*$value['unitPrice'];
                            $totalMoneyTq += $value['unitPrice'];
                            $totalMoneyVn += $moneyVn;
                            ?>
                            <tr>
                                <td data-th="Chọn sản phẩm">
                                    <div class="hinh-anh">
                                        <div class="form-item form-type-checkbox form-item-plain-<?php echo $value['id'] ?>-check">
                                            <input type="checkbox" id="edit-plain-<?php echo $value['id'] ?>-check" name="TbComplain[khieunai][<?php echo $value['id'] ?>][check]" value="1" class="form-checkbox">
                                        </div>
                                    </div>
                                </td>
                                <td data-th="Hình ảnh">
                                    <div class="san-pham-item-image">
                                        <a href="<?php echo $value['link'] ?>" target="_blank">
                                            <img width="48" height="48" src="<?php echo $value['image'] ?>">
                                        </a>
                                        <div class="image-hover">
                                            <img width="300" src="<?php echo $value['image'] ?>">
                                        </div>
                                    </div>
                                </td>
                                <td data-th="Tên sản phẩm">
                                    <div class="title">
                                        <a href="<?php echo $value['link'] ?>" target="_bank"><?php echo $value['name'] ?></a>
                                    </div>
                                </td>
                                <td data-th="Giá"><?php echo number_format($value['unitPrice'],2,".","") ?><em>¥</em></td>
                                <td data-th="Số lượng"><?php echo $value['quantity'] ?></td>
                                <td>
                                    <div class="img" style="text-align: center; overflow: hidden; position: relative;"  data-toggle="tooltip" title="Upload ảnh khiếu nại">
                                        <a href="javascript:void(0)" class="upload-anh" data-id="<?php echo $value['id'] ?>">
                                            <img src="/images/image-no-image.png" width="48" height="48"><br>
                                            <span style="background:#3182c1; color: #fff; padding: 0px 8px">Tải</span>
                                        </a>
                                        <input  class="file_img-<?php echo $value['id'] ?>" type="hidden" name="TbComplain[khieunai][<?php echo $value['id'] ?>][file_img_fid]" value="">
                                    </div>
                                </td>
                                <td>
                                    <div class="form-item form-type-textarea">
                                        <div class="form-textarea-wrapper">
                                            <textarea  data-id="<?php echo $value['id'] ?>" placeholder="Ghi chú khiếu nại..." title="Click chọn sản phẩm, upload ảnh trước khi viết ghi chú !" data-toggle="tooltip" readonly="1" id="edit-plain-<?php echo $value['id'] ?>-ghichu" name="TbComplain[khieunai][<?php echo $value['id'] ?>][ghichu]" cols="60" rows="2" class="form-textarea kn-content"></textarea>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php }  } ?>
                </tbody>
            </table>
        </div>
    <?php }  ?>
<?php
    $this->registerJs("
            jQuery('#frm-ajax').yiiActiveForm('remove', 'tbcomplain-content');
            jQuery('.tao-yeu-cau').unbind('click');
            jQuery('.tao-yeu-cau').on('click',function(){
                if ($('input:checkbox').length) {
                    if ($('input:checkbox').filter(':checked').length < 1){
                       alert('Hãy chọn tối thiểu 1 sản phẩm khiếu nại');
                        return false;
                    }
                }  
//                 $(\"#myForm input[type=text]\").each(function() {
//                    if(!isNaN(this.value)) {
//                        alert(this.value + \" is a valid number\");
//                    }
//                });
//                return false;
                
            });
    ");
?>

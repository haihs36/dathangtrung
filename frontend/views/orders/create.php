<?php
    $setting = \Yii::$app->controller->setting;
    $this->title = 'Tạo đơn hàng';
    $this->params['breadcrumbs'][] = $this->title;

?>
<span class="ti-gia" style="display:none;"><?php echo  \common\components\CommonLib::getCNY($setting['CNY'],Yii::$app->user->identity->cny);  ?></span>

<div class=" box-add-cart">
    <div class="">
        <form id="form-add-cart" class="dat-hang-online-form" action="/dat-hang" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
        <div class="bconten">
                <table id="cart" class="table table-bordered table-hover dataTable ">
                    <thead>
                    <tr>
                        <th width="10%">Ảnh sản phẩm</th>
                        <th width="12%">Link sản phẩm</th>
                        <th width="12%">Tên sản phẩm</th>
                        <th width="8%">Màu sắc</th>
                        <th width="8%">kích thước</th>
                        <th width="5%">Số lượng</th>
                        <th width="10%">Đơn giá(<span class="fa fa-yen"></span>)</th>
                        <th width="12%">Mô tả</th>
                        <th class="text-center" width="2%">#</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; ?>
                    <tr class="rowItem odd">
                        <td valign="middle" width="120px">
                            <div class="img text-center" style=" overflow: hidden; position: relative;">
                                <input type="hidden" name="sanpham_item[<?php echo $i; ?>][img]" value="">
                                <input type="file"  name="sanpham_item[<?php echo $i; ?>][img]" onchange="main.readURL(this,'img<?php echo $i; ?>')" aria-required="true">
                                <a href="javascript:void(0)" data-id="img<?php echo $i; ?>">
                                   <img src="/images/upload.png" style="max-width:130px;height: auto">
                                    <br><span style="background:#3182c1; color: #fff; padding: 0px 8px">Tải</span>
                                </a>
                            </div>
                        </td>
                        <td><input required class="form-control pitem" placeholder="Link sản phẩm" type="text" id="sanpham-item-<?php echo $i; ?>-link" value="" name="sanpham_item[<?php echo $i; ?>][link]"></td>
                        <td><input required class="form-control pitem" placeholder="Tên sản phẩm" type="text" id="sanpham-<?php echo $i; ?>-tensanpham" name="sanpham_item[<?php echo $i; ?>][tensanpham]"></td>
                        <td><input required class="form-control pitem" placeholder="Màu sắc" type="text" id="sanpham-<?php echo $i; ?>-color" name="sanpham_item[<?php echo $i; ?>][color]" /></td>
                        <td><input required class="form-control pitem" placeholder="Kích thước" type="text" id="sanpham-<?php echo $i; ?>-size" name="sanpham_item[<?php echo $i; ?>][size]" /></td>
                        <td><input required type="number" class="form-control pitem text-center item-qty allownumeric" min="1" value="1" placeholder="Số lượng" onblur="users.update_qty(<?php echo $i; ?>)" id="sanpham-item-<?php echo $i; ?>-qty" name="sanpham_item[<?php echo $i; ?>][qty]"></td>
                        <td><input required class="form-control item-price pitem allownumeric"  placeholder="Đơn giá" type="text" id="sanpham-item-<?php echo $i; ?>-price" value="" name="sanpham_item[<?php echo $i; ?>][price]"></td>
                        <td> <textarea class="form-control" placeholder="Ghi chú..." id="sanpham-item-<?php echo $i; ?>-mota" name="sanpham_item[<?php echo $i; ?>][mota]"></textarea></td>
                        <td class="text-center"></td>
                    </tr>
                    </tbody>
                </table>
        </div>
        <div class="text-right mt15" style="margin-bottom: 50px">
            <button href="javascript:void(0);" class="add them-moi btn bg-orange margin">+ Thêm</button>
            <button class="btn btn-danger btnAdd">
                <span class="glyphicon glyphicon-shopping-cart"></span> Thêm giỏ hàng
            </button>
        </div>
        </form>
        <?php if (!$check) { ?>
            <div id="jGrowl" class="center jGrowl">
                <div class="jGrowl-notification"></div>
                <div class="jGrowl-notification alert ui-state-highlight ui-corner-all error" style="display: block;">
                    <span class="pull-right close-jGrowl">×</span>
                    <div class="jGrowl-header"></div>
                    <div class="jGrowl-message">Sản phẩm chưa có hoặc không đầy đủ thông tin. Vui lòng nhập Link, Giá, Số lượng, Tên sản phẩm</div>
                </div>
            </div>
        <?php } ?>


    </div>
</div>
    <script>
        $(document).ready(function () {
            main.cart_mobile();
        });
    </script>
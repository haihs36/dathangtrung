<?php use yii\helpers\Url; ?>

<div class="view-content">
    <?php if(isset($result['shipping']) && $result['shipping']){ ?>
        <h4><i class="fa fa-fw fa-check"></i> Đơn hàng</h4>
        <table id="tbl_manager" class="table table-bordered table-hover dataTable">
            <thead>
            <tr>
                <th style="width: 15%;">Mã Vận đơn</th>
                <th class="text-center">
                    Mã đơn hàng
                </th>
                <th class="text-center" style="width: 15%;">
                    Trạng thái
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($result['shipping'] as $item){
                ?>
                <tr data-key="1156">
                    <td data-th="Mã ĐH">
                        <div class="identify green"><a target="_blank" href="<?= Url::toRoute(['orders/view','id'=>$item['orderID'],'#'=>'shop-'.$item['shopID']]) ?>"><b><?= $item['shippingCode'] ?></b></a></div>
                        <div class="date"><?= !empty($item['createDate']) ? date('d-m-Y',strtotime($item['createDate'])) : date('d-m-Y') ?></div>
                    </td>
                    <td class="text-center" data-th="Mã đơn hàng">
                        <?= !empty($item['identify']) ? $item['identify'] : 'not set' ?>
                    </td>
                    <td class="text-center" data-th="Tình trạng ship">
                        <span class="label <?= ($item['city'] > 0 ? ' label-primary' : 'label-warning') ?>">
                            <?= \common\components\CommonLib::getCity($item['city']) ?>
                        </span>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>
    <?php if(isset($result['shipper']) && $result['shipper']){ ?>
        <h4><i class="fa fa-fw fa-check"></i> Đơn ký gửi</h4>
        <table id="tbl_manager" class="table table-bordered table-hover dataTable">
            <thead>
            <tr>
                <th>Mã Vận đơn</th>
                <th class="text-center" style="width: 15%;">
                    Trạng thái
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
                foreach ($result['shipper'] as $item){
                    ?>
                    <tr data-key="1156">
                        <td data-th="Mã ĐH">
                            <div class="identify green"><a target="_blank" href="<?= Url::toRoute(['shipper/review','shipcode'=>$item['shippingCode']]) ?>"><b><?= $item['shippingCode'] ?></b></a></div>
                            <div class="date"><?= !empty($item['createDate']) ? date('d-m-Y',strtotime($item['createDate'])) : date('d-m-Y') ?></div>
                        </td>
                        <td class="text-center" data-th="Tình trạng ship">
                            <span class="label <?= ($item['shippingStatus'] > 0 ? ' label-primary' : 'label-warning') ?>">
                                <?= ($item['shippingStatus'] > 0 ? \common\components\CommonLib::statusShippingText($item['shippingStatus']) : 'Chưa ship') ?>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
    <?php
        if(empty($result['shipping']) && empty($result['shipper'])){
            echo '<p>Không tìm thấy </p>';
        }
    ?>
</div>

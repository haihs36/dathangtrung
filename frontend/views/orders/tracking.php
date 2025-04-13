<?php

$this->title = 'Tracking kiện hàng';
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['orders/index']];
if(isset($barcode) && !empty($barcode))
    $link= "https://m.kuaidi100.com/result.jsp?nu=".$barcode;
else
    $link= "https://m.kuaidi100.com";

?>


<div style="margin-top: 20px">
    <div class="grid-order">
        <?php \yii\widgets\Pjax::begin(); ?>
        <iframe height="1000" width="100%" src="<?= $link ?>" align="middle" style="border: none;overflow-y:auto">

        </iframe>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>

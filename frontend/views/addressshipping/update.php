<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\TbAddressShipping */

?>
<div class="tb-address-shipping-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php echo Html::a('Danh địa chỉ giao hàng của bạn', ['index'], ['class' => 'btn btn-success']) ?>
    </p> <br/>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

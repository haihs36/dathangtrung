<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="tb-address-shipping-index">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

  <div style="clear: both; padding-top: 20px;">
      <?= GridView::widget([
          'dataProvider' => $dataProvider,
          'summary'=>"",
          'columns' => [
              ['class' => 'yii\grid\SerialColumn'],
//            'id',
              'name',
//            ['class' => 'yii\grid\ActionColumn'],
              [
                  'label'      => '',
                  'headerOptions'  => ['class' => 'text-center'],
                  'contentOptions' => ['class' => 'text-center'],
                  'filterOptions'  => ['class' => 'text-center'],
                  'format'         => 'raw',
                  'value'          => function ($model) {
                      return $model->getAction();
                  },
              ],
          ],
      ]); ?>
  </div>
</div>

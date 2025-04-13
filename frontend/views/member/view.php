<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\TbCustomers */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tb Members', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-member-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fullname',
            'phone',
            'email:email',
            'username',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'group_id',
            'role',
            'fb_id',
            'fb_access_token:ntext',
            'twt_id',
            'twt_access_token:ntext',
            'twt_access_secret:ntext',
            'ldn_id',
            'status',
            'email_verified:email',
            'last_login',
            'by_admin',
            'created_at',
            'updated_at',
            'avatar',
            'gender',
            'cityCode',
            'districtId',
        ],
    ]) ?>

</div>

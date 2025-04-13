<?php
	use yii\bootstrap\ActiveForm;
	use yii\grid\GridView;

	$this->title                   = 'Tìm kiếm';
	$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-orders-index">
	<?php
		$form = ActiveForm::begin([
			'id'      => 'form',
			'method'  => 'post',
			'options' => [
				'class' => 'don-hang-da-dat-search-form'
			],
		]); ?>
	<div class="check-search">
		<div class="form-item form-type-textfield form-item-order-billLadinID">
			<div class="form-group field-tbordersearch-billLadinID">
				<input style="width: 300px" type="text" id="orderNumber" class="form-control" name="orderNumber" value="<?= !empty($params['orderNumber']) ? $params['orderNumber'] : '' ?>" placeholder="shipping code..">
			</div>
		</div>
		<button type="submit" id="btn-search-order" name="op" value="search" class="form-submit">
			<i class="fa fa-search" aria-hidden="true"></i> Search
		</button>
	</div>
	<?php echo \common\widgets\Alert::widget(); ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'filterModel'  => $searchModel,
		'layout'       => '{items}<div class="pager-container">{summary}{pager}</div>',
		'tableOptions' => ['class' => 'grid-order table  table-bordered table-hover table-striped'],
		'rowOptions'   => function ($model, $key, $index, $grid) {
			$class = $index % 2 ? 'success' : 'warning';

			return array('key' => $key, 'index' => $index, 'class' => $class);
		},
		'columns'      => [
			[
				'class'         => 'yii\grid\SerialColumn',
				'contentOptions' => ['class' => 'text-center'],
				'headerOptions' => ['class' => 'text-center', 'style' => 'width: 5%;'],
				'header'        => 'STT',
			],
			[
				'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 20%;'],
				'contentOptions' => ['class' => 'text-center'],
				'label'          => 'Shipping code',
				'attribute'      => 'billLadinID',
				'format'         => 'raw',
				'value'          => function ($model) {
					return isset($model->orderSupplier->billLadinID) ? $model->orderSupplier->billLadinID.'/'.$model->orderID : null;
				}
			],
			[
				'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 20%;'],
				'contentOptions' => ['class' => 'text-center'],
				'attribute'     => 'orderDate',
				'label'         => 'confirmed date',
				'format'        => 'raw',
				'value'         => function ($model) {
					return date('d-m-Y H:i:s', strtotime($model->orderDate));
				}
			],
			[
				'headerOptions'  => ['class' => 'text-center', 'style' => 'width: 20%;'],
				'contentOptions' => ['class' => 'text-center'],
				'label'         => 'Shipping status',
				'format'        => 'raw',
				'value'         => function ($model) {
					return \common\components\CommonLib::getStatusShipping($model->shippingStatus);
				}
			],
		],
	]); ?>
	<?php ActiveForm::end(); ?>
</div>

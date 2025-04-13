<?php
	use yii\helpers\Html;
    
	$labelOptions = ['class' => 'col-sm-2 control-label'];
	$inputOptions = ['class' => 'form-control'];
?>

<div class="box-footer">
	<label class="col-sm-2 control-label">
		<a class="dashed-link collapsed" data-toggle="collapse" href="#seo-form" aria-expanded="false" aria-controls="seo-form">
            <!--<i class="fa fa-caret-down"></i>-->
            Seo texts
        </a>
	</label>
</div>
<div class="collapse" id="seo-form">
	<div class="form-group">
		<?= Html::activeLabel($model, 'h1', $labelOptions) ?>
		<div class="col-sm-10">
			<?= Html::activeTextInput($model, 'h1', $inputOptions) ?>
		</div>
	</div>
	<div class="form-group">
		<?= Html::activeLabel($model, 'title_seo', $labelOptions) ?>
		<div class="col-sm-10">
		<?= Html::activeTextInput($model, 'title_seo', $inputOptions) ?>
		</div>
	</div>
	<div class="form-group">
		<?= Html::activeLabel($model, 'keyword_seo', $labelOptions) ?>
		<div class="col-sm-10">
		<?= Html::activeTextInput($model, 'keyword_seo', $inputOptions) ?>
		</div>
	</div>
	<div class="form-group">
		<?= Html::activeLabel($model, 'description_seo', $labelOptions) ?>
		<div class="col-sm-10">
		<?= Html::activeTextarea($model, 'description_seo', $inputOptions) ?>
		</div>
	</div>
</div>
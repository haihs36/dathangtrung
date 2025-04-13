<?php
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Html;

BootstrapPluginAsset::register($this);

$labelOptions = ['class' => 'col-sm-2 control-label'];
$inputOptions = ['class' => 'form-control'];
?>
<div class="box collapsed-box">
      <div class="box-header">
          <div class="form-group">
              <label class="col-sm-2 control-label">Seo texts</label>
              <div class="col-sm-10">
                  <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-plus"></i></button>
              </div>
          </div>
      </div>
    <div class="box-body" style="display: none;">
<!--        <a class="dashed-link collapsed" data-toggle="collapse" href="#seo-form" aria-expanded="false" aria-controls="seo-form">Seo texts</a>-->
        <div class="" id="seo-form">
            <div class="form-group">
                <?= Html::activeLabel($model, 'h1', $labelOptions) ?>
                <div class="col-sm-10">
                    <?= Html::activeTextInput($model, 'h1', $inputOptions) ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'title', $labelOptions) ?>
                <div class="col-sm-10">
                    <?= Html::activeTextInput($model, 'title', $inputOptions) ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'keywords', $labelOptions) ?>
                <div class="col-sm-10">
                    <?= Html::activeTextInput($model, 'keywords', $inputOptions) ?>
                </div>
            </div>
            <div class="form-group">
                <?= Html::activeLabel($model, 'description', $labelOptions) ?>
                <div class="col-sm-10">
                    <?= Html::activeTextarea($model, 'description', $inputOptions) ?>
                </div>
            </div>
        </div>
    </div>

</div>

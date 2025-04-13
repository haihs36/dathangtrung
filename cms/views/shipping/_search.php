<?php
    use yii\widgets\ActiveForm;

    $form = ActiveForm::begin([
        'id'      => 'form',
        'action'  => ['index'],
        'method'  => 'get',
        'options'              => [
            'class' => "form-horizontal",
        ]
    ]); ?>


    <div class="box-body">
        <?= $form->field($model, 'startDate', [
            'template' => '{label}<div class="input-group col-sm-4"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
        ])->textInput(['placeholder' => 'Từ ngày', 'class' => 'form-control', 'id' => 'startDate'])->label('Từ ngày',['class'=>"col-sm-2 control-label"]) ?>

        <?= $form->field($model, 'endDate', [
            'template' => '{label}<div class="input-group col-sm-4"><div class="input-group-addon"><i class="fa fa-calendar"></i> </div>{input}</div>'
        ])->textInput(['placeholder' => 'Đến ngày', 'class' => 'form-control', 'id' => 'endDate'])->label('Đến ngày', ['class' => "col-sm-2 control-label"]) ?>

        <div class="form-group mt15 mb15">
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-4 text-right">
                <button type="submit" class="btn btn-primary">Thống kê</button>
            </div>
        </div>


        <?php if(!empty($params['keywords'])){ ?>
            <div class="clear">
                Tìm kiếm với từ khóa: <?php echo (!empty($params['keywords']) ? ': <b>'.$params['keywords'].'</b>' : '') ?>
            </div>
        <?php } ?>
    </div>
<?php ActiveForm::end(); ?>





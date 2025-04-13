<?php
/*
cfUserMgmt for YII

Copyright 2015, CodeFire Technologies Pvt Ltd (www.CodeFire.org)

This software is covered by GNU General Public License v3 (GPL-3.0)
You should have received a copy of the GNU General Public License along with this program.  If not, see <http://opensource.org/licenses/GPL-3.0>.

*/


use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\widgets\Alert;

$this->title = 'Thay đổi mật khẩu';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php echo Alert::widget(); ?>
<div class="boxs">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"> <i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin();?>
        <div class="row">
            <div class="col-md-5">
                <?php echo $form->field($model, 'old_password')->passwordInput(['placeholder'=>'Mật khẩu cũ'])->label($model->getAttributeLabel('old_password'));?>
                <?php echo $form->field($model, 'password')->passwordInput(['placeholder'=>'Mật khẩu mới'])->label($model->getAttributeLabel('Mật khẩu mới'));?>
                <?php echo $form->field($model, 'confirm_password')->passwordInput(['placeholder'=>'Xác nhận mật khẩu'])->label($model->getAttributeLabel('confirm_password'));?>
                <?php echo Html::submitButton('Thay đổi mật khẩu', ['class'=>'btn btn-success']);?>
            </div>
        </div>
        <?php ActiveForm::end();?>

    </div>
</div>



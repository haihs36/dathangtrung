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

    $this->title                   = 'Thay đổi mật khẩu';
    $this->params['breadcrumbs'][] = ['label' => 'Danh sách khách hàng', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>
<?php echo Alert::widget(); ?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= Html::encode($this->title) ?></h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-5">
                <?php echo $form->field($model, 'password')->textInput(['placeholder' => 'Mật khẩu mới'])->label($model->getAttributeLabel('Mật khẩu mới')); ?>
                <?php echo $form->field($model, 'confirm_password')->textInput(['placeholder' => 'Xác nhận mật khẩu'])->label($model->getAttributeLabel('confirm_password')); ?>

                <p class="text-right"><?php echo Html::submitButton('Change Password', ['class' => 'btn btn-success']); ?></p>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>



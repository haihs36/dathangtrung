<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

    /* @var $this yii\web\View */
    /* @var $form yii\widgets\ActiveForm */
    /* @var $model \common\models\LoginForm */

    $this->title = 'Login';
    $this->params['breadcrumbs'][] = $this->title;
    $rule = '100';


?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

<script>
    var total = 45;
    var hot = 20;
    var normal = 25;

    function run1() {
        for (i = 1; i <= (hot / total) * 100; i++) {
            $("#hot").css("width", i + "%");
        }
    }
    function run2(j) {
        for (i = j; i <= (normal / total) * 100; i++) {
            $("#normal").css("width", i + "%");
        }
    }
    $(function () {

        var timeout = setTimeout(function () {
            for (i = 1; i <= (hot / total) * 100; i++) {
                $("#hot").css("width", i + "%");
                for (j = i + 1; j < (normal / total) * 100; j++) {
                    $("#normal").css("width", i + "%");
                }
            }
        }, 5000);


//       setInterval(function(){
//            run2(j);
//        },timeout);
    });

    //        var stops = [25,55,85,100];
    //        $.each(stops, function(index, value){
    //            setTimeout(function(){
    //                $( ".progress-bar" ).css( "width", value + "%" );
    //            }, index * 1500);
    //        });
</script>
<!--<div class="progress">-->
<!--    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;">-->
<!--        <span class="sr-only">0% Complete</span>-->
<!--    </div>-->
<!--</div>-->
<div style="width: 100%;background-color: #cccccc;padding: 5px 0">
    <div id="hot" style=";float:left;width:0%;padding: 3px 0;background-color: #00578d"></div>
    <div id="normal" style="float:left;width:0%;padding: 3px 0;background-color: #008000"></div>
</div>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>
            <?= $form->field($model, 'rememberMe')->checkbox() ?>
            <div class="form-group">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

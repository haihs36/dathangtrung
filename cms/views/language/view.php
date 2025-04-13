<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model cms\models\TbLanguage */

$this->params['breadcrumbs'][] = ['label' => 'Tb Languages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>


<div>
    <div id="translation"></div>
    <div class="destination" >
        <p><input id="keyword" placeholder="Nhập từ khóa cần dịch" type="text" title=""></p>
        <div id="result-s"></div>
        <div id="google_translate_element"></div>
    </div>
</div>
<script>
    (function($) {
        $('#keyword').on('keyup', function() {
            $('#result-s').html($(this).val());
        });
    })(jQuery);

</script>
<script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({ pageLanguage: "vi" }, "google_translate_element");
    }
    $(function () {
        $.getScript("//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit");
    });
</script>

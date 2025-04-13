<?php
    use yii\helpers\Html;
    use yii\grid\GridView;

    /* @var $this yii\web\View */
    /* @var $searchModel common\models\TbOrdersMessageSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */

    $this->title = 'Danh sách các thông báo cho đơn hàng';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="tb-orders-message-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
//        'layout' =>'{items}{summary}{pager}',
        'tableOptions' => ['class' => 'table  table-bordered table-hover table-striped'],
        'rowOptions'=>function ($model, $key, $index, $grid){
            $class=$index%2?'success':'warning';
            return array('key'=>$key,'index'=>$index,'class'=>$class);
        },

        'columns'      => [

            'id',
            [
                'label'      => 'Người gửi',
                'format'         => 'raw',
                'attribute'         => 'title',
                'value'          => function ($model) {
                    return $model->user->username;
                }
            ],
            [
                'label'      => 'Tiêu đề',
                'format'         => 'raw',
                'attribute'         => 'title',
                'value'          => function ($model) {
                    return $model->title;
                }
            ],
            [
                'label'          => 'Thao tác',
                'headerOptions'  => ['class' => 'text-center','style' => 'width: 15%;'],
                'contentOptions' => ['class' => 'text-center'],
                'format' => 'raw',
                'value'          => function ($model) {
                    return $model->getAction();
                }
            ],

//            'content:ntext',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <div id="sms-result" style="display: none">
        <div class="modal-header" style="padding: 0;margin: 0"><h3 >Nội dung chi tiết</h3></div>
         <div class="modal-body">
             <div class="result"></div>
         </div>
    </div>
</div>
<script>
    $(function () {
        $('.view-sms').on('click', function (e) {            
            var btnview = $(this);

           
            $('#sms-result').show();            
            var html = btnview.children('.detail').text();
            $('#sms-result .result').html(html);
          
            if(btnview.hasClass('isView')){
                return false;
            }   
            btnview.addClass('isView');
              
            $.ajax({
                url: btnview.attr('href'),
                type: 'post',
                success: function (rs) {
                   if(rs.status==1){
                    btnview.children('.title').html('Đã xem');
                   }     
                }
            });

            return false;
        });
    });
</script>


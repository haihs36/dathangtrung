<div class="left">
    <div class="support-online block">
        <h3>Hỗ trợ trực tuyến</h3>
        <?php $support = \common\models\TbSupport::find()->asArray()->all();
            if($support){
        ?>
        <ul>
            <?php
                foreach ($support as $item){
            ?>
            <li>
                <?php if(!empty($item['image'])){ ?>
                    <img class="avatar" src="<?= Yii::$app->params['FileDomain'].$item['image'] ?>" alt="<?= $item['name'] ?>" title="<?= $item['name'] ?>">
                <?php } ?>
                <a class="skype"  rel="nofollow">
                    <span class="name"><?= $item['name'] ?></span>
                </a>
                <div class="item">
                    <span class="phone">
                        <?php echo $item['mobile'] ?>
                     </span>
                    <?php if(!empty($item['thumb'])){ ?>
                        <span style="color: #000;font-weight: bold">Mã QR </span>
                         <img src="<?= Yii::$app->params['FileDomain'].$item['thumb'] ?>" style="max-width: 80px">

                    <?php } ?>
                </div>
            </li>
            <?php } ?>

        </ul>
        <?php } ?>
    </div>
</div>
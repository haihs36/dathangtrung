<?php
    $support = \common\models\TbSupport::find()->limit(6)->asArray()->all();
    $sp1 = array_splice($support,0,3);
?>
<h3>Hỗ trợ trực tuyến</h3>
<div class="support-online ">

    <?php
        if($sp1){
            ?>
            <ul>
                <?php
                    foreach ($sp1 as $k => $item){
                        ?>
                        <li style="<?= ($k+1)%3==0 ? 'padding-right:0':'' ?>">
                            <div>
                                <?php if(!empty($item['image'])){ ?>
                                    <img class="avatar" src="<?= Yii::$app->params['FileDomain'].$item['image'] ?>" alt="<?= $item['name'] ?>" title="<?= $item['name'] ?>">
                                <?php } ?>
                                <a class="skype" href="<?= !empty($item['skype']) ? 'skype:'.$item['skype'].'@vupham.com?call':'javascript:void(0)' ?>" rel="nofollow">
                                    <dl class="text"><span class="name"><?= $item['name'] ?></span></dl>
                                </a>
                                <span class="phone">
                                    <?php echo $item['mobile'] ?>
                                 </span>
                               <?php if(!empty($item['thumb'])){ ?>
                                    <dl>
                                    <span style="color: #000;font-weight: bold">Mã <?= $item['nameCode'] ?> </span>
                                    </dl>
                                     <dl>
                                        <img class="qrcode" src="<?= Yii::$app->params['FileDomain'].$item['thumb'] ?>" style="max-width: 120px">
                                     </dl>
                                <?php } ?>  
                            </div>
                        </li>
                    <?php } ?>

            </ul>
        <?php } ?>
</div>
<div class="support-online box2">
    <?php if($support){
        ?>
        <ul class="box2">
            <?php
                foreach ($support as $k => $item){
                    ?>
                    <li style="<?= ($k+1)%3==0 ? 'padding-right:0':'' ?>">
                        <div>
                            <?php if(!empty($item['image'])){ ?>
                                <img class="avatar" src="<?= Yii::$app->params['FileDomain'].$item['image'] ?>" alt="<?= $item['name'] ?>" title="<?= $item['name'] ?>">
                            <?php } ?>
                            <a class="skype" href="<?= !empty($item['skype']) ? 'skype:'.$item['skype'].'@vupham.com?call':'javascript:void(0)' ?>" rel="nofollow">
                                <dl class="text"><span class="name"><?= $item['name'] ?></span></dl>
                            </a>
                                <span class="phone">
                                    <?php echo $item['mobile'] ?>
                                 </span>
                                
                                <?php if(!empty($item['thumb'])){ ?>
                                 <dl>
                                    <span style="color: #000;font-weight: bold">Mã <?= $item['nameCode'] ?> </span>
                                     </dl>
                                    <dl>
                                    <img class="qrcode" src="<?= Yii::$app->params['FileDomain'].$item['thumb'] ?>" style="max-width: 120px">
                                     </dl>
                                <?php } ?>  
                        </div>
                    </li>
                <?php } ?>

        </ul>
    <?php } ?>
</div>
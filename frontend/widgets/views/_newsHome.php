<?php if($data){ ?>
    <?php foreach ($data as $item){
        $urlDetail = \yii\helpers\Url::to(['articles/detail', 'slug' => $item['slug'], 'id' => $item['news_id']]);
        ?>
        <div class="item">
            <article class="blog-item">
                <div class="blog-item-thumbnail">
                    <a href="<?php echo $urlDetail ?>">
                        <?php $thumb = \common\components\CommonLib::getAvatar($item['thumb'])?>
                        <picture>
                            <img width="480px" height="245px" style="width:480px;height: 245px " src="<?= $thumb ?>" alt="<?php echo $item['title']  ?>" />
                        </picture>

                    </a>
                    <div class="articles-date">
                        <?php
                            $date = date('m/d',$item['time']);
                            $year = date('Y',$item['time']);
                        ?>
                        <span><?= $date ?></span><?= $year ?>
                    </div>
                </div>
                <h3 class="blog-item-name margin-top-10">
                    <a href="<?php echo $urlDetail ?>" title="<?php echo $item['title']  ?>">
                        <?php echo \common\components\CommonLib::cut_string($item['title'],100);  ?>
                    </a>
                </h3>
                <p class="blog-item-summary margin-bottom-5"><?php echo \common\components\CommonLib::cut_string($item['short'],300);  ?></p>
            </article>
        </div>
        <?php } ?>
<?php } ?>



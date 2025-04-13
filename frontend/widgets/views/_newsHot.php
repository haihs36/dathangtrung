<?php
if ($data) {
?>
    <div class="heading">
        <h2 class="title-head"><span><i class="fa fa-ship" aria-hidden="true"></i> Bài viết nổi bật</span></h2>
    </div>
    <div class="list-blogs">
        <div class="row">
    <?php
        foreach ($data as $k=> $item) {
            $urlDetail = \yii\helpers\Url::to(['articles/detail', 'slug' => $item['slug'], 'id' => $item['news_id']]);
            $thumb = \common\components\CommonLib::getAvatar($item['thumb'])
            ?>
            <article class="blog-item blog-item-list col-md-12">
                <a title="<?php echo $item['title'] ?>" href="<?= $urlDetail ?>" class="panel-box-media">
                    <img src="<?= $thumb ?>" width="70" height="70" alt="<?php echo $item['title'] ?>">
                </a>
                <div class="blogs-rights">
                    <h3 class="blog-item-name">
                        <a href="<?= $urlDetail ?>" title="<?php echo $item['title'] ?>">
                            <?php echo $item['title'] ?>
                        </a>
                    </h3>
                    <div class="post-time"><?php echo date('m/d/Y',$item['time']) ?></div>
                </div>
            </article>
        <?php } ?>
        </div>
    </div>



<?php } ?>
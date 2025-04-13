<?php
    use yii\helpers\Url;

    $setting = Yii::$app->controller->setting;
    $baseUrl = Yii::$app->params['baseUrl'];
    /*seo*/
    $url = Url::to(['articles/catenews', 'slug' => $slug], true);

    $title_seo                  = !empty($current_cat->seoText->title) ? $current_cat->seoText->title : $current_cat->title;
    $keyword_seo                = !empty($current_cat->seoText->keywords) ? $current_cat->seoText->keywords : $current_cat->title;
    $description_seo            = !empty($current_cat->seoText->description) ? $current_cat->seoText->description : $current_cat->description;
    $data_seo['title']          = $title_seo;
    $data_seo['keywords']       = $keyword_seo;
    $data_seo['description']    = $description_seo;
    $data_seo['og:title']       = ['itemprop' => "headline", 'content' => !empty($current_cat->title) ? $current_cat->title : $title_seo];
    $data_seo['og:description'] = ['itemprop' => "description", 'content' => !empty($current_cat->description) ? $current_cat->description : $description_seo];;
    $data_seo['og:url']       = ['itemprop' => "url", 'content' => $url];
    $data_seo['og:site_name'] = $setting['site_name'];
    $data_seo['canonical']    = ['href' => $url];
    \frontend\widgets\SeoMeta::widget(['seo' => $data_seo]);
?>
<section class="page-title-section">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="page-header-wrap">
                    <div class="page-header">
                        <h2 class="txt-header"><?php echo $current_cat->title ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Single-Service-Start -->
<section class="page-content section-70 section-md-98">
    <div class="container">
        <div class="row">
            <div class=" col-xs-12">
                <div class="view-content">
                    <?php if ($data): ?>
                        <div class="item-list">
                            <ul>
                                <?php foreach ($data as $item):
                                    $urlDetail = Url::to(['articles/detail', 'slug' => $item['slug'], 'id' => $item['news_id']]);
                                    ?>
                                    <li class="views-row views-row-1 views-row-odd views-row-first">
                                        <div class="views-field views-field-field-image">
                                            <div class="field-content">
                                                <?php if(!empty($item['thumb'])){ ?>
                                                    <a title="<?php echo $item['title'] ?>" href="<?php echo $urlDetail ?>">
                                                        <img src="<?php echo Yii::$app->params['FileDomain'] . $item['thumb'] ?>" alt="<?php echo $item['title'] ?>" height="150" width="200">
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="views-field views-field-title">
                                            <span class="field-content"><a href="<?php echo $urlDetail ?>"><?php echo $item['title'] ?></a></span>
                                        </div>
                                        <div class="views-field views-field-field-lead">
                                            <div class="field-content"><?php echo \yii\helpers\StringHelper::truncate($item['short'], 200); ?></div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="text-center">
                            <?php
                                echo \yii\widgets\LinkPager::widget(
                                    [
                                        'pagination' => $pages,
                                        'prevPageLabel' => '← Previous',
                                        'nextPageLabel' => 'Next →',
                                    ]);
                            ?>
                        </div>
                    <?php endif ?>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section>
<!-- Single-Service-End-->
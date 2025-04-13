<?php
use common\helpers\Image;
use common\models\Photo;
use common\widgets\Fancybox;
use yii\helpers\Html;
use yii\helpers\Url;
use cms\assets\PhotosAsset;

PhotosAsset::register($this);
Fancybox::widget(['selector' => '.plugin-box']);

$class = get_class($this->context->model);
$item_id = $this->context->model->primaryKey;
$linkParams = [
    'class' => $class,
    'item_id' => $item_id,
];

$photoTemplate = '<tr data-key="{{photo_id}}"><td>{{photo_id}}</td>\
    <td><a href="{{photo_image}}" class="plugin-box" title="{{photo_description}}" rel="easyii-photos"><img class="photo-thumb" id="photo-{{photo_id}}" src="{{photo_thumb}}"></a></td>\
    <td>\
        <textarea class="form-control photo-description">{{photo_description}}</textarea>\
        <a href="' . Url::to(['/photos/description/{{photo_id}}']) . '" class="btn btn-sm btn-primary disabled save-photo-description">Save</a>\
    </td>\
    <td class="control vtop">\
        <div class="btn-group btn-group-sm" role="group">\
            <a href="' . Url::to(['/photos/up/{{photo_id}}'] + $linkParams) . '" class="btn btn-default move-up" title="Move up"><span class="glyphicon glyphicon-arrow-up"></span></a>\
            <a href="' . Url::to(['/photos/down/{{photo_id}}'] + $linkParams) . '" class="btn btn-default move-down" title="Move down"><span class="glyphicon glyphicon-arrow-down"></span></a>\
            <a href="' . Url::to(['/photos/image/{{photo_id}}'] + $linkParams) . '" class="btn btn-default change-image-button" title="Change image"><span class="glyphicon glyphicon-upload"></span></a>\
            <a href="' . Url::to(['/photos/delete/{{photo_id}}']) . '" class="btn btn-default color-red delete-photo" title="Delete item"><span class="glyphicon glyphicon-remove"></span></a>\
            <input type="file" name="Photo[image]" class="change-image-input hidden">\
        </div>\
    </td>\
</tr>';
$this->registerJs("
var photoTemplate = '{$photoTemplate}';
", \yii\web\View::POS_HEAD);
$photoTemplate = str_replace('>\\', '>', $photoTemplate);
?>

<button id="photo-upload" class="btn btn-success text-uppercase"><span class="glyphicon glyphicon-arrow-up"></span> Upload</button>
<small id="uploading-text" class="smooth">Uploading. Please wait<span></span></small>
<?php echo \common\widgets\Alert::widget() ?>
<table id="photo-table" class="table table-hover" style="display: <?= count($photos) ? 'table' : 'none' ?>;">
    <thead>
    <tr>
        <th width="50">#</th>
        <th width="150"> Image</th>
        <th>Description</th>
        <th width="150"></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($photos as $photo) : ?>
        <?= str_replace(
            ['{{photo_id}}', '{{photo_thumb}}', '{{photo_image}}', '{{photo_description}}'],
            [$photo->primaryKey, Image::thumb($photo->image, Photo::PHOTO_THUMB_WIDTH, Photo::PHOTO_THUMB_HEIGHT), $photo->image, $photo->description],
            $photoTemplate)
        ?>
    <?php endforeach; ?>
    </tbody>
</table>
<p class="empty" style="display: <?= count($photos) ? 'none' : 'block' ?>;">No photos uploaded yet</p>

<?= Html::beginForm(Url::to(['/photos/upload'] + $linkParams), 'post', ['enctype' => 'multipart/form-data']) ?>
<?= Html::fileInput('', null, [
    'id' => 'photo-file',
    'class' => 'hidden',
    'multiple' => 'multiple',
])
?>
<?php Html::endForm() ?>
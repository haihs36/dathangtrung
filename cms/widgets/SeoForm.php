<?php
	namespace app\widgets;

	use Yii;
	use yii\base\Widget;
	use yii\base\InvalidConfigException;

	class SeoForm extends Widget
	{
		public $model;

		public function init()
		{
			parent::init();

			if (empty($this->model)) {
				throw new InvalidConfigException('Required `model` param not set.');
			}
		}

		public function run()
		{
			echo $this->render('seo_form', [
				'model' => $this->model
			]);
		}

	}
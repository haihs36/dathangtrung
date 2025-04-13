<?php
	/**
	 * Created by PhpStorm.
	 * User: HaiHS
	 * Date: 8/17/2017
	 * Time: 2:29 PM
	 */

	namespace cms\models;


	use yii\base\Model;

	class TbChart extends Model
	{
		public $startDate;
		public $endDate;
		public $businessID;
		public $charType;


		public function rules()
		{
			return [
				[['businessID','charType'], 'integer'],
				[['startDate', 'endDate'], 'safe'],
			];
		}

		public static function getStatisticalType($type = 0){
			$list = [
				1 => 'Doanh thu đơn hàng (không bao gồm phí dịch vụ)',
				2 => 'Doanh thu % phí dịch vụ',
				3 => 'Tổng tiền cân nặng',
//				4 => 'Tiền ship cho khách ở VN',
				5 => 'Phí ship nội địa',
//				6 => 'Tiền bồi thường',
				7 => 'Tổng tiền chiết khấu được',
				9 => 'Thanh toán thực tế',
				8 => 'Chiết khấu đơn hàng cho KD',
				10 => 'Tổng số dư ví',
				11 => 'Tổng còn nợ',
			];

			return ($type) ? [$list[$type]] : $list;
		}

	}
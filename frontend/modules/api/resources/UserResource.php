<?php
/**
 * User: TheCodeholic
 * Date: 3/7/2020
 * Time: 9:27 AM
 */

namespace frontend\modules\api\resources;
use common\models\Custommer;


/**
 * Class UserResource
 *
 * @package frontend\modules\api\resources
 */
class UserResource extends Custommer
{

    public function fields()
    {
        return [
            'access_token', 'phone', 'email', 'expire_at', 'fullname','billingAddress'
        ];
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: haihs
 * Date: 3/12/2018
 * Time: 10:42 AM
 */

namespace common\components;


use yii\helpers\Url;
use Yii;

class AJAXController extends \yii\web\Controller
{


    public $upload_image;
    public $setting;
    public $error;
    public $enableCsrfValidation = false;

    public function init()
    {
        $this->upload_image = Yii::$app->params['UPLOAD_IMAGE'];
        $this->setting = CommonLib::getAllSettings();;

        parent::init();

    }

    public function back()
    {
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Set return url for module in sessions
     * @param mixed $url if not set, returnUrl will be current page
     */
    public function setReturnUrl($url = null)
    {
        Yii::$app->getSession()->set($this->module->id . '_return', $url ? Url::to($url) : Url::current());
    }

    /**
     * Get return url for module from session
     * @param mixed $defaultUrl if return url not found in sessions
     * @return mixed
     */
    public function getReturnUrl($defaultUrl = null)
    {
        return Yii::$app->getSession()->get($this->module->id . '_return', $defaultUrl ? Url::to($defaultUrl) : Url::to('/admin/' . $this->module->id));
    }

    public function formatResponse($success, $back = true)
    {

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($this->error) {
                return ['result' => 'error', 'error' => $this->error];
            } else {
                $response = ['result' => 'success'];
                if ($success) {
                    if (is_array($success)) {
                        $response = array_merge(['result' => 'success'], $success);
                    } else {
                        $response = array_merge(['result' => 'success'], ['message' => $success]);
                    }
                }

                return $response;
            }
        } else { die('sdf');
            if ($this->error) {
                $this->flash('error', $this->error);
            } else {
                if (is_array($success) && isset($success['message'])) {
                    $this->flash('success', $success['message']);
                } elseif (is_string($success)) {
                    $this->flash('success', $success);
                }
            }

            return $back ? $this->back() : $this->refresh();
        }
    }

    public function flash($type, $message)
    {
        Yii::$app->getSession()->setFlash($type == 'error' ? 'danger' : $type, $message);
    }

}
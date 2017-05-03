<?php

namespace app\modules\invoice\controllers;

use yii\web\Controller;

/**
 * Default controller for the `invoice` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionCreate($id)
    {
        return $id;
    }
}

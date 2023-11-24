<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\Barter;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;

use yii\web\ForbiddenHttpException;

class BarterController extends ActiveController
{

    public $modelClass = 'app\modules\v1\models\Barter';

    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => Cors::className(),
            ],
        ]);
        $behaviors['authenticator']['class'] = HttpBearerAuth::className();
        $behaviors['authenticator']['only'] = ['create', 'update', 'delete', 'index'];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new Barter;
        $user_id = Yii::$app->user->identity['id'];
        $activeData = new ActiveDataProvider([
            'query' => $model::find()->orderBy("id DESC"),
            'pagination' => [
                'defaultPageSize' => -1,
                'pageSizeLimit' => -1,
            ],
        ]);
        return $activeData;

    }


    /**
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws ForbiddenHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if ($action === 'update') {
            if (!empty($model->id))
                if ($model->id !== Yii::$app->user->identity['id'])
                    throw new ForbiddenHttpException(sprintf('Access is denied', $action));
        } elseif ($action === 'index') {
            if (!isset(Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity['id'])['root']))
                throw new ForbiddenHttpException(sprintf('Access is denied', $action));
        } elseif ($action === 'view') {
            if (!empty($model->id))
                if ($model->id !== Yii::$app->user->identity['id'])
                    if (!isset(Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity['id'])['root']))
                        throw new ForbiddenHttpException(sprintf('Access is denied.', $action));
        }
    }
}
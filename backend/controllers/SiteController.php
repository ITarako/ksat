<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Operations;
use common\models\User;
use yii\data\ActiveDataProvider;
use backend\models\ReplenishForm;
use common\models\SendForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'operations', 'replenish', 'send'],
                'rules' => [
                    [
                        'actions' => ['index', 'operations', 'replenish', 'send'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        $query = User::find()->where(['status'=>10]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionOperations()
    {
        $query = Operations::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);

        return $this->render('operations', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReplenish($email)
    {
        $model = new ReplenishForm();
        $model->email = $email ?? '';

        if ($model->load(Yii::$app->request->post())) {
            if($model->replenish())
                return $this->redirect(['index']);
        }

        return $this->render('replenish', [
            'model' => $model,
        ]);
    }

    public function actionSend($email)
    {
        $model = new SendForm();
        $model->email = $email ?? '';

        if ($model->load(Yii::$app->request->post())) {
            if($model->send())
                return $this->redirect(['index']);
        }

        return $this->render('send', [
            'model' => $model,
        ]);
    }

}

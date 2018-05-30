<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\SendForm;
use common\lib\Utils;
use frontend\models\SignupForm;
use frontend\services\SiteService;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $service;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'send', 'show'],
                'rules' => [
                    [
                        #'actions' => ['logout', 'send', 'show'],
                        'allow' => true,
                        'roles' => ['user'],
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
            ]
        ];
    }
    public function init()
    {
        $this->service = new SiteService();
        parent::init();
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if(Yii::$app->user->isGuest)
            return $this->render('index');

        return $this->redirect(['show']);
    }

    public function actionShow()
    {

        $user = Yii::$app->user->identity;
        [$account, $sended, $received, $dataProvider] = $this->service->show($user);

        return $this->render('show', [
            'account' => $account,
            'sended' => $sended,
            'received' => $received,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
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

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionSend()
    {
        $model = new SendForm();

        if ($model->load(Yii::$app->request->post())) {
            if($model->send())
                return $this->redirect(['index']);
        }

        return $this->render('send', [
            'model' => $model,
        ]);
    }

    public function actionVerify($auth_key, $email)
    {
        if($user = $this->service->verify($auth_key, $email)) {
            if (Yii::$app->getUser()->login($user)) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('index');
    }
}

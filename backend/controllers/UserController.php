<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\Operations;
use backend\models\CreateForm;
use backend\models\UpdateForm;
use common\lib\Utils;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use backend\services\UserService;


class UserController extends Controller
{
    public $service;
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin']
                    ]
                ]
            ]
        ];
    }

    public function init()
    {
        $this->service = new UserService();
        parent::init();
    }

    public function actionUpdate($email)
    {
        $model = new UpdateForm();
        $model->email =$email ?? '';
        $user = User::findByEmail($email);

        $postData = Yii::$app->request->post();
        if ($postData) {
            $model->load($postData);
            if ($model->update($postData['role'])) {
                return $this->goHome();
            }
        }

        return $this->render('update', [
            'model' => $model,
            'user' => $user,
        ]);
    }


    public function actionShow($email)
    {
        $user = User::find()->where("email='$email'")->one();
        if(empty($user)) {
            Yii::$app->session->setFlash('error', "User $email does not exit");
            return $this->redirect(['index']);
        }

        [$account, $sended, $received, $dataProvider] = $this->service->show($user);

        return $this->render('show', [
            'account' => $account,
            'sended' => $sended,
            'received' => $received,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new CreateForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->create()) {
                return $this->goHome();
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
}

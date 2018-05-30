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
#use yii\data\SqlDataProvider;

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
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'operations', 'replenish', 'send'],
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $count = Yii::$app->db->createCommand('SELECT COUNT(*) FROM user')->queryScalar();

        /* $and = '';
        $postData = Yii::$app->request->post();
        if($postData) {

            if($postData['email'])
                $and .=" and email='{$postData['email']}'";

            if($postData['from_date'])
                $and .=" and DATE(o.created_at) >='{$postData['from_date']}' and DATE(op.created_at) >='{$postData['from_date']}'";

            if($postData['to_date'])
                $and .=" and DATE(o.created_at) <='{$postData['to_date']}' and DATE(op.created_at) <='{$postData['to_date']}'";

        }
        $dataProvider = new SqlDataProvider([
            'sql' => "select u.email email, sum(o.value) sended, sum(op.value) received
            from public.user u
            inner join public.account a on a.id_user=u.id
            left join public.operations o on o.id_sender = a.id
            left join public.operations op on op.id_receiver = a.id
            where u.status=10
            $and
            group by u.email",
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]); */
        $query = User::find()->where(['status'=>10]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Login action.
     *
     * @return string
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
     * Logout action.
     *
     * @return string
     */
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

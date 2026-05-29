<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use app\models\forms\LoginForm;
use app\models\forms\SignupForm;
use app\models\forms\PasswordResetRequestForm;
use app\models\forms\ResetPasswordForm;
use app\models\Car;
use app\models\Tariff;
use app\models\Review;
use app\models\Faq;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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
        $popularCars = Car::find()
            ->where(['status' => Car::STATUS_AVAILABLE])
            ->limit(6)
            ->all();
        $tariffs = Tariff::listActive();
        $reviews = Review::find()->where(['status' => Review::STATUS_APPROVED])->orderBy(['created_at' => SORT_DESC])->limit(3)->all();

        return $this->render('index', [
            'popularCars' => $popularCars,
            'tariffs' => $tariffs,
            'reviews' => $reviews,
        ]);
    }

    public function actionLogin()
    {
        $this->layout = 'auth';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        $model->password = '';
        return $this->render('login', ['model' => $model]);
    }

    public function actionSignup()
    {
        $this->layout = 'auth';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $user = $model->signup()) {
            Yii::$app->user->login($user);
            Yii::$app->session->setFlash('success', 'Регистрация успешна! Добро пожаловать.');
            return $this->redirect(['/profile/index']);
        }
        return $this->render('signup', ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    public function actionRequestPasswordReset()
    {
        $this->layout = 'auth';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Инструкция по сбросу пароля отправлена на email.');
            } else {
                Yii::$app->session->setFlash('error', 'Не удалось отправить email. Попробуйте позже.');
            }
            return $this->redirect(['login']);
        }
        return $this->render('request-password-reset', ['model' => $model]);
    }

    public function actionResetPassword($token)
    {
        $this->layout = 'auth';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Пароль успешно изменён.');
            return $this->redirect(['login']);
        }
        return $this->render('reset-password', ['model' => $model]);
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}

<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Booking;
use app\models\Car;
use app\models\PromoCode;
use app\models\forms\BookingForm;
use app\models\forms\DamageReportForm;
use app\components\BookingService;

class BookingController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'finish' => ['post'],
                    'cancel' => ['post'],
                    'damage' => ['post', 'get'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $user = Yii::$app->user->identity;

        if (!$user->isVerified()) {
            Yii::$app->session->setFlash('error', 'Для аренды необходимо пройти верификацию.');
            return $this->redirect(['/profile/documents']);
        }
        if ($user->getActiveBooking()->one()) {
            Yii::$app->session->setFlash('error', 'У вас уже есть активная аренда.');
            return $this->redirect(['active']);
        }

        $form = new BookingForm();
        if (!$form->load(Yii::$app->request->post())) {
            return $this->redirect(['/car/index']);
        }
        $booking = $form->book($user);
        if ($booking) {
            Yii::$app->session->setFlash('success', 'Аренда #' . $booking->number . ' успешно создана!');
            return $this->redirect(['active']);
        }
        $errors = $form->getFirstErrors();
        Yii::$app->session->setFlash('error', $errors ? reset($errors) : 'Не удалось забронировать.');
        return $this->redirect(['/car/view', 'id' => $form->car_id]);
    }

    public function actionActive()
    {
        $user = Yii::$app->user->identity;
        $booking = $user->getActiveBooking()->with(['car', 'tariff'])->one();
        if (!$booking) {
            return $this->render('no-active');
        }

        $damageForm = new DamageReportForm();
        return $this->render('active', [
            'booking' => $booking,
            'damageForm' => $damageForm,
        ]);
    }

    public function actionFinish($id)
    {
        $booking = $this->findBooking($id);
        [$ok, $result] = BookingService::finish($booking);
        if ($ok) {
            Yii::$app->session->setFlash('success', 'Аренда успешно завершена!');
            return $this->redirect(['/trip/view', 'id' => $booking->id]);
        }
        Yii::$app->session->setFlash('error', $result);
        return $this->redirect(['active']);
    }

    public function actionCancel($id)
    {
        $booking = $this->findBooking($id);
        $reason = Yii::$app->request->post('reason', 'Отменено пользователем');
        [$ok, $result] = BookingService::cancel($booking, $reason);
        if ($ok) {
            Yii::$app->session->setFlash('success', 'Аренда отменена. Депозит возвращён.');
            return $this->redirect(['/trip/index']);
        }
        Yii::$app->session->setFlash('error', $result);
        return $this->redirect(['active']);
    }

    public function actionDamage($id)
    {
        $booking = $this->findBooking($id);
        $form = new DamageReportForm();

        if (Yii::$app->request->isPost) {
            $form->load(Yii::$app->request->post());
            if ($form->create($booking)) {
                Yii::$app->session->setFlash('success', 'Сообщение о повреждении отправлено. Мы рассмотрим его в ближайшее время.');
                return $this->redirect(['active']);
            }
        }
        return $this->redirect(['active']);
    }

    /**
     * AJAX: проверить промокод
     */
    public function actionCheckPromo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $code = Yii::$app->request->get('code');
        $carId = Yii::$app->request->get('car_id');
        $minutes = (int)Yii::$app->request->get('minutes', 60);

        $car = Car::findOne($carId);
        if (!$car) return ['success' => false, 'message' => 'Авто не найдено'];

        $cost = $car->tariff->calculateCost($minutes, 0);
        $promo = PromoCode::findOne(['code' => strtoupper(trim($code))]);
        if (!$promo) return ['success' => false, 'message' => 'Промокод не найден'];
        [$ok, $msg, $discount] = $promo->tryApply($cost['total'], Yii::$app->user->id);

        return [
            'success' => $ok,
            'message' => $msg,
            'discount' => $discount,
            'estimated' => $cost['total'],
            'deposit' => (float)$car->tariff->deposit,
            'total' => round($car->tariff->deposit + $cost['total'] - $discount, 2),
        ];
    }

    /**
     * AJAX: пересчитать стоимость по новой длительности
     */
    public function actionCalculate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $carId = Yii::$app->request->get('car_id');
        $minutes = max(15, min(1440, (int)Yii::$app->request->get('minutes', 60)));
        $car = Car::findOne($carId);
        if (!$car) return ['error' => 'Auto not found'];
        $cost = $car->tariff->calculateCost($minutes, 0);
        return [
            'estimated' => $cost['total'],
            'deposit' => (float)$car->tariff->deposit,
            'total' => round($car->tariff->deposit + $cost['total'], 2),
        ];
    }

    /**
     * AJAX: текущая стоимость активной аренды (для live update)
     */
    public function actionCurrentCost($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $booking = Booking::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$booking) return ['error' => 'Not found'];
        return [
            'cost' => $booking->calculateCurrentCost(),
            'minutes' => $booking->getDurationMinutes(),
        ];
    }

    private function findBooking($id)
    {
        $booking = Booking::findOne(['id' => $id, 'user_id' => Yii::$app->user->id]);
        if (!$booking) throw new NotFoundHttpException('Бронирование не найдено.');
        return $booking;
    }
}

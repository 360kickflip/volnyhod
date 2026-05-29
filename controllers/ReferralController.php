<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\User;
use app\models\ReferralReward;
use app\models\Setting;

class ReferralController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;

        // Если код ещё не сгенерирован (для старых аккаунтов)
        if (!$user->referral_code) {
            $user->generateReferralCode();
            $user->save(false);
        }

        $referrals = User::find()->where(['referred_by_user_id' => $user->id])->orderBy(['created_at' => SORT_DESC])->all();
        $rewards = ReferralReward::find()->where(['referrer_id' => $user->id])->indexBy('referred_id')->all();

        $stats = [
            'total_invited' => count($referrals),
            'total_rode' => ReferralReward::find()->where(['referrer_id' => $user->id, 'status' => ReferralReward::STATUS_PAID])->count(),
            'total_earned' => $user->getReferralEarnings(),
            'pending_count' => ReferralReward::find()->where(['referrer_id' => $user->id, 'status' => ReferralReward::STATUS_PENDING])->count(),
        ];

        return $this->render('index', [
            'user' => $user,
            'referrals' => $referrals,
            'rewards' => $rewards,
            'stats' => $stats,
            'bonusReferrer' => (float)Setting::get('referral_bonus_referrer', 300),
            'bonusReferred' => (float)Setting::get('referral_bonus_referred', 300),
            'programActive' => (bool)Setting::get('referral_program_active', '1'),
        ]);
    }
}

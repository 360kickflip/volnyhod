<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\ReferralReward;
use app\models\User;

class ReferralController extends BaseController
{
    public function actionIndex()
    {
        // Топ рефереров
        $top = Yii::$app->db->createCommand("
            SELECT u.id, u.name, u.email, u.referral_code,
                   COUNT(r.id) AS invited,
                   SUM(CASE WHEN rr.status = 'paid' THEN 1 ELSE 0 END) AS rode,
                   COALESCE(SUM(CASE WHEN rr.status = 'paid' THEN rr.amount_referrer ELSE 0 END), 0) AS earned
            FROM {{%user}} u
            LEFT JOIN {{%user}} r ON r.referred_by_user_id = u.id
            LEFT JOIN {{%referral_reward}} rr ON rr.referrer_id = u.id
            WHERE EXISTS (SELECT 1 FROM {{%user}} ru WHERE ru.referred_by_user_id = u.id)
            GROUP BY u.id, u.name, u.email, u.referral_code
            ORDER BY invited DESC, earned DESC
            LIMIT 20
        ")->queryAll();

        // Общая статистика
        $stats = [
            'total_invited' => (int)User::find()->where(['is not', 'referred_by_user_id', null])->count(),
            'total_rode' => (int)ReferralReward::find()->where(['status' => ReferralReward::STATUS_PAID])->count(),
            'total_pending' => (int)ReferralReward::find()->where(['status' => ReferralReward::STATUS_PENDING])->count(),
            'total_paid' => (float)ReferralReward::find()->where(['status' => ReferralReward::STATUS_PAID])->sum('amount_referrer + amount_referred'),
        ];

        $stats['conversion'] = $stats['total_invited'] > 0
            ? round(100 * $stats['total_rode'] / $stats['total_invited'], 1)
            : 0;

        // Последние награды
        $rewardsProvider = new ActiveDataProvider([
            'query' => ReferralReward::find()->with(['referrer', 'referred', 'booking'])->orderBy(['created_at' => SORT_DESC]),
            'pagination' => ['pageSize' => 25],
        ]);

        return $this->render('index', [
            'top' => $top,
            'stats' => $stats,
            'rewardsProvider' => $rewardsProvider,
        ]);
    }
}

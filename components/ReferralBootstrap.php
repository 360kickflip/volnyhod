<?php

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Application;
use yii\web\Cookie;
use app\models\User;

/**
 * При появлении в URL параметра ?ref=CODE сохраняет его в cookie на 30 дней.
 * При регистрации SignupForm читает cookie и привязывает реферал.
 */
class ReferralBootstrap implements BootstrapInterface
{
    const COOKIE_NAME = 'vh_ref';
    const COOKIE_TTL_DAYS = 30;

    public function bootstrap($app)
    {
        $app->on(Application::EVENT_BEFORE_REQUEST, function () use ($app) {
            if (!$app instanceof \yii\web\Application) return;
            $ref = $app->request->get('ref');
            if (!$ref) return;
            $code = strtoupper(trim($ref));
            // Валидация: код существует?
            $user = User::findByReferralCode($code);
            if (!$user) return;
            // Не сохраняем для самого себя
            if (!$app->user->isGuest && $app->user->id === $user->id) return;

            $cookie = new Cookie([
                'name' => self::COOKIE_NAME,
                'value' => $code,
                'expire' => time() + 86400 * self::COOKIE_TTL_DAYS,
                'httpOnly' => true,
                'sameSite' => Cookie::SAME_SITE_LAX,
            ]);
            $app->response->cookies->add($cookie);
        });
    }

    public static function getCookieCode(): ?string
    {
        $cookies = Yii::$app->request->cookies;
        $val = $cookies->getValue(self::COOKIE_NAME);
        return $val ?: null;
    }

    public static function clearCookie(): void
    {
        Yii::$app->response->cookies->remove(self::COOKIE_NAME);
    }
}

<?php
/** @var yii\web\View $this */
/** @var app\models\Page $page */

use yii\helpers\HtmlPurifier;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $page->title;
$this->params['meta_description'] = $page->meta_description;

// Очистка контента + сбор оглавления из <h2 id="...">
$content = HtmlPurifier::process($page->content, [
    'HTML.Allowed' => 'h2[id],h3[id],h4,p,ul,ol,li,br,strong,em,b,i,u,s,a[href|target|rel],blockquote,table,thead,tbody,tr,th,td,code,pre,hr,div[class],span[class]',
    'HTML.SafeIframe' => false,
    'HTML.TargetBlank' => true,
    'AutoFormat.Linkify' => true,
]);

// Собираем оглавление
preg_match_all('/<h2[^>]*id=["\']([^"\']+)["\'][^>]*>(.*?)<\/h2>/i', $content, $tocMatches, PREG_SET_ORDER);
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= Url::home() ?>" class="text-decoration-none">Главная</a></li>
        <li class="breadcrumb-item active"><?= Html::encode($page->title) ?></li>
    </ol>
</nav>

<div class="row g-4">
    <div class="<?= $tocMatches ? 'col-lg-9' : 'col-lg-9 mx-auto' ?>">
        <h1 class="mb-2"><?= Html::encode($page->title) ?></h1>
        <p class="text-soft small mb-4">Обновлено <?= Yii::$app->formatter->asDate($page->updated_at) ?></p>

        <div class="card">
            <div class="card-body p-4 p-md-5 legal-content">
                <?= $content ?>

                <hr class="my-4">
                <p class="small text-soft mb-0">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Если у вас остались вопросы — напишите в
                    <a href="<?= Url::to(['/support/index']) ?>">поддержку</a>
                    или на <a href="mailto:<?= Yii::$app->params['supportEmail'] ?>"><?= Yii::$app->params['supportEmail'] ?></a>.
                </p>
            </div>
        </div>
    </div>

    <?php if ($tocMatches): ?>
        <div class="col-lg-3 d-none d-lg-block">
            <div class="legal-toc">
                <h6>Содержание</h6>
                <?php foreach ($tocMatches as $m): ?>
                    <a href="#<?= Html::encode($m[1]) ?>" data-toc-link><?= strip_tags($m[2]) ?></a>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>
</div>

<?php if ($tocMatches): ?>
<?php $this->registerJs(<<<'JS'
const links = document.querySelectorAll('[data-toc-link]');
const sections = [...links].map(l => document.querySelector(l.getAttribute('href')));
function highlight() {
    let active = -1;
    sections.forEach((s, i) => { if (s && s.getBoundingClientRect().top - 120 <= 0) active = i; });
    links.forEach((l, i) => l.classList.toggle('is-active', i === active));
}
document.addEventListener('scroll', highlight, { passive: true });
highlight();
JS); ?>
<?php endif ?>

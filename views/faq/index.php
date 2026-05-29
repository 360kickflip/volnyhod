<?php
/** @var yii\web\View $this */
/** @var app\models\FaqCategory[] $categories */
/** @var app\models\Faq[] $orphan */

use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Часто задаваемые вопросы';
?>

<div class="row align-items-center mb-4">
    <div class="col-lg-8">
        <h1>Помощь и FAQ</h1>
        <p class="text-soft m-0">Ответы на самые популярные вопросы о сервисе.</p>
    </div>
    <div class="col-lg-4 text-lg-end">
        <a href="<?= Url::to(['/support/create']) ?>" class="btn btn-primary"><i class="fa-solid fa-headset me-1"></i> Не нашли ответ?</a>
    </div>
</div>

<?php if ($categories): ?>
    <ul class="nav vh-tabs mb-4" role="tablist" id="faq-tabs">
        <?php foreach ($categories as $i => $cat): ?>
            <li class="nav-item">
                <button class="nav-link <?= $i === 0 ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#cat-<?= $cat->id ?>">
                    <?php if ($cat->icon): ?><i class="fa-solid <?= Html::encode($cat->icon) ?> me-2"></i><?php endif ?><?= Html::encode($cat->name) ?>
                </button>
            </li>
        <?php endforeach ?>
    </ul>

    <div class="tab-content">
        <?php foreach ($categories as $i => $cat): ?>
            <div class="tab-pane fade <?= $i === 0 ? 'show active' : '' ?>" id="cat-<?= $cat->id ?>">
                <?php if (count($cat->faqs) === 0): ?>
                    <div class="text-soft">В этой категории пока нет вопросов.</div>
                <?php else: ?>
                    <div class="accordion" id="acc-<?= $cat->id ?>">
                        <?php foreach ($cat->faqs as $j => $faq): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-<?= $faq->id ?>">
                                        <?= Html::encode($faq->question) ?>
                                    </button>
                                </h2>
                                <div id="faq-<?= $faq->id ?>" class="accordion-collapse collapse" data-bs-parent="#acc-<?= $cat->id ?>">
                                    <div class="accordion-body"><?= nl2br(Html::encode($faq->answer)) ?></div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
<?php elseif ($orphan): ?>
    <div class="accordion">
        <?php foreach ($orphan as $faq): ?>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#f-<?= $faq->id ?>">
                        <?= Html::encode($faq->question) ?>
                    </button>
                </h2>
                <div id="f-<?= $faq->id ?>" class="accordion-collapse collapse">
                    <div class="accordion-body"><?= nl2br(Html::encode($faq->answer)) ?></div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>

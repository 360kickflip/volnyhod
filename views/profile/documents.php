<?php
/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var app\models\forms\DocumentUploadForm $form */
/** @var app\models\UserDocument[] $documents */

use app\models\UserDocument;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\helpers\Url;

$this->title = 'Документы';

$existingByType = [];
foreach ($documents as $doc) $existingByType[$doc->type] = $doc;

$types = [
    UserDocument::TYPE_LICENSE_FRONT => ['ВУ — лицевая сторона', 'fa-id-card'],
    UserDocument::TYPE_LICENSE_BACK => ['ВУ — оборотная сторона', 'fa-id-card-clip'],
    UserDocument::TYPE_PASSPORT_MAIN => ['Паспорт — главная', 'fa-passport'],
    UserDocument::TYPE_PASSPORT_REGISTRATION => ['Паспорт — прописка', 'fa-house'],
    UserDocument::TYPE_SELFIE => ['Селфи с документом', 'fa-camera'],
];
?>

<h1 class="mb-3">Профиль</h1>
<?= $this->render('_header', ['user' => $user]) ?>
<?= $this->render('_tabs', ['active' => 'documents']) ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="mb-2">Загруженные документы</h5>
                <p class="text-soft small mb-4">Для верификации загрузите все 4 документа: ВУ (с двух сторон), главную страницу паспорта и страницу с пропиской. Селфи с документом приветствуется.</p>

                <div class="row g-3">
                    <?php foreach ($types as $type => [$label, $icon]): ?>
                        <?php $doc = $existingByType[$type] ?? null; ?>
                        <div class="col-md-6">
                            <div class="card border-soft h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="fw-semibold"><i class="fa-solid <?= $icon ?> me-2 text-muted"></i><?= $label ?></div>
                                        <?php if ($doc): ?>
                                            <span class="badge bg-<?= match($doc->status) {
                                                UserDocument::STATUS_APPROVED => 'success',
                                                UserDocument::STATUS_REJECTED => 'danger',
                                                default => 'warning',
                                            } ?>">
                                                <?= UserDocument::statusLabel($doc->status) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Не загружен</span>
                                        <?php endif ?>
                                    </div>
                                    <?php if ($doc): ?>
                                        <div class="small text-soft">
                                            <i class="fa-regular fa-file me-1"></i><?= Html::encode($doc->original_name ?: $doc->file_path) ?><br>
                                            <i class="fa-regular fa-clock me-1"></i><?= Yii::$app->formatter->asDatetime($doc->uploaded_at) ?>
                                        </div>
                                        <?php if ($doc->status === UserDocument::STATUS_REJECTED && $doc->comment): ?>
                                            <div class="alert alert-danger small mt-2 mb-0">
                                                <i class="fa-solid fa-circle-info me-1"></i><?= Html::encode($doc->comment) ?>
                                            </div>
                                        <?php endif ?>
                                        <div class="mt-2 d-flex gap-1">
                                            <a href="<?= $doc->getUrl() ?>" target="_blank" class="btn btn-soft btn-sm flex-grow-1"><i class="fa-solid fa-eye me-1"></i>Просмотр</a>
                                            <?= Html::beginForm(['profile/delete-document', 'id' => $doc->id]) ?>
                                                <button type="submit" class="btn btn-soft btn-sm text-danger" title="Удалить" onclick="return confirm('Удалить документ?')"><i class="fa-solid fa-trash"></i></button>
                                            <?= Html::endForm() ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-soft small">Файл не загружен.</div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card sticky-top" style="top: 90px;">
            <div class="card-body p-4">
                <h5 class="mb-3"><i class="fa-solid fa-cloud-arrow-up me-2 text-primary"></i>Загрузить документ</h5>
                <?php $fb = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data'], 'fieldConfig' => ['options' => ['class' => 'mb-3'], 'labelOptions' => ['class' => 'form-label']]]) ?>
                    <?= $fb->field($form, 'type')->dropDownList(array_map(fn($v) => $v[0], $types), ['prompt' => '— выберите тип —']) ?>
                    <?= $fb->field($form, 'file')->fileInput(['accept' => 'image/*,.pdf']) ?>
                    <div class="form-text">JPG, PNG или PDF. Максимум 10 МБ.</div>
                    <div class="d-grid mt-3">
                        <?= Html::submitButton('<i class="fa-solid fa-upload me-2"></i>Загрузить', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end() ?>

                <hr>
                <h6 class="mt-3">Требования</h6>
                <ul class="small text-soft mb-0">
                    <li>Возраст — от 21 года</li>
                    <li>Стаж — от 2 лет</li>
                    <li>Документы должны быть читаемыми</li>
                    <li>Без бликов и обрезанных краёв</li>
                </ul>
            </div>
        </div>
    </div>
</div>

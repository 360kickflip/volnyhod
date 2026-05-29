<?php
/** @var string $active */
use yii\helpers\Url;
?>
<ul class="nav vh-tabs mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?= $active === 'index' ? 'active' : '' ?>" href="<?= Url::to(['/profile/index']) ?>">
            <i class="fa-solid fa-user me-2"></i>Личные данные
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $active === 'documents' ? 'active' : '' ?>" href="<?= Url::to(['/profile/documents']) ?>">
            <i class="fa-solid fa-id-card me-2"></i>Документы
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $active === 'security' ? 'active' : '' ?>" href="<?= Url::to(['/profile/security']) ?>">
            <i class="fa-solid fa-shield-halved me-2"></i>Безопасность
        </a>
    </li>
</ul>

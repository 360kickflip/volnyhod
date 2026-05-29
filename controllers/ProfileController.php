<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\models\UserDocument;
use app\models\forms\ProfileForm;
use app\models\forms\PasswordChangeForm;
use app\models\forms\DocumentUploadForm;

class ProfileController extends Controller
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
                    'upload-document' => ['post'],
                    'delete-document' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $form = new ProfileForm($user);

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            Yii::$app->session->setFlash('success', 'Профиль обновлён.');
            return $this->refresh();
        }
        return $this->render('index', ['user' => $user, 'form' => $form]);
    }

    public function actionDocuments()
    {
        $user = Yii::$app->user->identity;
        $form = new DocumentUploadForm();

        if (Yii::$app->request->isPost) {
            $form->load(Yii::$app->request->post());
            $form->file = UploadedFile::getInstance($form, 'file');
            if ($form->upload($user)) {
                Yii::$app->session->setFlash('success', 'Документ загружен и отправлен на проверку.');
                return $this->refresh();
            }
        }

        $documents = UserDocument::find()->where(['user_id' => $user->id])->orderBy(['type' => SORT_ASC])->all();
        return $this->render('documents', ['user' => $user, 'form' => $form, 'documents' => $documents]);
    }

    public function actionDeleteDocument($id)
    {
        $user = Yii::$app->user->identity;
        $doc = UserDocument::findOne(['id' => $id, 'user_id' => $user->id]);
        if (!$doc) throw new NotFoundHttpException();
        @unlink(Yii::getAlias('@webroot/uploads/documents/' . $doc->file_path));
        $doc->delete();
        Yii::$app->session->setFlash('success', 'Документ удалён.');
        return $this->redirect(['documents']);
    }

    public function actionSecurity()
    {
        $form = new PasswordChangeForm();
        if ($form->load(Yii::$app->request->post()) && $form->change()) {
            Yii::$app->session->setFlash('success', 'Пароль успешно изменён.');
            return $this->refresh();
        }
        return $this->render('security', ['form' => $form, 'user' => Yii::$app->user->identity]);
    }
}

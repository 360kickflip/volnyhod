<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\User;
use app\models\UserDocument;

class DocumentUploadForm extends Model
{
    public $type;
    /** @var UploadedFile */
    public $file;

    public function rules()
    {
        return [
            [['type', 'file'], 'required'],
            [['type'], 'in', 'range' => UserDocument::types()],
            [['file'], 'file', 'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'pdf'], 'maxSize' => 10 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return ['type' => 'Тип документа', 'file' => 'Файл'];
    }

    public function upload(User $user)
    {
        if (!$this->validate()) return false;

        $dir = Yii::getAlias('@webroot/uploads/documents');
        if (!is_dir($dir)) @mkdir($dir, 0775, true);

        $name = 'doc_' . $user->id . '_' . $this->type . '_' . uniqid() . '.' . $this->file->extension;
        $path = $dir . '/' . $name;
        if (!$this->file->saveAs($path)) {
            $this->addError('file', 'Не удалось сохранить файл.');
            return false;
        }

        // Если уже есть документ этого типа — заменяем
        $existing = UserDocument::find()->where(['user_id' => $user->id, 'type' => $this->type])->one();
        if ($existing) {
            @unlink($dir . '/' . $existing->file_path);
            $existing->file_path = $name;
            $existing->original_name = $this->file->name;
            $existing->status = UserDocument::STATUS_PENDING;
            $existing->uploaded_at = date('Y-m-d H:i:s');
            $existing->reviewed_at = null;
            $existing->comment = null;
            $existing->save(false);
        } else {
            $doc = new UserDocument();
            $doc->user_id = $user->id;
            $doc->type = $this->type;
            $doc->file_path = $name;
            $doc->original_name = $this->file->name;
            $doc->status = UserDocument::STATUS_PENDING;
            $doc->uploaded_at = date('Y-m-d H:i:s');
            $doc->save(false);
        }

        // Если не verification_status был none, переводим в pending
        if (in_array($user->verification_status, [User::VERIFICATION_NONE, User::VERIFICATION_REJECTED])) {
            $user->verification_status = User::VERIFICATION_PENDING;
            $user->save(false);
        }

        return true;
    }
}

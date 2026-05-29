<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\SupportTicket;
use app\models\SupportMessage;
use app\models\User;

class SupportTicketForm extends Model
{
    public $category;
    public $subject;
    public $message;
    /** @var UploadedFile[] */
    public $files;

    public function rules()
    {
        return [
            [['category', 'subject', 'message'], 'required'],
            [['category'], 'in', 'range' => SupportTicket::categories()],
            [['subject'], 'string', 'max' => 255],
            [['message'], 'string', 'min' => 5, 'max' => 5000],
            [['files'], 'each', 'rule' => ['file', 'extensions' => ['jpg', 'jpeg', 'png', 'pdf', 'webp'], 'maxSize' => 10 * 1024 * 1024]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'category' => 'Категория',
            'subject' => 'Тема',
            'message' => 'Сообщение',
            'files' => 'Прикрепить файлы',
        ];
    }

    public function create(User $user)
    {
        $this->files = UploadedFile::getInstances($this, 'files');
        if (!$this->validate()) return null;

        $now = date('Y-m-d H:i:s');
        $ticket = new SupportTicket();
        $ticket->number = SupportTicket::generateNumber();
        $ticket->user_id = $user->id;
        $ticket->category = $this->category;
        $ticket->subject = $this->subject;
        $ticket->priority = SupportTicket::PRIORITY_NORMAL;
        $ticket->status = SupportTicket::STATUS_OPEN;
        $ticket->save(false);

        $attachments = [];
        if ($this->files) {
            $dir = Yii::getAlias('@webroot/uploads/support');
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            foreach ($this->files as $f) {
                $name = 'tk_' . $ticket->id . '_' . uniqid() . '.' . $f->extension;
                if ($f->saveAs($dir . '/' . $name)) {
                    $attachments[] = ['path' => $name, 'name' => $f->name];
                }
            }
        }

        $msg = new SupportMessage();
        $msg->ticket_id = $ticket->id;
        $msg->author_id = $user->id;
        $msg->author_role = SupportMessage::ROLE_USER;
        $msg->message = $this->message;
        $msg->attachments = $attachments ? json_encode($attachments, JSON_UNESCAPED_UNICODE) : null;
        $msg->created_at = $now;
        $msg->save(false);

        return $ticket;
    }
}

<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\DamageReport;
use app\models\Booking;
use app\models\Notification;

class DamageReportForm extends Model
{
    public $description;
    public $severity = DamageReport::SEVERITY_MINOR;
    /** @var UploadedFile[] */
    public $files;

    public function rules()
    {
        return [
            [['description', 'severity'], 'required'],
            [['description'], 'string', 'min' => 10, 'max' => 5000],
            [['severity'], 'in', 'range' => [DamageReport::SEVERITY_MINOR, DamageReport::SEVERITY_MODERATE, DamageReport::SEVERITY_SEVERE]],
            [['files'], 'each', 'rule' => ['file', 'extensions' => ['jpg', 'jpeg', 'png', 'webp'], 'maxSize' => 10 * 1024 * 1024]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'description' => 'Описание проблемы',
            'severity' => 'Серьёзность',
            'files' => 'Фото повреждений',
        ];
    }

    public function create(Booking $booking)
    {
        $this->files = UploadedFile::getInstances($this, 'files');
        if (!$this->validate()) return null;

        $report = new DamageReport();
        $report->booking_id = $booking->id;
        $report->car_id = $booking->car_id;
        $report->user_id = $booking->user_id;
        $report->description = $this->description;
        $report->severity = $this->severity;
        $report->status = DamageReport::STATUS_REPORTED;
        $report->created_at = date('Y-m-d H:i:s');

        $photos = [];
        if ($this->files) {
            $dir = Yii::getAlias('@webroot/uploads/damages');
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            foreach ($this->files as $f) {
                $name = 'dmg_' . $booking->id . '_' . uniqid() . '.' . $f->extension;
                if ($f->saveAs($dir . '/' . $name)) {
                    $photos[] = $name;
                }
            }
        }
        if ($photos) $report->photos = json_encode($photos);
        $report->save(false);

        Notification::send($booking->user_id, Notification::TYPE_WARNING, 'Сообщение о повреждении принято',
            'Мы рассмотрим обращение и сообщим о результате.', '/trips/' . $booking->id, 'fa-exclamation-triangle');

        return $report;
    }
}

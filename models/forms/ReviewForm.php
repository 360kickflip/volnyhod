<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\Review;
use app\models\Booking;

class ReviewForm extends Model
{
    public $rating;
    public $text;
    /** @var UploadedFile[] */
    public $files;

    public function rules()
    {
        return [
            [['rating'], 'required'],
            [['rating'], 'integer', 'min' => 1, 'max' => 5],
            [['text'], 'string', 'max' => 3000],
            [['files'], 'each', 'rule' => ['file', 'extensions' => ['jpg', 'jpeg', 'png', 'webp'], 'maxSize' => 5 * 1024 * 1024]],
        ];
    }

    public function attributeLabels()
    {
        return ['rating' => 'Оценка', 'text' => 'Отзыв', 'files' => 'Фото'];
    }

    public function create(Booking $booking)
    {
        $this->files = UploadedFile::getInstances($this, 'files');
        if (!$this->validate()) return null;

        if (Review::findOne(['booking_id' => $booking->id])) return null;

        $review = new Review();
        $review->booking_id = $booking->id;
        $review->user_id = $booking->user_id;
        $review->car_id = $booking->car_id;
        $review->rating = $this->rating;
        $review->text = $this->text;
        $review->status = Review::STATUS_PENDING;

        $photos = [];
        if ($this->files) {
            $dir = Yii::getAlias('@webroot/uploads/reviews');
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            foreach ($this->files as $f) {
                $name = 'rev_' . $booking->id . '_' . uniqid() . '.' . $f->extension;
                if ($f->saveAs($dir . '/' . $name)) {
                    $photos[] = $name;
                }
            }
        }
        if ($photos) $review->photos = json_encode($photos);
        $review->save(false);

        return $review;
    }
}

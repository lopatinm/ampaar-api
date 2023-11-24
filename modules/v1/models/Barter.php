<?php
namespace app\modules\v1\models;
use http\Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\HttpException;

/**
 * This is the model class for table "barter".
 *
 * @property int $id
 * @property int $product_id
 * @property int $count
 * @property int $user_id
 * @property string $comment
 * @property string $name
 *
 */
class Barter extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'barter';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_id', 'user_id'], 'required'],
            [['comment', 'name'], 'string'],
            [['product_id', 'count', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment' => 'comment',
            'product_id' => 'Product id',
            'count' => 'Count',
            'user_id' => 'User id',
            'name' => 'name',
        ];
    }

    


}

<?php
namespace app\modules\v1\models;
use http\Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\HttpException;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property int $count
 * @property int $user_id
 *
 */
class Product extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'user_id'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['category_id', 'count', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'category_id' => 'Category id',
            'count' => 'Count',
            'user_id' => 'User id',
        ];
    }

    


}

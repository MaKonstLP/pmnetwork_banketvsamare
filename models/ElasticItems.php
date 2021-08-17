<?php
namespace frontend\modules\banketvsamare\models;

use Yii;
use common\models\Restaurants;
use common\models\RestaurantsTypes;
use yii\helpers\ArrayHelper;
use common\models\Subdomen;
use common\models\RestaurantsSpec;
use common\models\RestaurantsSpecial;
use common\models\RestaurantsExtra;
use common\models\RestaurantsLocation;
use common\models\ImagesModule;
use common\components\AsyncRenewImages;

class ElasticItems extends \yii\elasticsearch\ActiveRecord
{
    public function attributes()
    {
        return [
            'id',
            'restaurant_id',
            'restaurant_gorko_id',
            'restaurant_min_capacity',
            'restaurant_max_capacity',
            'restaurant_district',
            'restaurant_parent_district',
            'restaurant_city_id',
            'restaurant_alcohol',
            'restaurant_firework',
            'restaurant_name',
            'restaurant_address',
            'restaurant_cover_url',
            'restaurant_latitude',
            'restaurant_longitude',
            'restaurant_own_alcohol',
            'restaurant_own_alcohol_id',
            'restaurant_cuisine',
            'restaurant_parking',
            'restaurant_extra_services',
            'restaurant_payment',
            'restaurant_special',
            'restaurant_phone',
            'restaurant_images',
            'restaurant_commission',
            'restaurant_types',
            'restaurant_location',
            'restaurant_price',
            'restaurant_spec',
            'restaurant_specials',
            'restaurant_extra',
            'restaurant_slug',
            'rooms',
        ];
    }

    public static function index() {
        return 'pmn_banketvsamare_restaurants';
    }
    
    public static function type() {
        return 'items';
    }

    /**
     * @return array This model's mapping
     */
    public static function mapping()
    {
        return [
            static::type() => [
                'properties' => [
                    'id'                            => ['type' => 'integer'],
                    'restaurant_id'                 => ['type' => 'integer'],
                    'restaurant_gorko_id'           => ['type' => 'integer'],
                    'restaurant_min_capacity'       => ['type' => 'integer'],
                    'restaurant_max_capacity'       => ['type' => 'integer'],
                    'restaurant_district'           => ['type' => 'integer'],
                    'restaurant_parent_district'    => ['type' => 'integer'],
                    'restaurant_city_id'            => ['type' => 'integer'],
                    'restaurant_alcohol'            => ['type' => 'integer'],
                    'restaurant_firework'           => ['type' => 'integer'],
                    'restaurant_price'              => ['type' => 'integer'],
                    'restaurant_name'               => ['type' => 'text'],
                    'restaurant_address'            => ['type' => 'text'],
                    'restaurant_cover_url'          => ['type' => 'text'],
                    'restaurant_latitude'           => ['type' => 'text'],
                    'restaurant_longitude'          => ['type' => 'text'],
                    'restaurant_own_alcohol'        => ['type' => 'text'],
                    'restaurant_own_alcohol_id'     => ['type' => 'integer'],
                    'restaurant_cuisine'            => ['type' => 'text'],
                    'restaurant_parking'            => ['type' => 'integer'],
                    'restaurant_extra_services'     => ['type' => 'text'],
                    'restaurant_payment'            => ['type' => 'text'],
                    'restaurant_special'            => ['type' => 'text'],
                    'restaurant_phone'              => ['type' => 'text'],
                    'restaurant_commission'         => ['type' => 'integer'],
                    'restaurant_slug'               => ['type' => 'keyword'],
                    'restaurant_types'              => ['type' => 'nested', 'properties' =>[
                        'id'                            => ['type' => 'integer'],
                        'name'                          => ['type' => 'text'],
                    ]],
                    'restaurant_spec'               => ['type' => 'nested', 'properties' => [
                        'id'                            => ['type' => 'integer'],
                        'name'                          => ['type' => 'text'],
                    ]],
                    'restaurant_specials'           => ['type' => 'nested', 'properties' =>[
                        'id'                            => ['type' => 'integer'],
                        'name'                          => ['type' => 'text'],
                    ]],
                    'restaurant_extra'           => ['type' => 'nested', 'properties' =>[
                        'id'                            => ['type' => 'integer'],
                        'name'                          => ['type' => 'text'],
                    ]],
                    'restaurant_location'           => ['type' => 'nested', 'properties' =>[
                        'id'                            => ['type' => 'integer'],
                        'name'                          => ['type' => 'text'],
                    ]],
                    'restaurant_images'             => ['type' => 'nested', 'properties' =>[
                        'id'                            => ['type' => 'integer'],
                        'sort'                          => ['type' => 'integer'],
                        'realpath'                      => ['type' => 'text'],
                        'subpath'                       => ['type' => 'text'],
                        'waterpath'                     => ['type' => 'text'],
                        'timestamp'                     => ['type' => 'text'],
                    ]],
                    'rooms'                             => ['type' => 'nested', 'properties' =>[
                        'id'                            => ['type' => 'integer'],
                        'gorko_id'                      => ['type' => 'integer'],
                        'restaurant_id'                 => ['type' => 'integer'],
                        'price'                         => ['type' => 'integer'],
                        'capacity_reception'            => ['type' => 'integer'],
                        'capacity'                      => ['type' => 'integer'],
                        'type'                          => ['type' => 'integer'],
                        'rent_only'                     => ['type' => 'integer'],
                        'banquet_price'                 => ['type' => 'integer'],
                        'bright_room'                   => ['type' => 'integer'],
                        'separate_entrance'             => ['type' => 'integer'],
                        'type_name'                     => ['type' => 'text'],
                        'name'                          => ['type' => 'text'],
                        'features'                      => ['type' => 'text'],
                        'cover_url'                     => ['type' => 'text'],
                        'images'                        => ['type' => 'nested', 'properties' =>[
                            'id'                            => ['type' => 'integer'],
                            'sort'                          => ['type' => 'integer'],
                            'realpath'                      => ['type' => 'text'],
                            'subpath'                       => ['type' => 'text'],
                            'waterpath'                     => ['type' => 'text'],
                            'timestamp'                     => ['type' => 'text'],
                        ]],
                    ]]
                ]
            ],
        ];
    }

    /**
     * Set (update) mappings for this model
     */
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->setMapping(static::index(), static::type(), static::mapping());
    }

    /**
     * Create this model's index
     */
    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->createIndex(static::index(), [
            'settings' => [
                'number_of_replicas' => 0,
                'number_of_shards' => 1,
            ],
            'mappings' => static::mapping(),
            //'warmers' => [ /* ... */ ],
            //'aliases' => [ /* ... */ ],
            //'creation_date' => '...'
        ]);
    }

    /**
     * Delete this model's index
     */
    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(static::index(), static::type());
    }

    public static function refreshIndex($params) {
        $res = self::deleteIndex();
        $res = self::updateMapping();
        $res = self::createIndex();
        $res = self::updateIndex($params);
    }

    public static function updateIndex($params) {
        $connection = new \yii\db\Connection($params['main_connection_config']);
        $connection->open();
        Yii::$app->set('db', $connection);

        $restaurants_types = RestaurantsTypes::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_types = ArrayHelper::index($restaurants_types, 'value');

        $restaurants_specials = RestaurantsSpecial::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_specials = ArrayHelper::index($restaurants_specials, 'value');

        $restaurants_extra = RestaurantsExtra::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_extra = ArrayHelper::index($restaurants_extra, 'value');

        $restaurants_spec = RestaurantsSpec::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_spec = ArrayHelper::index($restaurants_spec, 'id');

        $restaurants_location = RestaurantsLocation::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_location = ArrayHelper::index($restaurants_location, 'value');

        $restaurants = Restaurants::find()
            ->with('rooms')
            ->with('rooms.images')
            ->with('images')
            ->with('subdomen')
            ->where(['city_id' => 4917])
            ->andWhere(['active' => 1])
            ->limit(100000)
            ->all();

        $connection = new \yii\db\Connection($params['site_connection_config']);
        $connection->open();
        Yii::$app->set('db', $connection);

        $images_module = ImagesModule::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $images_module = ArrayHelper::index($images_module, 'gorko_id');

        foreach ($restaurants as $restaurant) {
            $res = self::addRecord($restaurant, $restaurants_types, $restaurants_spec, $restaurants_specials ,$restaurants_extra, $restaurants_location, $images_module, $params);
        }
        echo 'Обновление индекса '. self::index().' '. self::type() .' завершено'."\n";
    }

    public static function getTransliterationForUrl($name)
    {
        $latin = array('-', "Sch", "sch", 'Yo', 'Zh', 'Kh', 'Ts', 'Ch', 'Sh', 'Yu', 'ya', 'yo', 'zh', 'kh', 'ts', 'ch', 'sh', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', '', 'Y', '', 'E', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', '', 'y', '', 'e');
        $cyrillic = array(' ', "Щ", "щ", 'Ё', 'Ж', 'Х', 'Ц', 'Ч', 'Ш', 'Ю', 'я', 'ё', 'ж', 'х', 'ц', 'ч', 'ш', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Ь', 'Ы', 'Ъ', 'Э', 'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'ь', 'ы', 'ъ', 'э');
        return trim(
            preg_replace(
                "/(.)\\1+/",
                "$1",
                strtolower(
                    preg_replace(
                        "/[^a-zA-Z0-9-]/",
                        '',
                        str_replace($cyrillic, $latin, $name)
                    )
                )
            ),
            '-'
        );
    }

    public static function addRecord($restaurant, $restaurants_types, $restaurants_spec, $restaurants_specials ,$restaurants_extra, $restaurants_location, $images_module, $params){
        $isExist = false;
        
        try{
            $record = self::get($restaurant->gorko_id);
            if(!$record){
                $record = new self();
                $record->setPrimaryKey($restaurant->gorko_id);
            }
            else{
                $isExist = true;
            }
        }
        catch(\Exception $e){
            $record = new self();
            $record->setPrimaryKey($restaurant->gorko_id);
        }

        //Св-ва ресторана
        $record->id = $restaurant->id;
        $record->restaurant_commission = $restaurant->commission;
        $record->restaurant_id = $restaurant->id;
        $record->restaurant_gorko_id = $restaurant->gorko_id;
        $record->restaurant_min_capacity = $restaurant->min_capacity;
        $record->restaurant_max_capacity = $restaurant->max_capacity;
        $record->restaurant_district = $restaurant->district;
        $record->restaurant_parent_district = $restaurant->parent_district;
        $record->restaurant_city_id = $restaurant->city_id;
        $record->restaurant_alcohol = $restaurant->alcohol_stock;
        $record->restaurant_firework = $restaurant->firework;
        $record->restaurant_name = $restaurant->name;
        $record->restaurant_address = $restaurant->address;
        $record->restaurant_cover_url = $restaurant->cover_url;
        $record->restaurant_latitude = $restaurant->latitude;
        $record->restaurant_longitude = $restaurant->longitude;
        $record->restaurant_own_alcohol = $restaurant->own_alcohol;
        $record->restaurant_own_alcohol_id = $restaurant->alcohol;
        $record->restaurant_cuisine = $restaurant->cuisine;
        $record->restaurant_parking = $restaurant->parking;
        $record->restaurant_extra_services = $restaurant->extra_services;
        $record->restaurant_payment = $restaurant->payment;
        $record->restaurant_special = $restaurant->special;
        $record->restaurant_phone = $restaurant->phone;

        //Тип мероприятия
        $restaurant_spec = [];
        $restaurant_spec_rest = explode(',', $restaurant->restaurants_spec);
        foreach ($restaurant_spec_rest as $key => $value) {
            $restaurant_spec_arr = [];
            $restaurant_spec_arr['id'] = $value;
            $restaurant_spec_arr['name'] = isset($restaurants_spec[$value]['name']) ? $restaurants_spec[$value]['name'] : '';
            array_push($restaurant_spec, $restaurant_spec_arr);
        }

        $record->restaurant_spec = $restaurant_spec;

        //Картинки ресторана
        $images = [];
        foreach ($restaurant->images as $key => $image) {
            $image_arr = [];
            $image_arr['id'] = $image->gorko_id;
            $image_arr['sort'] = $image->sort;
            $image_arr['realpath'] = $image->realpath;
            if(isset($images_module[$image->gorko_id])){
                $image_arr['subpath']   = $images_module[$image->gorko_id]['subpath'];
                $image_arr['waterpath'] = $images_module[$image->gorko_id]['waterpath'];
                $image_arr['timestamp'] = $images_module[$image->gorko_id]['timestamp'];
            }
            else{
                $queue_id = Yii::$app->queue->push(new AsyncRenewImages([
                    'gorko_id'      => $image->gorko_id,
                    'params'        => $params,
                    'rest_flag'     => true,
                    'rest_gorko_id' => $restaurant->gorko_id,
                    'room_gorko_id' => false,
                    'elastic_index' => static::index(),
                    'elastic_type'  => 'rest',
                ]));
            }                
            array_push($images, $image_arr);
        }
        $record->restaurant_images = $images;

        //Тип помещения
        $restaurant_types = [];
        $restaurant_types_rest = explode(',', $restaurant->type);
        foreach ($restaurant_types_rest as $key => $value) {
            $restaurant_types_arr = [];
            $restaurant_types_arr['id'] = $value;
            $restaurant_types_arr['name'] = isset($restaurants_types[$value]['text']) ? $restaurants_types[$value]['text'] : '';
            array_push($restaurant_types, $restaurant_types_arr);
        }
        $record->restaurant_types = $restaurant_types;

        //Особенности
        $restaurant_specials = [];
        $restaurant_specials_rest = explode(',', $restaurant->special_ids);
        foreach ($restaurant_specials_rest as $key => $value) {
            $restaurant_specials_arr = [];
            $restaurant_specials_arr['id'] = $value;
            $restaurant_specials_arr['name'] = isset($restaurants_specials[$value]['text']) ? $restaurants_specials[$value]['text'] : '';
            array_push($restaurant_specials, $restaurant_specials_arr);
        }
        $record->restaurant_specials = $restaurant_specials;

        //Extra
        $restaurant_extra = [];
        $restaurant_extra_rest = explode(',', $restaurant->extra_services_ids);
        foreach ($restaurant_extra_rest as $key => $value) {
            $restaurant_extra_arr = [];
            $restaurant_extra_arr['id'] = $value;
            $restaurant_extra_arr['name'] = isset($restaurants_extra[$value]['text']) ? $restaurants_extra[$value]['text'] : '';
            array_push($restaurant_extra, $restaurant_extra_arr);
        }
        $record->restaurant_extra = $restaurant_extra;

        //Тип локации
        $restaurant_location = [];
        $restaurant_location_rest = explode(',', $restaurant->location);
        foreach ($restaurant_location_rest as $key => $value) {
            $restaurant_location_arr = [];
            $restaurant_location_arr['id'] = $value;
            $restaurant_location_arr['name'] = isset($restaurants_location[$value]['text']) ? $restaurants_location[$value]['text'] : '';
            array_push($restaurant_location, $restaurant_location_arr);
        }
        $record->restaurant_location = $restaurant_location;

        if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $restaurant->gorko_id])->one()) {
            $record->restaurant_slug = $row['slug'];
        } else {
            $record->restaurant_slug = self::getTransliterationForUrl($restaurant->name);
            \Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $restaurant->gorko_id, 'slug' =>  $record->restaurant_slug])->execute();
        }

        //Особенности
        //$restaurant_specials = [];
        //$restaurant_specials_rest = explode(',', $restaurant->specials);
        //foreach ($restaurant_specials_rest as $key => $value) {
        //    $restaurant_specials_arr = [];
        //    $restaurant_specials_arr['id'] = $value;
        //    array_push($restaurant_specials, $restaurant_specials_arr);
        //}
        //$record->restaurant_specials = $restaurant_specials;

        //Св-ва залов
        $rooms = [];
        $restaurant_price = 9999999;
        foreach ($restaurant->rooms as $key => $room) {
            $room_arr = [];
            $room_arr['id'] = $room->id;
            $room_arr['gorko_id'] = $room->gorko_id;
            $room_arr['restaurant_id'] = $room->restaurant_id;
            $room_arr['capacity_reception'] = $room->capacity_reception;
            $room_arr['capacity'] = $room->capacity;
            $room_arr['type'] = $room->type;
            $room_arr['rent_only'] = $room->rent_only;
            $room_arr['banquet_price'] = $room->banquet_price;
            $room_arr['bright_room'] = $room->bright_room;
            $room_arr['separate_entrance'] = $room->separate_entrance;
            $room_arr['type_name'] = $room->type_name;
            $room_arr['name'] = $room->name;
            $room_arr['features'] = $room->features;
            $room_arr['cover_url'] = $room->cover_url;
            $room_arr['price'] = $room->price;
            if(($room->price < $restaurant_price) and $room->price)
                $restaurant_price = $room->price;

            //Картинки залов
            $images = [];
            foreach ($room->images as $key => $image) {
                $image_arr = [];
                $image_arr['id'] = $image->gorko_id;
                $image_arr['sort'] = $image->sort;
                $image_arr['realpath'] = $image->realpath;
                if(isset($images_module[$image->gorko_id])){
                    $image_arr['subpath']   = $images_module[$image->gorko_id]['subpath'];
                    $image_arr['waterpath'] = $images_module[$image->gorko_id]['waterpath'];
                    $image_arr['timestamp'] = $images_module[$image->gorko_id]['timestamp'];
                }
                else{
                    $queue_id = Yii::$app->queue->push(new AsyncRenewImages([
                        'gorko_id'      => $image->gorko_id,
                        'params'        => $params,
                        'rest_flag'     => false,
                        'rest_gorko_id' => $restaurant->gorko_id,
                        'room_gorko_id' => $room->gorko_id,
                        'elastic_index' => static::index(),
                        'elastic_type'  => 'rest',
                    ]));
                }                
                array_push($images, $image_arr);
            }
            $room_arr['images'] = $images;

            array_push($rooms, $room_arr);
        }
        $record->rooms = $rooms;

        $record->restaurant_price = $restaurant_price;

        
        
        try{
            if(!$isExist){
                $result = $record->insert();
            }
            else{
                $result = $record->update();
            }
        }
        catch(\Exception $e){
            $result = $e;
        }
        
        //print_r($result);
        //print_r("\n");

        return 1;
    }
}
<?php

namespace avtop\multilingual;

use yii\base\Behavior;
use yii\mongodb\ActiveRecord;

class MultilingualBehavior extends Behavior {

    /**
     * Multilingual attributes
     * @var array
     */
    public $attributes;

    /**
     * Available languages
     * It can be a simple array: array('fr', 'en') or an associative array: array('fr' => 'Français', 'en' => 'English')
     * For associative arrays, only the keys will be used.
     * @var array
     */
    public $sufix = 'Lang';

    /**
     * @inheritdoc
     */
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFindBehavior',
        ];
    }

    /**
     * Handle 'afterFind' event of the owner.
     */
    public function afterFindBehavior() {

        if (is_array($this->attributes)) {
            foreach ($this->attributes as $atribute) { // title, content
                $wildProperty = $this->owner->{$atribute . $this->sufix}; // Obj->titleLang[]
                 
                if (is_array($wildProperty)) {
                    if (array_key_exists(\Yii::$app->language, $wildProperty)) {
                        $this->owner->$atribute = $wildProperty[\Yii::$app->language];
                       
                    } else {
                        if (isset(\Yii::$app->sourceLanguage) && isset($wildProperty[\Yii::$app->sourceLanguage])) {
                            $this->owner->$atribute = $wildProperty[\Yii::$app->sourceLanguage];
                        } else {
                            $this->owner->$atribute = reset($wildProperty);
                        }
                    }
                }else{
                    $this->owner->$atribute = $wildProperty;
                }
            }
        }
    }

}

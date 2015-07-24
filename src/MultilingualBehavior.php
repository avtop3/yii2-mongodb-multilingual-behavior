<?php

namespace ABTOP\multilingual;

use Yii;
use yii\base\Behavior;
use yii\mongodb\ActiveQuery;
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
    public $languages;
    public $langAt = [];

    /**
     * @var string the default language.
     * Example: 'en'.
     */
    public $defaultLanguage;

    /**
     * @var boolean whether to abridge the language ID.
     * Default to true.
     */
    private $currentLanguage;

    /**
     * @inheritdoc
     */
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFindBehavior',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'beforeValidate',
        ];
    }

    /**
     * Relation to model translations
     * @return ActiveQuery
     */
    public function getTranslations() {
        
    }

    /**
     * Relation to model translation
     * @param $language
     * @return ActiveQuery
     */
    public function getTranslation($language = null) {
        
    }

    /**
     * Handle 'beforeValidate' event of the owner.
     */
    public function beforeValidate() {
        
    }
    
    
    public function beforeSave($data) {
       
        $attrArray;
        
         if(is_array($this->attributes)){
            foreach ($this->attributes as $atribute){
                foreach ($this->languages as $langCode => $languageName){
                    $langAttr = $atribute . '_' . $langCode; // name_en
                    $attrArray[$langCode] =  $this->owner->$langAttr;   
                }
                $this->owner->$atribute = $attrArray;
            }
        }
    }

    /**
     * Handle 'afterFind' event of the owner.
     */
    public function afterFindBehavior() {
        
        if(is_array($this->attributes)){
            foreach ($this->attributes as $atribute){ // title, content
                $multilangAttr = $this->owner->$atribute; // Obj->title[]
                
                if(is_array($multilangAttr)){
                    if( array_key_exists(\Yii::$app->language, $multilangAttr) ){
                        $this->owner->$atribute = $multilangAttr[ \Yii::$app->language ];
                    }else{
                        if( isset(\Yii::$app->sourceLanguage) && isset($multilangAttr[ \Yii::$app->sourceLanguage ]) ){
                            $this->owner->$atribute = $multilangAttr[ \Yii::$app->sourceLanguage ];
                        }else{
                            $this->owner->$atribute = 'Translation not found !!!';
                        }

                    }
                    if( is_array($multilangAttr) && array_key_exists(\Yii::$app->language, $multilangAttr) ){
                        foreach ($this->languages as $langCode => $languageName){
                            $langAttr = $atribute . '_' . $langCode; // name_en
                            $this->owner->$langAttr =  $multilangAttr[$langCode];   
                        }
                    }
                }
            }
        }
    }


    /**
     * @return mixed|string
     */
    public function getCurrentLanguage() {
        return $this->currentLanguage;
    }

}

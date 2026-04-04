<?php

namespace App\Traits;

trait Trans {
    function getTransNameAttribute() {
        return json_decode($this->name, true)[app()->getLocale()]??'';
    }

    function getNameEnAttribute() {
        return json_decode($this->name, true)['en']??'';
    }

    function getNameArAttribute() {
        return json_decode($this->name, true)['ar']??'';
    }

    function getTransDescriptionAttribute() {
        return json_decode($this->description, true)[app()->getLocale()]??'';
    }

    function getDescriptionEnAttribute() {
        return json_decode($this->description, true)['en']??'';
    }

    function getDescriptionArAttribute() {
        return json_decode($this->description, true)['ar']??'';
    }


    //  Mutators
    function setNameAttribute() {
        $name = [
            'en' => request()->name_en,
            'ar' => request()->name_ar,
        ];
        $this->attributes['name'] = json_encode($name, JSON_UNESCAPED_UNICODE);
    }

    function setDescriptionAttribute() {
        $description = [
            'en' => request()->description_en,
            'ar' => request()->description_ar,
        ];
        $this->attributes['description'] = json_encode($description, JSON_UNESCAPED_UNICODE);
    }
}

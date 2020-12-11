<?php


namespace WptBus\Lib;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

class Validator
{
    private static function getInstance()
    {
        static $validator = null;
        if ($validator === null) {
            $test_translation_path = __DIR__ . '/lang';
            $test_translation_locale = 'en';
            $translation_file_loader = new FileLoader(new Filesystem, $test_translation_path);
            $translator = new Translator($translation_file_loader, $test_translation_locale);
            $validator = new Factory($translator);
        }
        return $validator;
    }

    public static function check($data, $rules, $messages = [], $attributes = [])
    {
        $validator = self::getInstance()->make($data, $rules, $messages, $attributes);
        if ($validator->fails()) {
            return false;
        }
        return true;
    }
}
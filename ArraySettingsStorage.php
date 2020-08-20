<?php

namespace seacjs\ast;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

// todo: сделать более глубокую вложеность

class ArraySettingsStorage extends Model
{
    const FILE_PATH_DEFAULT = '@app/config/settings.php';
    public $fileData = [];
    public $filePath = '';
    public $fileBegin = '<?php' . PHP_EOL . PHP_EOL . 'return [' . PHP_EOL;
    public $fileEnd = '];';

    public static function process($data, $filePath = null)
    {
        $storage = new self();
        $storage->filePath = Yii::getAlias($filePath === null ? self::FILE_PATH_DEFAULT : $filePath);
        $storage->loadFromFile();
        $storage->fileData = ArrayHelper::merge($storage->fileData, $data);
        $storage->save();
    }

    public function loadFromFile()
    {
        $filePath = $this->filePath;

        if(!file_exists($this->filePath)) {
            $this->createFile($filePath);
        }

        $this->fileData = require($filePath);

        return $this;
    }

    public function save()
    {
        $fileContent = $this->fileBegin;

        foreach($this->fileData as $key => $item) {
            $fileContent .= '   '. $this->decorate($key) . ' => ' . $this->decorate($item) . ',' . PHP_EOL;
        }

        $fileContent .= $this->fileEnd;

        $filePath = $this->filePath;
        file_put_contents($filePath, $fileContent);
    }

    public function decorate($string)
    {
        return '"'. $string . '"';
    }

    public function createFile($filePath)
    {
        $fileContent = $this->fileBegin;
        $fileContent .= $this->fileEnd;
        file_put_contents($filePath, $fileContent);
    }

}
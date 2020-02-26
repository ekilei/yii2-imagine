# yii2-imagine

### Использование
Use
```angular2html
<?php 
    $bool = \ekilei\imagine\Imagine::optimize(
        // путь исходного файла, обязательно
        $filename, // path to source file, required
        // путь для нового файла, обязательно
        $newfilename, // path to new file, required
        [
            // щирина изображения для сжатия, опционально (по умолчанию $filename->weight)
            'w' => 1920,  // image weight to resize, optional (default $filename->weight)
            // высота изображения дял сжатия, опционально (по умолчанию $filename->height)
            'h' => 1080, // image height to resize, optional (default $filename->height)
            // качество изображения, опционально (по умолчанию 100)
            'q' => 75, // image quality, optional (default 100)            
        ],
        // удаления исходного файла, опционально (по умолчанию false)
        false, // remove source file, optional (default false)
        // минимальный размер memory_limit, опционально (по умолчанию 32)
        32 // Minimum memory_limit, optional (default 32)
    ) ?>
?>
```

### Установка
Install

```
composer require ekilei/yii2-imagine "dev-master"
```
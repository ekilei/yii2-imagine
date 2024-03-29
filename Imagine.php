<?php
namespace ekilei\imagine;
/**
 * $sourcePath - исходный путь к файлу
 * $targetPath - целевой путь сохранения
 * $options = ['w' => 400,'h' => 300,'q' => 65]
 * $removeOriginal - метка удалить исходный файл
 * $minMemory - минимальное выделение памяти чтобы функция в принице стартанула, далее идет выделение памяти взависимости
 * от разрешения изображения
*/

class Imagine {

    public static function optimize($sourcePath,$targetPath,$options = [],$removeOriginal = false,$minMemory = 32) {

        $q = 100;
        list($w_src,$h_src) = getimagesize($sourcePath);
        $image_mime = image_type_to_mime_type(exif_imagetype($sourcePath));
        try {
            $exif = @exif_read_data($sourcePath);
        }
        catch (\Exception $exp) {
            $exif = false;
        }
        $w = $w_src;
        $h = $h_src;

        if(isset($options['w'])) $w = $options['w'];
        if(isset($options['h'])) $h = $options['h'];
        if(isset($options['q'])) $q = $options['q'];

        $mem_limit = ini_get ('memory_limit');
        $mem = round(($w_src*$h_src*4)/(1024*1024))+$minMemory;
        ini_set('memory_limit',''.$mem.'M');

        switch($image_mime)
        {
            case "image/jpeg": $image = imagecreatefromjpeg($sourcePath); break;
            case "image/gif": $image = imagecreatefromgif($sourcePath); break;
            case "image/png": $image = imagecreatefrompng($sourcePath); break;
            case "image/webp": $image = imagecreatefromwebp($sourcePath); break;
                return false;
        }
        // узнаем размеры
        if(isset($exif['Orientation']) && !empty($exif['Orientation'])) {
            $mem = round(($w_src*$h_src*4)/(1024*1024))*2+$minMemory;
            ini_set('memory_limit',''.$mem.'M');

            switch($exif['Orientation']) {
                case 8:
                    $image = imagerotate($image,90,0);
                    list($w_src,$h_src) = [$h_src,$w_src];
                    break;
                case 3:
                    $image = imagerotate($image,180,0);
                    break;
                case 6:
                    $image = imagerotate($image,-90,0);
                    list($w_src,$h_src) = [$h_src,$w_src];
                    break;
            }
        }

        if($w_src>$w||$h_src>$h)
        {
            // картинка не по размеру
            if($w_src >= $h_src)
            {
                // картинка горизонтальная
                $w_new = $w;
                $h_new = $h_src/($w_src/$w);
                if($h_new > $h)
                {
                    // высотка оказалась больше, переделаем
                    $h_new = $h;
                    $w_new = $w_src/($h_src/$h);
                }
            }
            else
            {
                // картинка вертикальная
                if($h==$h_src) {
                    // задали размер только по ширине
                    $w_new = $w;
                    $h_new = $h_src/($w_src/$w);
                    if($h_new>$h) {
                        // высотка оказалась больше, переделаем
                        $h_new = $h;
                        $w_new = $w_src/($h_src/$h);
                    }
                } else {
                    // тут обычная обработка по высоте
                    $h_new = $h;
                    $w_new = $w_src/($h_src/$h);
                }

            }
        }
        else
        {
            $w_new = $w_src;
            $h_new = $h_src;
        }

        $newpic = imagecreatetruecolor($w_new,$h_new);
        imagecopyresampled($newpic, $image, 0, 0, 0, 0, $w_new, $h_new, $w_src, $h_src);
        switch($image_mime)
        {
            case "image/jpeg": imagejpeg($newpic,$targetPath,$q); break;
            case "image/gif": imagegif($newpic,$targetPath); break;
            case "image/png": imagepng($newpic,$targetPath); break;
            case "image/webp": imagewebp($newpic,$targetPath); break;
        }

        imagedestroy($newpic);
        imagedestroy($image);
        if($removeOriginal) unlink($sourcePath);
        ini_set ('memory_limit',$mem_limit);

        return true;
    }

}
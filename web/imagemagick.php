<?php

/**
 * Simple Imagick Footer
 *
 * @author   Ray Icemont <ray@icemont.dev>
 * @license  https://opensource.org/licenses/Apache-2.0
 */

setlocale(LC_ALL, 'ru_RU.utf-8');
mb_internal_encoding('utf-8');

define('MAIN_DIR', dirname(__FILE__));
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

if (isset($_FILES['image']['tmp_name']) &&
    in_array(mime_content_type($_FILES['image']['tmp_name']), $allowed_types)) {
    if (!$string = filter_input(INPUT_POST, 'text1', FILTER_SANITIZE_SPECIAL_CHARS)) {
        $string = "Header with the name of the picture";
    }

    if (!$string2 = filter_input(INPUT_POST, 'text2', FILTER_SANITIZE_SPECIAL_CHARS)) {
        $string2 = "Signed by Lorem Ipsum";
    }

    $string = mb_substr($string, 0, 35);
    $string2 = mb_substr($string2, 0, 65);

    try {
        $im = new Imagick();
        $im->readImage($_FILES['image']['tmp_name']);

        $im_res = $im->getImageGeometry();

        $footer = new Imagick();
        $footer->readImage(MAIN_DIR . '/footer.png');

        $draw = new ImagickDraw();
        $draw->setTextEncoding('UTF-8');
        $draw->setFillColor(new ImagickPixel('white'));
        $draw->setFont(MAIN_DIR . '/fonts/droid-sans.ttf');
        $draw->setFontSize(120);
        $footer->annotateImage($draw, 20, 150, 0, $string);

        $draw->setFont(MAIN_DIR . '/fonts/droid-serif-italic.ttf');
        $draw->setFontSize(62);
        $footer->annotateImage($draw, 20, 250, 0, $string2);
        $footer->resizeImage($im_res['width'], null, imagick::FILTER_LANCZOS, 0.9);
        $im->addImage($footer);

        $im->resetIterator();
        $combined = $im->appendImages(true);

        header("Content-Type: image/jpg");
        echo $combined->getImageBlob();
    } catch (ImagickException $e) {
        exit('Imagick Error: ' . $e->getMessage());
    } catch (ImagickDrawException $e) {
        exit('Imagick Draw Error: ' . $e->getMessage());
    }
} else {
    exit('Error: Access denied! Select an image file!');
}

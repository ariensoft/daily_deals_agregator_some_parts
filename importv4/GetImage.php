<?php

class GetImage {

    var $source;
    var $save_to;
    var $quality;
    var $custom_name;

    function download($method = 'curl') { // default method: cURL
        $info = @GetImageSize($this->source);
        if ($info != false) {
            $mime = $info['mime'];

// What sort of image?
            $type = substr(strrchr($mime, '/'), 1);

            switch ($type) {
                case 'jpeg':
                    $image_create_func = 'ImageCreateFromJPEG';
                    $image_save_func = 'ImageJPEG';
                    $new_image_ext = 'jpg';

                    // Best Quality: 100
                    $quality = isSet($this->quality) ? $this->quality : 80;
                    break;

                case 'png':
                    $image_create_func = 'ImageCreateFromPNG';
                    $image_save_func = 'ImagePNG';
                    $new_image_ext = 'png';

                    // Compression Level: from 0  (no compression) to 9
                    $quality = isSet($this->quality) ? $this->quality : 3;
                    break;

                case 'bmp':
                    $image_create_func = 'ImageCreateFromBMP';
                    $image_save_func = 'ImageBMP';
                    $new_image_ext = 'bmp';
                    break;

                case 'gif':
                    $image_create_func = 'ImageCreateFromGIF';
                    $image_save_func = 'ImageGIF';
                    $new_image_ext = 'gif';
                    break;

                case 'vnd.wap.wbmp':
                    $image_create_func = 'ImageCreateFromWBMP';
                    $image_save_func = 'ImageWBMP';
                    $new_image_ext = 'bmp';
                    break;

                case 'xbm':
                    $image_create_func = 'ImageCreateFromXBM';
                    $image_save_func = 'ImageXBM';
                    $new_image_ext = 'xbm';
                    break;

                default:
                    $image_create_func = 'ImageCreateFromJPEG';
                    $image_save_func = 'ImageJPEG';
                    $new_image_ext = 'jpg';
            }



            $ext = strrchr($this->source, ".");
            $strlen = strlen($ext);
            if (isSet($this->custom_name)) {
                $new_name = $this->custom_name . '.' . $new_image_ext;
            } else {
                $new_name = basename(substr($this->source, 0, -$strlen)) . '.' . $new_image_ext;
            }


            $save_to = $this->save_to . $new_name;

            if ($method == 'curl') {
                $save_image = $this->LoadImageCURL($save_to);
            } elseif ($method == 'gd') {
                $img = $image_create_func($this->source);

                if (isSet($quality)) {
                    $save_image = $image_save_func($img, $save_to, $quality);
                } else {
                    $save_image = $image_save_func($img, $save_to);
                }
            }

            if ($save_image) {
                return $new_name;
            } else {
                return FALSE;
            }
        }
        return FALSE;
    }

    function LoadImageCURL($save_to) {
        $ch = curl_init($this->source);
        $fp = fopen($save_to, "wb");

// set URL and other appropriate options
        $options = array(CURLOPT_FILE => $fp,
            CURLOPT_HEADER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 60); // 1 minute timeout (should be enough)

        curl_setopt_array($ch, $options);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

}

?>

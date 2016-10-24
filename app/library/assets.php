<?php

/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 22/10/14
 * Time: 13:51
 */
class Assets extends \Apprecie\Library\Messaging\PrivateMessageQueue
{
    public static function setInitialOrganisationBackground($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        $public = '/assets/' . \Apprecie\Library\Provisioning\PortalStrap::getActivePortalIdentifier(
            ) . '/' . $organisation->getOrganisationId() . '-background.jpg';
        $origBack = __DIR__ . '\..\..\public' . $public;

        if (!is_file($origBack)) {
            $back = static::getOrganisationBackground(
                \Phalcon\DI::getDefault()->get('portal')->getOwningOrganisation()->getOrganisationID(),
                false
            );

            if (is_file(__DIR__ . '\..\..\public' . $back)) {
                if (static::createAssetDirectory($organisation->getPortal()->getPortalGUID())) {
                    //whatever logo we have,  we want it now as our background
                    copy(__DIR__ . '\..\..\public' . $back, $origBack);
                }
            }
        }

        return $public;
    }

    public static function setInitialOrganisationVaultBackground($organisation)
    {
        $organisation = Organisation::resolve($organisation);

        $public = '/assets/' . \Apprecie\Library\Provisioning\PortalStrap::getActivePortalIdentifier(
            ) . '/' . $organisation->getOrganisationId() . '-vault-background.jpg';
        $origBack = __DIR__ . '\..\..\public' . $public;

        if (!is_file($origBack)) {
            $back = static::getOrganisationVaultBackground(
                \Phalcon\DI::getDefault()->get('portal')->getOwningOrganisation()->getOrganisationID(),
                false
            );

            if (is_file(__DIR__ . '\..\..\public' . $back)) {
                if (static::createAssetDirectory($organisation->getPortal()->getPortalGUID())) {
                    //whatever logo we have,  we want it now as our background
                    copy(__DIR__ . '\..\..\public' . $back, $origBack);
                }
            }
        }

        return $public;
    }

    public static function setInitialOrganisationLogo($organisation)
    {

        $public = '/assets/' . $organisation->getPortal()->getPortalGuid() . '/' . $organisation->getOrganisationId(
            ) . '-logo.jpg';
        $origLogo = __DIR__ . '\..\..\public' . $public;

        if (!is_file($origLogo)) {
            $logo = static::getOrganisationBrandLogo(
                $organisation->getPortal()->getOwningOrganisation()->getOrganisationID(),
                false
            );

            if (is_file(__DIR__ . '\..\..\public' . $logo)) {
                if (static::createAssetDirectory($organisation->getPortal()->getPortalGUID())) {
                    //whatever logo we have,  we want it now as our logo
                    copy(__DIR__ . '\..\..\public' . $logo, $origLogo);
                }
            }
        }

        return $public;
    }

    public static function getOrganisationBrandLogo($organisationId, $resolve = true)
    {
        $organisation = Organisation::resolve($organisationId);
        if ($resolve) {
            return static::setInitialOrganisationLogo($organisation);
        }

        $public = '/assets/' . $organisation->getPortal()->getPortalGuid() . '/' . $organisationId . '-logo.jpg';
        $logo = __DIR__ . '\..\..\public' . $public;
        if (file_exists($logo)) {
            return $public;
        }
        return '/img/apprecie.png';
    }

    public static function getUserProfileImageContainer($userId)
    {
        $image = Assets::getUserProfileImage($userId);
        return '<img src="' . $image . '" style="width: 30px; height:30px; display:inline;" class="profile-micro img-responsive"/>';
    }

    public static function getUserProfileImage($userId)
    {
        $public = '/assets/' . \Apprecie\Library\Provisioning\PortalStrap::getActivePortalIdentifier(
            ) . '/' . $userId . '.jpg';
        $logo = __DIR__ . '\..\..\public' . $public;
        if (file_exists($logo)) {
            return $public;
        }
        return '/img/no-profile-picture.png';
    }

    public static function getOrganisationBackground($organisationId, $resolve = true)
    {
        if ($resolve) {
            return static::setInitialOrganisationBackground($organisationId);
        }

        $public = '/assets/' . \Apprecie\Library\Provisioning\PortalStrap::getActivePortalIdentifier(
            ) . '/' . $organisationId . '-background.jpg';
        $logo = __DIR__ . '\..\..\public' . $public;
        if (file_exists($logo)) {
            return $public;
        }
        return '/img/background/background.jpg';
    }

    public static function getOrganisationVaultBackground($organisationId, $resolve = true)
    {
        if ($resolve) {
            return static::setInitialOrganisationVaultBackground($organisationId);
        }

        $public = '/assets/' . \Apprecie\Library\Provisioning\PortalStrap::getActivePortalIdentifier(
            ) . '/' . $organisationId . '-vault-background.jpg';
        $logo = __DIR__ . '\..\..\public' . $public;
        if (file_exists($logo)) {
            return $public;
        }
        return '/img/banner.jpg';
    }

    public static function getItemAssetDirectory($itemId)
    {
        $dir = __DIR__ . '\..\..\public\assets/items/' . $itemId;

        if (!is_dir($dir)) {
            self::createItemAssetDirectory($itemId);
        }

        return $dir;
    }

    public static function createItemAssetDirectory($itemId)
    {
        if (mkdir(__DIR__ . '\..\..\public/assets/items/' . $itemId . '/', 0777, true)) {
            return true;
        }
        return 'failed to create directory "' . $itemId . '"';
    }

    public static function copyItemAssets($srcItem, $destinationItem)
    {
        $src = Item::resolve($srcItem);
        $destination = Item::resolve($destinationItem);

        (new \Apprecie\Library\FileIO\Folder())
            ->Copy
            (
                Assets::getItemAssetDirectory($src->getItemId()),
                Assets::getItemAssetDirectory($destination->getItemId())
            );
    }

    public static function getItemPrimaryImage($itemId, $baseURL = '')
    {
        $itemMedia = ItemMedia::query()
            ->where('itemId=:1:')
            ->bind([1 => $itemId])
            ->orderBy('[order]')
            ->execute();
        if ($itemMedia->count() != 0) {
            foreach ($itemMedia as $media) {
                if ($media->getType() == 'image') {
                    if (file_exists(__DIR__ . '\..\..\public/' . $media->getSrc())) {
                        return $baseURL . $media->getSrc();
                    }
                } else {
                    if ($media->getThumbnail()) {
                        return $media->getThumbnail();
                    }
                }

            }
        }
        return $baseURL . '/img/no-item-image.jpg';
    }


    public static function getPortalAssetsDir()
    {
        $dir = __DIR__ . '\..\..\public\assets/' . \Apprecie\Library\Provisioning\PortalStrap::getActivePortalIdentifier(
            ) . '/';

        if (!is_dir($dir)) {
            static::createAssetDirectory(\Apprecie\Library\Provisioning\PortalStrap::getActivePortalIdentifier());
        }

        return $dir;
    }

    public static function getItemBannerImage($itemId){
        $location = Assets::getItemAssetDirectory($itemId) . '/' . $itemId . '-banner.jpg';
        if(file_exists($location)){
            return '/assets/items/'.$itemId.'/'.$itemId.'-banner.jpg';
        }
        else{
            return '/img/banner.jpg';
        }
    }

    public static function createAssetDirectory($portalGUID)
    {
        $target = __DIR__ . '\..\..\public/assets/' . $portalGUID . '/';

        if (!is_dir($target)) {
            if (mkdir(__DIR__ . '\..\..\public/assets/' . $portalGUID . '/')) {
                return true;
            }

            return 'failed to create directory "' . $portalGUID . '"';
        }

        return true;
    }

    public static function resize_image($file, $destination, $w, $h)
    {

        //Get the original image dimensions + type
        list($source_width, $source_height, $source_type) = getimagesize($file);

        //Figure out if we need to create a new JPG, PNG or GIF
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($ext == "jpg" || $ext == "jpeg") {
            @$source_gdim = imagecreatefromjpeg($file);
        } elseif ($ext == "png") {
            @$source_gdim = imagecreatefrompng($file);
        } elseif ($ext == "gif") {
            @$source_gdim = imagecreatefromgif($file);
        } else {
            //Invalid file type? Return.
            return false;
        }

        if ($source_gdim == null) {
            return false;
        }

        //If a width is supplied, but height is false, then we need to resize by width instead of cropping
        if ($w && !$h) {
            $ratio = $w / $source_width;
            $temp_width = $w;
            $temp_height = $source_height * $ratio;

            $desired_gdim = imagecreatetruecolor($temp_width, $temp_height);
            imagecopyresampled(
                $desired_gdim,
                $source_gdim,
                0,
                0,
                0,
                0,
                $temp_width,
                $temp_height,
                $source_width,
                $source_height
            );
        } else {
            $source_aspect_ratio = $source_width / $source_height;
            $desired_aspect_ratio = $w / $h;

            if ($source_aspect_ratio > $desired_aspect_ratio) {
                /*
                 * Triggered when source image is wider
                 */
                $temp_height = $h;
                $temp_width = ( int )($h * $source_aspect_ratio);
            } else {
                /*
                 * Triggered otherwise (i.e. source image is similar or taller)
                 */
                $temp_width = $w;
                $temp_height = ( int )($w / $source_aspect_ratio);
            }

            /*
             * Resize the image into a temporary GD image
             */

            $temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
            imagecopyresampled(
                $temp_gdim,
                $source_gdim,
                0,
                0,
                0,
                0,
                $temp_width,
                $temp_height,
                $source_width,
                $source_height
            );

            /*
             * Copy cropped region from temporary image into the desired GD image
             */

            $x0 = ($temp_width - $w) / 2;
            $y0 = ($temp_height - $h) / 2;
            $desired_gdim = imagecreatetruecolor($w, $h);
            imagecopy(
                $desired_gdim,
                $temp_gdim,
                0,
                0,
                $x0,
                $y0,
                $w,
                $h
            );
        }

        /*
         * Render the image
         * Alternatively, you can save the image in file-system or database
         */

        if ($ext == "jpg" || $ext == "jpeg") {
            ImageJpeg($desired_gdim, $destination, 100);
        } elseif ($ext == "png") {
            ImagePng($desired_gdim, $destination);
        } elseif ($ext == "gif") {
            ImageGif($desired_gdim, $destination);
        } else {
            return false;
        }

        ImageDestroy($desired_gdim);
        return true;
    }

}
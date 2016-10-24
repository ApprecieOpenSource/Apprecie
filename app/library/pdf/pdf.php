<?php
/**
 * Created by PhpStorm.
 * User: Daniel Dimmick
 * Date: 22/07/2015
 * Time: 11:08
 */
namespace Apprecie\Library\Pdf;

use Apprecie\Library\Messaging\PrivateMessageQueue;
use Apprecie\Library\Request\Url;
use External\Vendor\PdfCrowd\PdfCrowd;
use External\Vendor\PdfCrowd\PdfCrowdException;

class Pdf extends PrivateMessageQueue
{
    public function generate($itemId){
        try {
            $client = new PdfCrowd($this->config->pdfCrowd->user, $this->config->pdfCrowd->password);
            $pdfUrl = Url::getConfiguredPortalAddress(null, 'pdf', 'viewevent', [$itemId, $this->config->security->apiSharedKey]);

            $pdf = $client->convertURI(
                $pdfUrl
            );

            file_put_contents(\Assets::getItemAssetDirectory($itemId).'/'.$itemId.'.Pdf', $pdf);
        } catch (PdfCrowdException $why) {
            $this->appendMessageEx('Error generating pdf ' . $why->getMessage());
        }
    }
}
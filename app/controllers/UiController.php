<?php

class UiController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function setupController()
    {
        $this->setAllowRole('PortalAdministrator');
    }

    private static function validateHex($color)
    {
        if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
            return true;
        }
        return false;
    }

    public function indexAction()
    {
        $this->view->setLayout('application');

        $currentStyles = \Phalcon\DI::getDefault()->get('portal')->getPortalStyles();
        if ($this->request->isPost()) {
            $this->checkCSRF(true, false, true);

            \Apprecie\Library\Acl\AccessControl::userCanManageOrganisation($this->getAuthenticatedUser(), Organisation::getActiveUsersOrganisation());

            if ($currentStyles == null) {
                $currentStyles = new OrganisationStyles();
                $currentStyles->setOrganisationId(Organisation::getActiveUsersOrganisation()->getOrganisationId());

                // SET ALL THE STYLES TO THE DEFAULT VALUES

                $currentStyles->setNavigationPrimary('#5C5E62');
                $currentStyles->setNavigationSecondary('#F3713C');
                $currentStyles->setNavigationPrimaryA('#FFFFFF');
                $currentStyles->setNavigationSecondaryA('#FFFFFF');
                $currentStyles->setFontColor('#676a6c');
                $currentStyles->setA('#4494D0');
                $currentStyles->setAhover('#2F4050');
                $currentStyles->setButtonPrimary('#5C5E62');
                $currentStyles->setButtonPrimaryBorder('#5C5E62');
                $currentStyles->setButtonPrimaryHover('#5C5E62');
                $currentStyles->setButtonPrimaryHoverBorder('#5C5E62');
                $currentStyles->setButtonPrimaryColor('#FFFFFF');
                $currentStyles->setProgressBar('#4494D0');
            }

            // GET POST DATA
            $menuBackground = $this->request->getPost('menu-background');
            $activeMenuBackground = $this->request->getPost('menu-active-background');
            $navPrimaryA = $this->request->getPost('menu-link-color');
            $navSecondaryA = $this->request->getPost('menu-active-link-color');
            $fontColor = $this->request->getPost('font-color');
            $aColor = $this->request->getPost('link');
            $aHoverColor = $this->request->getPost('link-hover');
            $primaryButton = $this->request->getPost('primary-button-background');
            $primaryButtonBorder = $this->request->getPost('primary-button-border-colour');
            $primaryButtonHover = $this->request->getPost('primary-button-hover-colour');
            $primaryButtonHoverBorder = $this->request->getPost('primary-button-hover-border');
            $primaryButtonFontColor = $this->request->getPost('primary-button-colour');
            $progressBar = $this->request->getPost('progress-bar');


            // OVERRIDE THE DEFAULTS WITH THE VALIDATED SELECTED VALUES
            if ($this->validateHex($menuBackground)) {
                $currentStyles->setNavigationPrimary($menuBackground);
            }
            if ($this->validateHex($activeMenuBackground)) {
                $currentStyles->setNavigationSecondary($activeMenuBackground);
            }
            if ($this->validateHex($navPrimaryA)) {
                $currentStyles->setNavigationPrimaryA($navPrimaryA);
            }
            if ($this->validateHex($navSecondaryA)) {
                $currentStyles->setNavigationSecondaryA($navSecondaryA);
            }
            if ($this->validateHex($fontColor)) {
                $currentStyles->setFontColor($fontColor);
            }
            if ($this->validateHex($aColor)) {
                $currentStyles->setA($aColor);
            }
            if ($this->validateHex($aHoverColor)) {
                $currentStyles->setAhover($aHoverColor);
            }
            if ($this->validateHex($primaryButton)) {
                $currentStyles->setButtonPrimary($primaryButton);
            }
            if ($this->validateHex($primaryButtonBorder)) {
                $currentStyles->setButtonPrimaryBorder($primaryButtonBorder);
            }
            if ($this->validateHex($primaryButtonHover)) {
                $currentStyles->setButtonPrimaryHover($primaryButtonHover);
            }
            if ($this->validateHex($primaryButtonHoverBorder)) {
                $currentStyles->setButtonPrimaryHoverBorder($primaryButtonHoverBorder);
            }
            if ($this->validateHex($primaryButtonFontColor)) {
                $currentStyles->setButtonPrimaryColor($primaryButtonFontColor);
            }
            if ($this->validateHex($progressBar)) {
                $currentStyles->setProgressBar($progressBar);
            }
            $currentStyles->save();
        } elseif ($currentStyles == null) {
            $currentStyles = new OrganisationStyles();
            $currentStyles->setNavigationPrimary('#5C5E62');
            $currentStyles->setNavigationSecondary('#F3713C');
            $currentStyles->setNavigationPrimaryA('#FFFFFF');
            $currentStyles->setNavigationSecondaryA('#FFFFFF');
            $currentStyles->setFontColor('#676a6c');
            $currentStyles->setA('#4494D0');
            $currentStyles->setAhover('#2F4050');
            $currentStyles->setButtonPrimary('#5C5E62');
            $currentStyles->setButtonPrimaryBorder('#5C5E62');
            $currentStyles->setButtonPrimaryHover('#5C5E62');
            $currentStyles->setButtonPrimaryHoverBorder('#5C5E62');
            $currentStyles->setButtonPrimaryColor('#FFFFFF');
            $currentStyles->setProgressBar('#4494D0');
        }

        $this->view->styles = $currentStyles;
    }

    public function brandingAction()
    {
        $this->checkCSRF(true, false, true);

        \Apprecie\Library\Acl\AccessControl::userCanManageOrganisation($this->getAuthenticatedUser(), Organisation::getActiveUsersOrganisation());

        $this->view->disable();

        if (!file_exists(Assets::getPortalAssetsDir())) {
            mkdir(Assets::getPortalAssetsDir());
        }

        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getType() != 'image/jpeg') {
                    echo json_encode(array('status' => 'failed', 'message' => 'Invalid image type, must be JPG'));
                    return;
                }
                $tempLocation = Assets::getPortalAssetsDir() . Organisation::getActiveUsersOrganisation(
                    )->getOrganisationId() . '-logo-temp.jpg';
                $location = Assets::getPortalAssetsDir() . Organisation::getActiveUsersOrganisation(
                    )->getOrganisationId() . '-logo.jpg';
                $resizeLocation = $location . '.resize';
                $file->moveTo($tempLocation);
                $dimensions = getimagesize($tempLocation);
                if ($dimensions[0] < 150 or $dimensions[1] < 70) {
                    echo json_encode(array('status' => 'failed', 'message' => 'Image must be 150x70 or larger'));
                    unlink($tempLocation);
                    return;
                } else {
                    if (Assets::resize_image($tempLocation, $resizeLocation, 505,false)) {
                        if (file_exists($location)) {
                            unlink($location);
                        }
                        rename($resizeLocation, $location);
                        echo json_encode(
                            array(
                                'status' => 'success',
                                'url' => Assets::getOrganisationBrandLogo(
                                        Organisation::getActiveUsersOrganisation()->getOrganisationId()
                                    )
                            )
                        );
                    } else {
                        _jm('failed', 'The image was invalid or of an unexpected type');
                    }
                    unlink($tempLocation);
                }
            }
        } else {
            echo json_encode(array('status' => 'failed', 'message' => 'No image was found'));
        }
    }

    public function backgroundAction()
    {
        $this->checkCSRF(true, false, true);

        \Apprecie\Library\Acl\AccessControl::userCanManageOrganisation($this->getAuthenticatedUser(), Organisation::getActiveUsersOrganisation());

        if (!file_exists(Assets::getPortalAssetsDir())) {
            mkdir(Assets::getPortalAssetsDir());
        }

        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getType() != 'image/jpeg') {
                    echo json_encode(array('status' => 'failed', 'message' => 'Invalid image type, must be JPG'));
                    return;
                }
                $tempLocation = Assets::getPortalAssetsDir() . Organisation::getActiveUsersOrganisation(
                    )->getOrganisationId() . '-background-temp.jpg';
                $location = Assets::getPortalAssetsDir() . Organisation::getActiveUsersOrganisation(
                    )->getOrganisationId() . '-background.jpg';
                $file->moveTo($tempLocation);
                $dimensions = getimagesize($tempLocation);
                if ($dimensions[0] < 1920 or $dimensions[1] < 1080) {
                    echo json_encode(array('status' => 'failed', 'message' => 'Image must be 1920x1080 or larger'));
                    unlink($tempLocation);
                    return;
                } else {
                    if (file_exists($location)) {
                        unlink($location);
                    }
                    rename($tempLocation, $location);
                    echo json_encode(
                        array(
                            'status' => 'success',
                            'url' => Assets::getOrganisationBackground(
                                    Organisation::getActiveUsersOrganisation()->getOrganisationId()
                                )
                        )
                    );
                }
            }
        } else {
            echo json_encode(array('status' => 'failed', 'message' => 'No image was found'));
        }
    }

    public function vaultAction()
    {
        $this->checkCSRF(true, false, true);

        \Apprecie\Library\Acl\AccessControl::userCanManageOrganisation($this->getAuthenticatedUser(), Organisation::getActiveUsersOrganisation());

        if (!file_exists(Assets::getPortalAssetsDir())) {
            mkdir(Assets::getPortalAssetsDir());
        }

        if ($this->request->hasFiles() == true) {
            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getType() != 'image/jpeg') {
                    echo json_encode(array('status' => 'failed', 'message' => 'Invalid image type, must be JPG'));
                    return;
                }
                $tempLocation = Assets::getPortalAssetsDir() . Organisation::getActiveUsersOrganisation(
                    )->getOrganisationId() . '-vault-background-temp.jpg';
                $location = Assets::getPortalAssetsDir() . Organisation::getActiveUsersOrganisation(
                    )->getOrganisationId() . '-vault-background.jpg';
                $file->moveTo($tempLocation);
                $dimensions = getimagesize($tempLocation);
                if ($dimensions[0] < 1140 or $dimensions[1] < 312) {
                    echo json_encode(array('status' => 'failed', 'message' => 'Image must be 1140 x 312 '));
                    unlink($tempLocation);
                    return;
                } else {
                    if (file_exists($location)) {
                        unlink($location);
                    }
                    if(Assets::resize_image($tempLocation, $location, 1140, 312)){
                        echo json_encode(
                            array(
                                'status' => 'success',
                                'url' => Assets::getOrganisationVaultBackground(
                                    Organisation::getActiveUsersOrganisation()->getOrganisationId()
                                )
                            )
                        );
                    }
                }
            }
        } else {
            echo json_encode(array('status' => 'failed', 'message' => 'No image was found'));
        }
    }
}


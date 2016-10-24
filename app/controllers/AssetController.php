<?php

/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 07/01/15
 * Time: 09:53
 */
class AssetController extends \Apprecie\Library\Controllers\ApprecieControllerBase
{
    public function indexAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');
        $this->view->disable();

        _ep('hit action scan to update scanned database');
        _ep('hit action commit to update translation table with new found english strings (non destructive)');
        _ep(
            'hit purge to mark not found strings as decommissioned - note this is non destructive but forms a useful filter for translation'
        );
        _ep('Note that all commit and purge actions are based on last scan results,  so normally scan first');
    }

    public function scanAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');

        $this->view->disable();
        if (APPLICATION_ENV == 'dev' || APPLICATION_ENV == 'test') {
            $parser = new \Apprecie\Library\Translation\AssetParser();
            _ep($parser->discover());
        }
    }

    public function commitAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');

        $defaultLang = $this->getDI()->get('config')->environment->defaultLanguageId;
        $query = "INSERT IGNORE INTO translations (languageId, context, englishText) select {$defaultLang}, 'system scanned', englishText  from assetscanresults";

        $db = static::getDbAdapter();
        $db->execute($query);
    }

    public function purgeAction()
    {
        $this->requireRoleOrRedirect('SystemAdministrator');

        $query = 'update translations t1 left join assetscanresults t2 on t1.englishText = t2.englishText
                    set t1.decommissioned =
                      case
                        when t2.englishText IS NULL then 1
                        else 0
                      end';

        $db = static::getDbAdapter();
        $db->execute($query);
    }
} 
<?php
namespace App;
use App\Model\Install\Version;
use App\Model\Install\VersionIni;
use Lib\Controller;

/**
 * Class InstallController
 */
class InstallController extends Controller\AbstractController
{
    /**
     * Index action
     *
     * @todo Refactor SQL into proper place
     */
    public function indexAction()
    {
        $this->_installVersionTable();
        $this->_copyVersionsFromOldVersionAdapter();
    }

    /**
     * Copy versions information from old versions adapter
     */
    protected function _copyVersionsFromOldVersionAdapter()
    {
        foreach ($this->_getOldVersion()->getVersions() as $module => $version) {
            Version::setVersion($module, $version);
        }
        return $this;
    }

    /**
     * Get old version adapter to install.ini file
     *
     * @return VersionIni
     */
    protected function _getOldVersion()
    {
        return new VersionIni();
    }

    /**
     * Install versions table
     *
     * @return $this
     */
    protected function _installVersionTable()
    {
        //@startSkipCommitHooks
        $versionsSql
            = <<<SQL
DROP TABLE IF EXISTS `app_version`;
CREATE TABLE IF NOT EXISTS `app_version` (
  `module` varchar(50) NOT NULL,
  `version` varchar(50) NOT NULL,
  PRIMARY KEY (`module`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `app_version` (`module`, `version`) VALUES ('App', '0.0.0');
SQL;
        //@finishSkipCommitHooks
        $db = \Zend_Db_Table::getDefaultAdapter();
        $db->query($versionsSql);
        return $this;
    }
}

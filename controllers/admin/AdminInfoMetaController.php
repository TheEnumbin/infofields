<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
require_once(dirname(__FILE__) . '/../../classes/MetaModel.php');

class AdminInfoMetaController extends ModuleAdminController
{
    public $module;

    public function __construct()
    {
        $this->module = 'infofields';
        $this->bootstrap = true;
        $this->table = 'infofields_meta';
        $this->identifier = 'id_infofields_meta';
        $this->className = 'MetaModel';
        $this->lang = true;
        $this->deleted = false;
        $this->context = Context::getContext();
        parent::__construct();
    }

    public function processSave()
    {
        if (!$this->id_object) {
            return $this->processAdd();
        } else {
            return $this->processUpdate();
        }
    }
}

<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

$mod_name = 'infofields';
$languages = Language::getLanguages(false);
$main_tab = new Tab();
$main_tab->active = 1;
$main_tab->class_name = 'AdminInfofieldsMain';
$main_tab->name = [];

foreach ($languages as $lang) {
    $main_tab->name[$lang['id_lang']] = 'Custom Info Fields';
}
$main_tab->id_parent = '';
$main_tab->module = $mod_name;
$main_tab->add();

$tabs = [];

$id_parent = Tab::getIdFromClassName('AdminInfofieldsMain');
$tabs = [
    [
        'class_name' => 'AdminInfoLists',
        'id_parent' => $id_parent,
        'name' => 'Fields List',
        'icon' => 'brush',
    ],
    [
        'class_name' => 'AdminAjaxInfoFields',
        'id_parent' => -1,
        'name' => 'Infofields Ajax',
        'icon' => 'brush',
    ],
];


foreach($tabs as $t) {
    $tab = new Tab();
    $tab->active = 1;
    $tab->class_name = $t['class_name'];
    $tab->name = [];

    foreach ($languages as $lang) {
        $tab->name[$lang['id_lang']] = $t['name'];
    }
    $tab->id_parent = $t['id_parent'];
    $tab->module = $mod_name;
    $tab->add();
}

<?php
/**
 * This files reads site sections of the cms
 */
$pageSections = new PageSections();

$siteSections = parse_ini_file(getcwd() . DS . 'conf/siteSections.ini', true);
if ($siteSections) {
    $sectionsIni = $siteSections['sections'];
    if ($sectionsIni) {
        $activeSectionsInConf = $sectionsIni['activeSections'];
        if ($activeSectionsInConf) {
            $activeSections = array();
            foreach ($activeSectionsInConf as $section) {
                $key = array_search($section, PageSections::getPageSections());
                $index = array_search($section, array_values(PageSections::getPageSections()));
                if ($key && $index) {
                    $pageSections->addActiveSections(array_keys(PageSections::getPageSections())[$index], PageSections::getPageSections()[$key]);
                }
            }
        } else {
            $pageSections->setActiveSiteSections(PageSections::getPageSections());
        }
    } else {
        $pageSections->setActiveSiteSections(PageSections::getPageSections());
    }
} else {
    $pageSections->setActiveSiteSections(PageSections::getPageSections());
}
<?php
// BACKLOG-327: Convert VO configuration, perpend urn:collab:groups:surfteams.nl

/**
 * DbPatch makes the following variables available to PHP patches:
 *
 * @var $this       DbPatch_Command_Patch_PHP
 * @var $writer     DbPatch_Core_Writer
 * @var $db         Zend_Db_Adapter_Abstract
 * @var $phpFile    string
 */

// Determine the id of the Grouper Group Provider
$rows = $db->fetchAll("SELECT id FROM group_provider WHERE classname=?", 'EngineBlock_Group_Provider_Grouper');
if (count($rows) !== 1) {
    echo "No Grouper group providers?" . PHP_EOL;
    var_dump($rows);
    exit(1);
}
$groupProviderId = $rows[0]['id'];

// Determine the id of the Grouper GroupReplace Decorator
$rows = $db->fetchAll(
    'SELECT id FROM group_provider_decorator WHERE group_provider_id = ? AND classname=?',
    array($groupProviderId, 'EngineBlock_Group_Provider_Decorator_GroupIdReplace')
);
if (count($rows) !== 1) {
    echo "The Group IDs are not modified?!?" . PHP_EOL;
    var_dump($rows);
    exit(1);
}
$groupProviderDecoratorId = $rows[0]['id'];

// Determine the search and replace options for the Grouper group provider ids
$rows = $db->fetchAll(
    'SELECT `name`, `value` FROM group_provider_decorator_option WHERE group_provider_decorator_id = ? AND name IN (?,?)',
    array($groupProviderDecoratorId, 'search', 'replace')
);
if (count($rows) !== 2) {
    echo "Wrong number of Grouper Group Decorator Options?!?" . PHP_EOL;
    var_dump($rows);
    exit(1);
}
$options = array();
foreach ($rows as $row) {
    $options[$row['name']] = $row['value'];
}
if (!isset($options['search']) || !isset($options['replace'])) {
    echo "Missing either search or replace in group provider options!" . PHP_EOL;
    var_dump($options);
    exit(1);
}

// Process all the VO Groups
$voGroups = $db->fetchAll("SELECT vo_id,group_id FROM virtual_organisation_group");
foreach ($voGroups as $voGroup) {
    $newVoGroup = $voGroup;
    $newVoGroup['group_id'] = preg_replace($options['search'], $options['replace'], $newVoGroup['group_id']);
    $db->update('virtual_organisation_group', $newVoGroup, $voGroup);
    echo "{$voGroup['vo_id']}: {$voGroup['group_id']} => {$newVoGroup['group_id']}" . PHP_EOL;
}
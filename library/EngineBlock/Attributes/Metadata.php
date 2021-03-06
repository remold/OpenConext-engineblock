<?php

class EngineBlock_Attributes_Metadata
{
    protected $_definitions;
    protected $_logger;

    public function getDisplayName($attributeId, $ietfLanguageTag = 'en')
    {
        $this->_loadAttributeDefinitions();

        $name = $this->_getDataType($attributeId, 'Name', $ietfLanguageTag);
        if ($this->_definitions[$attributeId]['DisplayConsent']) {
            return $name;
        }
        return null;
    }

    public function sortConsentDisplayOrder(&$attributes)
    {
        $this->_loadAttributeDefinitions();

        uksort($attributes, array("EngineBlock_Attributes_Metadata", "sortCallback"));
    }

    public function sortCallback($a, $b) {
        $orderA = -1;
        $orderB = -1;
        if (isset($this->_definitions[$a]['DisplayOrder'])) {
            $orderA = $this->_definitions[$a]['DisplayOrder'];
        }
        if (isset($this->_definitions[$b]['DisplayOrder'])) {
            $orderB = $this->_definitions[$b]['DisplayOrder'];
        }
        return $orderA - $orderB;
    }

    public function getName($attributeId, $ietfLanguageTag = 'en', $fallbackToId = true)
    {
        $this->_loadAttributeDefinitions();

        $name = $this->_getDataType($attributeId, 'Name', $ietfLanguageTag);
        if (!$name && $fallbackToId) {
            $name = $attributeId;
        }
        return $name;
    }

    public function getDescription($attributeId, $ietfLanguageTag = 'en')
    {
        $this->_loadAttributeDefinitions();

        $description = $this->_getDataType($attributeId, 'Description', $ietfLanguageTag, $fallbackToId = true);
        if (!$description && $fallbackToId) {
            $description = '';
        }
        return $description;
    }

    protected function _getDataType($id, $type, $ietfLanguageTag = 'en')
    {
        $this->_loadLogger();

        if (isset($this->_definitions[$id][$type][$ietfLanguageTag])) {
            return $this->_definitions[$id][$type][$ietfLanguageTag];
        }
        $this->_logger->log(
            "Attribute lookup failure '$id' has no '$type' for language '$ietfLanguageTag'", EngineBlock_Log::NOTICE
        );
        return $id;
    }

    protected function _loadAttributeDefinitions()
    {
        static $s_defaultDefinitions;

        // Definitions loading default
        if (isset($this->_definitions)) {
            return $this->_definitions;
        }

        if (isset($s_defaultDefinitions)) {
            $this->_definitions = $s_defaultDefinitions;
            return $this->_definitions;
        }

        $s_defaultDefinitions = json_decode(
            file_get_contents(
                EngineBlock_ApplicationSingleton::getInstance()->getConfigurationValue(
                    'attributeDefinitionFile',
                    ENGINEBLOCK_FOLDER_APPLICATION . 'configs/attributes.json'
                )
            ),
            true
        );
        $this->_definitions = $s_defaultDefinitions;
        $this->_denormalizeDefinitions();
        return $this->_definitions;
    }

    protected function _denormalizeDefinitions()
    {
        foreach ($this->_definitions as $attributeName => $definition) {
            if (is_array($definition)) {
                continue;
            }

            $aliases = array($attributeName);
            while (!is_array($definition)) {
                $attributeName = $this->_definitions[$attributeName];

                if (empty($this->_definitions[$attributeName])) {
                    // @todo log
                    break;
                }
                $definition = $this->_definitions[$attributeName];
                $aliases[] = $attributeName;
            }

            foreach ($aliases as $alias) {
                $this->_definitions[$alias] = $definition;
                if ($attributeName !== $alias && is_array($this->_definitions[$alias])) {
                    $this->_definitions[$alias]['__original__'] = $attributeName;
                }
            }
        }
        return true;
    }

    public function setDefinition($definitions)
    {
        $this->_definitions = $definitions;
        return $this;
    }

    public function setLogger(Zend_Log $logger)
    {
        $this->_logger = $logger;
        return $this;
    }

    protected function _loadLogger()
    {
        if (!isset($this->_logger)) {
            $this->setLogger(EngineBlock_ApplicationSingleton::getLog());
        }
        return $this->_logger;
    }

}
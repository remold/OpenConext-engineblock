<?php

use JMS\Serializer\Annotation as Serializer;

class EngineBlock_Saml_MetaData_EntitiesDescriptor_EntityDescriptor
{
  /**
     * Ex: https://openidp.feide.no
     *
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("entityID")
     * @Serializer\Type("string")
     * @var string
     */
    protected $entityId;

    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
        return $this;
    }
}

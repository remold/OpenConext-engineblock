<?php

use JMS\Serializer\Annotation AS Serializer;

class EngineBlock_Saml_EntitiesDescriptor_Signature
{
    /**
     * @Serializer\SerializedName("xmlns:ds")
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     */
    protected  $xmlNameSpaceDS = 'http://www.w3.org/2000/09/xmldsig#';
}

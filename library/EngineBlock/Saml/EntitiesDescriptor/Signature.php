<?php

use JMS\Serializer\Annotation AS Serializer;

class EngineBlock_Saml_EntitiesDescriptor_Signature
{
    /**
     * @Serializer\SerializedName("xmlns:ds")
     * @Serializer\XmlAttribute
     * @Serializer\Type("string")
     */
    protected $xmlNameSpaceDS = 'http://www.w3.org/2000/09/xmldsig#';

    /**
     * @Serializer\SerializedName("ds:SignedInfo")
     * @Serializer\Type("EngineBlock_Saml_EntitiesDescriptor_Signature_SignedInfo")
     * @var EngineBlock_Saml_EntitiesDescriptor_Signature_SignedInfo
     */
    protected $signedInfo;

    public function setSignedInfo(EngineBlock_Saml_EntitiesDescriptor_Signature_SignedInfo $signedInfo)
    {
        $this->signedInfo = $signedInfo;
        return $this;
    }
}

<?php

use JMS\Serializer\Annotation AS Serializer;

/**
 * @Serializer\XmlRoot("md:EntitiesDescriptor")
 */
class EngineBlock_Saml_MetaData_EntitiesDescriptor
{
    /**
     * @todo find out how to denote a comment
     * @var string
     */
    protected $comment;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("xmlns:md")
     * @Serializer\Type("string")
     * @var string
     */
    protected $xmlNameSpaceMetaData = 'urn:oasis:names:tc:SAML:2.0:metadata';

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("xmlns:mdui")
     * @Serializer\Type("string")
     * @var string
     */
    protected $xmlNameSpaceMetaDataUi = 'urn:oasis:names:tc:SAML:metadata:ui';

    /**
     * Ex: 2013-02-08T13:51:50Z
     *
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("validUntil")
     * @Serializer\Type("string")
     * @var string
     */
    protected $validUntil;

    /**
     * Ex: CORTO0fd7b987256ebf3cb9410e431b8e5f7f3fb1b9f8
     *
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("ID")
     * @Serializer\Type("string");
     * @var string
     */
    protected $id;

    /**
     * @Serializer\SerializedName("ds:Signature");
     * @Serializer\Type("EngineBlock_Saml_Dsig_Signature")
     * @var EngineBlock_Saml_Dsig_Signature
     */
    protected $signature;

    /**
     * @Serializer\XmlList(inline = true, entry = "md:EntityDescriptor")
     * @Serializer\Type("array<EngineBlock_Saml_MetaData_EntitiesDescriptor_EntityDescriptor>")
     * @var array
     */
    protected $entityDescriptors;


    public function setSignature(EngineBlock_Saml_Dsig_Signature $signature)
    {
        $this->signature = $signature;
        return $this;
    }

    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setEntityDescriptors(array $entityDescriptors)
    {
        $this->entityDescriptors = $entityDescriptors;
        return $this;
    }
}
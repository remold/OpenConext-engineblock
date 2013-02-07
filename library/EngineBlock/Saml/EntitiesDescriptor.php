<?php

use JMS\Serializer\Annotation AS Serializer;

/**
 * @Serializer\XmlRoot("md:EntitiesDescriptor")
 */
class EngineBlock_Saml_EntitiesDescriptor
{
    /**
     * @todo find out how to denote a comment
     */
    protected $comment;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("xmlns:md")
     * @Serializer\Type("string")
     */
    protected $xmlNameSpaceMetaData = 'urn:oasis:names:tc:SAML:2.0:metadata';

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("xmlns:mdui")
     * @Serializer\Type("string")
     */
    protected $xmlNameSpaceMetaDataUi = 'urn:oasis:names:tc:SAML:metadata:ui';

    /**
     * Ex: 2013-02-08T13:51:50Z
     *
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("validUntil")
     * @Serializer\Type("string")
     */
    protected $validUntil;

    /**
     * Ex: CORTO0fd7b987256ebf3cb9410e431b8e5f7f3fb1b9f8
     *
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("ID")
     * @Serializer\Type("string");
     */
    protected $id;

    /**
     * @Serializer\SerializedName("ds:Signature");
     * @Serializer\Type("EngineBlock_Saml_EntitiesDescriptor_Signature")
     */
    protected $signature;

    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSignature($signature)
    {
        $this->signature = $signature;
        return $this;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;
        return $this;
    }

    public function getValidUntil()
    {
        return $this->validUntil;
    }

    public function setXmlNameSpaceMetaData($xmlNameSpaceMetaData)
    {
        $this->xmlNameSpaceMetaData = $xmlNameSpaceMetaData;
        return $this;
    }

    public function getXmlNameSpaceMetaData()
    {
        return $this->xmlNameSpaceMetaData;
    }

    public function setXmlNameSpaceMetaDataUi($xmlNameSpaceMetaDataUi)
    {
        $this->xmlNameSpaceMetaDataUi = $xmlNameSpaceMetaDataUi;
        return $this;
    }

    public function getXmlNameSpaceMetaDataUi()
    {
        return $this->xmlNameSpaceMetaDataUi;
    }
}
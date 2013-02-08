<?php

use JMS\Serializer\Annotation as Serializer;

class AuthnRequest
{
    /**
     * @
     * @Serializer\SerializedName("xmlns:samlp")
     */
    protected $xmlNameSpaceSamlP = "urn:oasis:names:tc:SAML:2.0:protocol";

    /**
     * @Serializer\SerializedName("xmlns:saml")
     */
    protected $xmlNameSpaceSaml ="urn:oasis:names:tc:SAML:2.0:assertion";

    /**
     * Ex: _5039dc7b286a753e6d9226cdcc3b86ae41b28e0e7b
     *
     * @Serializer\SerializedName("ID")
     */
    protected $id;

    /**
     * @Serializer\SerializedName("Version")
     */
    protected $version ="2.0";

    /**
     * Ex:2013-02-08T09:32:14Z
     *
     * @Serializer\SerializedName("IssueInstant")
     */
    protected $issueInstant;

    /**
     * Ex: https://engine.demo.openconext.org/authentication/idp/single-sign-on
     *
     * @Serializer\SerializedName("Destination")
     */
    protected $destination;

    /**
     * Ex: https://profile.demo.openconext.org/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp
     *
     * @Serializer\SerializedName("AssertionConsumerServiceURL")
     */
    protected $assertionConsumerServiceURL;

    /**
     * @Serializer\SerializedName("ProtocolBinding")
     */
    protected $protocolBinding ="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST";
}

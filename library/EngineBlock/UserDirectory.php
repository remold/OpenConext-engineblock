<?php

require_once 'Zend/Ldap.php';

class EngineBlock_UserDirectory
{
    const USER_ID_ATTRIBUTE          = 'uid';
    const LDAP_CLASS_COLLAB_PERSON   = 'collabPerson';
    const LDAP_ATTR_COLLAB_PERSON_ID = 'collabPersonId';
    const EDUPERSON_PREFIX           = 'urn:mace:dir:attribute-def:';

    protected $_ldapClient = NULL;

    /**
     * @return Zend_Ldap The ldap client
     */
    protected function _getLdapClient()
    {
        if ($this->_ldapClient == NULL) {
            $application = EngineBlock_ApplicationSingleton::getInstance();
            $config = $application->getConfigurationValuesForPrefix('ldap.');
       
            $ldapOptions = array('host'                 => $config['ldap.host'],
                                 'useSsl'               => $config['ldap.useSsl'],
                                 'username'             => $config['ldap.username'],
                                 'password'             => $config['ldap.password'],
                                 'bindRequiresDn'       => $config['ldap.bindRequiresDn'],
                                 'accountDomainName'    => $config['ldap.accountDomainName'],
                                 'baseDn'               => $config['ldap.baseDn']);
            $this->_ldapClient = new Zend_Ldap($ldapOptions);
            $this->_ldapClient->bind();
        }
        return $this->_ldapClient;
    }

    public function setLdapClient($client)
    {
        $this->_ldapClient = $client;
    }

    public function findUsersByIdentifier($identifier, $ldapAttributes = array())
    {
        $filter = '(&(objectclass=' . self::LDAP_CLASS_COLLAB_PERSON . ')';
        $filter .= '(' . self::LDAP_ATTR_COLLAB_PERSON_ID . '=' . $identifier . '))';

        $collection = $this->_getLdapClient()->search($filter, null, Zend_Ldap::SEARCH_SCOPE_SUB, $ldapAttributes);

        $result = array();
        if (($collection !== NULL) and ($collection !== FALSE)) {
            foreach ($collection as $item) {
                $result[] = $item;
            }
        }
        return $result;
    }

    protected function _getCommonNameFromAttributes($attributes)
    {
        if (isset($attributes['givenName'][0]) && isset($attributes['sn'][0])) {
            return $attributes['givenName'][0] . ' ' . $attributes['sn'][0];
        }

        if (isset($attributes['sn'][0])) {
            return $result = $attributes['sn'][0];
        }

        if (isset($attributes['displayName'][0])) {
            return $attributes['displayName'][0];
        }

        if (isset($attributes['mail'])) {
            return $attributes['mail'][0];
        }

        if (isset($attributes['givenName'])) {
            return $attributes['givenName'][0];
        }

        if (isset($attributes['uid'][0])) {
            return $attributes['uid'][0];
        }

        return "";
    }

    // TODO: cleanup, add constants for strings, document
    public function addUser($organization, $attributes, $attributeHash)
    {
        $now = date(DATE_RFC822);
        $info = array();
        $info['collabPersonHash']           = $attributeHash;
        $info['collabPersonRegistered']     = $now;
        $info['collabPersonLastUpdated']    = $now;
        $info['collabPersonLastAccessed']   = $now;
        // TODO: find out about IDP (need reference)? pass in parameter? call external module?
        $info['collabPersonIsGuest'] = FALSE;

        $identifyingAttributes = array(
            'uid',
            'cn' ,
            'sn' ,
            'mail',
            'displayName' ,
            'givenName'
        );
        foreach ($identifyingAttributes as $identifyingAttribute) {
            if (array_key_exists(self::EDUPERSON_PREFIX . $identifyingAttribute, $attributes)) {
                $info[$identifyingAttribute] = $attributes[self::EDUPERSON_PREFIX . $identifyingAttribute];
            }
        }
        // check mandatory attributes (uid)
        if (! array_key_exists('cn', $info)) {
            $info['cn'] = $this->_getCommonNameFromAttributes($info);
        }
        if (! array_key_exists('sn', $info)) {
            $info['sn'] = $info['cn'];
        }
        $info['objectClass'] = array(
            'collabPerson',
            'nlEduPerson',
            'inetOrgPerson',
            'organizationalPerson',
            'person',
            'top',
        );
        $info['o'] = $organization;
        $info['collabPersonId'] = 'urn:collab:person:' . $organization . ':' . $info['uid'][0];

        $dn = 'uid=' . $info['uid'][0] . ',o=' . $organization . ',' . $this->_getLdapClient()->getBaseDn();

        $this->addOrganization($organization);

        if (!$this->_getLdapClient()->exists($dn)) {
            $result = $this->_getLdapClient()->add($dn, $info);
            $result = ($result instanceof Zend_Ldap);
        } else {
            // TODO: check hash
            $result = TRUE;
        }
        return $result;
    }

    public function addOrganization($organization)
    {
        $info = array(
            'o' => $organization ,
            'objectclass' => array(
                'organization' ,
                'top'
            )
        );
        $dn = 'o=' . $organization . ',' . $this->_getLdapClient()->getBaseDn();
        if (!$this->_getLdapClient()->exists($dn)) {
            $result = $this->_getLdapClient()->add($dn, $info);
            $result = ($result instanceof Zend_Ldap);
        } else {
            $result = TRUE;
        }
        return $result;
    }
}

/**
 *     public function registerUserForAttributes($attributes, $attributeHash)
    {
        if (! defined('ENGINEBLOCK_USER_DB_DSN') && ENGINEBLOCK_USER_DB_DSN) {
            return false;
        }
        $uid = $attributes[self::USER_ID_ATTRIBUTE][0];
        $dbh = new PDO(ENGINEBLOCK_USER_DB_DSN, ENGINEBLOCK_USER_DB_USER, ENGINEBLOCK_USER_DB_PASSWORD);
        $statement = $dbh->prepare("INSERT INTO `users` (uid, last_seen) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE last_seen = NOW()");
        $statement->execute(array(
            $uid
        ));
        $sqlValues = array();
        $bindValues = array(

            self::USER_ID_ATTRIBUTE => $uid
        );
        $nameCount = 1;
        $valueCount = 1;
        foreach ($attributes as $attributeName => $attributeValues) {
            if ($attributeName === self::USER_ID_ATTRIBUTE) {
                continue;
            }
            $bindValues['attributename' . $nameCount] = $attributeName;
            foreach ($attributeValues as $attributeValue) {
                $sqlValues[] = "(:uid, :attributename{$nameCount}, :attributevalue{$valueCount})";
                $bindValues['attributevalue' . $valueCount] = $attributeValue;
                $valueCount ++;
            }
            $nameCount ++;
        }
        // No other attributes than uid found
        if (empty($sqlValues)) {
            return false;
        }
        $statement = $dbh->prepare("INSERT IGNORE INTO `user_attributes` (`user_uid`, `name`, `value`) VALUES " . implode(',', $sqlValues));
        $statement->execute($bindValues);
    }
 */

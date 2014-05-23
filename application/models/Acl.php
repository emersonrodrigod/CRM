<?php

class Acl extends Zend_Acl {

    public function __construct($options = null) {
        if (is_null($options)) {
            $options = new Zend_Config_Xml(APPLICATION_PATH . '/configs/acl.xml');
        }

        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
    }

    public function setConfig(Zend_Config $config) {
        $this->setOptions($config->toArray());
    }

    public function setOptions(array $options) {

        $allowed = array(
            'Allow', 'Deny', 'Resources', 'Roles'
        );

        foreach ($options as $key => $value) {
            $normalized = ucfirst($key);
            if (!in_array($normalized, $allowed)) {
                continue;
            }

            $method = 'set' . $normalized;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function setResources(array $options) {
        foreach ($options as $source) {
            $resourceId = $source['resource'];
            $this->addResource(new Zend_Acl_Resource($resourceId));
        }
    }

    public function setRoles(array $options) {
        foreach ($options as $source) {
            $role = $source['role'];
            $inherit = isset($source['inherit']) ? $source['inherit'] : null;
            $this->addRole(new Zend_Acl_Role($role), $inherit);
        }
    }

    public function setAllow(array $allow) {
        if (!empty($allow)) {
            foreach ($allow as $source) {
                $role = (array) $source['role'];
                $resource = $source['resource'];
                $privileges = array_key_exists('privileges', $source) ? explode(',', $source['privileges']) : null;
                $this->allow($role, $resource, $privileges);
            }
        } else {
            
        }
    }

    public function setDeny(array $deny) {
        foreach ($deny as $source) {
            $role = (array) $source['role'];
            $resource = $source['resource'];
            $privileges = array_key_exists('privileges', $source) ? explode(',', $source['privileges']) : null;
            $this->dany($role, $resource, $privileges);
        }
    }

}

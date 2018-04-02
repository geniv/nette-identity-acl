ACL
===

Installation
------------

```sh
$ composer require geniv/nette-identity-acl
```
or
```json
"geniv/nette-identity-acl": ">=1.0.0"
```

require:
```json
"php": ">=7.0.0",
"nette/nette": ">=2.4.0",
"geniv/nette-general-form": ">=1.0.0",
"geniv/nette-identity-authorizator": ">=1.0.0"
```

Include in application
----------------------

neon configure:
```neon
#identity acl
identityAcl:
#    autowired: true
#    roleFormContainer: Identity\Acl\RoleFormContainer
#    resourceFormContainer: Identity\Acl\ResourceFormContainer
#    privilegeFormContainer: Identity\Acl\PrivilegeFormContainer
#    aclFormContainer: Identity\Acl\AclFormContainer
```

neon configure extension:
```neon
extensions:
    identityAcl: Identity\Acl\Bridges\Nette\Extension
```

presenters:
```php
protected function createComponentRoleForm(RoleForm $roleForm): RoleForm
{
    //$roleForm->setRenderCallback(function ($data) { return $data; });
    //$roleForm->setTemplatePath(path);
    //$roleForm->onSuccess[] = function (array $values) { };
    //$roleForm->onError[] = function (array $values, Exception $e = null) { };
    return $roleForm;
}


protected function createComponentResourceForm(ResourceForm $resourceForm): ResourceForm
{
    //$resourceForm->setRenderCallback(function ($data) { return $data; });
    //$resourceForm->setTemplatePath(path);
    //$resourceForm->onSuccess[] = function (array $values) { };
    //$resourceForm->onError[] = function (array $values, Exception $e = null) { };
    return $resourceForm;
}


protected function createComponentPrivilegeForm(PrivilegeForm $privilegeForm): PrivilegeForm
{
    //$privilegeForm->setRenderCallback(function ($data) { return $data; });
    //$privilegeForm->setTemplatePath(path);
    //$privilegeForm->onSuccess[] = function (array $values) { };
    //$privilegeForm->onError[] = function (array $values, Exception $e = null) { };
    return $privilegeForm;
}


protected function createComponentAclForm(AclForm $aclForm, AclFormContainer $aclFormContainer): AclForm
{
    //$aclFormContainer->setRenderCallback(function ($data) { return $data; });
    //$aclForm->setTemplatePath(path);
    //$aclForm->onSuccess[] = function (array $values) { };
    //$aclForm->onError[] = function (array $values) { };
    return $aclForm;
}
```

usage:
```latte
{control roleForm}
{control resourceForm}
{control privilegeForm}
{control aclForm}
```

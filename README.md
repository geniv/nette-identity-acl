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

if you want to by redefine `aclFormContainer` then you must use extends, eg.: 
`class UserAclFormContainer extends AclFormContainer` and redefine only method: `public function getForm(Form $form)`.
If you use all content of class `AclFormContainer` will be not function!

neon configure extension:
```neon
extensions:
    identityAcl: Identity\Acl\Bridges\Nette\Extension
```

presenters:
```php
protected function createComponentRoleForm(RoleForm $roleForm): RoleForm
{
    //$roleForm->setTemplatePath(path);
    //$roleForm->onSuccess[] = function (Form $form, array $values) { };
    //$roleForm->onError[] = function (Form $form) { };
    return $roleForm;
}


protected function createComponentResourceComponent(ResourceComponent $resourceComponent): ResourceComponent
{
    //$resourceComponent->setRenderCallback(function ($data) { return $data; });
    //$resourceComponent->setTemplatePath(path);
    //$resourceComponent->onSuccess[] = function (array $values) { };
    //$resourceComponent->onError[] = function (array $values, Exception $e = null) { };
    return $resourceComponent;
}


protected function createComponentPrivilegeComponent(PrivilegeComponent $privilegeComponent): PrivilegeComponent
{
    //$privilegeComponent->setRenderCallback(function ($data) { return $data; });
    //$privilegeComponent->setTemplatePath(path);
    //$privilegeComponent->onSuccess[] = function (array $values) { };
    //$privilegeComponent->onError[] = function (array $values, Exception $e = null) { };
    return $privilegeComponent;
}


protected function createComponentAclForm(AclForm $aclComponent, AclFormContainer $aclFormContainer): AclForm
{
    //$aclFormContainer->setRenderCallback(function ($data) { return $data; });
    //$aclFormContainer->setMultiSelect(true);
    //$aclComponent->setTemplatePath(path);
    //$aclComponent->onSuccess[] = function (array $values) { };
    //$aclComponent->onError[] = function (array $values) { };
    return $aclComponent;
}
```

usage:
```latte
{control roleComponent}
{control resourceComponent}
{control privilegeComponent}
{control aclComponent}
```

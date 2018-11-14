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

**WARNING**: rename `role`, `resource` and `privilege` maybe given error in section ACL for NEON driver, 
because NEON ID are not linked to self.
DIBI driver with relationships are OK.

presenters:
```php
protected function createComponentRoleForm(RoleForm $roleForm): RoleForm
{
    //$roleForm->setTemplatePath(path);
    //$roleForm->onSuccess[] = function (Form $form, array $values) { };
    //$roleForm->onError[] = function (Form $form) { };
    return $roleForm;
}


protected function createComponentResourceForm(ResourceForm $resourceForm): ResourceForm
{
    //$resourceForm->setTemplatePath(path);
    //$resourceForm->onSuccess[] = function (Form $form, array $values) { };
    //$resourceForm->onError[] = function (Form $form) { };
    return $resourceForm;
}


protected function createComponentPrivilegeForm(PrivilegeForm $privilegeForm): PrivilegeForm
{
    //$privilegeForm->setTemplatePath(path);
    //$privilegeForm->onSuccess[] = function (Form $form, array $values) { };
    //$privilegeForm->onError[] = function (Form $form) { };
    return $privilegeForm;
}


protected function createComponentAclForm(AclForm $aclForm, AclFormContainer $aclFormContainer): AclForm
{
    //$aclFormContainer->onRender = function ($data) { return $data; };
    //$aclFormContainer->setMultiSelect(true);

//    $this['aclForm']['form']->setDefaults($this['aclForm']->getDefaults($id));

    //$aclForm->setTemplatePath(path);
    //$aclForm->onSuccess[] = function (Form $form, array $values) use ($aclForm) { $aclForm->saveAcl(array $values) };
    //$aclForm->onError[] = function (Form $form) { };
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

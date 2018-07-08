<?php declare(strict_types=1);

namespace Identity\Acl\Bridges\Nette;

use GeneralForm\GeneralForm;
use Identity\Acl\AclComponent;
use Identity\Acl\AclFormContainer;
use Identity\Acl\PrivilegeComponent;
use Identity\Acl\PrivilegeFormContainer;
use Identity\Acl\ResourceComponent;
use Identity\Acl\ResourceFormContainer;
use Identity\Acl\RoleForm;
use Identity\Acl\RoleFormContainer;
use Nette\DI\CompilerExtension;


/**
 * Class Extension
 *
 * @author  geniv
 * @package Identity\Acl\Bridges\Nette
 */
class Extension extends CompilerExtension
{
    /** @var array default values */
    private $defaults = [
        'autowired'              => true,
        'roleFormContainer'      => RoleFormContainer::class,
        'resourceFormContainer'  => ResourceFormContainer::class,
        'privilegeFormContainer' => PrivilegeFormContainer::class,
        'aclFormContainer'       => AclFormContainer::class,
    ];


    /**
     * Load configuration.
     */
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $roleFormContainer = GeneralForm::getDefinitionFormContainer($this, 'roleFormContainer', 'roleFormContainer');
        $resourceFormContainer = GeneralForm::getDefinitionFormContainer($this, 'resourceFormContainer', 'resourceFormContainer');
        $privilegeFormContainer = GeneralForm::getDefinitionFormContainer($this, 'privilegeFormContainer', 'privilegeFormContainer');
        $aclFormContainer = GeneralForm::getDefinitionFormContainer($this, 'aclFormContainer', 'aclFormContainer');

        // define role form
        $builder->addDefinition($this->prefix('role'))
            ->setFactory(RoleForm::class, [$roleFormContainer])
            ->setAutowired($config['autowired']);

        // define resource form
        $builder->addDefinition($this->prefix('resource'))
            ->setFactory(ResourceComponent::class, [$resourceFormContainer])
            ->setAutowired($config['autowired']);

        // define privilege form
        $builder->addDefinition($this->prefix('privilege'))
            ->setFactory(PrivilegeComponent::class, [$privilegeFormContainer])
            ->setAutowired($config['autowired']);

        // define acl form
        $builder->addDefinition($this->prefix('acl'))
            ->setFactory(AclComponent::class, [$aclFormContainer])
            ->setAutowired($config['autowired']);
    }
}

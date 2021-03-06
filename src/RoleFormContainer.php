<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use Nette\Application\UI\Form;
use Nette\SmartObject;


/**
 * Class RoleFormContainer
 *
 * @author  geniv
 * @package Identity\Acl
 */
class RoleFormContainer implements IFormContainer
{
    use SmartObject;


    /**
     * Get form.
     *
     * @param Form $form
     */
    public function getForm(Form $form)
    {
        $form->addText('role', 'acl-role-form#role')
            ->setRequired('acl-role-form#role-required');

        $form->addSubmit('save', 'acl-role-form#save');
    }
}

<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use Nette\Application\UI\Form;
use Nette\SmartObject;


/**
 * Class PrivilegeFormContainer
 *
 * @author  geniv
 * @package Identity\Acl
 */
class PrivilegeFormContainer implements IFormContainer
{
    use SmartObject;


    /**
     * Get form.
     *
     * @param Form $form
     */
    public function getForm(Form $form)
    {
        $form->addText('privilege', 'acl-privilege-form#privilege')
            ->setRequired('acl-privilege-form#privilege-required');

        $form->addSubmit('save', 'acl-privilege-form#save');
    }
}

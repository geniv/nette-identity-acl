<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use Nette\Application\UI\Form;
use Nette\SmartObject;


/**
 * Class ResourceFormContainer
 *
 * @author  geniv
 * @package Identity\Acl
 */
class ResourceFormContainer implements IFormContainer
{
    use SmartObject;


    /**
     * Get form.
     *
     * @param Form $form
     */
    public function getForm(Form $form)
    {
        $form->addText('resource', 'acl-resource-form#resource')
            ->setRequired('acl-resource-form#resource-required');

        $form->addSubmit('save', 'acl-resource-form#save');
    }
}

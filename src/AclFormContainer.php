<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use Identity\Authorizator\Authorizator;
use Nette\Application\UI\Form;


/**
 * Class AclFormContainer
 *
 * @author  geniv
 * @package Identity\Acl
 */
class AclFormContainer implements IFormContainer
{
    /** @var Authorizator */
    private $authorizator;


    /**
     * AclFormContainer constructor.
     *
     * @param Authorizator $authorizator
     */
    public function __construct(Authorizator $authorizator)
    {
        $this->authorizator = $authorizator;
    }


    /**
     * Get form.
     *
     * @param Form $form
     */
    public function getForm(Form $form)
    {
        $items = array_map(function ($row) { return $row['privilege']; }, $this->authorizator->getPrivilege());
        $items['all'] = 'all';

        $form->addGroup('acl-aclform-group-all');
        $form->addCheckbox('all', 'acl-aclform-all');

        foreach ($this->authorizator->getResource() as $item) {
            $form->addGroup($item['resource']);

            //'acl-aclform-' . $item['resource']
            $form->addCheckboxList($item['id'])
                ->setItems($items)
                ->setTranslator(null);
        }
        $form->addGroup();

        $form->addSubmit('save', 'acl-aclform-save');
    }
}

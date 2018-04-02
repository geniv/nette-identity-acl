<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use Identity\Authorizator\Authorizator;
use Nette\Application\UI\Form;
use Nette\Utils\Callback;


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
    /** @var callable */
    private $renderCallback;


    /**
     * AclFormContainer constructor.
     *
     * @param Authorizator $authorizator
     */
    public function __construct(Authorizator $authorizator)
    {
        $this->authorizator = $authorizator;
        $this->renderCallback = function ($data) { return $data; };
    }


    /**
     * Set render callback.
     *
     * @param callable $callback
     */
    public function setRenderCallback(callable $callback)
    {
        $this->renderCallback = $callback;
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
            $form->addGroup(Callback::invokeSafe($this->renderCallback, [$item['resource']], null));

            //'acl-aclform-' . $item['resource']
            $form->addCheckboxList($item['id'])
                ->setItems($items)
                ->setTranslator(null);
        }
        $form->addGroup();

        $form->addSubmit('save', 'acl-aclform-save');
    }
}

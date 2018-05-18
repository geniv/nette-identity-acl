<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use Identity\Authorizator\IIdentityAuthorizator;
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
    /** @var IIdentityAuthorizator */
    protected $identityAuthorizator;
    /** @var callable */
    protected $renderCallback;


    /**
     * AclFormContainer constructor.
     *
     * @param IIdentityAuthorizator $identityAuthorizator
     */
    public function __construct(IIdentityAuthorizator $identityAuthorizator)
    {
        $this->identityAuthorizator = $identityAuthorizator;
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
        $items = array_map(function ($row) { return $row['privilege']; }, $this->identityAuthorizator->getPrivilege());
        $items['all'] = 'all';

        $form->addGroup('acl-aclform-group-all');
        $form->addCheckbox('all', 'acl-aclform-all');

        foreach ($this->identityAuthorizator->getResource() as $item) {
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

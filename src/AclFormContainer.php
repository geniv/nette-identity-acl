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
    /** @var bool */
    private $multiSelect;


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
     * Set multi select.
     *
     * @param bool $state
     */
    public function setMultiSelect(bool $state)
    {
        $this->multiSelect = $state;
    }


    /**
     * Get form.
     *
     * @param Form $form
     */
    public function getForm(Form $form)
    {
        $privilege = array_flip(array_map(function ($row) { return $row['id']; }, $this->identityAuthorizator->getPrivilege()));
        $privilege['all'] = 'all';
        $items = $privilege;

        $form->addCheckbox('all', 'acl-aclform-all');

        $listCurrentAcl = $this->identityAuthorizator->loadListCurrentAcl();
        foreach ($this->identityAuthorizator->getResource() as $item) {
            // apply current list acl from file
            if (isset($listCurrentAcl[$item['resource']])) {
                $list = $listCurrentAcl[$item['resource']];
                // filter by privilege
                $items = array_filter($privilege, function ($row) use ($list) { return in_array($row, $list); });
                $items['all'] = 'all';  // add all
            }

            $label = Callback::invokeSafe($this->renderCallback, [$item['resource']], null);
            // switch element
            if ($this->multiSelect) {
                $element = $form->addMultiSelect($item['id'], $label);
            } else {
                $element = $form->addCheckboxList($item['id'], $label);
            }
            $element->setItems($items)->setTranslator(null);
        }

        $form->addSubmit('save', 'acl-aclform-save');
    }
}

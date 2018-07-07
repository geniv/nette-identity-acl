<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use GeneralForm\ITemplatePath;
use Identity\Authorizator\Drivers\UniqueConstraintViolationException;
use Identity\Authorizator\IIdentityAuthorizator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;


/**
 * Class PrivilegeComponent
 *
 * @author  geniv
 * @package Identity\Acl
 */
class PrivilegeComponent extends Control implements ITemplatePath
{
    /** @var IFormContainer */
    private $formContainer;
    /** @var IIdentityAuthorizator */
    private $identityAuthorizator;
    /** @var ITranslator|null */
    private $translator;
    /** @var string template path */
    private $templatePath;
    /** @var string */
    private $state = null;
    /** @var callback method */
    public $onSuccess, $onError;
    /** @var callable */
    private $renderCallback;


    /**
     * PrivilegeComponent constructor.
     *
     * @param IFormContainer        $formContainer
     * @param IIdentityAuthorizator $identityAuthorizator
     * @param ITranslator|null      $translator
     */
    public function __construct(IFormContainer $formContainer, IIdentityAuthorizator $identityAuthorizator, ITranslator $translator = null)
    {
        parent::__construct();

        $this->formContainer = $formContainer;
        $this->identityAuthorizator = $identityAuthorizator;
        $this->translator = $translator;

        $this->templatePath = __DIR__ . '/PrivilegeForm.latte';  // default path
        $this->renderCallback = function ($data) { return $data; };
    }


    /**
     * Set template path.
     *
     * @param string $path
     */
    public function setTemplatePath(string $path)
    {
        $this->templatePath = $path;
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
     * Create component form.
     *
     * @param string $name
     * @return Form
     */
    protected function createComponentForm(string $name): Form
    {
        $form = new Form($this, $name);
        $form->setTranslator($this->translator);

        $form->addHidden('id');
        $this->formContainer->getForm($form);

        $form->onSuccess[] = function (Form $form, array $values) {
            try {
                if ($this->identityAuthorizator->savePrivilege($values) >= 0) {
                    $this->onSuccess($values);
                }
            } catch (UniqueConstraintViolationException $e) {
                $this->onError($values, $e);
            }
        };
        return $form;
    }


    /**
     * Handle add.
     */
    public function handleAdd()
    {
        $this->state = 'add';
    }


    /**
     * Handle update.
     *
     * @param string $id
     */
    public function handleUpdate(string $id)
    {
        $this->state = 'update';

        $privilege = $this->identityAuthorizator->getPrivilege($id);
        if ($privilege) {
            $this['form']->setDefaults($privilege);
        }
    }


    /**
     * Handle delete.
     *
     * @param string $id
     */
    public function handleDelete(string $id)
    {
        $privilege = $this->identityAuthorizator->getPrivilege($id);
        if ($privilege) {
            if ($this->identityAuthorizator->savePrivilege(['id' => $id])) {
                $this->onSuccess($privilege);
            } else {
                $this->onError($privilege);
            }
        }
    }


    /**
     * Render privilege.
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->state = $this->state;
        $template->privilege = $this->identityAuthorizator->getPrivilege();
        $template->getValue = $this->renderCallback;

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }
}

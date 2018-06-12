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
 * Class RoleForm
 *
 * @author  geniv
 * @package Identity\Acl
 */
class RoleForm extends Control implements ITemplatePath
{
    /** @var IFormContainer */
    private $formContainer;
    /** @var IIdentityAuthorizator */
    private $identityAuthorizator;
    /** @var ITranslator|null */
    private $translator;
    /** @var string */
    private $templatePath;
    /** @var string */
    private $state = null;
    /** @var callback */
    public $onSuccess, $onError;
    /** @var callable */
    private $renderCallback;


    /**
     * RoleForm constructor.
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

        $this->templatePath = __DIR__ . '/RoleForm.latte';  // default path
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
                if ($this->identityAuthorizator->saveRole($values) >= 0) {
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

        $role = $this->identityAuthorizator->getRole($id);
        if ($role) {
            $this['form']->setDefaults($role);
        }
    }


    /**
     * Handle delete.
     *
     * @param string $id
     */
    public function handleDelete(string $id)
    {
        $role = $this->identityAuthorizator->getRole($id);
        if ($role) {
            if ($this->identityAuthorizator->saveRole(['id' => $id])) {
                $this->onSuccess($role);
            } else {
                $this->onError($role);
            }
        }
    }


    /**
     * Render role.
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->state = $this->state;
        $template->role = $this->identityAuthorizator->getRole();
        $template->getValue = $this->renderCallback;

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }
}

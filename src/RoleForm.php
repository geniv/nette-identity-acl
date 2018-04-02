<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use GeneralForm\ITemplatePath;
use Identity\Authorizator\Authorizator;
use Identity\Authorizator\Drivers\UniqueConstraintViolationException;
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
    /** @var Authorizator */
    private $authorizator;
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
     * @param IFormContainer   $formContainer
     * @param Authorizator     $authorizator
     * @param ITranslator|null $translator
     */
    public function __construct(IFormContainer $formContainer, Authorizator $authorizator, ITranslator $translator = null)
    {
        parent::__construct();

        $this->formContainer = $formContainer;
        $this->authorizator = $authorizator;
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
                if ($this->authorizator->saveRole($values) >= 0) {
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
     * @param $id
     */
    public function handleUpdate($id)
    {
        $this->state = 'update';

        $role = $this->authorizator->getRole();
        if (isset($role[$id])) {
            $this['form']->setDefaults($role[$id]);
        }
    }


    /**
     * Handle delete.
     *
     * @param $id
     */
    public function handleDelete($id)
    {
        $role = $this->authorizator->getRole();
        if (isset($role[$id])) {
            $values = (array) $role[$id];

            if ($this->authorizator->saveRole(['id' => $id])) {
                $this->onSuccess($values);
            } else {
                $this->onError($values);
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
        $template->role = $this->authorizator->getRole();
        $template->getValue = $this->renderCallback;

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }
}

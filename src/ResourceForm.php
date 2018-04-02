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
 * Class ResourceForm
 *
 * @author  geniv
 * @package Identity\Acl
 */
class ResourceForm extends Control implements ITemplatePath
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
    /** @var callback method */
    public $onSuccess, $onError;
    /** @var callable */
    private $renderCallback;


    /**
     * ResourceForm constructor.
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

        $this->templatePath = __DIR__ . '/ResourceForm.latte';  // default path
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
                if ($this->authorizator->saveResource($values) >= 0) {
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

        $resource = $this->authorizator->getResource();
        if (isset($resource[$id])) {
            $this['form']->setDefaults($resource[$id]);
        }
    }


    /**
     * Handle delete.
     *
     * @param $id
     */
    public function handleDelete($id)
    {
        $resource = $this->authorizator->getResource();
        if (isset($resource[$id])) {
            $values = (array) $resource[$id];

            if ($this->authorizator->saveResource(['id' => $id])) {
                $this->onSuccess($values);
            } else {
                $this->onError($values);
            }
        }
    }


    /**
     * Render resource.
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->state = $this->state;
        $template->resource = $this->authorizator->getResource();
        $template->getValue = $this->renderCallback;

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }
}

<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use GeneralForm\ITemplatePath;
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
    /** @var ITranslator|null */
    private $translator;
    /** @var string */
    private $templatePath;
    /** @var callback */
    public $onSuccess, $onError;


    /**
     * RoleForm constructor.
     *
     * @param IFormContainer   $formContainer
     * @param ITranslator|null $translator
     */
    public function __construct(IFormContainer $formContainer, ITranslator $translator = null)
    {
        parent::__construct();

        $this->formContainer = $formContainer;
        $this->translator = $translator;

        $this->templatePath = __DIR__ . '/RoleForm.latte';  // default path
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
     * Render.
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
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

        $form->onSuccess = $this->onSuccess;
        $form->onError = $this->onError;
        return $form;
    }
}

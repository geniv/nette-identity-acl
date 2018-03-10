<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use GeneralForm\ITemplatePath;
use Identity\Authorizator\Authorizator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;


/**
 * Class AclForm
 *
 * @author  geniv
 * @package Identity\Acl
 */
class AclForm extends Control implements ITemplatePath
{
    /** @var IFormContainer */
    private $formContainer;
    /** @var Authorizator */
    private $authorizator;
    /** @var ITranslator|null */
    private $translator;
    /** @var string */
    private $templatePath;
    /** @var int */
    private $idRole = null;
    /** @var callback method */
    public $onSuccess, $onError;


    /**
     * AclForm constructor.
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

        $this->templatePath = __DIR__ . '/AclForm.latte';  // default path
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
     * Create component form.
     *
     * @param string $name
     * @return Form
     */
    protected function createComponentForm(string $name): Form
    {
        $form = new Form($this, $name);
        $form->setTranslator($this->translator);

        $form->addHidden('idRole');
        $this->formContainer->getForm($form);

        $form->onSuccess[] = function (Form $form, array $values) {
            $idRole = $values['idRole'];
            unset($values['idRole']);

            if ($this->authorizator->saveAcl($idRole, $values)) {
                $this->onSuccess($values);
            } else {
                $this->onError($values);
            }
        };
        return $form;
    }


    /**
     * Handle update.
     *
     * @param $id
     */
    public function handleUpdate($id)
    {
        $this->idRole = $id;

        $defaultItems = [];
        foreach ($this->authorizator->getResource() as $item) {
            $acl = $this->authorizator->getAcl($id, $item['id']);

            if ($this->authorizator->isAll($id, $item['id'])) {
                // idRole, idResource, ALL
                $defaultItems[$item['id']] = 'all';
            } else {
                $defaultItems[$item['id']] = array_values(array_map(function ($row) { return $row['id_privilege']; }, $acl));
            }
        }

        if ($this->authorizator->isAll($id)) {
            // idRole, ALL, ALL
            $defaultItems['all'] = true;
        }
        $this['form']->setDefaults(['idRole' => $id] + $defaultItems);
    }


    /**
     * Render.
     */
    public function render()
    {
        $template = $this->getTemplate();

        $template->role = $this->authorizator->getRole();
        $template->idRole = $this->idRole;

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }
}

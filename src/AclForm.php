<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use GeneralForm\ITemplatePath;
use Identity\Authorizator\IIdentityAuthorizator;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;


/**
 * Class AclComponent
 *
 * @author  geniv
 * @package Identity\Acl
 */
class AclForm extends Control implements ITemplatePath
{
    /** @var IFormContainer */
    private $formContainer;
    /** @var IIdentityAuthorizator */
    private $identityAuthorizator;
    /** @var ITranslator|null */
    private $translator;
    /** @var string */
    private $templatePath;
    /** @var int */
    private $idRole = null;
    /** @var callback method */
    public $onSuccess, $onError;


    /**
     * AclComponent constructor.
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

            if ($this->identityAuthorizator->saveAcl($idRole, $values)) {
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
     * @param string $id
     */
    public function handleUpdate(string $id)
    {
        $this->idRole = $id;

        $defaultItems = [];
        foreach ($this->identityAuthorizator->getResource() as $item) {
            $acl = $this->identityAuthorizator->getAcl($id, (string) $item['id']);

            if ($this->identityAuthorizator->isAll($id, (string) $item['id'])) {
                // idRole, idResource, ALL
                $defaultItems[$item['id']] = 'all';
            } else {
                $defaultItems[$item['id']] = array_values(array_map(function ($row) { return $row['id_privilege']; }, $acl));
            }
        }

        if ($this->identityAuthorizator->isAll($id)) {
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

        $template->role = $this->identityAuthorizator->getRole();
        $template->idRole = $this->idRole;

        $template->setTranslator($this->translator);
        $template->setFile($this->templatePath);
        $template->render();
    }
}

<?php declare(strict_types=1);

namespace Identity\Acl;

use GeneralForm\IFormContainer;
use GeneralForm\ITemplatePath;
use Identity\Authorizator\IIdentityAuthorizator;
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
    /** @var IIdentityAuthorizator */
    private $identityAuthorizator;
    /** @var ITranslator|null */
    private $translator;
    /** @var string */
    private $templatePath;
    /** @var callback method */
    public $onSuccess, $onError;


    /**
     * AclForm constructor.
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
     * Save acl.
     *
     * @param array $values
     * @return int
     */
    public function saveAcl(array $values): int
    {
        // support method
        $idRole = $values['idRole'];
        unset($values['idRole']);
        return $this->identityAuthorizator->saveAcl($idRole, $values);
    }


    /**
     * Get defaults.
     *
     * @param string $id
     * @return array
     */
    public function getDefaults(string $id): array
    {
        // support method
        $result = [];
        foreach ($this->identityAuthorizator->getResource() as $item) {
            $acl = $this->identityAuthorizator->getAcl($id, (string) $item['id']);

            if ($this->identityAuthorizator->isAll($id, (string) $item['id'])) {
                // idRole, idResource, ALL
                $result[$item['id']] = 'all';
            } else {
                $result[$item['id']] = array_values(array_map(function ($row) { return $row['id_privilege']; }, $acl));
            }
        }

        if ($this->identityAuthorizator->isAll($id)) {
            // idRole, ALL, ALL
            $result['all'] = true;
        }
        return ['idRole' => $id] + $result;
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

        $form->addHidden('idRole');
        $this->formContainer->getForm($form);

        $form->onSuccess = $this->onSuccess;
        $form->onError = $this->onError;
        return $form;
    }
}

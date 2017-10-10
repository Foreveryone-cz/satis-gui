<?php

namespace App\Presenters;

use App\Model\Builder;
use App\Model\PackageManager;
use Nette\Application\UI\Form;
use PDOException;
use RuntimeException;



/**
 * Homepage presenter.
 */
class PackagesPresenter extends SecuredPresenter
{

	/** @var PackageManager */
	private $packageManager;

	/** @var Builder */
	private $builder;



	function __construct(PackageManager $packageManager, Builder $builder)
	{
		$this->packageManager = $packageManager;
		$this->builder = $builder;
	}


	protected function createComponentFormAdd()
	{
		$form = new Form;

		$form->addText('type', 'Type')->setDefaultValue('vcs')->setRequired();
		$form->addText('url', 'Url')->setRequired();
		$form->addText('group', 'Group')->setDefaultValue('common');

		$form->addSubmit('btnSubmit', 'Add');

		$form->onSuccess[] = $this->addPackage;

		return $form;
	}


	public function addPackage(Form $form)
	{
		$values = $form->getValues();

		try {
		    $type   = $values->type;
		    $group  = $values->group;
		    $urls   = preg_split('~\s+~', $values->url);

		    foreach($urls as $url) {
		        $link = trim($url);
		        if($link) {
		            $record = $this->packageManager->findByUrl($link);
		            if($record) {
		                $this->packageManager->update($record->id, [
		                    'group' => $group,
                        ]);
                    } else {
                        $this->packageManager->add($type, $link, $group);
                    }

                }
            }

			$this->flashMessage('Package added.', 'success');
		} catch (PDOException $e) {
			if ($e->getCode() === '23000') {
				$this->flashMessage('Package already exists.', 'danger');
			}
		}

		$this->redirect('this');
	}


	public function renderDefault()
	{
		$packages = $this->packageManager->getAll();


		$this->template->packages = $packages;
	}


	public function handleDelete($id)
	{
		$this->packageManager->delete($id);
		$this->packageManager->compileConfig();
		$this->flashMessage('Package deleted.', 'success');
		$this->redirect('this');
	}

}

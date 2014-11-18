 <?php

class ArchiveController extends Zend_Controller_Action
{
	public function init()
	{

		$this->_config = Zend_Registry::get('config');

        $this->view->identity = Zend_Auth::getInstance()->getIdentity();

		// current academic year
		$this->view->currentAcademicYear = $this->_helper->currentAcademicYear();

		// archive name 
		$this->view->archiveName = sprintf('CoP_SeminarGradingSystemArchive_%s_%s', date('Y-m-d'), date('His'));

		

	}


	public function indexAction()
	{
		$this->view->archiveForm = new Application_Form_Archive();

		$this->view->archiveList = $this->_getArchiveList();

		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();
			if($this->view->archiveForm->isValid($formData)) {

				if(is_numeric($formData['acadmicYearId'])) {

					$this->view->message = '';

					// if the year was submitted, start the archive
					if(isset($formData['acadmicYearId'])) {

						// get section metadata for the current academic year
						$sectionMapper = new Application_Model_SectionMapper(); 
						$currentSectionMetadata = $sectionMapper->getSectionsByAcademicYearId($this->view->currentAcademicYear['id']);
						
						// get the users for each section
						$sectionUsers = $this->_getStudentUsersBySectionId($currentSectionMetadata);

						// create the archive
						$archive = $this->_helper->createArchive($sectionUsers, $this->view->archiveName);

						$this->view->archiveList = $this->_getArchiveList();

						if(isset($archive->error)) {
							$this->view->message = sprintf("<p>%s</p>", $archive->error);
						} else {
							$this->view->message = '<p>New sections successfully created.</p>';
						}

					}


				}
			} else {
				$this->view->archiveForm->populate($formData);
			}	

		}

	}

	private function _getArchiveList()
	{
		$archivePathFiles = scandir($this->_config->archive->path);
		$archives = array();
		foreach($archivePathFiles as $key => $filename) {
			if(strpos($filename, '.zip')) {
				$archives[] = $this->_config->archive->path . '/' . $filename;
			}
		}
		rsort($archives);
		return $archives;
	}

	private function _getStudentUsersBySectionId($currentSectionMetadata)
	{
		$userMapper = new Application_Model_UserMapper();
		$roleMapper = new Application_Model_RoleMapper();
		$sectionTable = new Application_Model_DbTable_Sections();

		$roles = $roleMapper->fetchAll();
		
		$studentRoleId = array_search('Student', $roles);

		$sectionUsers = array(
			);

		foreach($currentSectionMetadata as $key => $sectionMetadata) {
			$section = $sectionTable->find($sectionMetadata['section_id'])->toArray();
			$sectionUsers[] = array(

				'id' => $sectionMetadata['id'],
				'section' => $section[0],
				'users' => $userMapper->getUsersBySectionIdAndRoleId($sectionMetadata['section_id'], $studentRoleId)
				);
		}

		return $sectionUsers;


	}
}

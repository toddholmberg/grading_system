<?php

class Application_Model_DbTable_Survey extends Zend_Db_Table_Row_Abstract
{

    protected $_name = 'survey';
	protected $_tableClass = 'Application_Model_DbTable_Surveys';

	protected $_referenceMap = array(
		'Seminar' => array(
			'columns' => array('seminar_id'),
			'refTableClass' => 'Application_Model_DbTable_Seminar',
			'refColumns' => array('id')
		),
		'UserSection' => array(
			'columns' => array('reviewer_user_section_id'),
			'refTableClass' => 'Application_Model_DbTable_UserSection',
			'refColumns' => array('id')
		)
	);

protected $_id;
protected $_seminar_id;
protected $_reviewer_user_section_id;
protected $_survey_date;
protected $_qualtrics_id;
protected $_grade_sum;
protected $_grade_weighted_average;
protected $_grade_standard_deviation;
protected $_ps_pace;
protected $_ps_eyecontact;
protected $_ps_professionalism;
protected $_ps_materials;
protected $_ps_comments;
protected $_im_handouts;
protected $_im_grammar;
protected $_im_charts;
protected $_im_cites;
protected $_im_comments;
protected $_op_introduction;
protected $_op_purpose;
protected $_op_objectives;
protected $_op_background;
protected $_op_organization;
protected $_op_comments;
protected $_cd_objectives;
protected $_cd_outcome;
protected $_cd_analysis;
protected $_cd_samplesize;
protected $_cd_withdrawals;
protected $_cd_details;
protected $_cd_comments;
protected $_cc_data;
protected $_cc_importance;
protected $_cc_recommendations;
protected $_cc_role;
protected $_cc_comments;
protected $_qa_answers;
protected $_qa_interaction;
protected $_qa_comments;
protected $_ok_demonstrated;
protected $_ok_difference;
protected $_ok_deep;
protected $_ok_discussion;
protected $_ok_think;
protected $_ok_comments;
protected $_comments_like;
protected $_comments_improve;
protected $_comments_overall;

}


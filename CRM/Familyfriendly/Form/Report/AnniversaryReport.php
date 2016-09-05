<?php

class CRM_Familyfriendly_Form_Report_AnniversaryReport extends CRM_Report_Form {
	protected $_summary = NULL;
	protected $_emailField_a = FALSE;
	protected $_emailField_b = FALSE;
	protected $_customGroupExtends = array();
	public $_drilldownReport = array('contact/detail' => 'Link to Detail Report');

	function __construct() {

		$contact_type = CRM_Contact_BAO_ContactType::getSelectElements(FALSE, TRUE, '_');

		$together_choices_array =  array( 0 => 'Less than 1 year');
		$max_together_filter = 100;
		for ($i = 1; $i <= $max_together_filter; $i++){
			$special_label = "";
			if( $i == 25){
				$special_label = " - Silver ";
			}else if($i == 50){
				$special_label = " - Golden ";
			}
			$together_choices_array[$i] = $i.$special_label;

		}

		$together_choices_next_array = array();
		for ($i = 1; $i <= $max_together_filter; $i++){
			$special_label = "";
			if( $i == 25){
				$special_label = " - Silver ";
			}else if($i == 50) {
				$special_label = " - Golden ";
			}
			$together_choices_next_array[$i] = $i.$special_label;

		}


		
		$cur_domain_id = "-1";
			
		$result = civicrm_api3('Domain', 'get', array(
				'sequential' => 1,
				'current_domain' => "",
		));
			
		if( $result['is_error'] == 0 ){
			$cur_domain_id = $result['id'];
		
		}
		// get membership ids and org contact ids.
		$mem_ids = array();
		$org_ids = array();
		$api_result = civicrm_api3('MembershipType', 'get', array(
				'sequential' => 1,
				'is_active' => 1,
				'domain_id' =>  $cur_domain_id ,
				'options' => array('sort' => "name"),
		));
		
		if( $api_result['is_error'] == 0 ){
			$tmp_api_values = $api_result['values'];
			foreach($tmp_api_values as $cur){
		
				$tmp_id = $cur['id'];
				$mem_ids[$tmp_id] = $cur['name'];
					
				$org_id = $cur['member_of_contact_id'];
				// get display name of org
				$result = civicrm_api3('Contact', 'getsingle', array(
						'sequential' => 1,
						'id' => $org_id ,
				));
				$org_ids[$org_id] = $result['display_name'];
		
					
			}
		
		}
	
		//$mem_org_ids = array();
		//$mem_types_ids = array();

	 $this->_columns = array(
	 		'civicrm_contact' =>
	 		array(
	 				'dao' => 'CRM_Contact_DAO_Contact',
	 				'fields' =>
	 				array(
	 						'sort_name_a' =>
	 						array('title' => ts('Name'),
	 								'name' => 'sort_name',
	 								'required' => TRUE,
	 						),
	 						'id' =>
	 						array(
	 								'no_display' => TRUE,
	 								'required' => TRUE,
	 						),

	 				),
	 				'filters' =>
	 				array(
	 						'sort_name_a' =>
	 						array('title' => ts('Contact A'),
	 								'name' => 'sort_name',
	 								'operator' => 'like',
	 								'type' => CRM_Report_Form::OP_STRING,
	 						),
	 				),
	 				'grouping' => 'conact_a_fields',
	 		),
	 		'civicrm_contact_b' =>
	 		array(
	 				'dao' => 'CRM_Contact_DAO_Contact',
	 				'alias' => 'contact_b',
	 				'fields' =>
	 				array(
	 						'sort_name_b' =>
	 						array('title' => ts('Spouse Name'),
	 								'name' => 'sort_name',
	 								'required' => TRUE,
	 						),
	 						'id' =>
	 						array(
	 								'no_display' => TRUE,
	 								'required' => TRUE,
	 						),
	 						'joint_greeting' =>
	 						array('title' => ts('Joint Greeting'),
	 								'dbAlias' =>  " '' ",
	 						),

	 				),
	 				'filters' =>
	 				array(
	 						'sort_name_b' =>
	 						array('title' => ts('Contact B'),
	 								'name' => 'sort_name',
	 								'operator' => 'like',
	 								'type' => CRM_Report_Form::OP_STRING,
	 						),
	 				),
	 				'grouping' => 'conact_b_fields',
	 		),
	 		'civicrm_email' =>
	 		array(
	 				'dao' => 'CRM_Core_DAO_Email',
	 				'fields' =>
	 				array(
	 						'email_a' =>
	 						array('title' => ts('Email of Contact A'),
	 								'name' => 'email',
	 						),
	 				),
	 				'grouping' => 'conact_a_fields',
	 		),
	 		'civicrm_email_b' =>
	 		array(
	 				'dao' => 'CRM_Core_DAO_Email',
	 				'alias' => 'email_b',
	 				'fields' =>
	 				array(
	 						'email_b' =>
	 						array('title' => ts('Email of Contact B'),
	 								'name' => 'email',
	 						),
	 				),
	 				'grouping' => 'conact_b_fields',
	 		),
	 		'civicrm_relationship_type' =>
	 		array(
	 				'dao' => 'CRM_Contact_DAO_RelationshipType',
	 				'fields' =>
	 				array(
	 						'label_a_b' =>
	 						array('title' => ts('Relationship A-B '),
	 								'default' => FALSE,
	 						),
	 						'label_b_a' =>
	 						array('title' => ts('Relationship B-A '),
	 								'default' => FALSE,
	 						),
	 				),
	 				'filters' =>
	 				array(

	 				),
	 				'grouping' => 'relation-fields',
	 		),
	 		'civicrm_relationship' =>
	 		array(
	 				'dao' => 'CRM_Contact_DAO_Relationship',
	 				'fields' =>
	 				array(
	 						'anniversary_date_formatted' =>
	 						array('title' => ts('Anniversary Date (formatted)'),
	 								'dbAlias' =>  " CONCAT( monthname(start_date) , ' ',  day(start_date)) ",
	 								'default' => TRUE,
	 						),
	 						'occasion_date' =>
	 						array('title' => ts('Anniversary Date (sortable)'),
	 								'dbAlias' =>  "date_format(start_date, '%m-%d' ) ",
	 						),
	 						'anniversary_year' =>
	 						array('title' => ts('Year of Wedding'),
	 								'dbAlias' =>  " YEAR(start_date) ",
	 								'default' => TRUE,
	 						),
	 						'years_married_now' =>
	 						array('title' => ts('Num. Years Married (as of today)'),
	 								'dbAlias' =>  " TIMESTAMPDIFF(YEAR, start_date, CURDATE()) ",
	 						),
	 						'years_married_next' =>
	 						array('title' => ts('Num. Years Married (on next anniversary)'),
	 								'dbAlias' =>  " TIMESTAMPDIFF(YEAR, start_date, CURDATE()) + 1  ",
	 								'default' => TRUE,
	 						),
	 						'occasion_type' =>
	 						array('title' => ts('Occasion Type'),
	 								'dbAlias' =>  " 'Anniversary' ",
	 								'default' => TRUE,
	 						),
	 						'description' =>
	 						array('title' => ts('Relationship Description'),
	 						),
	 				),
	 				'filters' =>
	 				array(
	 						'occasion_date' =>
	 						array(
	 								'dbAlias' => " concat( YEAR( CURDATE()),   date_format(start_date, '%m%d' ) ) ",
	 								'title' => ts('Date Range'),
	 								'operatorType' => CRM_Report_Form::OP_DATE,
	 								'type' => CRM_Utils_Type::T_DATE
	 						),
	 						'years_married_now' =>
	 						array('title' => ts('Num. of Years Married (as of today)'),
	 								'dbAlias' => " TIMESTAMPDIFF(YEAR, start_date, CURDATE()) ",
	 								'operatorType' => CRM_Report_Form::OP_MULTISELECT,
	 								'type' => CRM_Utils_Type::T_INT,
	 								'options' =>  $together_choices_array,
	 						),
	 						'years_married_next' =>
	 						array('title' => ts('Num. of Years Married (on next anniversary)'),
	 								'dbAlias' => " TIMESTAMPDIFF(YEAR, start_date, CURDATE()) + 1  ",
	 								'operatorType' => CRM_Report_Form::OP_MULTISELECT,
	 								'type' => CRM_Utils_Type::T_INT, 
	 								'options' => $together_choices_next_array,
	 						),
	 						'membership_org' =>
	 						array( 'title' => ts('Membership Organization'),
	 								'name' => " membership_org ",
	 								'membership_org' => TRUE,
	 								'operatorType' => CRM_Report_Form::OP_MULTISELECT,
	 								'type' => CRM_Utils_Type::T_INT,
	 								'options' => $org_ids,
	 						),

	 						'membership_type' =>
	 						array( 'title' => ts('Membership Type'),
	 								'name' => " membership_type ",
	 								'membership_type' => TRUE,
	 								'operatorType' => CRM_Report_Form::OP_MULTISELECT,
	 								'type' => CRM_Utils_Type::T_INT,
	 								'options' => $mem_ids,
	 						),

	 				),
	 				'order_bys' =>
	 				array(
	 						'sort_name' =>
	 						array('title' => ts('Last Name, First Name'),
	 						),

	 						'occasion_date' =>
	 						array(
	 								'dbAlias' => " date_format(start_date, '%m%d' )",
	 								'title' => ts('Anniversary Date'),
	 						),
	 						'occasion_month' =>
	 						array(
	 								'dbAlias' => " date_format(start_date, '%m' )",
	 								'title' => ts('Anniversary Month'),
	 						),
	 						'years_married' =>
	 						array(
	 								'dbAlias' => "  TIMESTAMPDIFF(YEAR, start_date, CURDATE()) ",
	 								'title' => ts('Num. of Years Married'),
	 						),
	 				),
	 				'grouping' => 'relation-fields',
	 		),
	 		'civicrm_address' =>
	 		array(
	 				'dao' => 'CRM_Core_DAO_Address',
	 				'filters' =>
	 				array(

	 				),
	 				'grouping' => 'contact-fields',
	 		),
	 		'civicrm_group' =>
	 		array(
	 				'dao' => 'CRM_Contact_DAO_Group',
	 				'alias' => 'cgroup',
	 				'filters' =>
	 				array(
	 						'gid' =>
	 						array(
	 								'name' => 'group_id',
	 								'title' => ts('Group'),
	 								'operatorType' => CRM_Report_Form::OP_MULTISELECT,
	 								'group' => TRUE,
	 								'type' => CRM_Utils_Type::T_INT,
	 								'options' => CRM_Core_PseudoConstant::nestedGroup(),
	 						),
	 				),
	 		),
	 );

	 $this->_tagFilter = TRUE;
	 parent::__construct();
	}

	function preProcess() {
		parent::preProcess();
	}

	function select() {
		$select = $this->_columnHeaders = array();
		foreach ($this->_columns as $tableName => $table) {
			if (array_key_exists('fields', $table)) {
				foreach ($table['fields'] as $fieldName => $field) {
					if (CRM_Utils_Array::value('required', $field) ||
							CRM_Utils_Array::value($fieldName, $this->_params['fields'])
							) {

								if ($fieldName == 'email_a') {
									$this->_emailField_a = TRUE;
								}
								if ($fieldName == 'email_b') {
									$this->_emailField_b = TRUE;
								}
								$select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
								$this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);
								$this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = CRM_Utils_Array::value('title', $field);
							}
				}
			}
		}

		$this->_select = "SELECT " . implode(', ', $select) . " ";
	}

	function from() {
		$this->_from = "
		FROM civicrm_relationship {$this->_aliases['civicrm_relationship']}

		INNER JOIN civicrm_contact {$this->_aliases['civicrm_contact']}
		ON ( {$this->_aliases['civicrm_relationship']}.contact_id_a =
		{$this->_aliases['civicrm_contact']}.id )

		INNER JOIN civicrm_contact {$this->_aliases['civicrm_contact_b']}
		ON ( {$this->_aliases['civicrm_relationship']}.contact_id_b =
		{$this->_aliases['civicrm_contact_b']}.id )

		{$this->_aclFrom} ";

		if (!empty($this->_params['country_id_value']) ||
				!empty($this->_params['state_province_id_value'])
				) {
					$this->_from .= "
					INNER  JOIN civicrm_address {$this->_aliases['civicrm_address']}
					ON (( {$this->_aliases['civicrm_address']}.contact_id =
					{$this->_aliases['civicrm_contact']}.id  OR
					{$this->_aliases['civicrm_address']}.contact_id =
					{$this->_aliases['civicrm_contact_b']}.id ) AND
					{$this->_aliases['civicrm_address']}.is_primary = 1 ) ";
				}

				$this->_from .= "
				INNER JOIN civicrm_relationship_type {$this->_aliases['civicrm_relationship_type']}
				ON ( {$this->_aliases['civicrm_relationship']}.relationship_type_id  =
				{$this->_aliases['civicrm_relationship_type']}.id  ) ";

				// include Email Field
				if ($this->_emailField_a) {
					$this->_from .= "
					LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']}
					ON ( {$this->_aliases['civicrm_contact']}.id =
					{$this->_aliases['civicrm_email']}.contact_id AND
					{$this->_aliases['civicrm_email']}.is_primary = 1 )";
				}
				if ($this->_emailField_b) {
					$this->_from .= "
					LEFT JOIN civicrm_email {$this->_aliases['civicrm_email_b']}
					ON ( {$this->_aliases['civicrm_contact_b']}.id =
					{$this->_aliases['civicrm_email_b']}.contact_id AND
					{$this->_aliases['civicrm_email_b']}.is_primary = 1 )";
				}
	}

	function where() {
		$whereClauses = $havingClauses = array();
		foreach ($this->_columns as $tableName => $table) {
			if (array_key_exists('filters', $table)) {
				foreach ($table['filters'] as $fieldName => $field) {

					$clause = NULL;
					if (CRM_Utils_Array::value('type', $field) & CRM_Utils_Type::T_DATE) {
						$relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
						$from     = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
						$to       = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);


						$clause = $this->dateClause($field['dbAlias'], $relative, $from, $to, $field['type']);
					}
					else {
						$op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
						if ($op) {

							if ($tableName == 'civicrm_relationship_type' &&
									($fieldName == 'contact_type_a' || $fieldName == 'contact_type_b')
									) {
										$cTypes = CRM_Utils_Array::value("{$fieldName}_value", $this->_params);
										$contactTypes = $contactSubTypes = array();
										if (!empty($cTypes)) {
											foreach ($cTypes as $ctype) {
												$getTypes = CRM_Utils_System::explode('_', $ctype, 2);
												if ($getTypes[1] && !in_array($getTypes[1], $contactSubTypes)) {
													$contactSubTypes[] = $getTypes[1];
												}
												elseif ($getTypes[0] && !in_array($getTypes[0], $contactTypes)) {
													$contactTypes[] = $getTypes[0];
												}
											}
										}

										if (!empty($contactTypes)) {
											$clause = $this->whereClause($field,
													$op,
													$contactTypes,
													CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
													CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
													);
										}

										if (!empty($contactSubTypes)) {
											if ($fieldName == 'contact_type_a') {
												$field['name'] = 'contact_sub_type_a';
											}
											else {
												$field['name'] = 'contact_sub_type_b';
											}
											$field['dbAlias'] = $field['alias'] . '.' . $field['name'];
											$subTypeClause = $this->whereClause($field,
													$op,
													$contactSubTypes,
													CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
													CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
													);
											if ($clause) {
												$clause = '(' . $clause . ' OR ' . $subTypeClause . ')';
											}
											else {
												$clause = $subTypeClause;
											}
										}
									}
									else {

										$clause = $this->whereClause($field,
												$op,
												CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
												CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
												CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
												);
									}
						}
					}

					if (!empty($clause)) {
						if (CRM_Utils_Array::value('having', $field)) {
							$havingClauses[] = $clause;
						}
						else {
							$whereClauses[] = $clause;
						}
					}
				}
			}
		}


		// Make sure no one is deleted or dececased.
		$whereClauses[] = " {$this->_aliases['civicrm_contact']}.is_deleted <> 1 ";
		$whereClauses[] = " {$this->_aliases['civicrm_contact_b']}.is_deleted <> 1 ";
		$whereClauses[] = " {$this->_aliases['civicrm_contact']}.is_deceased <> 1 ";
		$whereClauses[] = " {$this->_aliases['civicrm_contact_b']}.is_deceased <> 1 ";

		// Make sure the relationship is active and has a start date.
		$whereClauses[] = " {$this->_aliases['civicrm_relationship']}.is_active = 1 ";
		$whereClauses[] = " {$this->_aliases['civicrm_relationship']}.start_date IS NOT NULL ";

		$whereClauses[] = " lower( {$this->_aliases['civicrm_relationship_type']}.name_a_b ) like '%spouse%' ";

		if (empty($whereClauses)) {
			$this->_where = 'WHERE ( 1 ) ';
			$this->_having = '';
		}
		else {
			$this->_where = 'WHERE ' . implode(' AND ', $whereClauses);
		}

		if ($this->_aclWhere) {
			$this->_where .= " AND {$this->_aclWhere} ";
		}
		// print "<br><br>debug sql: ". $this->_where;

		if (!empty($havingClauses)) {
			// use this clause to construct group by clause.
			$this->_having = 'HAVING ' . implode(' AND ', $havingClauses);
		}
	}

	function statistics(&$rows) {
		$statistics = parent::statistics($rows);

		$isStatusFilter = FALSE;
		$relStatus = NULL;
		if (CRM_Utils_Array::value('is_active_value', $this->_params) == '1') {
			$relStatus = 'Is equal to Active';
		}
		elseif (CRM_Utils_Array::value('is_active_value', $this->_params) == '0') {
			$relStatus = 'Is equal to Inactive';
		}
		if (CRM_Utils_Array::value('filters', $statistics)) {
			foreach ($statistics['filters'] as $id => $value) {
				//for displaying relationship type filter
				if ($value['title'] == 'Relationship') {
					$relTypes = CRM_Core_PseudoConstant::relationshipType();
					$statistics['filters'][$id]['value'] = 'Is equal to ' . $relTypes[$this->_params['relationship_type_id_value']]['label_' . $this->relationType];
				}

				//for displaying relationship status
				if ($value['title'] == 'Relationship Status') {
					$isStatusFilter = TRUE;
					$statistics['filters'][$id]['value'] = $relStatus;
				}
			}
		}
		//for displaying relationship status
		if (!$isStatusFilter && $relStatus) {
			$statistics['filters'][] = array(
					'title' => 'Relationship Status',
					'value' => $relStatus,
			);
		}
		return $statistics;
	}

	function groupBy() {
		$this->_groupBy = " ";
		$groupBy = array();
		if ($this->relationType == 'a_b') {
			$groupBy[] = " {$this->_aliases['civicrm_contact']}.id";
		}
		elseif ($this->relationType == 'b_a') {
			$groupBy[] = " {$this->_aliases['civicrm_contact_b']}.id";
		}

		if (!empty($groupBy)) {
			$this->_groupBy = " GROUP BY  " . implode(', ', $groupBy) . " ,  {$this->_aliases['civicrm_relationship']}.id ";
		}
		else {
			$this->_groupBy = " GROUP BY {$this->_aliases['civicrm_relationship']}.id ";
		}
	}



	function postProcess() {
		$this->beginPostProcess();

		$this->relationType = NULL;
		$relType = array();
		if (CRM_Utils_Array::value('relationship_type_id_value', $this->_params)) {
			$relType = explode('_', $this->_params['relationship_type_id_value']);

			$this->relationType = $relType[1] . '_' . $relType[2];
			$this->_params['relationship_type_id_value'] = intval($relType[0]);
		}

		$this->buildACLClause(array($this->_aliases['civicrm_contact'], $this->_aliases['civicrm_contact_b']));
		$sql = $this->buildQuery();
		$this->buildRows($sql, $rows);

		$this->formatDisplay($rows);
		$this->doTemplateAssignment($rows);

		if (!empty($relType)) {
			// store its old value, CRM-5837
			$this->_params['relationship_type_id_value'] = implode('_', $relType);
		}
		$this->endPostProcess($rows);
	}

	function alterDisplay(&$rows) {
		// custom code to alter rows
		$entryFound = FALSE;

		foreach ($rows as $rowNum => $row) {

			// handle country
			if (array_key_exists('civicrm_address_country_id', $row)) {
				if ($value = $row['civicrm_address_country_id']) {
					$rows[$rowNum]['civicrm_address_country_id'] = CRM_Core_PseudoConstant::country($value, FALSE);
				}
				$entryFound = TRUE;
			}

			if (array_key_exists('civicrm_address_state_province_id', $row)) {
				if ($value = $row['civicrm_address_state_province_id']) {
					$rows[$rowNum]['civicrm_address_state_province_id'] = CRM_Core_PseudoConstant::stateProvince($value, FALSE);
				}
				$entryFound = TRUE;
			}

			if (array_key_exists('civicrm_contact_sort_name_a', $row) &&
					array_key_exists('civicrm_contact_id', $row)
					) {
					
						/*
						 $url = CRM_Report_Utils_Report::getNextUrl('contact/detail',
								'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_id'],
								$this->_absoluteUrl, $this->_id, $this->_drilldownReport
								);
								*/
						$url = "/civicrm/contact/view?reset=1&cid=".$row['civicrm_contact_id'];
						
						$rows[$rowNum]['civicrm_contact_sort_name_a_link'] = $url;
						$rows[$rowNum]['civicrm_contact_sort_name_a_hover'] = ts("View Contact details for this contact.");
						$entryFound = TRUE;
					}

					if (array_key_exists('civicrm_contact_b_sort_name_b', $row) &&
							array_key_exists('civicrm_contact_b_id', $row)
							) {
								
								/*
								$url = CRM_Report_Utils_Report::getNextUrl('contact/detail',
										'reset=1&force=1&id_op=eq&id_value=' . $row['civicrm_contact_b_id'],
										$this->_absoluteUrl, $this->_id, $this->_drilldownReport
										);
										*/
								
								$url = "/civicrm/contact/view?reset=1&cid=".$row['civicrm_contact_b_id'];
								$rows[$rowNum]['civicrm_contact_b_sort_name_b_link'] = $url;
								$rows[$rowNum]['civicrm_contact_b_sort_name_b_hover'] = ts("View Contact details for this contact.");
								$entryFound = TRUE;
							}

							 
							if (array_key_exists('civicrm_contact_b_joint_greeting', $row)) {
								$params = array(
										'version' => 3,
										'sequential' => 1,
										'contact_id' => $row['civicrm_contact_id'],
								);
								$result = civicrm_api('JointGreetings', 'getsingle', $params);

								$rows[$rowNum]['civicrm_contact_b_joint_greeting'] = $result['greetings.joint_casual'];

							}



							// skip looking further in rows, if first row itself doesn't
							// have the column we need
							if (!$entryFound) {
								break;
							}
		}
	}
	
}
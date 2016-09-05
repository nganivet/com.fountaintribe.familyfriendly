<?php

// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array(
		0 => array(
				'name' => 'Anniversary Report',
				'entity' => 'ReportTemplate',
				'params' => array(
						'version' => 3,
						'label' => 'Anniversary Report',
						'description' => 'Anniversary Report - Lists wedding anniversaries (Extension: com.fountaintribe.familyfriendly https://civicrm.org/extensions/family-friendly)',
						'class_name' => 'CRM_Familyfriendly_Form_Report_AnniversaryReport',
						'report_url' => 'fountaintribe/familyfriendly/anniversary',
						'component' => '',
				),
		),
);

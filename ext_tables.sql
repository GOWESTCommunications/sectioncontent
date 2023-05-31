#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_sectioncontent_abstract_title text  NULL,
	tx_sectioncontent_abstract_subtitle text  NULL,
	tx_sectioncontent_abstract_description text  NULL,
	tx_sectioncontent_abstract_attr_1 text  NULL,
	tx_sectioncontent_abstract_attr_2 text  NULL,
	tx_sectioncontent_abstract_attr_3 text  NULL,
	tx_sectioncontent_abstract_attr_4 text  NULL,
	tx_sectioncontent_abstract_attr_5 text  NULL,
	tx_sectioncontent_abstract_attr_6 text  NULL,
	tx_sectioncontent_abstract_attr_7 text  NULL,
	tx_sectioncontent_abstract_attr_8 text  NULL,
	tx_sectioncontent_abstract_image int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_image_2 int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_image_3 int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_image_4 int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_reference_url text  NULL
);
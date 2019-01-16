#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_sectioncontent_abstract_title text NOT NULL,
	tx_sectioncontent_abstract_subtitle text NOT NULL,
	tx_sectioncontent_abstract_description text NOT NULL,
	tx_sectioncontent_abstract_attr_1 text NOT NULL,
	tx_sectioncontent_abstract_attr_2 text NOT NULL,
	tx_sectioncontent_abstract_attr_3 text NOT NULL,
	tx_sectioncontent_abstract_attr_4 text NOT NULL,
	tx_sectioncontent_abstract_attr_5 text NOT NULL,
	tx_sectioncontent_abstract_attr_6 text NOT NULL,
	tx_sectioncontent_abstract_attr_7 text NOT NULL,
	tx_sectioncontent_abstract_attr_8 text NOT NULL,
	tx_sectioncontent_abstract_image int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_image_2 int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_image_3 int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_image_4 int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_reference_url text NOT NULL
);

#
# Table structure for table 'pages_language_overlay'
#
CREATE TABLE pages_language_overlay (
	tx_sectioncontent_abstract_title text NOT NULL,
	tx_sectioncontent_abstract_subtitle text NOT NULL,
	tx_sectioncontent_abstract_description text NOT NULL,
	tx_sectioncontent_abstract_attr_1 text NOT NULL,
	tx_sectioncontent_abstract_attr_2 text NOT NULL,
	tx_sectioncontent_abstract_attr_3 text NOT NULL,
	tx_sectioncontent_abstract_attr_4 text NOT NULL,
	tx_sectioncontent_abstract_attr_5 text NOT NULL,
	tx_sectioncontent_abstract_attr_6 text NOT NULL,
	tx_sectioncontent_abstract_attr_7 text NOT NULL,
	tx_sectioncontent_abstract_attr_8 text NOT NULL,
	tx_sectioncontent_abstract_image int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_image_2 int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_image_3 int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_image_4 int(11) unsigned DEFAULT '0' NOT NULL,
	tx_sectioncontent_abstract_reference_url text NOT NULL
);
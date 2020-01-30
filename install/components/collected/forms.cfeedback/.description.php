<?
/**
 * Copyright (c) 30/1/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("MAIN_CFEEDBACK_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("MAIN_CFEEDBACK_COMPONENT_DESCR"),
	"ICON" => "/images/feedback.gif",
	"PATH" => array(
		"ID" => "collected",
		"NAME" => GetMessage("COMPONENT_FOLDER_NAME"),
		"CHILD" => array(
			"ID" => "forms",
			"NAME" => GetMessage("COMPONENT_SUBFOLDER_NAME"),
			"SORT" => 10,
		)
	),
);
?>
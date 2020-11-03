<?
/**
 * Copyright (c) 30/1/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

global $DOCUMENT_ROOT, $MESS;

IncludeModuleLangFile(__FILE__);

if (class_exists("kit_cfeedback")) return;

Class kit_cfeedback extends CModule
{
	var $MODULE_ID = "kit.cfeedback";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;
	var $MODULE_GROUP_RIGHTS = "Y";

	function kit_cfeedback()
	{
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
		
		$this->PARTNER_NAME = "ASDAFF";
		$this->PARTNER_URI = "https://asdaff.github.io/";

		$this->MODULE_NAME = GetMessage("KIT_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("KIT_MODULE_DESCRIPTION");
	}

	function InstallEvents()
	{
	
		$arFilter = array(
			"TYPE_ID" => "KIT_CFEEDBACK_FORM",
			);
		$rsET = CEventType::GetList($arFilter);
		if ($arET = $rsET->Fetch()):

		else:

			$DESCRIPTION = GetMessage("KIT_ETYPE_DESCRIPTION_TEXT");


			$et = new CEventType;
			$et->Add(array(
					"LID"           => "ru",
					"EVENT_NAME"    => "KIT_CFEEDBACK_FORM",
					"NAME"          => GetMessage("KIT_ETYPE_NAME"),
					"DESCRIPTION"   => $DESCRIPTION
				));

		endif;

		$arFilter = Array(
			"TYPE_ID"       => "KIT_CFEEDBACK_FORM",
			);
		$rsMess = CEventMessage::GetList($by="id", $order="desc", $arFilter);

		if ($arMess = $rsMess->Fetch()):
		else:

			$rsSites = CSite::GetList($by="def", $order="desc", Array());
			$arSite = $rsSites->Fetch();

			$arrMess["ACTIVE"] = "Y";
			$arrMess["EVENT_NAME"] = "KIT_CFEEDBACK_FORM";
			$arrMess["LID"] = $arSite["ID"];
			$arrMess["EMAIL_FROM"] = "#DEFAULT_EMAIL_FROM#";
			$arrMess["EMAIL_TO"] = "#EMAIL_TO#";
			$arrMess["SUBJECT"] = GetMessage("KIT_EMESS_SUBJECT");
			$arrMess["BODY_TYPE"] = "text";
			$arrMess["MESSAGE"] = GetMessage("KIT_EMESS_MESSAGE");

			$emess = new CEventMessage;
			$emess->Add($arrMess);

		endif;
	
	}
	
	function DoInstall()
	{
		global $APPLICATION;

		if (!IsModuleInstalled("kit.cfeedback"))
		{
			RegisterModule("kit.cfeedback");
			CopyDirFiles(
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kit.cfeedback/install/components/",
				$_SERVER["DOCUMENT_ROOT"]."/bitrix/components",
				true, true
			);
			
			$this->InstallEvents();
			
		}
	}

	function DoUninstall()
	{
		UnRegisterModule("kit.cfeedback");
	}
}
?>
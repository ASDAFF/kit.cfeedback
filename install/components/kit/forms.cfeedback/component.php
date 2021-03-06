<?php
/**
 * Copyright (c) 30/1/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $this
 * @global CMain $APPLICATION
 * @global CUser $USER
 */

$arResult["PARAMS_HASH"] = md5(serialize($arParams).$this->GetTemplateName());

$arParams["USE_CAPTCHA"] = (($arParams["USE_CAPTCHA"] != "N" && !$USER->IsAuthorized()) ? "Y" : "N");
$arParams["EVENT_NAME"] = trim($arParams["EVENT_NAME"]);
if($arParams["EVENT_NAME"] == '')
	$arParams["EVENT_NAME"] = "KIT_CFEEDBACK_FORM";
$arParams["EMAIL_TO"] = trim($arParams["EMAIL_TO"]);
if($arParams["EMAIL_TO"] == '')
	$arParams["EMAIL_TO"] = COption::GetOptionString("main", "email_from");
$arParams["OK_TEXT"] = trim($arParams["OK_TEXT"]);
if($arParams["OK_TEXT"] == '')
	$arParams["OK_TEXT"] = GetMessage("COLLECT_OK_MESSAGE");

if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["submit"] <> '' && (!isset($_POST["PARAMS_HASH"]) || $arResult["PARAMS_HASH"] === $_POST["PARAMS_HASH"]))
{
	$arResult["ERROR_MESSAGE"] = array();
	if(check_bitrix_sessid())
	{

			if($arParams["USE_FIELD_NAME"]=="Y" && $arParams["CHECK_FIELD_NAME"]=="Y" && strlen($_POST["f_name"]) <= 1)
				$arResult["ERROR_MESSAGE"][] = GetMessage("COLLECT_REQ_NAME");
			if($arParams["USE_FIELD_PHONE"]=="Y" && $arParams["CHECK_FIELD_PHONE"]=="Y" && strlen($_POST["f_phone"]) <= 1)
				$arResult["ERROR_MESSAGE"][] = GetMessage("COLLECT_REQ_PHONE");
			if($arParams["USE_FIELD_EMAIL"]=="Y" && $arParams["CHECK_FIELD_EMAIL"]=="Y" && strlen($_POST["f_email"]) <= 1)
				$arResult["ERROR_MESSAGE"][] = GetMessage("COLLECT_REQ_EMAIL");
			if($arParams["USE_FIELD_TEXT"]=="Y" && $arParams["CHECK_FIELD_TEXT"]=="Y" && strlen($_POST["f_text"]) <= 3)
				$arResult["ERROR_MESSAGE"][] = GetMessage("COLLECT_REQ_TEXT");

		if(strlen($_POST["f_email"]) > 1 && !check_email($_POST["f_email"]))
			$arResult["ERROR_MESSAGE"][] = GetMessage("COLLECT_EMAIL_NOT_VALID");
		if($arParams["USE_CAPTCHA"] == "Y")
		{
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
			$captcha_code = $_POST["captcha_sid"];
			$captcha_word = $_POST["captcha_word"];
			$cpt = new CCaptcha();
			$captchaPass = COption::GetOptionString("main", "captcha_password", "");
			if (strlen($captcha_word) > 0 && strlen($captcha_code) > 0)
			{
				if (!$cpt->CheckCodeCrypt($captcha_word, $captcha_code, $captchaPass))
					$arResult["ERROR_MESSAGE"][] = GetMessage("COLLECT_CAPTCHA_WRONG");
			}
			else
				$arResult["ERROR_MESSAGE"][] = GetMessage("COLLECT_CAPTHCA_EMPTY");

		}			
		if(empty($arResult["ERROR_MESSAGE"]))
		{
			$arFields = Array(
				"NAME" => $_POST["f_name"],
				"PHONE" => $_POST["f_phone"],
				"EMAIL" => $_POST["f_email"],
				"EMAIL_TO" => $arParams["EMAIL_TO"],
				"TEXT" => $_POST["f_text"],
			);
			if(!empty($arParams["EVENT_MESSAGE_ID"]))
			{
				foreach($arParams["EVENT_MESSAGE_ID"] as $v)
					if(IntVal($v) > 0)
						CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, "N", IntVal($v));
			}
			else
				CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields);
			$_SESSION["COLLECT_NAME"] = htmlspecialcharsbx($_POST["f_name"]);
			$_SESSION["COLLECT_PHONE"] = htmlspecialcharsbx($_POST["f_phone"]);
			$_SESSION["COLLECT_EMAIL"] = htmlspecialcharsbx($_POST["f_email"]);
			LocalRedirect($APPLICATION->GetCurPageParam("success=".$arResult["PARAMS_HASH"], Array("success")));
		}
		
		$arResult["TEXT"] = htmlspecialcharsbx($_POST["f_text"]);
		$arResult["NAME"] = htmlspecialcharsbx($_POST["f_name"]);
		$arResult["PHONE"] = htmlspecialcharsbx($_POST["f_phone"]);
		$arResult["EMAIL"] = htmlspecialcharsbx($_POST["f_email"]);
	}
	else
		$arResult["ERROR_MESSAGE"][] = GetMessage("COLLECT_SESS_EXP");
}
elseif($_REQUEST["success"] == $arResult["PARAMS_HASH"])
{
	$arResult["OK_MESSAGE"] = $arParams["OK_TEXT"];
}

if(empty($arResult["ERROR_MESSAGE"]))
{
	if($USER->IsAuthorized())
	{
		$arResult["NAME"] = $USER->GetFormattedName(false);
		$arResult["EMAIL"] = htmlspecialcharsbx($USER->GetEmail());
	}
	else
	{
		if(strlen($_SESSION["COLLECT_NAME"]) > 0)
			$arResult["NAME"] = htmlspecialcharsbx($_SESSION["COLLECT_NAME"]);
		if(strlen($_SESSION["COLLECT_PHONE"]) > 0)
			$arResult["PHONE"] = htmlspecialcharsbx($_SESSION["COLLECT_PHONE"]);
		if(strlen($_SESSION["COLLECT_EMAIL"]) > 0)
			$arResult["EMAIL"] = htmlspecialcharsbx($_SESSION["COLLECT_EMAIL"]);
	}
}

if($arParams["USE_CAPTCHA"] == "Y")
	$arResult["capCode"] =  htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

$this->IncludeComponentTemplate();

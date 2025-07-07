<?php
require 'vendor/autoload.php';

use morphos\Russian\Cases;
use morphos\Russian\NounDeclension;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CBPDativeActivity extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = array(
            'words' => ''
        );

        $this->SetPropertiesTypes(array(
            'Result' => array(
                'Type' => 'bool'
            )
        ));
    }

    protected function ReInitialize()
    {
        parent::ReInitialize();
        $this->Result = false;
    }

    public function Execute()
    {
        $phrase = $this->words; // Ожидаем, что это одно словосочетание

        function declinePhraseToDative($phrase) {
            // Разбиваем фразу на слова
            $words = explode(' ', $phrase);
            $declinedWords = [];

            if (count($words) <= 2) {
                // Склоняем все слова, если их меньше или равно двум и второе слово не заканчивается на "я"
                foreach ($words as $index => $word) {
                    if ($index == 1 && mb_substr($word, -1) === 'я') {
                        $declinedWords[] = $word; // Не склоняем второе слово, если заканчивается на "я"
                    } else {
                        $declinedWord = NounDeclension::getCase($word, Cases::DATIVE);
                        $declinedWords[] = $declinedWord;
                    }
                }
            } else {
                // Склоняем только первое слово, если слов больше двух
                $declinedWord = NounDeclension::getCase($words[0], Cases::DATIVE);
                $declinedWords[] = $declinedWord;
                // Добавляем остальные слова без изменений
                $declinedWords = array_merge($declinedWords, array_slice($words, 1));
            }

            // Объединяем слова обратно в фразу
            return implode(' ', $declinedWords);
        }

        $result = declinePhraseToDative(trim($phrase));

        $this->WriteToTrackingService('Результат склонения: ' . $result);

        return CBPActivityExecutionStatus::Closed;
    }

    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = "", $popupWindow = null)
    {
        $runtime = CBPRuntime::GetRuntime();
        $documentService = $runtime->GetService("DocumentService");

        if (!is_array($arWorkflowParameters))
            $arWorkflowParameters = array();
        if (!is_array($arWorkflowVariables))
            $arWorkflowVariables = array();

        $arMap = array(
            "words" => "words"
        );

        if (!is_array($arCurrentValues))
        {
            $arCurrentValues = array();
            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
            if (is_array($arCurrentActivity["Properties"]))
            {
                foreach ($arMap as $k => $v)
                {
                    if (array_key_exists($k, $arCurrentActivity["Properties"]))
                    {
                        $arCurrentValues[$arMap[$k]] = $arCurrentActivity["Properties"][$k];
                    }
                    else
                    {
                        $arCurrentValues[$arMap[$k]] = "";
                    }
                }
            }
            else
            {
                foreach ($arMap as $k => $v)
                    $arCurrentValues[$arMap[$k]] = "";
            }
        }

        $arFieldTypes = $documentService->GetDocumentFieldTypes($documentType);
        $arDocumentFields = $documentService->GetDocumentFields($documentType);

        return $runtime->ExecuteResourceFile(
            __FILE__,
            "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "arDocumentFields" => $arDocumentFields,
                "arFieldTypes" => $arFieldTypes,
                "javascriptFunctions" => null,
                "formName" => $formName,
                "popupWindow" => &$popupWindow,
            )
        );
    }

    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
    {
        $arErrors = array();
        $runtime = CBPRuntime::GetRuntime();
        $arMap = array(
            "words" => "words"
        );
        $arProperties = array();

        foreach ($arMap as $key => $value)
        {
            $arProperties[$key] = $arCurrentValues[$value];
        }

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $arCurrentActivity["Properties"] = $arProperties;

        return true;
    }
}
?>
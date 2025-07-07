<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<tr>
    <td align="right" width="40%"><b>Слова в дательный падеж</b> <span style="color:#FF0000;">*</span> :</td>
    <td width="60%">
        <?=CBPDocument::ShowParameterField("string", 'words', $arCurrentValues['words'], array('size' => '50'))?>
    </td>
</tr>

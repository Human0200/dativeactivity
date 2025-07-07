<?php
require 'vendor/autoload.php';

use morphos\Russian\Cases;
use morphos\Russian\NounDeclension;

function declinePhraseToDative($phrase) {
    // Разбиваем фразу на слова
    $words = explode(' ', $phrase);
    $declinedWords = [];

    echo "Исходная фраза: $phrase\n";
    echo "Слова: " . implode(', ', $words) . "\n";

    if (count($words) <= 2) {
        // Склоняем все слова, если их меньше или равно двум и второе слово не заканчивается на "я"
        foreach ($words as $index => $word) {
            if ($index == 1 && mb_substr($word, -1) === 'я') {
                echo "Не склоняем второе слово: $word\n";
                $declinedWords[] = $word; // Не склоняем второе слово, если заканчивается на "я"
            } else {
                $declinedWord = NounDeclension::getCase($word, Cases::DATIVE);
                echo "Склоняем слово: $word -> $declinedWord\n";
                $declinedWords[] = $declinedWord;
            }
        }
    } else {
        // Склоняем только первое слово, если слов больше двух
        $declinedWord = NounDeclension::getCase($words[0], Cases::DATIVE);
        echo "Склоняем первое слово: {$words[0]} -> $declinedWord\n";
        $declinedWords[] = $declinedWord;
        // Добавляем остальные слова без изменений
        $declinedWords = array_merge($declinedWords, array_slice($words, 1));
    }

    // Объединяем слова обратно в фразу
    $result = implode(' ', $declinedWords);
    echo "Результат: $result\n\n";
    return $result;
}

// Примеры использования
$phrases = [
    'Старший бухгалтер',
    'Руководитель финансового отдела',
    'Ведущий инженер',
    'Специалист снабжения',
    'Начальник департамента интеграции Битрикс24'
];

$results = [];
foreach ($phrases as $phrase) {
    $results[] = declinePhraseToDative($phrase);
}

// Выводим результаты в формате JSON
echo json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
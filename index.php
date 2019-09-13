<?php

function readDocx($filePath) {
	
    $zip = new ZipArchive;
    $dataFile = 'word/document.xml';
	
    if (true === $zip->open($filePath)) {  
        if (($index = $zip->locateName($dataFile)) !== false) {
            $data = $zip->getFromIndex($index);    
            $zip->close();     
            $xml = DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            $contents = explode('\n',strip_tags($xml->saveXML()));
            $text = '';
            foreach($contents as $i=>$content) {
                $text .= $contents[$i];
            }
            return $text;
        }
        $zip->close();
    }
    return "";
}

function createHeader($header_, $text) {
	$header = '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="' .trim($header_). ' в Москве. Бесплатная диагностика. Гарантия качества. Записаться - 8(495)150-70-69">
    <meta name="keywords" content="' . trim($header_) . '">
    <title>' . trim($header_) . ' в Москве - Автосервис "Роверсити</title>
    </head>
	<body><h1 align="center">' . $header_ . '</h1><hr>' . $text . '
</body>
</html>
	';
	return $header;
}
function getHeader($read){
	$flag = 0;
	$array_words = explode(' ', $read);
	$head = array();
	$another_text_array = array();
	foreach($array_words as $key => $result){
		if ($flag == 0){
			if ($key == 0) {
				$word = $result;
				$head[] = $result;
				$another_text_array[] = $word;
			}
			if($key>0){			
				$word_ = trim($word);
				
				if (!preg_match("/$word_/i", "$result", $matches, PREG_OFFSET_CAPTURE)){
					$head[] = $result;				
				} else { 
					$first = substr("$result",0,$matches[0][1]);
					$head[] = $first;
					$flag = 1;
				}			
			}
		} else {
			$another_text_array[] = $result;
		}
		
	}
	$array_result = array(implode(' ', $head), implode(' ', $another_text_array));
	return $array_result;
}

/**
*		Инструкция:
*		файлы должны быть в одной папке с парсером. В массиве $array_names_files имена файлов.
* 		Реализовать можно было и по другому. Выбрал самый простой вариант.
*       
*       По реализации:
*       не стал загоняться, сделал все по - простому из-за отсутствия времени. Работал 3 дня по часу в день, получается задача
*       заняла 3-4 часа в общей сложности
*
**/

$array_names_files = array(
	'Покраска сколов на автомобиле Land Rover', 
	'Покраска зеркала Land Rover',
	'Покраска вмятины Land Rover',
	'Покраска арок Land Rover',
	'Покраска двери Land Rover',
	'Покраска деталей Land Rover',
	'Покраска капота Land Rover',
	'Покраска крыла Land Rover',
	'Покраска крыши Land Rover',
	'Покраска кузова Land Rover',
	'Покраска пластика салона Land Rover',
	'Покраска порогов Land Rover',
	'Покраска царапин на автомобиле Land Rover',
	'Покрасочные работы кузова Land Rover'
);

foreach($array_names_files as $result) {
	$read = readDocx("$result.docx"); 
	$headerAndText = getHeader($read);
	$html = createHeader($headerAndText[0],$headerAndText[1]);
	file_put_contents("$result.html", $html);
}

?>

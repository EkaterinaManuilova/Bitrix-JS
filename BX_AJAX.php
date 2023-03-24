<?php
//подключаем пролог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//устанавливаем заголовок страницы
$APPLICATION->SetTitle("AJAX");
//подключение ядро Bitrix и расширение AJAX
CJSCore::Init(array('ajax'));
$sidAjax = 'testAjax';
//проверка на наличие в запросе элемента 'ajax_form' и содержимого этого элемента-задано в переменной
if(isset($_REQUEST['ajax_form']) && $_REQUEST['ajax_form'] === $sidAjax){//если true
    $GLOBALS['APPLICATION']->RestartBuffer();//очистка буфера перед выводом данных для вывода результата без шапки и футера
    echo CUtil::PhpToJSObject(array(//пишем Hello, ошибок нет
        'RESULT' => 'HELLO',
        'ERROR' => ''
    ));
    die();//выход
}

?>
//если запрос еще не был отправлен, показ страницы
<div class="group">
    <div id="block"></div >
    <div id="process">wait ... </div >
</div>
<script>
    window.BXDEBUG = true;//включение вывода ошибок в консоль
    //функция загрузки данных
    function DEMOLoad(){
        BX.hide(BX("block"));//скрытие элемента "block"
        BX.show(BX("process"));//показ элемента "process"
        BX.ajax.loadJSON(//загрузка json-объект со страницы и вызов функции DEMOResponse.
            '<?=$APPLICATION->GetCurPage()?>?ajax_form=<?=$sidAjax?>',
            DEMOResponse
        );
    }
    //функция отображения данных на странице
    function DEMOResponse (data){
        BX.debug('AJAX-DEMOResponse ', data);//вывод в консоль полученных данных data
        BX("block").innerHTML = data.RESULT;//вставка в элемент "block" содержимого data.RESULT
        BX.show(BX("block"));//показ элемента "block"
        BX.hide(BX("process"));//скрытие элемента "process"
//вызов всех обработчиков собития DEMOUpdate для элемента BX("block")
        BX.onCustomEvent(
            BX(BX("block")),
            'DEMOUpdate'
        );
    }
//проверка DOM-структуры на загрузку, добавление обработчика событий
    BX.ready(function(){
        /*
        BX.addCustomEvent(BX("block"), 'DEMOUpdate', function(){
           window.location.href = window.location.href;
        });
        */
        BX.hide(BX("block"));//скрытие элемента "block"
        BX.hide(BX("process"));//скрытие элемента "process"

        //установка обработчика клика на дочерние элементы body, имеющие класс css_ajax
        BX.bindDelegate(
            document.body, 'click', {className: 'css_ajax' },
            //функция-обработчик клика: отменяет стандартное поведение и загружает данные
            function(e){
                if(!e)
                    e = window.event;

                DEMOLoad();
                return BX.PreventDefault(e);//отмена стандартного поведения
            }
        );

    });

</script>
<div class="css_ajax">click Me</div>
<?
//подключаем эпилог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>

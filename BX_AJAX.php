<?php
/**
 * Этот файл демонстрирует работу с AJAX-запросами с использованием Библиотеки Bitrix JS
 */

//подключаем пролог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//устанавливаем заголовок страницы
$APPLICATION->SetTitle("AJAX");

   // Инициализация ядра AJAX библиотеки Bitrix
   CJSCore::Init(array('ajax'));
   
   // Создаем уникальный идентификатор для AJAX-формы
   $sidAjax = 'testAjax';
   
   // Обработка AJAX-запроса
   // Проверяем, является ли текущий запрос AJAX-запросом с нашим идентификатором
if(isset($_REQUEST['ajax_form']) && $_REQUEST['ajax_form'] == $sidAjax){
   // Останавливаем стандартный вывод Bitrix (шапка, футер и т.д.)
   $GLOBALS['APPLICATION']->RestartBuffer();
   
   // Возвращаем данные в формате JSON
   // CUtil::PhpToJSObject преобразует PHP-массив в JavaScript-объект
   echo CUtil::PhpToJSObject(array(
            'RESULT' => 'HELLO', // Сообщение, которое будет отображено на странице
            'ERROR' => ''        // Поле для возможных ошибок (в данном случае пустое)
   ));
   
   // Завершаем выполнение скрипта, предотвращая вывод остального содержимого
   die();
}

?>
<div class="group">
   <!-- Блок для отображения результата AJAX-запроса -->
   <div id="block"></div>
   <!-- Блок с индикатором загрузки -->
   <div id="process">wait ... </div>
</div>

<script>
   // Включение режима отладки Bitrix JS
   window.BXDEBUG = true;

/**
 * Функция, инициирующая AJAX-запрос
 * Скрывает блок результата и показывает индикатор загрузки
 */
function DEMOLoad(){
   // Скрываем блок для результата
   BX.hide(BX("block"));
   // Показываем индикатор загрузки
   BX.show(BX("process"));
   
   // Отправляем AJAX-запрос методом GET
   // BX.ajax.loadJSON - упрощенный метод для загрузки JSON-данных
   BX.ajax.loadJSON(
      '<?=$APPLICATION->GetCurPage()?>?ajax_form=<?=$sidAjax?>', // URL с параметром идентификатора AJAX-формы
      DEMOResponse // Функция обратного вызова для обработки ответа
   );
}

/**
 * Функция для обработки ответа от сервера
 * @param {Object} data - полученные данные в формате JSON
 */
function DEMOResponse(data){
   // Вывод отладочной информации в консоль
   BX.debug('AJAX-DEMOResponse ', data);
   
   // Устанавливаем полученный результат в блок
   BX("block").innerHTML = data.RESULT;
   
   // Показываем блок с результатом
   BX.show(BX("block"));
   
   // Скрываем индикатор загрузки
   BX.hide(BX("process"));

   // Генерируем пользовательское событие DEMOUpdate
   // Это позволяет другим частям кода реагировать на обновление блока
   BX.onCustomEvent(
      BX(BX("block")),
      'DEMOUpdate'
   );
}

// Выполнение кода после полной загрузки DOM
BX.ready(function(){
   /*
   // Закомментированный код для перезагрузки страницы после обновления блока
   BX.addCustomEvent(BX("block"), 'DEMOUpdate', function(){
      window.location.href = window.location.href;
   });
   */
   
   // Изначально скрываем блок для результата и индикатор загрузки
   BX.hide(BX("block"));
   BX.hide(BX("process"));
   
   // Делегирование события клика на элементы с классом 'css_ajax'
   // BX.bindDelegate позволяет назначить обработчик события для всех элементов,
   // соответствующих определенному селектору, в том числе для тех, которые будут добавлены позже
    BX.bindDelegate(
      document.body,               // Элемент, на котором слушаем события
      'click',                     // Тип события
      {className: 'css_ajax' },    // Селектор для фильтрации элементов
      function(e){                 // Обработчик события
         // Кроссбраузерная проверка объекта события
         if(!e)
            e = window.event;
         
         // Вызываем функцию для AJAX-запроса
         DEMOLoad();
         
         // Предотвращаем стандартное поведение (например, переход по ссылке)
         return BX.PreventDefault(e);
      }
   );
   
});
</script>

<!-- Элемент, при клике на который будет выполнен AJAX-запрос -->
<div class="css_ajax">click Me</div>

<?php
//подключаем эпилог ядра bitrix
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>

/*
 *  (c) 2019 ИП Рагозин Денис Николаевич. Все права защищены.
 *
 *  Настоящий файл является частью программного продукта, разработанного ИП Рагозиным Денисом Николаевичем
 *  (ОГРНИП 315668300000095, ИНН 660902635476).
 *
 *  Алгоритм и исходные коды программного кода программного продукта являются коммерческой тайной
 *  ИП Рагозина Денис Николаевича. Любое их использование без согласия ИП Рагозина Денис Николаевича рассматривается,
 *  как нарушение его авторских прав.
 *   Ответственность за нарушение авторских прав наступает в соответствии с действующим законодательством РФ.
 */

function tinymce_button_video_select(ed) {
  $.ajax({
    url: urlPrefix + '/admin/api/videos',
    dataType: 'json',
    success: function(data){
      if (!data.length) {
        ed.windowManager.alert('Нет доступных видео');
        return;
      }
      ed.windowManager.open({
        title: "Выбрать видео",
        body: [{
          type: "ListBox",
          name: "video",
          label: "Видео",
          minWidth: 600,
          values: data,
          // onSelect: function (item) {
          //   var data = item.control.settings.data;
          // }
        }],
        onsubmit: function (e) {
          ed.insertContent('{video}' + e.data.video + "{/video}")
        },
      });
    }
  });
}

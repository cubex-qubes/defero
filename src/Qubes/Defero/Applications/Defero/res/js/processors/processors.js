/**
 * Created by tom.kay on 23/09/13.
 */

$(function () {
    $('#campaign-processors').sortable({
     containerSelector: 'table',
     itemPath: '> tbody',
     itemSelector: 'tr',
     placeholder: '<tr class="placeholder"/>',
     distance:20,
     onDrop: function($item, container, _super)
     {
       _super($item,container);
       $('#campaign-processors').css('color','#ccc').sortable('disable');
       $.ajax({
         url:window.location.href+'/processors/reorder',
         type:'POST',
         dataType:'json',
         data:{'order':container.serialize()},
         success:function(data) {
           window.location.reload(true);
         }
       });
     }
   });
});
/**
 * Created by tom.kay on 08/01/14.
 */

$(function () {
  $('#campaigns').sortable({
    containerSelector: 'table',
    itemPath: '> tbody',
    itemSelector: 'tr',
    placeholder: '<tr class="placeholder"/>',
    distance:20,
    serialize: function (parent, children, isContainer) {
      if (!isContainer) {
        return parent.data('cid');
      }
     return children;
    },
    onDrop: function($item, container, _super)
    {
      _super($item,container);
      console.log(container.serialize());
      $('#campaigns').css('color','#ccc').sortable('disable');
      $.ajax({
        url:window.location.href.replace('/campaigns','/campaigns/reorder'),
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

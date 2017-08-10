$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#processes').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#processes').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'title'},
      {'data': 'number'},
      {
        'data': 'line',
        'orderable': false
      },
      {
        'data': 'users',
        'orderable': false
      },
      {'data': 'created_at'},
      {'data': 'updated_at'},
      {
        'data': 'operations',
        'orderable': false
      }
    ]
  });

  //$("#processes_filter").css("display","none");
  /*$('.column_filter').on( 'keyup click', function(){
    var i = $(this).attr('data-column');
    var v = $(this).val();
    $('#processes').DataTable().columns(i).search(v).draw();
  });*/

});
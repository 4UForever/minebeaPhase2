$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#process-working').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'scrollX': true,
    'ajax': {
      'url': $('#process-working').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'desc']],
    'columns': [
      {'data': 'id'},
      {
        'data': 'full_name',
        'orderable': false
      },
      {'data': 'on_process'},
      {
        'data': 'working_process',
        'orderable': false
      },
      {
        'data': 'operations',
        'orderable': false
      }
    ]
  });

  $("#process-working_filter").css("display","none");
  $('.column_filter').on( 'keyup click', function(){
    var i = $(this).attr('data-column');
    var v = $(this).val();
    $('#process-working').DataTable().columns(i).search(v).draw();
  });

});
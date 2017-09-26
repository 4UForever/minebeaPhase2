$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#import-target').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#import-target').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'year'},
      {'data': 'month'},
      {'data': 'day'},
      {
        'data': 'line',
        'orderable': false
      },
      {
        'data': 'product',
        'orderable': false
      },
      {
        'data': 'process',
        'orderable': false
      },
      {'data': 'target_pc'},
      {'data': 'stock_pc'}
    ]
  });

  $("#import-target_filter").css("display","none");
  $('.column_filter').on( 'keyup click', function(){
    var i = $(this).attr('data-column');
    var v = $(this).val();
    $('#import-target').DataTable().columns(i).search(v).draw();
  });

});
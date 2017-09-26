$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#import-price').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#import-price').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'year'},
      {'data': 'month'},
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
      {'data': 'circle_time'},
      {'data': 'unit_price'}
    ]
  });

  $("#import-price_filter").css("display","none");
  $('.column_filter').on( 'keyup click', function(){
    var i = $(this).attr('data-column');
    var v = $(this).val();
    $('#import-price').DataTable().columns(i).search(v).draw();
  });

});
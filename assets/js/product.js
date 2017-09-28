


$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#products').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#products').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'title'},
      {
        'data': 'lines',
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

});



$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#groups').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#groups').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'name'},
      {'data': 'created_at'},
      {'data': 'updated_at'},
      {
        'data': 'operations',
        'orderable': false
      }
    ]
  });

});
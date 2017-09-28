


$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#document_categories').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#document_categories').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'title'},
      {'data': 'created_at'},
      {'data': 'updated_at'},
      {
        'data': 'operations',
        'orderable': false
      }
    ]
  });

});
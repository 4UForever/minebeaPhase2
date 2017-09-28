


$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#users').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#users').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[5, 'desc']],
    'columns': [
      {'data': 'id'},
      {'data': 'email'},
      {
        'data': 'group_names',
        'orderable': false
      },
      {'data': 'first_name'},
      {'data': 'last_name'},
      {'data': 'last_login'},
      {
        'data': 'processes',
        'orderable': false
      },
      {
        'data': 'leader',
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
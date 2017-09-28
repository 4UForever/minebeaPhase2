



$(document).ready(function() {                                 
  
  // ---------------------------------------------------------------------------
  // Data table
  
  $('#activities tfoot th').each(function() {
    var title = $('#activities thead th').eq($(this).index()).text();
    $(this).html('<input style="width:100%;" type="text" placeholder="Search ' + title + '"/>');
  }); 
  
  var datatable = $('#activities').dataTable({ 
    'initComplete': function(settings, json) {                
      $('.read-more-link').click(function() {
        var content = $(this).parents('.read-more-sub').siblings('.read-more-full').html();
        $.colorbox({
          'html': content,
          'width': '50%'
        });
      });  
    },      
    'drawCallback': function(settings) {                 
      $('.read-more-link').click(function() {
        var content = $(this).parents('.read-more-sub').siblings('.read-more-full').html();
        $.colorbox({
          'html': content,
          'width': '50%'
        });
      }); 
    },
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#activities').attr('data-url'),
      'type': 'POST' 
    },         
    'order': [[0, 'desc']],
    'columns': [      
      {'data': 'id'},
      {'data': 'type_title'},                   
      {'data': 'user_full_name'},                   
      {'data': 'line_title'},                   
      {'data': 'product_title'},                   
      {'data': 'process_title'},                   
      {'data': 'comment'},                   
      {'data': 'created_at'}, 
    ]
  });  
  
  var datatable = $('#activities').DataTable();      
  
  datatable.columns().every(function() {
    var self = this;

    $('input', this.footer()).on('keyup change', function() {
      self.search(this.value).draw();
    });
  });         
  
});
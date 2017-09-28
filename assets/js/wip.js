$(document).ready(function() {

  // ---------------------------------------------------------------------------
  // Data table

  $('#wips').dataTable({
    'responsive': true,
    'processing': true,
    'serverSide': true,
    'ajax': {
      'url': $('#wips').attr('data-url'),
      'type': 'POST'
    },
    'iDisplayLength': 50,
    'order': [[0, 'asc']],
    'columns': [
      {'data': 'id'},
      {'data': 'title'},
      {
        'data': 'line',
        'orderable': false
      },
      {
        'data': 'product',
        'orderable': false
      },
      {
        'data': 'processes',
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

  $("#wips_filter").css("display","none");
  $('.column_filter').on( 'keyup click', function(){
    var i = $(this).attr('data-column');
    var v = $(this).val();
    $('#wips').DataTable().columns(i).search(v).draw();
  });

  $('#line_id').change(function(){
    console.log("change law");
    var params = "line_id="+$(this).val();
    $.ajax({
      type: "GET",
      url: "/admin/wip/product-select",
      dataType: "html",
      data: params,
      cache: false,
      success: function(data){
        $("#product-choose").html(data);
      }
    });
  });

});